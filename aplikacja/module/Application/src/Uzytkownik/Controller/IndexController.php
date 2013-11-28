<?php

namespace Uzytkownik\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    protected $uzytkownikTable;
    
    public function indexAction()
    {
        $objectManager = $this
        ->getServiceLocator()
        ->get('Doctrine\ORM\EntityManager');

        $user = new \Uzytkownik\Entity\Uzytkownik();
        $user->imie = 'Marco Pivetta';

        $objectManager->persist($user);
        $objectManager->flush();
        
        return array('users' => $this->getUzytkownikTable()->fetchAll());
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
