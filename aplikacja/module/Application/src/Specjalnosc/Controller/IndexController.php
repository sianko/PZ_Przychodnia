<?php

namespace Specjalnosc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class IndexController extends AbstractActionController
{
    
    private function queryCreator()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $repository = $objectManager->getRepository('Application\Entity\Specjalnosc');
        $queryBuilder = $repository->createQueryBuilder('Application\Entity\Specjalnosc');
        
        return $queryBuilder->getQuery();
    }
    
    public function indexAction()
    {
        $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($query)));

        $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 0));
        
        $query = $this->queryCreator();
        
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
    
}
