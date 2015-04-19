<?php
// src/Cloudy/Bundle/CrudBundle/EventListener/TestListener.php
namespace Cloudy\Bundle\CrudBundle\EventListener;

use Cloudy\Bundle\CrudBundle\Event\SubmitEvent;

class TestListener
{
	public function onSubmitEvent(SubmitEvent $event)
	{
		echo "test";
	}
}