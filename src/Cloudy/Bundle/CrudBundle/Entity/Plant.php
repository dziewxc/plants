<?php
namespace Cloudy\Bundle\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
*@ORM\Entity
*@ORM\Table(name="plants")
*/
class Plant
{
    /**
    *@ORM\Id
    *@ORM\Column(type="integer")
    */
    protected $id;
    /**
    *@ORM\Column(name="location", type="string")
    */
    protected $location;
    /**
    *@ORM\Column(name="flat_surface", type="integer")
    */
    protected $flatSurface;
    /**
    *@ORM\Column(name="residents_count", type="integer")
    */
    protected $residentsCount;
    /**
    *@ORM\Column(type="datetime", name="posted_at")
    */
    protected $postedAt;
    protected $user;
    /**
    *@ORM\Column(name="electronics_amount", type="integer")
    */
    protected $electronicsAmount;
    /**
    *@ORM\Column(name="if_garage", type="boolean")
    */
    protected $ifGarage;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Plant
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set location
     *
     * @param string $location
     *
     * @return Plant
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
     * Set flatSurface
     *
     * @param integer $flatSurface
     *
     * @return Plant
     */
    public function setFlatSurface($flatSurface)
    {
        $this->flatSurface = $flatSurface;

        return $this;
    }

    /**
     * Get flatSurface
     *
     * @return integer
     */
    public function getFlatSurface()
    {
        return $this->flatSurface;
    }

    /**
     * Set residentsCount
     *
     * @param integer $residentsCount
     *
     * @return Plant
     */
    public function setResidentsCount($residentsCount)
    {
        $this->residentsCount = $residentsCount;

        return $this;
    }

    /**
     * Get residentsCount
     *
     * @return integer
     */
    public function getResidentsCount()
    {
        return $this->residentsCount;
    }

    /**
     * Set postedAt
     *
     * @param \DateTime $postedAt
     *
     * @return Plant
     */
    public function setPostedAt($postedAt)
    {
        $this->postedAt = $postedAt;

        return $this;
    }

    /**
     * Get postedAt
     *
     * @return \DateTime
     */
    public function getPostedAt()
    {
        return $this->postedAt;
    }
}
