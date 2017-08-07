<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CityRepository")
 */
class City
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var
     * @ORM\OneToMany(targetEntity="Temperatur", mappedBy="city")
     */
    private $temperaturs;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->temperaturs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add temperatur
     *
     * @param \AppBundle\Entity\Temperatur $temperatur
     *
     * @return City
     */
    public function addTemperatur(\AppBundle\Entity\Temperatur $temperatur)
    {
        $this->temperaturs[] = $temperatur;

        return $this;
    }

    /**
     * Remove temperatur
     *
     * @param \AppBundle\Entity\Temperatur $temperatur
     */
    public function removeTemperatur(\AppBundle\Entity\Temperatur $temperatur)
    {
        $this->temperaturs->removeElement($temperatur);
    }

    /**
     * Get temperaturs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTemperaturs()
    {
        return $this->temperaturs;
    }
}
