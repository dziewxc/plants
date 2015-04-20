<?php
// src/Cloudy/Bundle/CrudBundle/EventListener/TestListener.php
namespace Cloudy\Bundle\CrudBundle\EventListener;

use Cloudy\Bundle\CrudBundle\Event\SubmitEvent;
use Symfony\Component\EventDispatcher\Event;
use Cloudy\Bundle\CrudBundle\Entity\Enquiry;
use Cloudy\Bundle\CrudBundle\Controller\PageController;

class TestListener
{
	public function onFooAction(SubmitEvent $event)
	{
		echo "yes";
	}
}