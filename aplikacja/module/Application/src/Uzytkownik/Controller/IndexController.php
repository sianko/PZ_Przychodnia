<?php

namespace Uzytkownik\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class IndexController extends AbstractActionController
{
    protected $uzytkownikTable;
    
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $repository = $objectManager->getRepository('Application\Entity\Specjalnosc');
        $queryBuilder = $repository->createQueryBuilder('Application\Entity\Specjalnosc');
        
        $query = $queryBuilder->getQuery();
        //$query = $objectManager->createQuery($query);

        $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($query)));

        $paginator->setCurrentPageNumber(1)
                    ->setItemCountPerPage(5);
                    
        $queryBuilder->setFirstResult(($paginator->getCurrentPageNumber()-1)*$paginator->getItemCountPerPage())
                     ->setMaxResults($paginator->getItemCountPerPage())
                     ->orderBy($queryBuilder->getRootAlias().'.nazwa', 'ASC');
        $all = $queryBuilder->getQuery()->execute();
        
        //Zend\Paginator\Paginator::setDefaultScrollingStyle('Sliding');
        
        return array('paginator' => $paginator, 'all' => $all);
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
    
    public function getUzytkownikTable()
     {
         if (!$this->uzytkownikTable) {
             $sm = $this->getServiceLocator();
             $this->uzytkownikTable = $sm->get('Uzytkownik\Model\UzytkownikTable');
         }
         return $this->uzytkownikTable;
     }
}
