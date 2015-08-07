<?php
namespace Dogs\DogBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserInterface;

class RegistrationFormHandler extends BaseHandler
{
    protected function onSuccess(UserInterface $user, $confirmation)
    {
        //modifying the user
        parent::onSuccess($user, $confirmation);
        
        //functionality that don't conserns user
    }
}