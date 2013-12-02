<?php
namespace Application\Entity;

interface OsobaInterface
{
    public function getId();

    public function setImie($imie);
    
    public function getImie();
    
    public function setNazwisko($nazwisko);
    
    public function getNazwisko();
    
    public function setPesel($pesel);
    
    public function getPesel();
    
    public function setAdres($adres);
    
    public function getAdres();
    
    public function setTelefon($telefon);
    
    public function getTelefon();
    
    public function setEmail($email);
    
    public function getEmail();
    
    public function setDataUr($dataUr);
    
    public function getDataUr();
    
    public function setPlec($plec);
    
    public function getPlec();
    
    public function setPoziom($poziom);
    
    public function getPoziom();
    
    public function setHaslo($haslo);
    
    public function getHaslo();
    
    public function setSol($sol);
    
    public function getSol();
}