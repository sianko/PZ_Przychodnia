<?php

namespace Wizyta\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Wizyta\Model\Narzedzia as Narzedzia;
use Zend\Validator\Date as DataValid;

class IndexController extends AbstractActionController
{
    
    public function indexAction()
    {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            
            $get_oid = (int)$this->params()->fromRoute('oid', 0);

            $get_did = (int)$this->params()->fromRoute('did', 0);
            
            if(!$this->identity()) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
            
            if($this->identity()->poziom == 0){
                $get_oid = $this->identity()->id;
                $get_did = 0;
            } else if($get_did > 0 && intval($this->identity()->poziom) === 1){
                $get_oid = 0;
                
                $l = $objectManager->find('Application\Entity\Lekarz', $get_did);
                
                if(!($l instanceof \Application\Entity\Lekarz) || ($l->getId() != $this->identity()->id)){
                    return $this->redirect()->toRoute('wizyta');
                }
            } else if(intval($this->identity()->poziom) === 1){
                $repository = $objectManager->getRepository('Application\Entity\Lekarz');
                $queryBuilder = $repository->createQueryBuilder('dr');
                $queryBuilder->where($queryBuilder->getRootAlias().'.os = '.$this->identity()->id);
                $query = $queryBuilder->getQuery();
                $tabLek = $query->execute();
            }
    
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            
            $repository = $objectManager->getRepository('Application\Entity\Wizyta');
            $queryBuilder = $repository->createQueryBuilder('wz');
            
            $get_category = (int)$this->params()->fromRoute('category', 0);
            $get_category_subparam = 0;
            if($get_category >= 10){
                $get_category_subparam = $get_category % 10;
                $get_category = ($get_category-$get_category_subparam)/10;
            }
            
            if($get_category == 0 || $get_category == 3){ // tylko przyszłe
                $queryBuilder->where($queryBuilder->getRootAlias().'.data >= \''.date('Y-m-d H:i').'\'');
            } else if($get_category == 2){ // tylko przeszłe
                $queryBuilder->where($queryBuilder->getRootAlias().'.data < \''.date('Y-m-d H:i').'\'');
            }
            
            if($get_category_subparam == 1){
               $queryBuilder->andWhere($queryBuilder->getRootAlias().'.pacjent != 1');
            } else if($get_category_subparam == 2){
               $queryBuilder->andWhere($queryBuilder->getRootAlias().'.pacjent != 1');
               $queryBuilder->andWhere($queryBuilder->getRootAlias().'.status = 0');
            } else if($get_category_subparam == 3){
               $queryBuilder->andWhere($queryBuilder->getRootAlias().'.pacjent != 1');
               $queryBuilder->andWhere($queryBuilder->getRootAlias().'.status > 0');
            }
            
            if($get_oid > 0){
                $queryBuilder->andWhere($queryBuilder->getRootAlias().'.pacjent = '.$get_oid);
            }
            
            if($get_did > 0){
                $queryBuilder->andWhere($queryBuilder->getRootAlias().'.lekarz = '.$get_did);
            }
            
            if(isset($tabLek) && is_array($tabLek)){
                
                foreach($tabLek as $lek){
                    $warunekLID[] = $lek->getLid();
                }
                
                $queryBuilder->andWhere($queryBuilder->getRootAlias().'.lekarz = '.implode(' OR '.$queryBuilder->getRootAlias().'.lekarz = ', $warunekLID));
            }

            $get_sort = $this->params()->fromRoute('sort', 'data');
            $get_met = $this->params()->fromRoute('met', 'asc');
            
            if(!(($get_sort == 'data' || $get_sort == 'id' || $get_sort == 'status' || $get_sort == 'lekarz' || $get_sort == 'pacjent') && ($get_met == 'desc' || $get_met == 'asc')))
            {
                $get_sort = 'data'; 
                $get_met = 'desc';
            }
            
            $queryBuilder->orderBy($queryBuilder->getRootAlias().'.'.$get_sort, $get_met);
            
            $query = $queryBuilder->getQuery();

            $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($query)));

            $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 0));
            //$paginator->setDefaultItemCountPerPage(2);
            
            $repository = $objectManager->getRepository('Application\Entity\Lekarz');
            $lekarzeLista = $repository->findAll();
            
            $repository = $objectManager->getRepository('Application\Entity\Osoba');
            $createQueryBuilder = $repository->createQueryBuilder('os');
            $createQueryBuilder->where($createQueryBuilder->getRootAlias().'.poziom != 1');
            $q = $createQueryBuilder->getQuery();
            $osobyLista = $q->execute();
            
            if($get_category == 0 || $get_category == 3) $prefiksDlaParametru = '3';
            else if($get_category == 1) $prefiksDlaParametru = '1';
            else if($get_category == 2) $prefiksDlaParametru = '2';
            else $prefiksDlaParametru = '';
            
            $sufiksDlaParametru = '';
            if($get_category_subparam > 0) $sufiksDlaParametru = $get_category_subparam;
            
            // $prefiksDlaParametru potrzebny jest przy przesyłaniu parametrów met. GET; 
            // dzięki temu jeden parametr "category" może "obsłużyć" więcej przypadków
            
            return array('all' => $paginator->getCurrentItems(), 'stronicowanieStrony' => $paginator->getPages('Sliding'), 'osoby' => $osobyLista, 'lekarze' => $lekarzeLista, 'prefiksDlaParametru' => $prefiksDlaParametru, 'sufiksDlaParametru' => $sufiksDlaParametru);
    }
    
    public function wolneTerminyAction(){
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $dr = $objectManager->find('Application\Entity\Lekarz', 3);
        
        return array('terminy' => Narzedzia::wolneTerminy($this, $dr, 12, 2013));
    }
    
    
    public function usunAction()
    {
        $get_id = (int)$this->params()->fromRoute('id', 0);   
        
        if(!$this->identity() || !($this->identity()->poziom == 1 || $this->identity()->poziom == 2)) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        
        $msg = null;

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $form = new \Application\Form\YesCancelForm();
            return array('msg' => array(0=>2, 1=>'Czy na pewno chcesz dokonać usunięcia?'), 'form' => $form);
        } else if($request->getPost()->get('submity')) {
        
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
            if($get_id < 1 || !($wiz = $objectManager->find('Application\Entity\Wizyta',$get_id))){
                return array('msg' => array(0=>0, 1=>'Niewłaściwy identyfikator wizyty.'));    

            } else {
                if($this->identity()->poziom == 1 && ($wiz->getLekarz()->getId() != $this->identity()->id || $wiz->getPacjent()->getId() != 1)){
                    return array('msg' => array(0=>0, 1=>'Nie masz uprawnień do tej operacji.'));
                }
            
                $objectManager->remove($wiz);
                $objectManager->flush();
                return array('msg' => array(0=>1, 1=>'Usunięto pomyślnie.'));
            }
            
            
            
        } else {
            return $this->redirect()->toRoute('wizyta', array('controller' => 'index'));
        }
        
        
       return array('msg' => array(0=>0, 1=>'Błąd.'));  
    }
    
    public function odwolajAction()
    {
        $get_id = (int)$this->params()->fromRoute('id', 0);   
        $get_dr = (int)$this->params()->fromRoute('did', 0); 
        
        if(!$this->identity()) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        
        $msg = null;

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $form = new \Application\Form\YesCancelForm();
            return array('msg' => array(0=>2, 1=>'Czy na pewno chcesz odwołać wizytę?'), 'form' => $form);
        } else if($request->getPost()->get('submity')) {
        
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
            if($get_id < 1 || !($wiz = $objectManager->find('Application\Entity\Wizyta',$get_id))){
                return array('msg' => array(0=>0, 1=>'Niewłaściwy identyfikator wizyty.'));    

            } else {
                if($this->identity()->poziom == 2 && $get_dr == 1){
                    $nowyStatus = 2;
                } else if($this->identity()->poziom == 2){
                    $nowyStatus = 1;
                } else {
                    $nowyStatus = ($this->identity()->poziom) + 1;
                }
                $wiz->setStatus($nowyStatus);
                $objectManager->persist($wiz);
                
                \Wizyta\Model\Narzedzia::powiadom(array($wiz->getPacjent(), $wiz->getLekarz()), 'Odwołano wizytę z dnia '.$wiz->getData()->format('d.m.Y H:i'), 'Odwołano wizytę!');
                
                
                $objectManager->flush();
                return array('msg' => array(0=>1, 1=>'Odwołano pomyślnie.'));
            }
            
            
            
        } else {
            return $this->redirect()->toRoute('wizyta', array('controller' => 'index'));
        }
        
        
       return array('msg' => array(0=>0, 1=>'Błąd.'));  
    }
    
}
