<?php

namespace Uzytkownik\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

use Zend\Db\Adapter\Adapter as DbAdapter;

use CustomZend\Authentication\Adapter\SaltedAuthAdapter as AuthAdapter;

use Uzytkownik\Model\Uzytkownik;
use Uzytkownik\Form\AuthForm;
use Uzytkownik\Form\AuthFilter;

class LogowanieController extends AbstractActionController
{
    public function indexAction()
    {
		$user = $this->identity();
        $result = null;
        $form = new AuthForm();
        
        if (!$user) {

            $request = $this->getRequest();
            if ($request->isPost()) {
                
                $form->setInputFilter(new AuthFilter());	
                $form->setData($request->getPost());
                 if ($form->isValid()) {
                    $data = $form->getData();
                    $sm = $this->getServiceLocator();
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');	

                    $authAdapter = new AuthAdapter($dbAdapter,
                                               'osoby', // there is a method setTableName to do the same
                                               'pesel', // there is a method setIdentityColumn to do the same
                                               'haslo', // there is a method setCredentialColumn to do the same
                                               'sol'    // there is a method setSaltColumn to do the same
                                              );
                    $authAdapter
                        ->setIdentity($data['pesel'])
                        ->setCredential($data['haslo'])
                    ;
                    
                    $auth = new AuthenticationService();
                    // or prepare in the globa.config.php and get it from there. Better to be in a module, so we can replace in another module.
                    // $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                    // $sm->setService('Zend\Authentication\AuthenticationService', $auth); // You can set the service here but will be loaded only if this action called.
                    $result = $auth->authenticate($authAdapter);			
                    
                    switch ($result->getCode()) {
                        case Result::FAILURE_IDENTITY_NOT_FOUND:
                            // do stuff for nonexistent identity
                            break;

                        case Result::FAILURE_CREDENTIAL_INVALID:
                            // do stuff for invalid credential
                            break;

                        case Result::SUCCESS:
                            $storage = $auth->getStorage();
                            $storage->write($authAdapter->getResultRowObject(
                                null,
                                'password'
                            ));
                            $auth->getIdentity()->haslo = '';
                            $auth->getIdentity()->sol = '';
                            $time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
                            if ($data['rememberme']) {
                                $sessionManager = new \Zend\Session\SessionManager();
                                $sessionManager->rememberMe($time);
                            }
                            
                            
                            return $this->redirect()->toRoute('uzytkownik', array('controller' => 'profil'));	
                           
                            break;

                      
                    }				
                   		
                 }
            }
        } else if($user->aktywny != 1){
        
            $pokazKomunikat = '<div class="alert alert-danger"><b>Uwaga</b> Konto nie zostało jeszcze aktywowane. Aby aktywować konto, trzeba zgłosić się do Recepcji w naszej przychodni, w celu weryfikacji danych.<br /><i>Przepraszamy za utrudnienia!</i></div>';
            return array('pokazKomunikat' => $pokazKomunikat);

        } else return $this->redirect()->toRoute('uzytkownik', array('controller' => 'profil'));
        
        $get_id = (int)$this->params()->fromRoute('id', 0);
        $url = $this->url()->fromRoute('uzytkownik', array('controller' => 'rejestracja'));
        $pokazKomunikat = $get_id === 1 ? '<div class="alert alert-warning">Dostęp do tej części strony wymaga zalogowania. Nie masz konta? <a href="'.$url.'">Zarejestruj się</a></div>' : false;  
        
        return array('form' => $form, 'result' => $result, 'pokazKomunikat' => $pokazKomunikat);
            
    }
    
    
    public function wylogujAction()
	{
		$auth = new AuthenticationService();

		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		}			
		
		$auth->clearIdentity();
		$sessionManager = new \Zend\Session\SessionManager();
		$sessionManager->forgetMe();
		
		return $this->redirect()->toRoute('home');		
	}	
    
    /*
        Funkcja tymczasowa - do usunięcia
    */
    public function hasherAction()
    {
        $pass = $this->params()->fromRoute('pass', 0);
        if (!$pass) {
            $src = 'brak danych';
        } else {
            $src = $pass;
        }

        $sol = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?@#$%0123456789', true);
        
         
        $bcrypt = new Bcrypt(array(
            'salt' => $sol,
            'cost' => 6
        ));
        $wynik_hash = $bcrypt->create($src); 
        $sol = $bcrypt->getSalt();
        return array(
                'wynik_hash'=>$wynik_hash,
                'src'=>$src,
                'sol'=>$sol
                );
    }
}
