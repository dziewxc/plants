<?php
//src Dogs/DogBundle/Scraper/Scraper.php

namespace Dogs\DogBundle\Scraper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Goutte\Client;

class Scraper //extends \DOMXPath
{
    static $domains = array();
    
    private $adoptionListPage;
    private $page;
    private $dom;
    private $results = array();
    
    public function registerPage($pageLabel, $pageUrl)
    {
        if(!(filter_var($pageUrl, FILTER_VALIDATE_URL) === FALSE)) {
            if(array_key_exists($pageLabel, self::$domains))
            {
                echo "Ta etykieta jest już zarezerwowana dla strony " . self::$domains[$pageLabel];
                return true;
                
            } elseif(in_array($pageUrl, self::$domains))
            {
                echo "Ta strona jest już zarezerwowana, możesz jej użyć z etykietą: " . array_search($pageUrl, self::$domains);
                return true;
            } else
            {
                self::$domains[$pageLabel] = $pageUrl;
                return true;
            }
        }
        echo "Nie podałeś adresu url!";
    }
    
    public function scrap($pageLabel)
    {
        $this->load($pageLabel);
    }
    
    public function load($pageLabel)
    {
        $targetPage = $this->getPageByLabel($pageLabel);
        $this->dom = new \DOMDocument();
        @$this->dom->loadHTMLFile($targetPage);
    }
    
    public function getRegisteredPages()
    {
        return self::$domains;
    }
    
    private function getPageByLabel($pageLabel) 
    {
        return self::$domains[$pageLabel];
    }
    
    private function jamnikiPage()
    {
        
    }
    
    private function queryCss($selector, $contextNode = null)
    {
        return $this->query(CssSelector::toXPath($selector), $contextNode);
    }

}