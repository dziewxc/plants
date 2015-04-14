<?php
// src/Cloudy/Bundle/CrudBundle/Entity/UserData.php

namespace Cloudy\Bundle\CrudBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class UserData
{
	protected $flatSurface;
	protected $electronicsLevel;
	protected $occupantsCount;
	protected $ifSmokers;
	
	public function setFlatSurface($flatSurface)
	{
		$this->flatSurface = $flatSurface;
	}
	
	public function getflatSurface()
	{
		return $this->flatSurface;
	}
	
	public function setElectronicsLevel($electronicsLevel)
	{
		$this->electronicsLevel = $electronicsLevel;
	}
	
	public function getElectronicsLevel() 
	{
		return $this->electronicsLevel;
	}
	
	public function setOccupantsCount($occupantsCount)
	{
		$this->occupantsCount = $occupantsCount;
	}
	
	public function getOccupantsCount()
	{
		return $this->occupantsCount;
	}
	
	public function setIfSmokers($ifSmokers)
	{
		$this->ifSmokers = $ifSmokers;
	}
	
	public function getIfSmokers()
	{
		return $this->ifSmokers;
	}
	
}



