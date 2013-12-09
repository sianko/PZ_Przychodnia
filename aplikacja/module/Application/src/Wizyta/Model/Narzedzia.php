<?php

namespace Wizyta\Model;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

use Zend\Validator\Date as DataValid;
use \CustomZend\Stdlib\DateTime as DateTime;

class Narzedzia
{
    /**
     *  Powiadamiane via mail
     *  @param array(Entity\Osoba) $adresaci
     *  @param string $wiadomosc
     *
     *  @return bool
     */
    public static function powiadom($adresaci, $wiadomosc = null, $temat = null)
    {
        foreach($adresaci as $osoba)
        {
            $emails[] = $osoba->getEmail();
        }
        
        $recipents = implode(', ', $emails);
        

        if($wiadomosc === null){
            $wiadomosc = 'Zarejestrowano wizytę.';
        }
        
        $message = '
                <p style="background: #00A3D9; color: #fff;">Wirtualna Przychodnia</p>
                <div>
                '.$wiadomosc.'
                </div>
                <p style="background: #00A3D9; color: #fff;">Wirtualna Przychodnia<br />tel. 452 456 842<br />fax. 484 125 458<br />www: wp.med.pl</p>
                ';
        
        if($temat === null){
            $subject = 'Informacja z Wirtualnej Przychodni';
        } else {
            $subject = $temat;
        }


        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-2' . "\r\n";
        $headers .= 'From: Wirtualna Przychodnia <wirtualna-przychodnia@example.com>' . "\r\n";
        $headers .= 'Bcc: ' . $recipents . "\r\n";

        //return mail('', $subject, $message, $headers);
        return true;
    }
    
    
    
    public static function wolneTerminy($context, $lekarz, $miesiac, $rok)
    {
        $miesiac = intval($miesiac);
        $rok = intval($rok);
        
        $podanaData = new \DateTime($rok.'-'.$miesiac.'-01');
        
        $objectManager = $context->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Application\Entity\Wizyta');
        $queryBuilder = $repository->createQueryBuilder('Application\Entity\Wizyta');
        $alias = $queryBuilder->getRootAlias();
        
        //$queryBuilder->select($queryBuilder->getRootAlias()'');
        $queryBuilder->join($alias.'.lekarz', 'z');
        $queryBuilder->where($alias.'.lekarz = '. $lekarz->getLid());
        $queryBuilder->andWhere($alias.'.data >= \''. $rok .'-'.$miesiac.'-01 00:00:00\'');
        $queryBuilder->andWhere($alias.'.data <= \''. $rok .'-'.$miesiac.'-31 23:59:59\'');
        $query = $queryBuilder->getQuery();
        $wynik = $query->execute();
        
        $grafik = $lekarz->getGrafikArray();
 
        // "Zerowanie" pomocniczego kontenera
        for($nrDnia = 1; $nrDnia <= $podanaData->format('t'); $nrDnia++){
                $kalendarz[$nrDnia] = false;
        }
        
        // Analiza wszystkich wizyt z bazy z określonego miesiąca
        foreach($wynik as $w){
            $indeks = $w->getData()->format('Y-m-d');
            $numerDnia = $w->getData()->format('j');
            
            // Konwersja tablicy stringów na tablicę obiektów DateTime (względem daty wizyty)
            foreach($grafik[$w->getData()->format('N')-1] as $g){
                $explode = explode('-', $g);
                if(count($explode) == 2){
                    $gPocz = new \DateTime($indeks.' '.$explode[0]);
                    $gKon = new \DateTime($indeks.' '.$explode[1]);
                    
                    $zGrafikuDateTime[] = array(0 => $gPocz, 1 => $gKon);
                } else {
                    throw new \Exception("Nieprawidłowy format godzin w grafiku.");
                }
            }

            if(isset($zGrafikuDateTime)){ // przeciwym przypadek oznacza, że danego dnia lekarz nie przyjmuje (wg grafiku) 
               
                // Analiza wszystkich przedziałów godzinowych z grafiku z określonego dnia tygodnia
                foreach($zGrafikuDateTime as $zGrafiku){
                    
                    
                    if($w->getData() <= $zGrafiku[0] && $w->getDataKoniec() >= $zGrafiku[1]){ // wizyta obejmuje cały przedział godzinowy
                            $kalendarz[$numerDnia] = array();
                    } 
                    else if(($w->getDataKoniec() >= $zGrafiku[0] && $w->getDataKoniec() <= $zGrafiku[1]) || ($w->getData() >= $zGrafiku[0] && $w->getData() <= $zGrafiku[1])){


                        if($kalendarz[$numerDnia] !== false){
                            
                            foreach($kalendarz[$numerDnia] as $key => $dotychczasoweWizyty){
                                if(($w->getDataKoniec() >= $dotychczasoweWizyty[0] && $w->getDataKoniec() <= $dotychczasoweWizyty[1]) || ($w->getData() >= $dotychczasoweWizyty[0] && $w->getData() <= $dotychczasoweWizyty[1])){
                                    
                                    if($dotychczasoweWizyty[0] < $w->getData()){
                                        $kalendarz[$numerDnia][] = array(0 => $dotychczasoweWizyty[0], 1 => $w->getData());
                                    }
                                    
                                    if($dotychczasoweWizyty[1] > $w->getDataKoniec()){
                                        $kalendarz[$numerDnia][] = array(0 => $w->getDataKoniec(), 1 => $dotychczasoweWizyty[1]);
                                    }
                                    
                                    unset($kalendarz[$numerDnia][$key]);
                                }
                            }
                            
                        } else {
                            $kalendarz[$numerDnia] = array();
                            
                            if($zGrafiku[0] < $w->getData()){
                                $kalendarz[$numerDnia][] = array(0 => $zGrafiku[0], 1 => $w->getData());
                            }
                            
                            if($zGrafiku[1] > $w->getDataKoniec()){
                                $kalendarz[$numerDnia][] = array(0 => $w->getDataKoniec(), 1 => $zGrafiku[1]);
                            }
                        }
                    } else {
                        if($kalendarz[$numerDnia] === false){
                            $kalendarz[$numerDnia] = array(0 => $zGrafiku);
                        }
                    }
                    
                    
                }
                unset($zGrafikuDateTime);
            }
            
        }
        
        // Dni, w których nie ma żadnych wizyt, ustawiane są godziny z grafiku
        for($nrDnia = 1; $nrDnia <= $podanaData->format('t'); $nrDnia++){
                
            if($kalendarz[$nrDnia] === false){
                $indeks = $podanaData->format('Y-m').'-'.$nrDnia;
                $sprawdzanaData = new \DateTime($indeks);
                
                // Konwersja tablicy stringów na tablicę obiektów DateTime
                foreach($grafik[$sprawdzanaData->format('N')-1] as $g){
                    $explode = explode('-', $g);
                    if(count($explode) == 2){
                        $gPocz = new \DateTime($indeks.' '.$explode[0]);
                        $gKon = new \DateTime($indeks.' '.$explode[1]);
                        
                        $zGrafikuDateTime[] = array(0 => $gPocz, 1 => $gKon);
                    } else {
                        throw new \Exception("Nieprawidłowy format godzin w grafiku.");
                    }
                }
            
                if(isset($zGrafikuDateTime)){
                    foreach($zGrafikuDateTime as $zGrafiku){
                        $kalendarz[$nrDnia] = array(0 => $zGrafiku);
                    }
                    unset($zGrafikuDateTime);
                }
            
            }
    
        }
        
        return $kalendarz;
       
    }
    
    
    
}
