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
            if($osoba instanceof \Application\Entity\OsobaInterface){
                $emails[] = $osoba->getEmail();
            } else {
                $emails[] = $osoba;
            }
        }
        
        $recipents = implode(', ', $emails);
        

        if($wiadomosc === null){
            $wiadomosc = 'Zarejestrowano wizytę.';
        }
        
        $message = '
                <p style="color: #00A3D9; font-weight: bold; border-bottom: 1px solid #00A3D9;">&lsaquo; Dzień dobry, Wirtualna Przychodnia uprzejmie informuje:</p>
                <div style="padding-left: 10px;">
                '.$wiadomosc.'
                </div>
                <p style="color: #00A3D9; font-weight: bold; border-top: 1px solid #00A3D9; font-size: 9px; font-style: italic;">Wirtualna Przychodnia<br />tel. 452 456 842<br />fax. 484 125 458<br />www: wp.med.pl</p>
                ';
        
        if($temat === null){
            $subject = 'Informacja z Wirtualnej Przychodni';
        } else {
            $subject = $temat;
        }


        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: Wirtualna Przychodnia <wirtualna-przychodnia@example.com>' . "\r\n";
        $headers .= 'Bcc: ' . $recipents . "\r\n";

        return mail('', $subject, $message, $headers);
        
    }
    
    /**
     *  Zwraca tablicę terminów z danego miesiąca, które można zarezerwować.
     *  
     *  @param $context = $this
     *  @param Application\Entity\Lekarz $lekarz
     *  @param int $miesiac
     *  @param int $rok
     *
     *  @return array of \DateTime - postaci: tablica[int: dzień miesiąca][int: przedziały wolnych godzin][0:godzina początkowa | 1: godzina końcowa]
     */
    
    public static function wolneTerminy($context, $lekarz, $miesiac, $rok, $przyszlosc = true)
    {
        $miesiac = intval($miesiac);
        $rok = intval($rok);
        
        $podanaData = new \DateTime($rok.'-'.$miesiac.'-01');
        
        $objectManager = $context->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Application\Entity\Wizyta');
        $queryBuilder = $repository->createQueryBuilder('wz');
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
        
            if($w->getStatus() > 0) continue; // jeżeli wizyta została odwołana, traktujemy ją jakby jej nie było
            
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
                
           $indeks = $podanaData->format('Y-m').'-'.$nrDnia;

           if($kalendarz[$nrDnia] === false){
                $sprawdzanaData = new \DateTime($indeks);
                
                // Tylko przyszłe terminy
                if($przyszlosc && strtotime($indeks.' 23:59:59') <= time()){
                    $kalendarz[$nrDnia] = array();
                }
                else {
                
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
            } else if($przyszlosc && strtotime($indeks.' 23:59:59') <= time()){
                $kalendarz[$nrDnia] = array();
            }
    
        }
        
        return $kalendarz;
       
    }
    
    /**
     *  Odwoływanie wizyty. Ustawienia na podstawie $this->identity()->poziom
     *  
     *  @param $context = $this
     *  @param int|array id wizyty albo array(data rozpoczęcia, data zakończenia) - wizyty z zakresu
     *  @param bool $powiadomienie - warunkuje wysłanie powiadomień o odwołaniu wizyty
     *  
     *  @return bool
     */
    
    public function odwolajWizyte($context, $cel, $powiadomienie = true, $odwoluje = null){
        
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Application\Entity\Lekarz');
        
        if($odwoluje === null){
            
            if($this->identity()->poziom == 1){
                $nowyStatusWizyty = 2;
                $queryBuilder = $repository->createQueryBuilder('Application\Entity\Lekarz');
                $alias = $queryBuilder->getRootAlias();
                $queryBuilder->where($alias.'.os = '. $this->identity()->id);
                $query = $queryBuilder->getQuery();
                $wynik = $query->execute();
                
                foreach($wynik as $l){
                    $tablicaWarunkow[] = $l->getLid();
                }
                
            } else {
                $nowyStatusWizyty = 1;
                $tablicaWarunkow[0] = $this->identity()->id;
            }
            
        } else if($this->identity()->poziom == 2) {
        
            if($odwoluje === null) return false;
            $os = $odwoluje;
            $nowyStatusWizyty = intval($os->getPoziom())+1;
            
            if($nowyStatusWizyty === 2){
                $queryBuilder = $repository->createQueryBuilder('Application\Entity\Lekarz');
                $alias = $queryBuilder->getRootAlias();
                $queryBuilder->where($alias.'.os = '. $os->getId());
                $query = $queryBuilder->getQuery();
                $wynik = $query->execute();
                
                foreach($wynik as $l){
                    $tablicaWarunkow[] = $l->getLid();
                }
            }
            else {
                $tablicaWarunkow[0] = $os->getId();
            }
        } else {
            throw new \Exception('Nie masz uprawnień do tej operacji.');
            return false;
            }
            
        unset($alias);
       
        $repo = $objectManager->getRepository('\Application\Entity\Wizyta');
        $queryBuilder1 = $repo->createQueryBuilder('wz');

        
        $alias = $queryBuilder1->getRootAlias().'.';
        
        if(is_array($cel)){
            $queryBuilder1->where($alias.'data >= \''.date('Y-m-d H:i:s', strtotime($cel[0])).'\'');
            $queryBuilder1->andWhere($alias.'dataKoniec <= \''.date('Y-m-d H:i:s', strtotime($cel[1])).'\'');

            
            if(isset($tablicaWarunkow)){
                $warunek = implode(' OR '.$alias.''.($nowyStatusWizyty === 2 ? 'lekarz' : 'pacjent').' = ', $tablicaWarunkow);
                $queryBuilder1->andWhere($alias.''.($nowyStatusWizyty === 2 ? 'lekarz' : 'pacjent').' = '.$warunek);
            }
            
            
        } else {
            $queryBuilder1->where($alias.'id = '.intval($cel));
        }
        
        $queryBuilder1->andWhere($alias.'status = 0');
        
        //echo 'SQL: '.$queryBuilder1->getDql();
        $query = $queryBuilder1->getQuery();
        $pobrane = $query->execute();
 
        $listaMailingowa = array();
        
        foreach($pobrane as $rekord){
            $rekord->setStatus($nowyStatusWizyty);
            $objectManager->persist($rekord);
            
            if($powiadomienie && $nowyStatusWizyty == 2){
                $listaMailingowa[] = $rekord->getPacjent();
            } else if($powiadomienie) {
                $listaMailingowa[] = $rekord->getLekarz();
            }
            
            $ostatniaWizytaData = $rekord->getData()->format('H:i d.m.Y');
            $ostatniaWizytaDataKoniec = $rekord->getDataKoniec()->format('H:i d.m.Y');;
        }

        $objectManager->flush();

        if($powiadomienie && count($listaMailingowa) > 0){
            if(is_array($cel)){
                $komunikatEmail = 'Informujemy, że wizyty które miały odbyć się w dniach <b>'.date('H:i d.m.Y', strtotime($cel[0])).' - '.date('H:i d.m.Y', strtotime($cel[1])).'</b> zostały odwołane.';
            } else {
                
                $komunikatEmail = 'Informujemy, że wizyta która miała odbyć się w dniu <b>'.$ostatniaWizytaData.' - '.$ostatniaWizytaDataKoniec.'</b> została odwołana.';
            }
            self::powiadom($listaMailingowa, '<p>'.$komunikatEmail.'</p>', 'Wizyta została odwołana!');
        }
        
        return true;
    }
    
    
    public static function nazwaMiesiaca($nr, $mianownik = true){
        switch (intval($nr)){
            case 1 : return $mianownik ? 'styczeń' : 'stycznia';
            case 2 : return $mianownik ? 'luty' : 'lutego';
            case 3 : return $mianownik ? 'marzec' : 'marca';
            case 4 : return $mianownik ? 'kwiecień' : 'kwietnia';
            case 5 : return $mianownik ? 'maj' : 'maja';
            case 6 : return $mianownik ? 'czerwiec' : 'czerwca';
            case 7 : return $mianownik ? 'lipiec' : 'lipca';
            case 8 : return $mianownik ? 'sierpień' : 'sierpnia';
            case 9 : return $mianownik ? 'wrzesień' : 'września';
            case 10 : return $mianownik ? 'październik' : 'października';
            case 11 : return $mianownik ? 'listopad' : 'listopada';
            case 12 : return $mianownik ? 'grudzień' : 'grudnia';
        }
    }
    
    public static function nazwaDnia($nr){
        switch (intval($nr)){
            case 1 : return 'poniedziałek';
            case 2 : return 'wtorek';
            case 3 : return 'środa';
            case 4 : return 'czwartek';
            case 5 : return 'piątek';
            case 6 : return 'sobota';
            case 7 : return 'niedziela';
        }
    }
}
