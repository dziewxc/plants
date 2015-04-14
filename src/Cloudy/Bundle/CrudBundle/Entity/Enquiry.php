<?php
// src/Cloudy/Bundle/CrudBundle/Entity/Enquiry.php

namespace Cloudy\Bundle\CrudBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Enquiry
{
    protected $name;
    protected $email;
    protected $subject;
    protected $body;
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());

        $metadata->addPropertyConstraint('email', new Assert\Email());

        $metadata->addPropertyConstraint('subject', new Assert\NotBlank());
        $metadata->addPropertyConstraint('body', new Assert\Length(array(
			'min' => 5,
			'max' => 50,
			'minMessage' => 'You have to write more than {{ limit }} characters',
			'maxMessage' => 'You have to write less than {{ limit }} characters'
		)));
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}