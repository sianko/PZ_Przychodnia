<?php

namespace Wizyta\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity as DB;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Wizyta\Model\Narzedzia as Narzedzia;
use Zend\Validator\Date as DataValid;

class CronController extends AbstractActionController
{
    
    public function wykonajZadanieAction()
    {
        $za1Dzien = new \DateTime(date('Y-m-d H:i:s', time() + floor(86400/3)));
        
        $za2Dni = new \DateTime(date('Y-m-d H:i:s', time() + 2*86400));
        
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repository = $objectManager->getRepository('Application\Entity\Wizyta');
        $createQueryBuilder = $repository->createQueryBuilder('wz');
        $createQueryBuilder->where($createQueryBuilder->getRootAlias().'.data >= \''.$za1Dzien->format('Y-m-d H:i:s').'\'');
        $createQueryBuilder->andWhere($createQueryBuilder->getRootAlias().'.dataKoniec <= \''.$za2Dni->format('Y-m-d H:i:s').'\'');
        
        $q = $createQueryBuilder->getQuery();
        $wizytyLista = $q->execute();
        
        $mailLekarze = array();
        $mailPacjenci = array();
        
        foreach($wizytyLista as $wizyta){
            if($wizyta->getStatus() == 0){
                $mailLekarze[$wizyta->getLekarz()->getEmail()][] = $wizyta->getData();
                $mailPacjent[$wizyta->getPacjent()->getEmail()][] = $wizyta->getData();
            }
        }
        
        
        foreach($mailLekarze as $mail => $ml){
            $wiadomosc = 'Przypominamy o zbliżających się wizytach: <br><br>';
            
            foreach($ml as $g){
                $wiadomosc .= '- '.$g->format('d.m.Y').' ('.(\Wizyta\Model\Narzedzia::nazwaDnia($g->format('N'))).')'.' godz. '.$g->format('H:i').'<br>';
            }
            
            $wiadomosc .= '<br><br>Jeżeli nie mogą Państwo przybyć, prosimy o odwołanie wizyty za pośrednictem panelu użytkownika na stronie naszej przychodni.';
            
            \Wizyta\Model\Narzedzia::powiadom(array($mail), $wiadomosc, 'Przypomnienie o wizycie');
        }
        
        
        foreach($mailPacjenci as $mail => $ml){
            $wiadomosc = 'Przypominamy o zbliżających się wizytach: <br><br>';
            
            foreach($ml as $g){
                $wiadomosc .= '- '.$g->format('d.m.Y').' ('.(\Wizyta\Model\Narzedzia::nazwaDnia($g->format('N'))).')'.' godz. '.$g->format('H:i').'<br>';
            }
            
            $wiadomosc .= '<br><br>Jeżeli nie mogą Państwo przybyć, prosimy o odwołanie wizyty za pośrednictem panelu użytkownika na stronie naszej przychodni.';
            
            \Wizyta\Model\Narzedzia::powiadom(array($mail), $wiadomosc, 'Przypomnienie o wizycie');
        }
        
        return;
    }
    
    
    
}
