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
            if(!$this->identity() || $this->identity()->poziom != 2) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie'));
            
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            
            $repository = $objectManager->getRepository('Application\Entity\Wizyta');
            $queryBuilder = $repository->createQueryBuilder('Application\Entity\Wizyta');
            
            $get_category = (int)$this->params()->fromRoute('category', 0);
            if($get_category == 1){ // tylko przyszłe
                $queryBuilder->where($queryBuilder->getRootAlias().'data >= '.date('Y-m-d H:i'));
            } else if($get_category == 2){ // tylko przeszłe
                $queryBuilder->where($queryBuilder->getRootAlias().'data < '.date('Y-m-d H:i'));
            }
            
            $get_oid = (int)$this->params()->fromRoute('oid', 0);
            if($get_oid > 0){
                $queryBuilder->andWhere($queryBuilder->getRootAlias().'.pacjent = '.$get_oid);
            }
            
            $get_sort = $this->params()->fromRoute('sort', 'data');
            $get_met = $this->params()->fromRoute('met', 'desc');
            
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
            
            $repository = $objectManager->getRepository('Application\Entity\Osoba');
            $osoby = $repository->findAll();
            
            
            return array('all' => $paginator->getCurrentItems(), 'stronicowanieStrony' => $paginator->getPages('Sliding'), 'osoby' => $osoby);
    }
    
    public function wolneTerminyAction(){
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $dr = $objectManager->find('Application\Entity\Lekarz', 3);
        
        return array('terminy' => Narzedzia::wolneTerminy($this, $dr, 12, 2013));
    }
    
    
}
