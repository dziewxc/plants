<?php
namespace Dogs\DogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dogs\DogBundle\Scraper\Scraper;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    public function indexAction()  ///zmienic na index
    {
        $time1 = microtime(true);
        $scraper = new Scraper();
        
        //usystematyzowac to, co wchodzi do bazy
        //przed dodaniem do bazy danych, zabezpieczyc dane
        $scraper->registerPage('krakow.eadopcje', 'http://krakow.eadopcje.org');
        $scraper->registerPage('jamniki.eadopcje', 'http://jamniki.eadopcje.org/do_adopcji');
        $scraper->registerPage('psy', 'http://www.psy.pl/adopcje/go:tab:1/');
        
        $jamniki = $scraper->scrap('jamniki.eadopcje');
        $psy = $scraper->scrap('psy');
        $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        echo $time;
        
        
        //wyniki można wrzucić do wspólnej tablicy tak, żeby nie edytować twiga za każdym razem, jak się dodaje nowy scrap
        return $this->render('DogBundle:Default:index.html.twig', array(
            'jamniki'=> $jamniki,
            'psy' => $psy
        ));
    }
    
    public function newAction()
    {
        //$time1 = microtime(true);
        
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(120);
        $response->setSharedMaxAge(120);
        $response->headers->addCacheControlDirective('no-cache', false);
        
        $time2 = date('H:i');

        //$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        //echo $time . "</br>";
        
        $result = $this->render('DogBundle:Default:new.html.twig', array(
            'date'=> $time2,
        ));
        
        $response->setContent($result);
        return $response;
        
    }
}
