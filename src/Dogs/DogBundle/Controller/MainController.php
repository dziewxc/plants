<?php
namespace Dogs\DogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dogs\DogBundle\Scraper\Scraper;

class MainController extends Controller
{
    public function indexAction()
    {
        $scraper = new Scraper();
        
        $scraper->registerPage('krakow.eadopcje', 'http://krakow.eadopcje.org');
        $scraper->registerPage('jamniki.eadopcje', 'http://jamniki.eadopcje.org/do_adopcji');
        
        $scraper2 = new Scraper();
        echo "<pre>";
        print_r($scraper->getRegisteredPages());
        print_r($scraper2->getRegisteredPages());
        echo "</pre>";

        $scraper->scrap('jamniki.eadopcje');
        
        
        return $this->render('DogBundle:Default:index.html.twig', array());
    }
}
