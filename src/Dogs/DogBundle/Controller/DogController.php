<?php
namespace Dogs\DogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DogController extends Controller
{
    public function listAction(Request $request)
    {
        $em    = $this->getDoctrine()->getEntityManager('dogs');
        $query = $em->createQuery('SELECT d FROM DogBundle:Dog d');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            3
        );

        return $this->render('DogBundle:Dog:dog.html.twig', array('pagination' => $pagination));
    }
}