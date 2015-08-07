<?php
namespace Dogs\DogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    public function indexAction() 
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
       
        return $this->render('DogBundle:Default:index.html.twig', array(
            'dogs' => $dogs,
        ));
        //$response->setContent($result);
        //return $response;
    }
    
    public function newAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(120);
        $response->setSharedMaxAge(120);
        $response->headers->addCacheControlDirective('no-cache', false);
        
        $time2 = date('H:i');
        
        $result = $this->render('DogBundle:Default:new.html.twig', array(
            'date'=> $time2,
        ));
        $response->setContent($result);
        return $response;

    }
    
    public function mainAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername('John');
        $user->setEmail('john.doe@example.com');

        //$userManager->updateUser($user);
        return $this->render('DogBundle:Default:header.html.twig', array());
    }
    
    public function adminAction()
    {
        return new Response('Admin page!');
    }
}
