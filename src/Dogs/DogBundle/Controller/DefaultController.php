<?php

namespace Dogs\DogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DogBundle:Default:index.html.twig', array('name' => $name));
    }
}
