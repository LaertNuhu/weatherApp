<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Temperatur
 *
 * @ORM\Table(name="temperatur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TemperaturRepository")
 */
class Temperatur
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
     * @ORM\Column(name="min", type="string", length=255)
     */
    private $min;

    /**
     * @var string
     *
     * @ORM\Column(name="max", type="string", length=255)
     */
    private $max;

    /**
     * @var
     *
     * @ORM\Column(name="current_temp", type="string", length=255)
     */
    private $currentTemp;

    /**
     * @var
     *
     * @ORM\Column(name="humidity", type="string", length=255)
     */
    private $humidity;

    /**
     * @var
     *
     * @ORM\Column(name="icon", type="string", length=255)
     */
    private $icon;

    /**
     * @var
     *
     * @ORM\Column(name="wind_speed", type="string", length=255)
     */
    private $windSpeed;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="City", inversedBy="temperaturs")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $city;


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
     * Set min
     *
     * @param string $min
     *
     * @return Temperatur
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return string
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param string $max
     *
     * @return Temperatur
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return string
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set city
     *
     * @param \AppBundle\Entity\City $city
     *
     * @return Temperatur
     */
    public function setCity(\AppBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \AppBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set currentTemp
     *
     * @param string $currentTemp
     *
     * @return Temperatur
     */
    public function setCurrentTemp($currentTemp)
    {
        $this->currentTemp = $currentTemp;

        return $this;
    }

    /**
     * Get currentTemp
     *
     * @return string
     */
    public function getCurrentTemp()
    {
        return $this->currentTemp;
    }

    /**
     * Set humidity
     *
     * @param string $humidity
     *
     * @return Temperatur
     */
    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;

        return $this;
    }

    /**
     * Get humidity
     *
     * @return string
     */
    public function getHumidity()
    {
        return $this->humidity;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return Temperatur
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set windSpeed
     *
     * @param string $windSpeed
     *
     * @return Temperatur
     */
    public function setWindSpeed($windSpeed)
    {
        $this->windSpeed = $windSpeed;

        return $this;
    }

    /**
     * Get windSpeed
     *
     * @return string
     */
    public function getWindSpeed()
    {
        return $this->windSpeed;
    }
}
