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
        $em = $this->getDoctrine()->getEntityManager('dogs');
        $time = date('Y-m-d', time() - 60 * 60 * 24);
        $dogsFromYesterday = $em->getRepository('DogBundle:Dog')->getListOfDogsFromYesterday($time);
        if(!$dogsFromYesterday)
        {
           $scraper = $this->get('dog_scraper');
           $scraper->scrap($time);
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
}
