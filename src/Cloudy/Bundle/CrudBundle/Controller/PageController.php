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
		
	}
}