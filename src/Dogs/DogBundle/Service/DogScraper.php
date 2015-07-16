<?php
namespace Dogs\DogBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dogs\DogBundle\Entity\Dog;
use Dogs\DogBundle\Scraper\Scraper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Doctrine\ORM\EntityManager;

class DogScraper
{
    protected $em;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function scrap()
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
        

        $scraper = new Scraper();
        $scraper->registerPage('krakow.eadopcje', 'http://krakow.eadopcje.org');
        $scraper->registerPage('jamniki.eadopcje', 'http://jamniki.eadopcje.org/do_adopcji/polska/psiaki/wszystkie/0');
        $scraper->registerPage('psy', 'http://www.psy.pl/adopcje/page1.html');
        
        $jamniki = $scraper->scrap('jamniki.eadopcje');
        $psy = $scraper->scrap('psy');
        $em = $this->em;
        
        foreach($psy as $pies)
        {
            $dog = new Dog();
            $dog->setName($pies['name']);
            $dog->setLocation($pies['location']);
            $dog->setBreed($pies['breed']);
            $dog->setAge($pies['age']);
            $dog->setSterilization($pies['sterilization']);
            $dog->setSex($pies['sex']);
            $dog->setDescription($pies['description']);
            $dog->setUrl($pies['url']);
            $dog->setTitle($pies['title']);
            $em->persist($dog);
        }
        
        foreach($jamniki as $pies)
        {
            $dog = new Dog();
            $dog->setName($pies['name']);
            $dog->setLocation($pies['location']);
            $dog->setBreed($pies['breed']);
            $dog->setAge($pies['age']);
            $dog->setSterilization($pies['sterilization']);
            $dog->setSex($pies['sex']);
            $dog->setDescription($pies['description']);
            $dog->setUrl($pies['url']);
            $dog->setTitle($pies['title']);
            $em->persist($dog);
        }
       $em->flush();
       
    }
}



