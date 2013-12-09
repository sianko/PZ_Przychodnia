<?php

namespace Wizyta\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

use Zend\Validator\Date as DataValid;
use \CustomZend\Stdlib\DateTime as DateTime;

class RejestracjaController extends AbstractActionController
{
    
    public function zapiszSieAction()
    {
         $msg= null;
         
         //$r = self::zapiszNaWizyte($this, '2013-12-10 17:00:00', 1, 6, true, true, null, true);
         $r = self::zapiszNaWizyte($this, '2013-12-10 10:00:00', 1, 3, true, true, 60*8);
         
         if($r === true){
            $msg[0] = 1;
            $msg[1] = 'Wizyty zostały zarejestrowane.';
         } else {
            $msg[0] = 0;
            $msg[1] = 'Nie udało się zarejestrować wizyt. ['.$r.']';
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
            \Wizyta\Model\Narzedzia::powiadom($podwiadomLista, 'Zrejestrowano wizytę na dzień <b>'.$data->format('d.m.Y').'</b> na godzinę <b>'.$data->format('H:i').'</b>.');
        }
        
        
        
        $objectManager->flush();
        
        return true;
    }
    
    
    
    
    
    
}
