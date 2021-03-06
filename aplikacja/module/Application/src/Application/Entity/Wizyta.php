<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wizyta
 *
 * @ORM\Table(name="wizyty", indexes={@ORM\Index(name="fk_wizyty_pacjent", columns={"pacjent_id"}), @ORM\Index(name="fk_wizyty_lekarz", columns={"lekarz_id"})})
 * @ORM\Entity
 */
class Wizyta
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
     * @var \DateTime
     *
     * @ORM\Column(name="data", type="datetime", nullable=false)
     */
    private $data;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_koniec", type="datetime", nullable=false)
     */
    private $dataKoniec;    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var \Application\Entity\Lekarz
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Lekarz", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lekarz_id", referencedColumnName="id")
     * })
     */
    private $lekarz;

    /**
     * @var \Application\Entity\Osoba
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Osoba", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pacjent_id", referencedColumnName="id")
     * })
     */
    private $pacjent;



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
     * Set data
     *
     * @param \DateTime $data
     * @return Wizyta
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime 
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Set data_koniec
     *
     * @param \DateTime $data
     * @return Wizyta
     */
    public function setDataKoniec($data)
    {
        $this->dataKoniec = $data;

        return $this;
    }

    /**
     * Get data_koniec
     *
     * @return \DateTime 
     */
    public function getDataKoniec()
    {
        return $this->dataKoniec;
    }    

    /**
     * Set status
     *
     * @param boolean $status
     * @return Wizyta
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set lekarz
     *
     * @param \Application\Entity\Lekarz $lekarz
     * @return Wizyta
     */
    public function setLekarz(\Application\Entity\Lekarz $lekarz = null)
    {
        $this->lekarz = $lekarz;

        return $this;
    }

    /**
     * Get lekarz
     *
     * @return \Application\Entity\Lekarz 
     */
    public function getLekarz()
    {
        if($this->lekarz == null) $this->lekarz = new \Application\Entity\Lekarz();
        return $this->lekarz;
    }

    /**
     * Set pacjent
     *
     * @param \Application\Entity\Osoba $pacjent
     * @return Wizyta
     */
    public function setPacjent(\Application\Entity\Osoba $pacjent = null)
    {
        $this->pacjent = $pacjent;

        return $this;
    }

    /**
     * Get pacjent
     *
     * @return \Application\Entity\Osoba 
     */
    public function getPacjent()
    {
        if($this->pacjent == null) $this->pacjent = new \Application\Entity\Osoba();
        return $this->pacjent;
    }

    
}
