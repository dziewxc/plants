<?php
//src Dogs/DogBundle/Entity/Dog.php

namespace Dogs\DogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
*@ORM\Entity(repositoryClass="Dogs\DogBundle\Entity\Repository\DogRepository")
*@ORM\Table(name="dogs")
*@ORM\HasLifeCycleCallbacks
*/
class Dog
{
    /**
    *@ORM\Id
    *@ORM\Column(type="integer")
    *@ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;
    /**
    *@ORM\Column(type="string")
    */
    protected $name;
    /**
    *@ORM\Column(type="string", length=100)
    */
    protected $location;
    /**
    *@ORM\Column(type="string", length=50)
    */
    protected $breed;
    /**
    *@ORM\Column(type="integer")
    */
    protected $age;
    /**
    *@ORM\Column(type="boolean")
    */
    protected $sterilization;
    /**
    *@ORM\Column(type="string", length=10)
    */
    protected $sex;
    /**
    *@ORM\Column(type="text")
    */
    protected $description;
    /**
    *@ORM\Column(type="datetime")
    */
    protected $created;
    /**
    *@ORM\Column(type="string", length=200)
    */
    protected $url;
    /**
    *@ORM\Column(type="string", length=100)
    */
    protected $title;
    
	public function __construct()
		{
			$this->setCreated(new \DateTime());

		}
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
     * Set name
     *
     * @param string $name
     *
     * @return Dog
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
     * Set location
     *
     * @param string $location
     *
     * @return Dog
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set breed
     *
     * @param string $breed
     *
     * @return Dog
     */
    public function setBreed($breed)
    {
        $this->breed = $breed;

        return $this;
    }

    /**
     * Get breed
     *
     * @return string
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return Dog
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set sterilization
     *
     * @param boolean $sterilization
     *
     * @return Dog
     */
    public function setSterilization($sterilization)
    {
        $this->sterilization = $sterilization;

        return $this;
    }

    /**
     * Get sterilization
     *
     * @return boolean
     */
    public function getSterilization()
    {
        return $this->sterilization;
    }

    /**
     * Set sex
     *
     * @param string $sex
     *
     * @return Dog
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set description
     *
     * @param \text $description
     *
     * @return Dog
     */
    public function setDescription(\text $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return \text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created
     *
     * @param \datetime $created
     *
     * @return Dog
     */
    public function setCreated(\DateTime  $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Dog
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Dog
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
