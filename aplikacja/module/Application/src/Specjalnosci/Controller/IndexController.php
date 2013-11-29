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

        $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 0));
        
        return array('all' => $paginator->getCurrentItems(), 'stronicowanieLiczbaStron' => $paginator->getCurrentItemCount(), 'stronicowanieStrony' => $paginator->getPages('Sliding'));
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
