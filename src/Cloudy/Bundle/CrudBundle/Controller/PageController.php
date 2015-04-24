<?php
// src/Cloudy/Bundle/CrudBundle/Controller/PageController.php

namespace Cloudy\Bundle\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cloudy\Bundle\CrudBundle\Entity\Enquiry;
use Cloudy\Bundle\CrudBundle\Entity\UserData;
use Cloudy\Bundle\CrudBundle\Form\EnquiryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Cloudy\Bundle\CrudBundle\CloudyEvents;
use Cloudy\Bundle\CrudBundle\Event\SubmitEvent;
use Cloudy\Bundle\CrudBundle\EventListener\TestListener;

class PageController extends Controller
{
	public $name;
	
	public function setName($name) 
	{
		$this->name = $name;
	}
	
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $blogs = $em->createQueryBuilder()
                    ->select('b')
                    ->from('CloudyCrudBundle:Blog',  'b')
                    ->addOrderBy('b.created', 'DESC')
                    ->getQuery()
                    ->getResult();

        return $this->render('CloudyCrudBundle:Page:index.html.twig', array(
            'blogs' => $blogs
        ));
    }

	public function aboutAction()
    {
		return $this->render('CloudyCrudBundle:Page:about.html.twig');
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