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
        $dogWrapper = $this->queryCss('div[align]', $contextNode = null, $this->dom);
        
        foreach($dogWrapper as $dog)  //dla każdej ramki z psem na stronie z adopcjami...
        {   
            $ahref = $this->queryCss('a', $dog, $this->dom)->item(0);  //zaznacz link prowadzący do profilu psa
            $link = $ahref ? $ahref->getAttribute('href') : false;

            $file = file_get_contents('http://' . $this->host . '/' . $link);

            $file = mb_convert_encoding($file, 'utf-8', mb_detect_encoding($file));   //tysiące obejść, by móc ładować pliki z kodowaniem utf8
            $file = mb_convert_encoding($file, 'html-entities', 'utf-8'); 
            
            $newdom = new \DOMDocument();
            @$newdom->loadHTML($file);

            $frame = $this->queryCss('td[style]', $contextNode = null, $dom = $newdom)->item(1)->nodeValue;
            
            //jakas petla tutaj?
            $regex = '/Imię: (.*)Płeć/';
            preg_match($regex, $frame, $match);
            $this->results['name'] = $match[1];
            
            $regex = '/Płeć: (.*)Wiek/';
            preg_match($regex, $frame, $match);
            switch($match[1])
            {
                case 'piesek' :
                    $sex = 'pies';
                    break;
                case 'suczka' :
                    $sex = 'suka';
                    break;
                default : 
                    $sex = $match[1];
            }
            $this->results['sex'] = $sex;
            
            $regex = '/Wiek: (.*)Miasto/';
            preg_match($regex, $frame, $match);
            $this->results['age'] = $match[1];
            
            $regex = '/Województwo: (.*)Status/';
            preg_match($regex, $frame, $match1);
            $regex = '/Miasto: (.*)Województwo/';
            preg_match($regex, $frame, $match);
            $this->results['location'] = $match[1] . ', ' . $match1[1];
            
            $this->results['breed'] = 'jamnik';
            
            $sterilizationFrame = $this->queryCss('td[colspan]', $contextNode = null, $dom = $newdom)->item(3)->nodeValue;
            $regex = '/Sterylizacja\\/Kastracja: (.*)/';
            preg_match($regex, $sterilizationFrame, $match);
            $this->results['sterilization'] = $match[1];
            
            $descriptionFrame = $this->queryCss('td[colspan]', $contextNode = null, $dom = $newdom)->item(2)->nodeValue; //trzeba usunac EOL
            $regex = '/Charakter: (.*)/s';
            preg_match($regex, strip_tags($descriptionFrame), $match);
            $description = preg_replace('/([\r\n\t])/',' ',$match[1]);
            $this->results['description'] = $description;
            
            echo "<pre>";
            print_r($this->results);
            echo "</pre>";
        }
    }
    
    private function krakowEadopcjeScrap()
    {
        //ciało
    }
    
    private function queryCss($selector, $contextNode = null, $dom)  //jak tutaj wpisać $dom = $this->dom ?
    {
        $domScraper = new \DOMXPath($dom);
        return $domScraper->query(CssSelector::toXPath($selector), $contextNode);
    }

}