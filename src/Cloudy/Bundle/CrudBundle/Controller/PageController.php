<?php
// src/Cloudy/Bundle/CrudBundle/Controller/PageController.php

namespace Cloudy\Bundle\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cloudy\Bundle\CrudBundle\Entity\Enquiry;
use Cloudy\Bundle\CrudBundle\Entity\EntityRepository\BlogRepository;
use Cloudy\Bundle\CrudBundle\Entity\UserData;
use Cloudy\Bundle\CrudBundle\Form\EnquiryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Cloudy\Bundle\CrudBundle\CloudyEvents;
use Cloudy\Bundle\CrudBundle\Event\SubmitEvent;
use Cloudy\Bundle\CrudBundle\EventListener\TestListener;
use PHPImageWorkshop\ImageWorkshop;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

class PageController extends Controller
{	
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $blogs = $em->getRepository('CloudyCrudBundle:Blog')
                    ->getLatestBlogs();

        return $this->render('CloudyCrudBundle:Page:index.html.twig', array(
            'blogs' => $blogs
        ));
    }

	public function aboutAction()
    {
        $defaults = array(
            'duration' => '12:17:26',
        );

        $formBuilder = $this->get('form.factory')->createBuilder('form', $defaults);
        $formBuilder->add('text', 'textarea', array('required' => false));
        $formBuilder->add('duration', 'time', array('with_seconds' => true, 'input' => 'string'));
        $formBuilder->add('gender', 'choice', array(
            'choices' => array(
                'm' => 'men', 
                'f' => 'fem'
                ),
        ));
        $formBuilder->add('blog', 'entity', array(  //szuka metody __toString i z niej pobiera co ma wyświetlać
            'class' => 'Cloudy\Bundle\CrudBundle\Entity\Blog',
            'query_builder' => function($BlogRepository)
            {
                return $BlogRepository->createQueryBuilder('b')
                          ->select('b')  
                          ->leftJoin('b.comments', 'c')
                          ->addOrderBy('b.created', 'DESC')
                          ->setMaxResults(3);
            }
        ));
        $form = $formBuilder->getForm();
        
        $request = $this->getRequest();
		if ($request->getMethod() == 'POST')
			$form->bind($request);

        $form->handleRequest($request);
		return $this->render('CloudyCrudBundle:Page:about.html.twig', array(
			'form' => $form->createView()
		));
    }
	
	public function contactAction()
	{
		$enquiry = new Enquiry();
		$form = $this->createForm(new EnquiryType(), $enquiry);

		$request = $this->getRequest();
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			
			if ($form->isValid()) {
				 $message = \Swift_Message::newInstance()
					->setSubject('Contact enquiry from cloudyblog')
					->setFrom('aa.pietruczuk@gmail.com')
					->setTo($this->container->getParameter('cloudy_crud.emails.contact_email'))
					->setBody($this->renderView('CloudyCrudBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
				$this->get('mailer')->send($message);
				
				$data = $enquiry->getName();
				
				
				$this->get('session')->getFlashBag()->add('blogger-notice', $data . ', Your contact enquiry was successfully sent. Thank you!');		

				return $this->redirect($this->generateUrl('CloudyCrudBundle_contact'));
			}
		}
		$data = "lala";
		$dispatcher = new EventDispatcher();
		$listener = new TestListener();
		$dispatcher->addListener('foo.action', array($listener, 'onFooAction'));
		$event = new SubmitEvent($data);
		$dispatcher->dispatch('foo.action', $event);
		
		return $this->render('CloudyCrudBundle:Page:contact.html.twig', array(
			'form' => $form->createView()
		));
	}
	
	public function plantsCalculatorAction(Request $request)
	{
		//$package = new Package(new EmptyVersionStrategy());
        //echo $package->getUrl('/image.png');
        $html = file_get_contents('http://cloudymind.pl/eksperyment-z-lustrem/');
        $crawler = new Crawler($html);

        foreach ($crawler as $domElement) {
            print $domElement->nodeName;
        }
        
        $crawler = $crawler->filterXPath('descendant-or-self::body/p');
        
        return $this->render('CloudyCrudBundle:Page:plantscalculator.html.twig');
	}
    
    public function domAction()
    {
        $domain = 'http://bgbstudio.com';
        $playersCategory = 'proizvodi/blu-ray-plejeri';
        $targetPage = $domain . '/' . $playersCategory;
         
        $dom = new \DOMDocument();
         
        @$dom->loadHTMLFile($targetPage);  //jest blad z naglowkiem w html'u, wygluszamy go na razie
        $productsInfo = [];
        
        $container = $dom->getElementById('lista-proizvoda-na-akciji');
        $divs = $container->getElementsByTagName('div');
        foreach($divs as $div)
        {
            if($div->getAttribute('class') === 'product-block-content')
            {
                $info = array();
                $productWrapper = $div;
                $discount = $productWrapper->getElementsByTagName('div')->item(0);
                
                $info['discount'] = $discount && $discount->getAttribute('class') == 'badge-sale'
                    ? $discount->nodeValue
                    : 0
                ;
                $link = $productWrapper->getElementsByTagName('a')->item(0);
                $info['url'] = $domain . $link->getAttribute('href');
                
                $info['title'] = $productWrapper
                    ->getElementsByTagName('h2')
                    ->item(0)
                    ->getElementsByTagName('a')
                    ->item(0)    //błąd
                    ->nodeValue
                ;
                
                $currentPrice = $productWrapper
                    ->getElementsByTagName('p')
                    ->item(1);
                    
                $info['current_price'] = $currentPrice && $currentPrice->getAttribute('class') === 'product-block-price' 
                    ? $currentPrice->nodeValue
                    : 'N/A'
                ;
                
                $oldPrice = $productWrapper
                    ->getElementsByTagName('p')
                    ->item(2)
                ;
                
                $info['old_price'] = $oldPrice && $oldPrice->getAttribute('class') === 'product-block-price-old'
                    ? $oldPrice->nodeValue
                    : 'N/A'
                ;
                
                $productsInfo[] = $info;
            }
        }
        
        //XPath queries
        
        $dom2 = new \DOMDocument();
        @$dom2->loadHTMLFile($targetPage);
        
        $xpath = new \DOMXPath($dom2);
        
        $linkXPath = 'descendant-or-self::a';  //query
        $nodeList = $xpath->query($linkXPath);
        
        $productWrapperXPath = "descendant-or-self::*[@class and contains(concat(' ', normalize-space(@class), ' '), ' product-block-content ')]";
        $productWrappersList = $xpath->query($productWrapperXPath);
        
        foreach($productWrappersList as $wrapper)
        {
            $info = array();
            
            $discountXPath = "descendant-or-self::*[@class and contains(concat(' ', normalize-space(@class), ' '), ' badge-sale ')]";
            $discount = $xpath->query($discountXPath, $wrapper)->item(0);
            $info['discount'] = $discount ? $discount->nodeValue : 0;
            
            $linkXPath = 'descendant-or-self::a';
            $link = $xpath->query($linkXPath, $wrapper)->item(0);
            $info['url'] = $domain . $link->getAttribute('href');
            
            $titleXPath = 'descendant-or-self::h2/descendant-or-self::*/a';
            $info['title'] = $xpath->query($titleXPath, $wrapper)->item(0)->nodeValue;

            $currentPriceXPath = "descendant-or-self::p[@class and contains(concat(' ', normalize-space(@class), ' '), ' product-block-price ')]";
            $currentPrice = $xpath->query($currentPriceXPath, $wrapper)->item(0);
            $info['current_price'] = $currentPrice ? $currentPrice->nodeValue : 'N/A';
         
            $oldPriceXPath = "descendant-or-self::p[@class and contains(concat(' ', normalize-space(@class), ' '), ' product-block-price-old ')]";
            $oldPrice = $xpath->query($oldPriceXPath, $wrapper)->item(0);
            $info['old_price'] = $oldPrice ? $oldPrice->nodeValue : 'N/A';
            
            $productsInfo2[] = $info;
        }

        return $this->render('CloudyCrudBundle:Page:dom.html.twig', array(
            'products' => $productsInfo,
            'products2' => $productsInfo2));
    }
    
    public function cssSelectorAction()
    {
        echo CssSelector::toXPath('p.product-block-price');
        return $this->render('CloudyCrudBundle:Page:cssselector.html.twig', array());
    }
}




