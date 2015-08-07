<?php
// src/Dogs/DogBundle/Entity/Order.php

namespace Dogs\DogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass="Dogs\DogBundle\Entity\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 * @ORM\HasLifecycleCallbacks
 */
 
 class Order
 {
     /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
 
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
