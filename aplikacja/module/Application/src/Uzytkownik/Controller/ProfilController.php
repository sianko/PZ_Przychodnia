<?php

namespace Uzytkownik\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ProfilController extends AbstractActionController
{
    public function indexAction()
    {
        if(!$this->identity()) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie'));
        
        return array();
        
    }

    public function edytujAction()
    {
        return array();
    }

}
