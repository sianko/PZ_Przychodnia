<?php

namespace Lekarz\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

class ProfilController extends AbstractActionController
{
    //protected $uzytkownikTable;
    
    public function indexAction()
    {
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $repository = $objectManager->getRepository('Application\Entity\Lekarz');
            $queryBuilder = $repository->createQueryBuilder('Application\Entity\Lekarz');
            
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

    public function dodajAction()
    { 
        return $this->redirect()->toRoute('lekarz', array('controller' => 'profil', 'action' => 'edytuj', 'id' => 0));
    }

    public function edytujAction()
    {
        $get_id = (int)$this->params()->fromRoute('id', 0);   
        
        if(!$this->identity() || !($this->identity()->poziom == 1 || $this->identity()->poziom == 2)) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie'));
        
        $edycjaCzyDodawanie = 1;
        $msg = null;
        
        $form = new \Lekarz\Form\UserForm();

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
        if($get_id < 1 || !($lekarz = $objectManager->find('Application\Entity\Lekarz',$get_id))){
            
            $edycjaCzyDodawanie = 0;
            $lekarz = new DB\Lekarz();
            
            if($this->identity()->poziom == 1){
                $istniejacaOsoba = $objectManager->find('Application\Entity\Osoba', $this->identity()->id);
                $lekarz->setOs($istniejacaOsoba);
            }
        } else if($this->identity()->poziom == 1 && $lekarz->getId() != $this->identity()->id){
            return array('form' => $form, 'edycjaCzyDodawanie' => 0, 'msg' => array(0=>3, 1=>'Nie masz uprawnień do tej operacji.'));
        }
            
            $repository = $objectManager->getRepository('Application\Entity\Specjalnosc');
            $specs = $repository->findAll();
            
            foreach($specs as $spec)
            {
                $specjalnosci[$spec->getId()] = $spec->getNazwa();
            }
            
        $form->get('specjalnosc')->setValueOptions($specjalnosci);
            
        $request = $this->getRequest();
        if (!$request->isPost()) {

            $form->get('imie')->setValue($lekarz->getImie());
            $form->get('nazwisko')->setValue($lekarz->getNazwisko());
            $form->get('pesel')->setValue($lekarz->getPesel());
            $form->get('email')->setValue($lekarz->getEmail());
            $form->get('adres')->setValue($lekarz->getAdres());
            $form->get('telefon')->setValue($lekarz->getTelefon());
            $form->get('tytul_naukowy')->setValue($lekarz->getTytulNaukowy());
            $form->get('specjalnosc')->setValue($lekarz->getSpec()->getId());
            $form->get('plec')->setValue($lekarz->getPlec());
            $form->get('czas_na_pacjenta')->setValue($lekarz->getMinutNaPacjenta());
            $form->get('lid')->setValue($lekarz->getLid());
            
            $dataSplit = explode('-', (($lekarz->getDataUr()) ? $lekarz->getDataUr()->format('d-m-Y') : date('d-m-Y')));
            $form->get('data_ur_0')->setValue($dataSplit[0]);
            $form->get('data_ur_1')->setValue($dataSplit[1]);
            $form->get('data_ur_2')->setValue($dataSplit[2]);
            
            $iteracja=0;
            foreach($lekarz->getGrafikArray() as $dzien)
            {
                $form->get('grafik_'.$iteracja++)->setValue(implode(';', $dzien));
            }
            
        } else {
            
            if($request->getPost()->get('istniejacaOsoba') == '1'){
                $form->setInputFilter(new \Lekarz\Form\LekarzFilter());	
                $form->get('istniejacyUzytkownik')->setValueOptions(array($request->getPost()->get('istniejacyUzytkownik') => 'osoba'));
                
            }
            else {
                $form->setInputFilter(new \Lekarz\Form\UserFilter());
            }
            
            
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                
                if($data['istniejacaOsoba'] == '0' || $data['istniejacyUzytkownik'] < 1){
                    $lekarz->setImie($data['imie']);
                    $lekarz->setNazwisko($data['nazwisko']);
                    $lekarz->setPesel($data['pesel']);
                    $lekarz->setAdres($data['adres']);
                    $lekarz->setEmail($data['email']);
                    $lekarz->setTelefon($data['telefon']);
                    $lekarz->setPlec($data['plec']);
                    
                    $lekarz->setDataUr(new \DateTime($data['data_ur_2'].'-'.$data['data_ur_1'].'-'.$data['data_ur_0']));
                } else {
                    
                    $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                    $istniejacaOsoba = $objectManager->find('Application\Entity\Osoba',$data['istniejacyUzytkownik']);
                    $lekarz->setOs($istniejacaOsoba);
                }
                    
                    if($lekarz->getPoziom() != 2){
                        $lekarz->setPoziom(1);
                    }
                    
                    $lekarz->setTytulNaukowy($data['tytul_naukowy']);
                    $lekarz->setMinutNaPacjenta($data['czas_na_pacjenta']);
                    $lekarz->setSpec($objectManager->find('Application\Entity\Specjalnosc',$data['specjalnosc']));
                    
                    $iteracja=0;
                    for($iteracja=0; $iteracja<7; $iteracja++)
                    {
                        $grafik[$iteracja] = explode(';',str_replace(' ', '', $data['grafik_'.$iteracja]));
                    }
                    
                    $lekarz->setGrafikArray($grafik);
                    
                    // Jeżeli dodawany jest nowy użytkownik - generuję losowe hasło
                    if($get_id == 0 && $data['istniejacaOsoba'] == '0' && $this->identity()->poziom == 2){
                        $hasloLosowe = Rand::getString(10, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?@#$%0123456789', true);
                        $sol = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?@#$%0123456789', true);
                        $bcrypt = new Bcrypt(array(
                            'salt' => $sol,
                            'cost' => 6
                        ));
                        $wynik_hash = $bcrypt->create($hasloLosowe); 
                        $sol = $bcrypt->getSalt();
                        
                        $lekarz->setSol($sol);
                        $lekarz->setHaslo($wynik_hash);
                        $msg[2] = $hasloLosowe;
                    }
                    
                    $objectManager->persist($lekarz);
                    $objectManager->flush();
                    
                    
                    $msg[1] = 'Baza została zaktualizowana poprawnie.';
                    $msg[0] = true;
                
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
        
        if(!$this->identity() || !($this->identity()->poziom == 1 || $this->identity()->poziom == 2)) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie'));
        
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
                $objectManager->remove($lekarz);
                $objectManager->flush();
                return array('msg' => array(0=>1, 1=>'Usunięto pomyślnie.'));
            }
            
            
            
        } else {
            return $this->redirect()->toRoute('lekarz', array('controller' => 'profil'));
        }
        
        
       return array('msg' => array(0=>0, 1=>'Błąd.'));  
    }
    
}
