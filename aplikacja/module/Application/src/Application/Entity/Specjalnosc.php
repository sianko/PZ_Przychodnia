<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Specjalnosc
 *
 * @ORM\Table(name="specjalnosci", uniqueConstraints={@ORM\UniqueConstraint(name="nazwa", columns={"nazwa"})})
 * @ORM\Entity
 */
class Specjalnosc
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
     * @ORM\Column(name="nazwa", type="string", length=30, nullable=false)
     */
    private $nazwa;



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
     * Set nazwa
     *
     * @param string $nazwa
     * @return Specjalnosc
     */
    public function setNazwa($nazwa)
    {
        $this->nazwa = $nazwa;

        return $this;
    }

    /**
     * Get nazwa
     *
     * @return string 
     */
    public function getNazwa()
    {
        return $this->nazwa;
    }
}
