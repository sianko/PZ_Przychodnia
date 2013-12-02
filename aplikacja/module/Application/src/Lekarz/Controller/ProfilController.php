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
        return array();
    }

    public function edytujAction()
    {
        $get_id= (int)$this->params()->fromRoute('id', 0);   
        
        if(!$this->identity() || !(($this->identity()->id == $get_id && $this->identity()->poziom == 1) || $this->identity()->poziom == 2)) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie'));
        
        $edycjaCzyDodawanie = 1;
        $msg = null;
        
        $form = new \Lekarz\Form\UserForm();

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
        if($get_id < 1 || !($lekarz = $objectManager->find('Application\Entity\Lekarz',$get_id))){
            $edycjaCzyDodawanie = 0;
            $lekarz = new DB\Lekarz();
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
            
            $form->setInputFilter(new \Lekarz\Form\UserFilter());	
            $form->setData($request->getPost());
             if ($form->isValid()) {
                $data = $form->getData();
                $lekarz->setImie($data['imie']);
                $lekarz->setNazwisko($data['nazwisko']);
                $lekarz->setPesel($data['pesel']);
                $lekarz->setAdres($data['adres']);
                $lekarz->setEmail($data['email']);
                $lekarz->setTelefon($data['telefon']);
                $lekarz->setTytulNaukowy($data['tytul_naukowy']);
                $lekarz->setMinutNaPacjenta($data['czas_na_pacjenta']);
                $lekarz->setPlec($data['plec']);
                $lekarz->setPoziom(1);
                $lekarz->setDataUr(new \DateTime($data['data_ur_2'].'-'.$data['data_ur_1'].'-'.$data['data_ur_0']));
                $lekarz->setSpec($objectManager->find('Application\Entity\Specjalnosc',$data['specjalnosc']));
                
                
                $iteracja=0;
                for($iteracja=0; $iteracja<7; $iteracja++)
                {
                    $grafik[$iteracja] = explode(';',str_replace(' ', '', $data['grafik_'.$iteracja]));
                }
                
                $lekarz->setGrafikArray($grafik);
                
                // Jeżeli dodawany jest nowy użytkownik - generuję losowe hasło
                if(empty($data['lid'])){
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
        return array();
    }
    
}
