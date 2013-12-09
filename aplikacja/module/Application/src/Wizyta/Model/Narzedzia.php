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
    
    
}
