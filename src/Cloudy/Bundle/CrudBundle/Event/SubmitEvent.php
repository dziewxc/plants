<?php
// src/Cloudy/Bundle/CrudBundle/Event/SubmitEvent.php
namespace Cloudy\Bundle\CrudBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class SubmitEvent extends Event
{
	public $name;
	
	public function __construct ($data)
    {
        $this->name = $data;
    }
	
	public function getData()
    {
        return $this->name;
    }
}