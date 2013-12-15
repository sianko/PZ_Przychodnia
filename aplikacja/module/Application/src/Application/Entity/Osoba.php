<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Osoba
 *
 * @ORM\Table(name="osoby", uniqueConstraints={@ORM\UniqueConstraint(name="pesel", columns={"pesel"})})
 * @ORM\Entity
 */
class Osoba implements OsobaInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="imie", type="string", length=20, nullable=false)
     */
    private $imie;

    /**
     * @var string
     *
     * @ORM\Column(name="nazwisko", type="string", length=40, nullable=false)
     */
    private $nazwisko;

    /**
     * @var string
     *
     * @ORM\Column(name="pesel", type="string", length=11, nullable=false)
     */
    private $pesel;

    /**
     * @var string
     *
     * @ORM\Column(name="adres", type="string", length=200, nullable=false)
     */
    private $adres;

    /**
     * @var string
     *
     * @ORM\Column(name="telefon", type="string", length=11, nullable=true)
     */
    private $telefon;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_ur", type="date", nullable=false)
     */
    private $dataUr;

    /**
     * @var string
     *
     * @ORM\Column(name="plec", type="string", length=1, nullable=false)
     */
    private $plec;

    /**
     * @var integer
     *
     * @ORM\Column(name="poziom", type="integer", nullable=false)
     */
    private $poziom = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="haslo", type="string", length=60, nullable=false)
     */
    private $haslo;

    /**
     * @var string
     *
     * @ORM\Column(name="sol", type="string", length=16, nullable=false)
     */
    private $sol;

    /**
     * @var integer
     *
     * @ORM\Column(name="aktywny", type="integer", nullable=false)
     */
    private $aktywny = '0';    
    
    


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set id
     *
     * @param integer 
     * @return Osoba
     */
    public function setId($id)
    {
        $this->id = intval($id);
        return $this;
    }

    /**
     * Set imie
     *
     * @param string $imie
     * @return Osoba
     */
    public function setImie($imie)
    {
        $this->imie = $imie;

        return $this;
    }

    /**
     * Get imie
     *
     * @return string 
     */
    public function getImie()
    {
        return $this->imie;
    }

    /**
     * Set nazwisko
     *
     * @param string $nazwisko
     * @return Osoba
     */
    public function setNazwisko($nazwisko)
    {
        $this->nazwisko = $nazwisko;

        return $this;
    }

    /**
     * Get nazwisko
     *
     * @return string 
     */
    public function getNazwisko()
    {
        return $this->nazwisko;
    }

    /**
     * Set pesel
     *
     * @param string $pesel
     * @return Osoba
     */
    public function setPesel($pesel)
    {
        $this->pesel = $pesel;

        return $this;
    }

    /**
     * Get pesel
     *
     * @return string 
     */
    public function getPesel()
    {
        return $this->pesel;
    }

    /**
     * Set adres
     *
     * @param string $adres
     * @return Osoba
     */
    public function setAdres($adres)
    {
        $this->adres = $adres;

        return $this;
    }

    /**
     * Get adres
     *
     * @return string 
     */
    public function getAdres()
    {
        return $this->adres;
    }

    /**
     * Set telefon
     *
     * @param string $telefon
     * @return Osoba
     */
    public function setTelefon($telefon)
    {
        $this->telefon = $telefon;

        return $this;
    }

    /**
     * Get telefon
     *
     * @return string 
     */
    public function getTelefon()
    {
        return $this->telefon;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Osoba
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dataUr
     *
     * @param \DateTime $dataUr
     * @return Osoba
     */
    public function setDataUr($dataUr)
    {
        $this->dataUr = $dataUr;

        return $this;
    }

    /**
     * Get dataUr
     *
     * @return \DateTime 
     */
    public function getDataUr()
    {
        return $this->dataUr;
    }

    /**
     * Set plec
     *
     * @param string $plec
     * @return Osoba
     */
    public function setPlec($plec)
    {
        $this->plec = $plec;

        return $this;
    }

    /**
     * Get plec
     *
     * @return string 
     */
    public function getPlec()
    {
        return $this->plec;
    }

    /**
     * Set poziom
     *
     * @param integer $poziom
     * @return Osoba
     */
    public function setPoziom($poziom)
    {
        $this->poziom = $poziom;

        return $this;
    }

    /**
     * Get poziom
     *
     * @return integer 
     */
    public function getPoziom()
    {
        return $this->poziom;
    }

    /**
     * Set haslo
     *
     * @param string $haslo
     * @return Osoba
     */
    public function setHaslo($haslo)
    {
        $this->haslo = $haslo;

        return $this;
    }

    /**
     * Get haslo
     *
     * @return string 
     */
    public function getHaslo()
    {
        return $this->haslo;
    }

    /**
     * Set sol
     *
     * @param string $sol
     * @return Osoba
     */
    public function setSol($sol)
    {
        $this->sol = $sol;

        return $this;
    }

    /**
     * Get sol
     *
     * @return string 
     */
    public function getSol()
    {
        return $this->sol;
    }
    
    
    /**
     * Set aktywny
     *
     * @param integer $aktywny
     * @return Osoba
     */
    public function setAktywny($aktywny)
    {
        $this->aktywny = $aktywny == 1 ? 1 : 0;

        return $this;
    }

    /**
     * Get aktywny
     *
     * @return integer 
     */
    public function getAktywny()
    {
        return $this->aktywny;
    }    
    
    
}
