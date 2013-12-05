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
         
         $r = self::zapiszNaWizyte($this, date("Y-m-d H:i:s"), 1, 6, null,true);
         
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
     *  @param bool $danaOsoba    określa, czy $lekarz wskazuje na obiekt Lekarz (false), czy Osoba (true)
     *  @param int $czasTrwania   pozwala określić nietypowy czas trwania [w minutach] wizyt(y)
     *
     *  @return bool|string
     */
    
    public static function zapiszNaWizyte($context, $data, $pacjent, $lekarz, $czasTrwania = null, $danaOsoba = false)
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
                $dataKonca = $data->getTimestamp() + $dr->getMinutNaPacjenta()*60;
            } else {
                $dataKonca = $data->getTimestamp() + $czasTrwania*60;
            }
            $wizyta->setDataKoniec(new \DateTime(date('Y-m-d H:i:s',$dataKonca)));
            $objectManager->persist($wizyta);
        }
        
        $objectManager->flush();
        
        return true;
    }
    
    /**
     *  Powiadamiane via mail
     *  @param array(Entity\Osoba) $adresaci
     *  @param string $wiadomosc
     */
    private function powiadom($adresaci, $wiadomosc = null, $temat = null){
        foreach($adresaci as $osoba)
        {
            $emails[] = $osoba->getEmail();
        }
        
        $recipents = implode(', ', $emails);
        
        if($wiadomosc === null){
            $message = '
                <p style="background: #00A3D9; color: #fff;">Wirtualna Przychodnia</p>
                <div>
                
                </div>
                <p style="background: #00A3D9; color: #fff;">Wirtualna Przychodnia<br />tel. 452 456 842<br />fax. 484 125 458<br />www: wp.med.pl</p>
                ';
        } else {
            $message = $wiadomosc;
        }
        
        if($temat === null){
            $subject = 'Informacja z Wirtualnej Przychodni';
        } else {
            $subject = $temat;
        }


        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-2' . "\r\n";
        $headers .= 'From: Wirtualna Przychodnia <wirtualna-przychodnia@example.com>' . "\r\n";
        $headers .= 'Bcc: ' . $recipents . "\r\n";

        mail('', $subject, $message, $headers);
    }
    
    
}
