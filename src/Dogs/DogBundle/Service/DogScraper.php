<?php
namespace Dogs\DogBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dogs\DogBundle\Entity\Dog;
use Dogs\DogBundle\Scraper\Scraper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class DogScraper
{
    public function scrap($time)
    {
        //kernel
        /*$request = Request::createFromGlobals();
        $dispatcher = new EventDispatcher();
        $resolver = new ControllerResolver();
        $kernel = new HttpKernel($dispatcher, $resolver);
        
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
        */
        //kernel
        
        $dog = new Dog();
        $scraper = new Scraper();
        $scraper->registerPage('krakow.eadopcje', 'http://krakow.eadopcje.org');
        $scraper->registerPage('jamniki.eadopcje', 'http://jamniki.eadopcje.org/do_adopcji/polska/psiaki/wszystkie/0');
        $scraper->registerPage('psy', 'http://www.psy.pl/adopcje/page1.html');
        
        $jamniki = $scraper->scrap('jamniki.eadopcje', $time);
        $psy = $scraper->scrap('psy', $time);
        echo "<pre>";
        print_r($jamniki);
        print_r($psy);
        echo "</pre>";
    }
}



