<?php

namespace Wizyta\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Query\Expr as Expressions;

use Zend\Validator\Date as DataValid;
use \CustomZend\Stdlib\DateTime as DateTime;

class RejestracjaController extends AbstractActionController
{
    
    public function indexAction(){
    
            if(!($this->identity())) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));

            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $get_did = $this->params()->fromRoute('did', 0);
            $get_category = $this->params()->fromRoute('category', 0);

            if($get_category == 0) $get_category = date('mY');
            
            if($get_did == 0){
                $repo = $objectManager->getRepository('\Application\Entity\Lekarz');
                $queryBuilder = $repo->createQueryBuilder('dr');
                $queryBuilder->setMaxResults(1);
                $q = $queryBuilder->getQuery();
                $pierwszyLepszy = $q->execute();

                $pierwszyLepszy = $pierwszyLepszy[0];

                $get_did = $pierwszyLepszy->getLid();
            }
            
            $dlugoscParam = strlen($get_category);
            if($dlugoscParam > 6){
                $m = date('m');
                $r = date('Y');
            } else if($dlugoscParam == 6){
                $m = substr($get_category, 0, 2);
                $r = substr($get_category, 2, 4);
            } else {
                $m = '0'.substr($get_category, 0, 1);
                $r = substr($get_category, 1, 4);
            }
            
            if($m > 12) return $this->redirect()->toRoute($this->route, array('controller' => 'rejestracja','category' => '01'.($r+1), 'did' => $get_did));
            else if($m < 1) return $this->redirect()->toRoute($this->route, array('controller' => 'rejestracja', 'category' => '12'.($r-1), 'did' => $get_did));
            
            if(isset($pierwszyLepszy)){
                $os = $pierwszyLepszy;
            } else {
                $os = $objectManager->find('\Application\Entity\Lekarz', (int)$get_did);
            }
            
            $k = \Wizyta\Model\Narzedzia::wolneTerminy($this, $os, $m, $r);

            
            $dzienTygPierwszegoDniaMiesiaca = new \DateTime($r.'-'.$m.'-1');
            $dzienTygPierwszegoDniaMiesiaca = $dzienTygPierwszegoDniaMiesiaca->format('N');
            
            $repo = $objectManager->getRepository('\Application\Entity\Lekarz');
            $queryBuilder = $repo->createQueryBuilder('dr');
            $queryBuilder->join('\Application\Entity\Specjalnosc', 'sp', Expressions\Join::WITH, $queryBuilder->getRootAlias().'.spec = sp.id');
            
            $queryBuilder->orderBy('sp.nazwa', 'ASC');
            $q = $queryBuilder->getQuery();
            $lekarze = $q->execute();

            $pacjenci = array();
            if($this->identity()->poziom == 2){
                $repo = $objectManager->getRepository('\Application\Entity\Osoba');
                $queryBuilder = $repo->createQueryBuilder('os');
                $queryBuilder->where($queryBuilder->getRootAlias().'.poziom = 0');
                $q = $queryBuilder->getQuery();
                $pacjenci = $q->execute();
            }
            
            
            return array('kalendarz' => $k, 'l' => $os, 'dzienTygPierwszegoDniaMiesiaca' => $dzienTygPierwszegoDniaMiesiaca, 'nazwaMiesiaca' => \Wizyta\Model\Narzedzia::nazwaMiesiaca($m), 'miesiac' => $m, 'rok' => $r, 'lekarze' => $lekarze, 'pacjenci' => $pacjenci);
    }
    
    
    public function zapiszSieAction()
    {
         if(!($this->identity()) || $this->identity()->aktywny != 1) return $this->redirect()->toRoute('uzytkownik', array('controller' => 'logowanie', 'id' => 1));
         
         $get_did = (int)$this->params()->fromRoute('did', 0);
         $get_id = $this->params()->fromRoute('id', 0);
         
         $msg= null;
         
         if($get_id == 0 || $get_did == 0 || strlen($get_id) != 12){
            $msg[0] = 0;
            $msg[1] = 'Niewłaściwe parametry. Spróbuj ponownie <a href="'.$this->url('wizyta', array('controller'=>'rejestracja', 'action' => 'index')).'" title="">wybrać termin</a>.';
         } else {
            $ustawDate = new \DateTime($get_id[8].$get_id[9].$get_id[10].$get_id[11].'-'.$get_id[6].$get_id[7].'-'.$get_id[4].$get_id[5].' '.$get_id[0].$get_id[1].':'.$get_id[2].$get_id[3].':00');
         
            $request = $this->getRequest();
            if (!$request->isPost()) {
                $form = new \Application\Form\YesCancelForm();
                return array('msg' => array(0=>2, 1=>'Czy na pewno chcesz zarezerwować wizytę na dzień <b>'.$ustawDate->format('H:i d.m.Y').'</b> ('.(\Wizyta\Model\Narzedzia::nazwaDnia($ustawDate->format('N'))).')?'), 'form' => $form);
            } else if($request->getPost()->get('submity')) {
                
                $get_oid = $this->params()->fromRoute('oid', 0);
                
                if($this->identity()->poziom != 2)
                {
                    $get_oid = $this->identity()->id;
                } else if($get_oid <= 0) {
                    return array('msg' => array(0 => 0, 1 => 'Nie wybrano pacjenta!'));
                }
                
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $pacjent = $objectManager->find('\Application\Entity\Osoba', $get_oid);
                $lekarz = $objectManager->find('\Application\Entity\Lekarz', $get_did);
                
                $koniecWizyty = new \DateTime(date('Y-m-d H:i:s', $ustawDate->format('U')+$lekarz->getMinutNaPacjenta()*60));
                
                $przeszlosc = new \DateTime(date('Y-m-d H:i:s', time()-600));
                if($przeszlosc > $ustawDate){
                    $msg[0] = 0;
                    $msg[1] = 'Nie można zarejestrować wizyty w tym terminie. Podano przeszłą datę.';  
                } else if(self::maWizyte($this, $pacjent, $ustawDate, $koniecWizyty)){
                    $msg[0] = 0;
                    $msg[1] = 'Nie można zarejestrować wizyty w tym terminie. Masz inną wizytę w tym czasie.';            
                } else if(self::maWizyte($this, $lekarz, $ustawDate, $koniecWizyty)){
                    $msg[0] = 0;
                    $msg[1] = 'Nie można zarejestrować wizyty w tym terminie. Lekarz nie jest wtedy dostępny.';
                } else {
                    $r = self::zapiszNaWizyte($this, $ustawDate->format('Y-m-d H:i:s'), $get_oid, $get_did);
                    
                    if($r === true){
                        $msg[0] = 1;
                        $msg[1] = 'Wizyta została zarejestrowana.';
                    } else {
                        $msg[0] = 0;
                        $msg[1] = 'Nie udało się zarejestrować wizyty.';
                    }
                }
            } else {
                return $this->redirect()->toRoute('wizyta', array('controller' => 'rejestracja', 'did' => $get_did));
            }
         }
         return array('msg' => $msg); 
    }
    
    /**
     *  Tworzy nową wizytę
     *  
     *  @param string|\DateTime $data
     *  @param int|Application\Entity\Osoba $pacjent
     *  @param int|Application\Entity\Lekarz $lekarz
     *  @param bool $powiadomPacjenta
     *  @param bool $powiadomLekarza
     *  @param bool $danaOsoba    określa, czy $lekarz wskazuje na obiekt Lekarz (false), czy Osoba (true)
     *  @param int $czasTrwania   pozwala określić nietypowy czas trwania [w minutach] wizyt(y)
     *
     *  @return bool|string
     */
    
    public static function zapiszNaWizyte($context, $data, $pacjent, $lekarz, $powiadomPacjenta = true, $powiadomLekarza = true, $czasTrwania = null, $danaOsoba = false)
    {
       // Set & Valid $data
        if(!($data instanceof \DateTime)){
            try{
                $data = new \DateTime($data);
            } catch (\Exception $exc){
                return $exc->getMessage();
            }
        }
        
        $objectManager = $context->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
        // Set & Valid $pacjent
        if(!($pacjent instanceof \Application\Entity\Osoba)){
            try{
                $pacjent = $objectManager->find('\Application\Entity\Osoba', (int)$pacjent);

                if(!$pacjent) throw new \Exception('Pacjent o podanym ID nie istnieje.');
                
            } catch (\Exception $exc){
                return $exc->getMessage();
            }
        }
        
        // Set & Valid $lekarz
        if(!($lekarz instanceof \Application\Entity\Lekarz) && !($lekarz instanceof \Application\Entity\Osoba)){
            try{
                $lekarzArray = array();
                if($danaOsoba){
                    $repo = $objectManager->getRepository('\Application\Entity\Lekarz');
                    $lekarzArray = $repo->findBy(array('os' => $lekarz));
                } else {
                    $lekarzArray[0] = $objectManager->find('\Application\Entity\Lekarz', (int)$lekarz);
                    if(!$lekarzArray[0]) unset($lekarzArray[0]);
                }
                
                if(count($lekarzArray) == 0) throw new \Exception('Lekarz o podanym ID nie istnieje.');
                
            } catch (\Exception $exc){
                return $exc->getMessage();
            }
        }

        foreach($lekarzArray as $dr){
            $wizyta = new DB\Wizyta();
            $wizyta->setLekarz($dr);
            $wizyta->setPacjent($pacjent);
            $wizyta->setData($data);
            if($czasTrwania === null){
                $dataKonca = $data->format('U') + $dr->getMinutNaPacjenta()*60;
            } else {
                $dataKonca = $data->format('U') + $czasTrwania*60;
            }
            
            $dataKonca = new \DateTime(date('Y-m-d H:i:s',$dataKonca));
            
            if($dataKonca->format('Y-m-d') !== $data->format('Y-m-d')){
                
                $przedzialyCzasowe[0][0] = $data->format('Y-m-d H:i:s');
                $przedzialyCzasowe[0][1] = $data->format('Y-m-d').' 23:59:59';
                $przedzialyCzasowe[1][0] = $dataKonca->format('Y-m-d').' 00:00:00';  
                $przedzialyCzasowe[1][1] = $dataKonca->format('Y-m-d H:i:s');
              
                $tmpData1 = new \DateTime($przedzialyCzasowe[0][1]);
                $tmpData2 = new \DateTime($przedzialyCzasowe[1][0]);
                
                $liczbaDni = floor((((int)$tmpData2->format('U')) - ((int)$tmpData1->format('U')) - 1)/86400); 
                $tmpOstatniaData = (int)$tmpData1->format('U');
                for($k = 0; $k < $liczbaDni; $k++){
                    $d = new \DateTime(date('Y-m-d H:i:s', $tmpOstatniaData + 2));
                    $przedzialyCzasowe[] = array(
                                                0 => $d->format('Y-m-d').' 00:00:00',
                                                1 => $d->format('Y-m-d').' 23:59:59'
                                                );
                    $tmpOstatniaData += 86400;
                }
                
                foreach($przedzialyCzasowe as $przedzial){
                    $wizyta2 = new DB\Wizyta();
                    $wizyta2->setLekarz($dr);
                    $wizyta2->setPacjent($pacjent);
                    $wizyta2->setData(new \DateTime($przedzial[0]));
                    $wizyta2->setDataKoniec(new \DateTime($przedzial[1]));
                    $objectManager->persist($wizyta2);
                }
                unset($przedzialyCzasowe);
                
            } else {
                $wizyta->setDataKoniec(new \DateTime($dataKonca->format('Y-m-d H:i:s')));
                $objectManager->persist($wizyta);
            }
        }
        
        if($powiadomPacjenta) $podwiadomLista[] = $pacjent;
        
        if($powiadomLekarza) $powiadomLista[] = $lekarzArray[0]->getOs();
        
        if(isset($powiadomLista)){
            \Wizyta\Model\Narzedzia::powiadom($podwiadomLista, 'Zrejestrowano wizytę na dzień <b>'.$data->format('d.m.Y').'</b> ('.(\Wizyta\Model\Narzedzia::nazwaDnia($data->format('N'))).') na godzinę <b>'.$data->format('H:i').'</b>.');
        }
        
        
        
        $objectManager->flush();
        
        return true;
    }
    
    /**
     * @param Entity\Lekarz | Entity\Osoba $osoba
     * @param \DateTime $data
     * @param \DateTime $dataKoniec    
     *
     * @return bool
     */
    
    public static function maWizyte($context, $osoba, $data, $dataKoniec){

        $dataKonca = $dataKoniec->format('Y-m-d H:i:s');
        $dataPoczatku = $data->format('Y-m-d H:i:s');
    
        $objectManager = $context->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    
        $repo = $objectManager->getRepository('\Application\Entity\Wizyta');
        $queryBuilder = $repo->createQueryBuilder('wz');
        $queryBuilder->where($queryBuilder->getRootAlias().'.data > \''.$dataPoczatku.'\' AND '.$queryBuilder->getRootAlias().'.data < \''.$dataKonca.'\'');
        $queryBuilder->orWhere($queryBuilder->getRootAlias().'.dataKoniec > \''.$dataPoczatku.'\' AND '.$queryBuilder->getRootAlias().'.dataKoniec < \''.$dataKonca.'\'');
        $queryBuilder->orWhere($queryBuilder->getRootAlias().'.data < \''.$dataPoczatku.'\' AND '.$queryBuilder->getRootAlias().'.dataKoniec > \''.$dataKonca.'\'');    
    
        if($osoba instanceof \Application\Entity\Lekarz){
            $queryBuilder->andWhere($queryBuilder->getRootAlias().'.lekarz = '.$osoba->getLid());

        } else {
            $queryBuilder->andWhere($queryBuilder->getRootAlias().'.pacjent = '.$osoba->getId());
        }
        
        $q = $queryBuilder->getQuery();
        $w = $q->getArrayResult();
        if(count($w) > 0) return true;
        else return false;
    }
    
    
    
}
