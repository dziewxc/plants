<?php
// src/Cloudy/Bundle/CrudBundle/Controller/PageController.php

namespace Cloudy\Bundle\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('CloudyCrudBundle:Page:index.html.twig');
    }
}