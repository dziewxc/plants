<?php
// src/Cloudy/Bundle/CrudBundle/Scraper/Scraper.php

namespace Cloudy\Bundle\CrudBundle\Scraper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Goutte\Client;

class Scraper extends \DOMXPath
{
    private $domain;
    private $adoptionListPage;
    
    
    public function load()
    {
        $this->dom = new \DOMDocument();
        @$this->dom->loadHTMLFile($this->adoptionListPage);
    }
    
    public function setDomain($domain) 
    {
        $this->domain = $domain;
    }
    
    public function getDomain() 
    {
        return $this->domain;
    }
    
    public function setAdoptionListPage($page) 
    {
        $this->adoptionListPage = $this->domain . '/' . $page;
    }
    
    public function getAdoptionListPage() 
    {
        return $this->adoptionListPage;
    }
    
}