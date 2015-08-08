<?php
namespace Dogs\DogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    public function addAction() 
    {
        /*
        $response = new Response();
        $response->setPublic();
        
        $s = strtotime('midnight +1 day') - time();
        
        $date = new \DateTime();
        $date->modify('+' . $s . ' seconds');
        
        $response->setExpires($date);
        
        $response->setMaxAge($s);
        $response->setSharedMaxAge($s);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-cache', false);
        */
        ini_set('max_execution_time', 600);
        $em = $this->getDoctrine()->getEntityManager('dogs');
        $time = date('Y-m-d');
        
        $dogsFromToday = $em->getRepository('DogBundle:Dog')->getListOfDogsFromToday($time);
        if(!$dogsFromToday)
        {
           $scraper = $this->get('dog_scraper');
           $scraper->scrap($em);
        }
        
        $dogs = $em->getRepository('DogBundle:Dog')->getListOfDogs();
        
        if (!$dogs) {
            throw $this->createNotFoundException('Nie ma psów');
        }
       
        return $this->render('DogBundle:Default:add.html.twig', array(
            'dogs' => $dogs,
        ));
        //$response->setContent($result);
        //return $response;
    }
    
    public function mainAction(Request $request)
    {
        $em    = $this->getDoctrine()->getEntityManager('dogs');
        $query = $em->createQuery('SELECT d FROM DogBundle:Dog d');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            5
        );

        return $this->render('DogBundle:Dog:dog.html.twig', array('pagination' => $pagination));
    }
    
    public function testAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername('John');
        $user->setEmail('john.doe@example.com');

        //$userManager->updateUser($user);
        
        return $this->render('DogBundle:Default:header.html.twig', array());
    }
}
