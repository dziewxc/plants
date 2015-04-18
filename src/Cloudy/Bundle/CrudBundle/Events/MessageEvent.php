<?php
// src/Cloudy/Bundle/CrudBundle/Events/MessageEvent.php
namespace Cloudy\Bundle\CrudBundle\Events;

use Symfony\Component\EventDispatcher\Event;

class MessageEvent extends Event
{
    protected $order;
	
	public function __construct()
	{
		echo "dodane";
	}

    public function getOrder()
    {
        return $this->order;
    }
}