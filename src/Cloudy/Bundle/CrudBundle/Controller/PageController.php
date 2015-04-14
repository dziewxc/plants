<?php
// src/Cloudy/Bundle/CrudBundle/Controller/PageController.php

namespace Cloudy\Bundle\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cloudy\Bundle\CrudBundle\Entity\Enquiry;
use Cloudy\Bundle\CrudBundle\Entity\UserData;
use Cloudy\Bundle\CrudBundle\Form\EnquiryType;
use Symfony\Component\HttpFoundation\Request;

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
		$userdata = new UserData;
		$userdata->setFlatSurface(200);
		$userdata->setIfSmokers(true);
		$userdata->setOccupantsCount(10);
		$userdata->setElectronicsLevel(5);
		
		$form = $this->createFormBuilder($userdata)
			->add('flatSurface', 'integer')
			->add('ifSmokers', 'checkbox')
			->add('OccupantsCount', 'integer')
			->add('ElectronicsLevel', 'integer')
			->add('save', 'submit', array('label' => 'Add'))
			->getForm();
		
		return $this->render('CloudyCrudBundle:Page:plantscalculator.html.twig', array(
			'form' =>$form->createView()
		));
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}