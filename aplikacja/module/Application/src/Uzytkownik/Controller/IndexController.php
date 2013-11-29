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
        if(!$this->identity() || $this->identity()->poziom != 2) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie'));
        else {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $repository = $objectManager->getRepository('Application\Entity\Osoba');
            $queryBuilder = $repository->createQueryBuilder('Application\Entity\Osoba');
           
            $query = $queryBuilder->getQuery();

            $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($query)));

            $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 0));
            //$paginator->setDefaultItemCountPerPage(2);
            
            return array('all' => $paginator->getCurrentItems(), 'stronicowanieStrony' => $paginator->getPages('Sliding'));
        }
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
    
    /*public function getUzytkownikTable()
     {
         if (!$this->uzytkownikTable) {
             $sm = $this->getServiceLocator();
             $this->uzytkownikTable = $sm->get('Uzytkownik\Model\UzytkownikTable');
         }
         return $this->uzytkownikTable;
     }*/
}
