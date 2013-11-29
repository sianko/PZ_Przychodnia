<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lekarz
 *
 * @ORM\Table(name="lekarze", indexes={@ORM\Index(name="fk_lekarze_spec", columns={"spec_id"}), @ORM\Index(name="fk_lekarze_os", columns={"os_id"})})
 * @ORM\Entity
 */
class Lekarz
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
     * @var integer
     *
     * @ORM\Column(name="tytul_naukowy", type="integer", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="Application\Entity\Osoba")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="os_id", referencedColumnName="id")
     * })
     */
    private $os;

    /**
     * @var \Application\Entity\Specjalnosc
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Specjalnosc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="spec_id", referencedColumnName="id")
     * })
     */
    private $spec;



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
     * Set tytulNaukowy
     *
     * @param integer $tytulNaukowy
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
     * @return integer 
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
        return $this->spec;
    }
}
