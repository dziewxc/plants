<?php
namespace Dogs\DogBundle\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $response = parent::registerAction();
        return $response;
    }
}