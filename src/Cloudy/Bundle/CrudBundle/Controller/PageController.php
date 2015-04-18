<?php
// src/Cloudy/Bundle/CrudBundle/Controller/PageController.php

namespace Cloudy\Bundle\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cloudy\Bundle\CrudBundle\Entity\Enquiry;
use Cloudy\Bundle\CrudBundle\Entity\UserData;
use Cloudy\Bundle\CrudBundle\Form\EnquiryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Cloudy\Bundle\CrudBundle\Events;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('CloudyCrudBundle:Page:index.html.twig');
    }

	public function aboutAction()
    {
        return $this->render('CloudyCrudBundle:Page:about.html.twig');
    }
	
	public function contactAction()
	{
		$enquiry = new Enquiry();
		$form = $this->createForm(new EnquiryType(), $enquiry);
		$dispatcher = new EventDispatcher();
		
		$newevent = new \Cloudy\Bundle\CrudBundle\Events\MessageEvent;
		$dispatcher->addListener('blog.contact', array($newevent, 'onBlogContact'));

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
				
				$event = new \Cloudy\Bundle\CrudBundle\Events\MessageEvent($message);
				$dispatcher->dispatch('post.submit');
				
				
				$this->get('session')->getFlashBag()->add('blogger-notice','Your contact enquiry was successfully sent. Thank you!');

				// Redirect - This is important to prevent users re-posting
				// the form if they refresh the page
				return $this->redirect($this->generateUrl('CloudyCrudBundle_contact'));
			}
		}
		
		

		return $this->render('CloudyCrudBundle:Page:contact.html.twig', array(
			'form' => $form->createView()
		));
	}
	
	public function plantsCalculatorAction(Request $request)
	{
		$userdata = new UserData();
		
		$form = $this->createFormBuilder($userdata)
			->add('flatSurface', 'integer')
			->add('electronicsLevel', 'integer')
			->add('next', 'submit', array('label' => 'next', 'validation_groups' => 'false'))
			->add('occupantsCount', 'integer')
			->add('ifSmokers', 'choice')
			->add('save', 'submit', array('label' => 'save'))
			->getForm();
			
		$form->handleRequest($request);
		
		if($form->isValid())
		{
			$nextAction = $form->get('save')->isClicked()
				? 'task'
				: 'nottask';
			
			return $this->redirectToRoute('CloudyCrudBundle_plantscalculator');
		}
		
		
		
		/*return $this->render('CloudyCrudBundle:Page:plantscalculator.html.twig', array(
			'form' =>$form->createView() //'variable_name' => variable_value
		));*/
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}