<?php

namespace Uzytkownik\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class IndexController extends AbstractActionController
{
    //protected $uzytkownikTable;
    
    public function indexAction()
    {
        if(!$this->identity() || $this->identity()->poziom != 2) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        else {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $repository = $objectManager->getRepository('Application\Entity\Osoba');
            $queryBuilder = $repository->createQueryBuilder('os');
           
            $queryBuilder->orderBy($queryBuilder->getRootAlias().'.aktywny', 'ASC');
            $queryBuilder->addOrderBy($queryBuilder->getRootAlias().'.nazwisko', 'ASC');
           
            $query = $queryBuilder->getQuery();

            $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($query)));

            $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 0));
            //$paginator->setDefaultItemCountPerPage(2);
            
            return array('all' => $paginator->getCurrentItems(), 'stronicowanieStrony' => $paginator->getPages('Sliding'));
        }
    }
    
    
    public function zmienPoziomAction()
    {
        if(!$this->identity() || $this->identity()->poziom != 2) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        
        else {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $get_id = (int)$this->params()->fromRoute('id', 0);
            $get_page = (int)$this->params()->fromRoute('page', 2);
            
            if($get_page != 0 || $get_page != 1 || $get_page != 2) $get_page = 0; 
            
            $o = $objectManager->find('Application\Entity\Osoba', $get_id);
            if($o instanceof \Application\Entity\Osoba){
                $o->setPoziom($get_page);
                $objectManager->persist($o);
                $objectManager->flush();
            }
            
            return $this->redirect()->toRoute('uzytkownik', array('controller' => 'index'));
        }
    }
  
    public function aktywujAction()
    {
        if(!$this->identity() || $this->identity()->poziom != 2) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        
        else {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $get_id = (int)$this->params()->fromRoute('id', 0);
            
            $o = $objectManager->find('Application\Entity\Osoba', $get_id);
            if($o instanceof \Application\Entity\Osoba){
                $o->setAktywny(1);
                $objectManager->persist($o);
                $objectManager->flush();
            }
            
            return $this->redirect()->toRoute('uzytkownik', array('controller' => 'index'));
        }
    }

  
    public function listaOsobSkroconaAction(){
        $get_id = 0;
        
        if(!$this->identity() || $this->identity()->poziom != 2){
            return array('wynik' => '');
        } /*else if($this->identity()->poziom == 1){
            $get_id = $this->identity()->id; 
        } else if($this->identity()->poziom != 2){
            return array('wynik' => '');
        } */
        
        //$get_id = (int)$this->params()->fromRoute('id', 0); 
        
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Application\Entity\Osoba');
        $queryBuilder = $repository->createQueryBuilder('Application\Entity\Osoba');
        $queryBuilder->select($queryBuilder->getRootAlias().'.id');
        $queryBuilder->addSelect($queryBuilder->getRootAlias().'.imie');
        $queryBuilder->addSelect($queryBuilder->getRootAlias().'.nazwisko');
        $queryBuilder->addSelect($queryBuilder->getRootAlias().'.pesel');
        
        if($get_id > 0){
            $queryBuilder->where($queryBuilder->getRootAlias().'.id = '.$get_id);
        }
        
        $query = $queryBuilder->getQuery();
        
        $lista = $query->execute();

        $request = new \Zend\Http\Request();
        $request->setMethod(\Zend\Http\Request::METHOD_POST);
        
        
        return array('wynik' => \Zend\Json\Json::encode($lista, true), 'list' => $lista, 'request'=>$this->getRequest());
    }

    public function dodajAction()
    {
        return array();
    }

    public function edytujAction()
    {
        return array();
    }

    public function usunAction()
    {
        return array();
    }
    
}
