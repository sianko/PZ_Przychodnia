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

    public function indexAction()
    {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $repository = $objectManager->getRepository('Application\Entity\Uzytkownik');
            $queryBuilder = $repository->createQueryBuilder('dr');
            
            $get_category = (int)$this->params()->fromRoute('category', 0);
            if($get_category > 0){
                $queryBuilder->where($queryBuilder->getRootAlias().'.spec = '.$get_category);
            }
            
            $query = $queryBuilder->getQuery();

            $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($query)));

            $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 0));
            //$paginator->setDefaultItemCountPerPage(2);
            
            $repository = $objectManager->getRepository('Application\Entity\Specjalnosc');
            $specs = $repository->findAll();
            
            return array('all' => $paginator->getCurrentItems(), 'stronicowanieStrony' => $paginator->getPages('Sliding'), 'specjalnosci' => $specs);
    }
    
    public function mojeSpecjalnosciAction()
    {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $repository = $objectManager->getRepository('Application\Entity\Lekarz');
            $queryBuilder = $repository->createQueryBuilder('dr');
            
            $queryBuilder->where($queryBuilder->getRootAlias().'.os = '.$this->identity()->id);
            
            $query = $queryBuilder->getQuery();

            $paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($query)));

            $paginator->setCurrentPageNumber($this->params()->fromRoute('page', 0));

            
            return array('all' => $paginator->getCurrentItems(), 'stronicowanieStrony' => $paginator->getPages('Sliding'));
    }

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

    public function usunAction()
    {
        $get_id = (int)$this->params()->fromRoute('id', 0);   
        
        if(!$this->identity() || !($this->identity()->poziom == 1 || $this->identity()->poziom == 2)) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        
        $msg = null;

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $form = new \Application\Form\YesCancelForm();
            return array('msg' => array(0=>2, 1=>'Czy na pewno chcesz dokonać usunięcia?<br />Usunięty zostanie tylko profil zawodowy, a nie profil osoby.'), 'form' => $form);
        } else if($request->getPost()->get('submity')) {
        
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
            if($get_id < 1 || !($lekarz = $objectManager->find('Application\Entity\Lekarz',$get_id))){
                return array('msg' => array(0=>0, 1=>'Niewłaściwy identyfikator lekarza.'));    

            } else if($this->identity()->poziom == 1 && $lekarz->getId() != $this->identity()->id){
                return array('msg' => array(0=>0, 1=>'Nie masz uprawnień do tej operacji.'));
            } else {
                
                // Usunięcie powiązanych wizyt w celu zachowania integralności bazy
                $repo = $objectManager->getRepository('Application\Entity\Wizyta');
                $powiazaneWizyty = $repo->findBy(array('lekarz' => $lekarz->getLid()));
                foreach($powiazaneWizyty as $powiazana){
                    $objectManager->remove($powiazana);
                }
                
                $objectManager->remove($lekarz);
                $objectManager->flush();
                return array('msg' => array(0=>1, 1=>'Usunięto pomyślnie.'));
            }
            
            
            
        } else {
            return $this->redirect()->toRoute('lekarz', array('controller' => 'profil'));
        }
        
        
       return array('msg' => array(0=>0, 1=>'Błąd.'));  
    }
    
    
    
    public function zaznaczUrlopAction()
    {
        $get_id = (int)$this->params()->fromRoute('id', 0);   
        
        if(!$this->identity() || !($this->identity()->poziom == 1 || $this->identity()->poziom == 2)) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
        
        $msg = null;

        $form = new \Lekarz\Form\UrlopForm();
        
        $request = $this->getRequest();
        if (!$request->isPost()) {
        
            if($this->identity()->poziom == 2){
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $repo = $objectManager->getRepository('Application\Entity\Lekarz');
            $lekarze = $repo->findAll();
            
            if($get_id > 0){
            
                foreach($lekarze as $dr){
                    if($dr->getLid() == $get_id){ 
                        $wybranyLekarz = $dr; 
                        break;
                    }
                }
                
            return array('form' => $form, 'lekarze' => $lekarze, 'wybranyLekarz' => $wybranyLekarz);
            }
            
            return array('form' => $form, 'lekarze' => $lekarze);
            }
            
            return array('form' => $form);
            
        } else {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                
                // zera wiodące [minuty]
                $data['data_od_4'] = $data['data_od_4'] < 10 ? '0'.$data['data_od_4'] : $data['data_od_4'];
                $data['data_do_4'] = $data['data_do_4'] < 10 ? '0'.$data['data_do_4'] : $data['data_do_4'];
                
                $data_od = $data['data_od_2'].'-'.$data['data_od_1'].'-'.$data['data_od_0'].' '.$data['data_od_3'].':'.$data['data_od_4'];
                $data_do = $data['data_do_2'].'-'.$data['data_do_1'].'-'.$data['data_do_0'].' '.$data['data_do_3'].':'.$data['data_do_4'];
                
                $powiadomienie = isset($data['powiadom']) && $data['powiadom'] == 'tak' ? true : false;
                
                $validator = new DataValid(array('format' => 'Y-n-j G:i'));
                if($validator->isValid($data_od) && $validator->isValid($data_do) && strtotime($data_od) <= strtotime($data_do)  && strtotime($data_od) >= time()-300){
                
                    $roznicaCzasu = strtotime($data_do) - strtotime($data_od);  
                    $roznicaCzasu /= 60;
                    
                    $pobranyLekarz = null;
                    if($this->identity()->poziom == 2){
                        
                        if($get_id > 0){
                           $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                           $pobranyLekarz = $objectManager->find('Application\Entity\Lekarz', $get_id);
                        } else {
                            return array('msg' => array(0=>0, 1=>'Trzeba wybrać lekarza.'));
                        }
                    }
                    
                    \Wizyta\Model\Narzedzia::odwolajWizyte($this, array($data_od, $data_do), $powiadomienie, $pobranyLekarz);
                    
                    // Użytkownik o id = 1, jest użytkownikiem "systemowym", na którego nie można się zalogować.
                    \Wizyta\Controller\RejestracjaController::zapiszNaWizyte($this, $data_od, 1, $pobranyLekarz == null ? $this->identity()->id : $pobranyLekarz->getLid(), false, false, $roznicaCzasu, true);
                
                    return array('msg' => array(0=>1, 1=>'Dodano informację o nieobecności ' . ($powiadomienie ? ' oraz poinformowano pacjentów o zmianach.' : '.')));  
                
                } else {
                     return array('msg' => array(0=>0, 1=>'Podany przedział jest nieprawidłowy.'));  
                }
                
                
                
            }
        
        }
        
        
       return array('msg' => array(0=>0, 1=>'Błąd.'));  
    }
    
}

