<?php
//src Dogs/DogBundle/Scraper/Scraper.php

namespace Dogs\DogBundle\Scraper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;

class Scraper 
{
    static $domains = array();
    
    private $adoptionListPage;
    private $host;
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
                $this->host = parse_url($pageUrl)['host'];
                return true;
            }
        }
        echo "Nie podałeś adresu url!";
    }
    
    public function getRegisteredPages()
    {
        return self::$domains;
    }
    
    private function getPageByLabel($pageLabel) 
    {
        return self::$domains[$pageLabel];
    }    
    
    
    public function scrap($pageLabel)
    {
        if(array_key_exists($pageLabel, self::$domains))
        {
            $this->adoptionListPage = self::$domains[$pageLabel];
            $this->load($pageLabel);
        } else
        {
            echo "Nie ma takiej etykiety";
        }
    }
    
    public function load($pageLabel)
    {
        $this->dom = new \DOMDocument();
        @$this->dom->loadHTMLFile($this->getPageByLabel($pageLabel));
        
        switch($pageLabel)
        {
            case 'jamniki.eadopcje':
                $this->jamnikiEadopcjeScrap();
                break;
            case 'krakow.eadopcje':
                $this->krakowEadopcjeScrap();
                break;
        }
    }
    
    private function jamnikiEadopcjeScrap()
    {
        $dogWrapper = $this->queryCss('div[align]');
        
        foreach($dogWrapper as $dog)
        {
            $ahref = $this->queryCss('a', $dog)->item(0);
            $link = $ahref ? $ahref->getAttribute('href') : false;
            
            echo $link;
            @$this->dom->loadHTMLFile($this->host . '/' . $link);
            $td = $this->queryCss('td[style]')->item(1)->nodeValue;
            
            //$name = $this->queryCss('b', $td)->item();
            echo $td . "</br>";
        }
    }
    
    private function krakowEadopcjeScrap()
    {
        //ciało
    }
    
    private function queryCss($selector, $contextNode = null)
    {
        $domScraper = new \DOMXPath($this->dom);
        return $domScraper->query(CssSelector::toXPath($selector), $contextNode);
    }

}