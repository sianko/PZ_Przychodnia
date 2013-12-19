<?php

namespace Uzytkownik\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

use Zend\Validator\Date as DataValid;

class RejestracjaController extends AbstractActionController
{
    public function dodajAction()
    { 
        return $this->redirect()->toRoute('uzytkownik', array('controller' => 'rejestracja', 'action' => 'edytuj', 'id' => 0));
    }

    public function edytujAction()
    {
        $get_id = (int)$this->params()->fromRoute('id', 0);    //pob z tablicy GET
        
        // if(!$this->identity() || !($this->identity()->poziom == 1 || $this->identity()->poziom == 2)) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        
        $edycjaCzyDodawanie = 1;
        $msg = null; //info na ekran
        
        $form = new \Uzytkownik\Form\UserForm();

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
        if($get_id < 1 || !($osoba = $objectManager->find('Application\Entity\Osoba',$get_id))){
            
            $edycjaCzyDodawanie = 0;
            $osoba = new DB\Osoba(); //DB alias Application\Entity\Osoba
            
            
        } else if($this->identity()->poziom != 2 && $osoba->getId() != $this->identity()->id){
            return array('form' => $form, 'edycjaCzyDodawanie' => 0, 'msg' => array(0=>3, 1=>'Nie masz uprawnień do tej operacji.'));
        }
                                    
        $request = $this->getRequest();
        if (!$request->isPost()) {//jezeli POSTEM to tu jest true

            $form->get('imie')->setValue($osoba->getImie());
            $form->get('nazwisko')->setValue($osoba->getNazwisko());
            $form->get('pesel')->setValue($osoba->getPesel());
            $form->get('email')->setValue($osoba->getEmail());
            $form->get('adres')->setValue($osoba->getAdres());
            $form->get('telefon')->setValue($osoba->getTelefon());
            $form->get('plec')->setValue($osoba->getPlec());
                        
            $dataSplit = explode('-', (($osoba->getDataUr()) ? $osoba->getDataUr()->format('d-m-Y') : date('d-m-Y')));
            $form->get('data_ur_dd')->setValue($dataSplit[0]);
            $form->get('data_ur_mm')->setValue($dataSplit[1]);
            $form->get('data_ur_rr')->setValue($dataSplit[2]);
            
                        
        } else {
            //dla wpisywania nowej osoby po klinkieciu SUBMIT
            
            //ustawianie filtr do walidacji
                $form->setInputFilter(new \Uzytkownik\Form\UserFilter());
            
            $form->setData($request->getPost()); //ustawia dane w formularzu
            if ($form->isValid()) {
                $data = $form->getData();
                if ($data['haslo']!==$data['haslo_powt'])  
				return array('msg' => array(0=>false, 1=>'Hasła się nie pokrywaja'));
                
                    $osoba->setImie($data['imie']);
                    $osoba->setNazwisko($data['nazwisko']);
                    $osoba->setPesel($data['pesel']);
                    $osoba->setAdres($data['adres']);
                    $osoba->setEmail($data['email']);
                    $osoba->setTelefon($data['telefon']);
					
					//
					$pass = $data['haslo'];
					

					$sol = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?@#$%0123456789', true);
        
         
					$bcrypt = new Bcrypt(array(
					'salt' => $sol,
					'cost' => 6 ));
					$wynik_hash = $bcrypt->create($pass); 
					$sol = $bcrypt->getSalt();
							
					//
                    $osoba->setHaslo($wynik_hash);
                    $osoba->setSol($sol);
                    
					$osoba->setDataUr(new \DateTime($data['data_ur_rr'].'-'.$data['data_ur_mm'].'-'.$data['data_ur_dd']));
					$osoba->setPlec($data['plec']);
					                    
                    
                    
                    try{
                    $objectManager->persist($osoba);
                    $objectManager->flush();
                    
					$msg[1] = 'Baza została zaktualizowana poprawnie.';
                    $msg[0] = true;
					}
					catch(\Exception $exc){
						$msg[1] = 'Formularz został wypełniony niewłaściwie.';
						$msg[0] = false;
					}
                    
                    
                
            } else{
                $msg[1] = 'Formularz został wypełniony niewłaściwie.';
                $msg[0] = false;
            }
        }
        
        return array('form' => $form, 'edycjaCzyDodawanie' => $edycjaCzyDodawanie, 'msg' => $msg);
    }

}

