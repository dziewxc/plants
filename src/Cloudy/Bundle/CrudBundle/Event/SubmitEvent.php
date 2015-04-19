<?php
// src/Cloudy/Bundle/CrudBundle/Event/SubmitEvent.php
namespace Cloudy\Bundle\CrudBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class SubmitEvent extends Event
{
	protected $data;
	
	public function __construct ()
    {
        echo "udalo sie";
    }
	
	public function getData()
    {
        return $this->data;
    }
}