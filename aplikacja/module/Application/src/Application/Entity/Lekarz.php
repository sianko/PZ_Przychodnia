<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lekarz
 *
 * @ORM\Table(name="lekarze", indexes={@ORM\Index(name="fk_lekarze_spec", columns={"spec_id"}), @ORM\Index(name="fk_lekarze_os", columns={"os_id"})})
 * @ORM\Entity
 */
class Lekarz implements OsobaInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $lid;

    /**
     * @var string
     *
     * @ORM\Column(name="tytul_naukowy", type="string", length=15, nullable=true)
     */
    private $tytulNaukowy;

    /**
     * @var string
     *
     * @ORM\Column(name="grafik", type="string", length=500, nullable=true)
     */
    private $grafik;

    /**
     * @var integer
     *
     * @ORM\Column(name="minut_na_pacjenta", type="integer", nullable=true)
     */
    private $minutNaPacjenta = '30';

    /**
     * @var \Application\Entity\Osoba
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Osoba", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="os_id", referencedColumnName="id")
     * })
     */
    private $os;

    /**
     * @var \Application\Entity\Specjalnosc
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Specjalnosc", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="spec_id", referencedColumnName="id")
     * })
     */
    private $spec;



    /**
     * Get lid
     *
     * @return integer 
     */
    public function getLid()
    {
        return $this->lid;
    }

    /**
     * Set tytulNaukowy
     *
     * @param string $tytulNaukowy
     * @return Lekarz
     */
    public function setTytulNaukowy($tytulNaukowy)
    {
        $this->tytulNaukowy = $tytulNaukowy;

        return $this;
    }

    /**
     * Get tytulNaukowy
     *
     * @return string 
     */
    public function getTytulNaukowy()
    {
        return $this->tytulNaukowy;
    }

    /**
     * Set grafik
     *
     * @param string $grafik
     * @return Lekarz
     */
    public function setGrafik($grafik)
    {
        $this->grafik = $grafik;

        return $this;
    }

    /**
     * Get grafik
     *
     * @return string 
     */
    public function getGrafik()
    {
        return $this->grafik;
    }
    
    
    /**
     * Convert string of grafik to array
     *
     * @return array
     */
    public function getGrafikArray()
    {
        if(empty($this->grafik)) return array();
        
        $dni = explode('%', $this->grafik);
        
        if(count($dni) < 7) return array();
        
        foreach($dni as $d)
        {
            $wynik[] = explode(';', $d);
        }
        
        return $wynik;
    }
    
    /**
     * Convert array to string
     *
     * @param array $array
     * @return Lekarz
     */
    public function setGrafikArray($array)
    {
        if(!is_array($array)){
            throw new Exception("Parametr nie jest tablicą.");
        }
        
        
        foreach($array as $dzien)
        {
            if(!is_array($dzien)){
                throw new Exception("Parametr nie jest dwuwymiarową tablicą.");
            }
            
            $wynik[] = implode(';', $dzien);
        }
        
        $this->grafik = implode('%', $wynik);

        return $this;
    }
    

    /**
     * Set minutNaPacjenta
     *
     * @param integer $minutNaPacjenta
     * @return Lekarz
     */
    public function setMinutNaPacjenta($minutNaPacjenta)
    {
        $this->minutNaPacjenta = $minutNaPacjenta;

        return $this;
    }

    /**
     * Get minutNaPacjenta
     *
     * @return integer 
     */
    public function getMinutNaPacjenta()
    {
        return $this->minutNaPacjenta;
    }

    /**
     * Set os
     *
     * @param \Application\Entity\Osoba $os
     * @return Lekarz
     */
    public function setOs(\Application\Entity\Osoba $os = null)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return \Application\Entity\Osoba 
     */
    public function getOs()
    {
        if($this->os == null) $this->os = new \Application\Entity\Osoba();
        return $this->os;
    }

    /**
     * Set spec
     *
     * @param \Application\Entity\Specjalnosc $spec
     * @return Lekarz
     */
    public function setSpec(\Application\Entity\Specjalnosc $spec = null)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * Get spec
     *
     * @return \Application\Entity\Specjalnosc 
     */
    public function getSpec()
    {
        if($this->spec == null) $this->spec = new \Application\Entity\Specjalnosc();
        return $this->spec;
    }
    
    
    
    /**
     *  OsobaInterface Implementation
     */
    public function getId()
    {
        return  $this->getOs()->getId();
    }

    /**
     * Set imie
     *
     * @param string $imie
     * @return Lekarz
     */
    public function setImie($imie)
    {
         $this->getOs()->setImie($imie);

        return $this;
    }

    /**
     * Get imie
     *
     * @return string 
     */
    public function getImie()
    {
        return  $this->getOs()->getImie();
    }

    /**
     * Set nazwisko
     *
     * @param string $nazwisko
     * @return Lekarz
     */
    public function setNazwisko($nazwisko)
    {
        $this->getOs()->setNazwisko($nazwisko);

        return $this;
    }

    /**
     * Get nazwisko
     *
     * @return string 
     */
    public function getNazwisko()
    {
        return $this->getOs()->getNazwisko();
    }

    /**
     * Set pesel
     *
     * @param string $pesel
     * @return Lekarz
     */
    public function setPesel($pesel)
    {
        $this->getOs()->setPesel($pesel);

        return $this;
    }

    /**
     * Get pesel
     *
     * @return string 
     */
    public function getPesel()
    {
        return $this->getOs()->getPesel();
    }

    /**
     * Set adres
     *
     * @param string $adres
     * @return Lekarz
     */
    public function setAdres($adres)
    {
        $this->getOs()->setAdres($adres);

        return $this;
    }

    /**
     * Get adres
     *
     * @return string 
     */
    public function getAdres()
    {
        return $this->getOs()->getAdres();
    }

    /**
     * Set telefon
     *
     * @param string $telefon
     * @return Lekarz
     */
    public function setTelefon($telefon)
    {
        $this->getOs()->setTelefon($telefon);

        return $this;
    }

    /**
     * Get telefon
     *
     * @return string 
     */
    public function getTelefon()
    {
        return $this->getOs()->getTelefon();
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Lekarz
     */
    public function setEmail($email)
    {
        $this->getOs()->setEmail($email);

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->getOs()->getEmail();
    }

    /**
     * Set dataUr
     *
     * @param \DateTime $dataUr
     * @return Lekarz
     */
    public function setDataUr($dataUr)
    {
        $this->getOs()->setDataUr($dataUr);

        return $this;
    }

    /**
     * Get dataUr
     *
     * @return \DateTime 
     */
    public function getDataUr()
    {
        return $this->getOs()->getDataUr();
    }

    /**
     * Set plec
     *
     * @param string $plec
     * @return Lekarz
     */
    public function setPlec($plec)
    {
        $this->getOs()->setPlec($plec);

        return $this;
    }

    /**
     * Get plec
     *
     * @return string 
     */
    public function getPlec()
    {
        return $this->getOs()->getPlec();
    }

    /**
     * Set poziom
     *
     * @param integer $poziom
     * @return Lekarz
     */
    public function setPoziom($poziom)
    {
        $this->getOs()->setPoziom($poziom);

        return $this;
    }

    /**
     * Get poziom
     *
     * @return integer 
     */
    public function getPoziom()
    {
        return $this->getOs()->getPoziom();
    }

    /**
     * Set haslo
     *
     * @param string $haslo
     * @return Lekarz
     */
    public function setHaslo($haslo)
    {
        $this->getOs()->setHaslo($haslo);

        return $this;
    }

    /**
     * Get haslo
     *
     * @return string 
     */
    public function getHaslo()
    {
        return $this->getOs()->getHaslo();
    }

    /**
     * Set sol
     *
     * @param string $sol
     * @return Lekarz
     */
    public function setSol($sol)
    {
        $this->getOs()->setSol($sol);

        return $this;
    }

    /**
     * Get sol
     *
     * @return string 
     */
    public function getSol()
    {
        return $this->getOs()->getSol();
    }
    
}
