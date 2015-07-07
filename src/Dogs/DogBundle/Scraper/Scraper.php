<?php
//src Dogs/DogBundle/Scraper/Scraper.php

namespace Dogs\DogBundle\Scraper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Goutte\Client;

class Scraper 
{
    static $domains = array(); 
    private $adoptionListPage;  //adres strony
    private $host;  //adres strony głównej
    private $dom;   
    private $results = array();
    
    public function registerPage($pageLabel, $pageUrl)
    {
        if(!(filter_var($pageUrl, FILTER_VALIDATE_URL) === FALSE)) {
            if(array_key_exists($pageLabel, self::$domains))
            {
                echo "Ta etykieta jest już zarezerwowana dla strony " . self::$domains[$pageLabel]; //tu błąd powinien być wrzucony do $errors[]
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
        return $this->results;
    }
    
    public function load($pageLabel)
    {
        $this->host = parse_url(self::$domains[$pageLabel])['host'];
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
            case 'psy':
                $this->psyScrap();
                break;
        }
    }
    
    private function jamnikiEadopcjeScrap()
    {
        $this->results = array();
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
            
            $doginfo['link'] = 'http://' . $this->host . '/' . $link;
            
            //jakas petla tutaj?
            $regex = '/Imię: (.*)Płeć/';
            preg_match($regex, $frame, $match);
            $doginfo['name'] = $match[1];
            
            // sex
            
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
                    $sex = 'N/A';
            }
            $doginfo['sex'] = $sex;
            
            //age
            
            $regex = '/Wiek: (.*)Miasto/';
            preg_match($regex, $frame, $match);
            $age = $match[1];
            
            if(isset($age) && $age !== '') 
            {
                $years = array("rok", "lata", "lat");
                $months = array("miesiąc", "miesiące", "miesięcy");
                foreach($years as $year)
                {
                    $regex2 = "/(. *) {$year}/";
                    preg_match($regex2, $age, $match2);
                    
                    if(isset($match2[1]))
                    {
                        $ageInMonths = $match2[1] * 12;
                        $doginfo['age'] = $ageInMonths;
                    }
                }
                if(!isset($doginfo['age']))
                {
                    foreach($months as $month)
                    {
                        $regex2 = "/(. *) {$month}/";
                        preg_match($regex2, $age, $match2);
                        
                        if(isset($match2[1]))
                        {
                            $doginfo['age'] = $match2[1];
                        }
                    }
                }
                if(!isset($doginfo['age']))
                {
                    $doginfo['age'] = "N/A";
                }
            } else 
            {
                $doginfo['age'] = "N/A";
            }
            
            //location

            $regex = '/Województwo: (.*)Status/';
            preg_match($regex, $frame, $match);
            $doginfo['location'] = $match[1];
            
            $doginfo['breed'] = 'jamnik';
            
            //sterilization
            
            $sterilizationFrame = $this->queryCss('td[colspan]', $contextNode = null, $dom = $newdom)->item(3)->nodeValue;
            $regex = '/Sterylizacja\\/Kastracja: (.*)/';
            preg_match($regex, $sterilizationFrame, $match);
            $sterilizationValue = array("Tak", "Nie", "Nie, ale jest warunkiem adopcji.");
            if(in_array($match[1], $sterilizationValue))
            {
                $doginfo['sterilization'] = $match[1];
            } else
            {
                $doginfo['sterilization'] = "N/A";
            }
            
            //description
            
            $descriptionFrame = $this->queryCss('td[colspan]', $contextNode = null, $dom = $newdom)->item(2)->nodeValue; //trzeba usunac EOL
            $regex = '/Charakter: (.*)/s';
            preg_match($regex, strip_tags($descriptionFrame), $match);
            $description = preg_replace('/([\r\n\t])/',' ',$match[1]);
            $doginfo['description'] = $description ? $description : "Brak opisu";
            
            $this->results[] = $doginfo;
        }
    }
    
    private function krakowEadopcjeScrap()
    {
        //body
    }
    
    private function psyScrap() //ma tytul zamiast imienia 
    //sterylizacja nie dziala, nie mozna brac rzeczy z list4a p i udawać, że wszystko w porządku
    //dlaczego tu nie ma opisu!?
    {
        $this->results = array();
        $tableContent = $this->queryCss('tbody', null, $this->dom)->item(0);
        $dogWrapper = $this->queryCss('tr', $tableContent, $this->dom);
        
        foreach($dogWrapper as $dog)
        {
            $created = $this->queryCss(".date", $dog, $this->dom)->item(0);
            $doginfo['created'] = $created ? $created->nodeValue : '0';
            
            $location = $this->queryCss("td", $dog, $this->dom)->item(2);
            
            $string = htmlentities($location->nodeValue, null, 'utf-8');
            $string = str_replace("&nbsp;", "", $string);

            if($location && (strlen($string) > 0))
            {
                $doginfo['location'] = $location->nodeValue;
            } else 
            {
                $doginfo['location'] = 'N/A';
            }
            
            $title = $this->queryCss('c-red', $dog, $this->dom)->item(0);
            $doginfo['title'] = $title ? $title->nodeValue : "N/A";

            $link = $this->queryCss('.colD.taC.nB a', $dog, $this->dom)->item(0);
            $link = $link->getAttribute('href');
            
            $file = file_get_contents('http://' . $this->host . $link);

            $file = mb_convert_encoding($file, 'utf-8', mb_detect_encoding($file));   //tysiące obejść, by móc ładować pliki z kodowaniem utf8
            $file = mb_convert_encoding($file, 'html-entities', 'utf-8'); 
            
            $newdom = new \DOMDocument();
            @$newdom->loadHTML($file);
            

            $doginfo['name'] = 'N/A';
            
            $doginfo['link'] = 'http://' . $this->host . '/' . $link;
            
            //sex
            
            $sex = $this->queryCss('.list4a-borderfix p', null, $newdom)->item(0)->nodeValue;
            $sexValue = array("pies", "suka");
            if(in_array($sex, $sexValue))
            {
                $doginfo['sex'] = $sex;
            } else
            {
                $doginfo['sex'] = "N/A";
            }

            $doginfo['age'] = 'N/A';
            
            $description = $this->queryCss('.kernel distinction m_0_0_20_0', null, $newdom)->item(0);
            $doginfo['description'] = $description ? $description->nodeValue : 'Brak opisu';

            $breed = $this->queryCss('.list4a p', null, $newdom)->item(1);
            $doginfo['breed'] = $breed ? $breed->nodeValue : 'N/A';
            
            $sterilization = $this->queryCss('.list4 p', null, $newdom)->item(1)->nodeValue;
            $sterilizationValue = array("Tak", "Nie");
            if(in_array($sterilization, $sterilizationValue))
            {
                $doginfo['sterilization'] = $sterilization;
            } else
            {
                $doginfo['sterilization'] = "N/A";
            }
            //$doginfo['name'] = $match[1];
            
            
            $title = $this->queryCss('.medium', null, $newdom)->item(0);
            $doginfo['title'] = $title ? $title->nodeValue : 'N/A';

            $size = $this->queryCss('.list4 p', null, $newdom)->item(0);
            $doginfo['size'] = $size ? $size->nodeValue : 'N/A';

            $this->results[] = $doginfo;
        }
    }
    
    private function queryCss($selector, $contextNode = null, $dom)  //jak tutaj wpisać $dom = $this->dom ?
    {
        $domScraper = new \DOMXPath($dom);
        return $domScraper->query(CssSelector::toXPath($selector), $contextNode);
    }

}