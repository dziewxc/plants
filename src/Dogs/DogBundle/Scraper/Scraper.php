<?php
//src Dogs/DogBundle/Scraper/Scraper.php

namespace Dogs\DogBundle\Scraper;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
use Goutte\Client;

class Scraper 
{
    private static $domains = array(); 
    private $adoptionListPage;  //adres strony
    private $host;  //adres strony głównej
    private $dom;
    private $results = array();
    private $jamnikiBreakNumber = 0;
    private $psyBreakNumber = 0;
    
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
        $this->results = array();
        
        switch($pageLabel)
        {
            case 'jamniki.eadopcje':
                $this->jamnikiEadopcjeScrap();
                if($this->jamnikiBreakNumber === 0)
                {
                    $currentSite = self::$domains[$pageLabel];
                    $regex = "/http:\\/\\/jamniki.eadopcje.org\\/do_adopcji\\/polska\\/psiaki\\/wszystkie\\/(.*?)/";
                    preg_match($regex, $currentSite, $match);
                    $newSufix = $match[1] + 10;
                    $newSite = "http://jamniki.eadopcje.org/do_adopcji/polska/psiaki/wszystkie/" . $newSufix;
                    self::$domains[$pageLabel] = $newSite;
                    $this->dom = new \DOMDocument();
                    @$this->dom->loadHTMLFile($this->getPageByLabel($pageLabel));
                    $this->jamnikiEadopcjeScrap();
                }
                break;
            case 'krakow.eadopcje':
                $this->krakowEadopcjeScrap();
                break;
            case 'psy':
                $this->psyScrap();
                if($this->psyBreakNumber === 0) //psyTodayCount jest bez sensu
                {
                    $currentSite = self::$domains[$pageLabel];
                    $regex = "/http:\\/\\/www.psy.pl\\/adopcje\\/page(.*?).html/";
                    preg_match($regex, $currentSite, $match);
                    $newNumber = $match[1] + 1;
                    $newSite = "http://www.psy.pl/adopcje/page" . $newNumber . ".html";
                    self::$domains[$pageLabel] = $newSite;
                    $this->dom = new \DOMDocument();
                    @$this->dom->loadHTMLFile($this->getPageByLabel($pageLabel));
                    $this->psyScrap();
                }

                break;
        }
    }
    
    private function jamnikiEadopcjeScrap()
    {
        
        $dogWrapper = $this->queryCss('div[align]', $contextNode = null, $this->dom);
        
        foreach($dogWrapper as $dog)
        {
            //pobierz adres kobnkretnego psa i załaduj stronę z nim
            $ahref = $this->queryCss('a', $dog, $this->dom)->item(0);
            $link = $ahref ? $ahref->getAttribute('href') : false;
            
            $file = file_get_contents('http://' . $this->host . '/' . $link);

            //ustaw kodowanie
            $file = mb_convert_encoding($file, 'utf-8', mb_detect_encoding($file));
            $file = mb_convert_encoding($file, 'html-entities', 'utf-8'); 
            
            $newdom = new \DOMDocument();
            @$newdom->loadHTML($file);

            $frame = $this->queryCss('td[style]', $contextNode = null, $dom = $newdom)->item(1)->nodeValue;
            
            //date
            $date = $this->queryCss('td[colspan]', $contextNode = null, $dom = $newdom)->item(0)->nodeValue;
            $year = date('Y');
            $regex = "/, (.*?){$year}/";
            preg_match($regex, $date, $match);
            $date = $match[1] . $year;
            $today = date('d.m.Y');
            $todayCount = 0;
            if($today === $date)
            {
                $todayCount++;
                continue;
            }
            $yesterday = date('d.m.Y', time() - 60 * 60 * 24);
            if(!($yesterday === $date))
            {
                $this->jamnikiBreakNumber = 1;
                break;
            }
            
            //url
            
            $doginfo['url'] = 'http://' . $this->host . '/' . $link;
            
            //name
            $regex = '/Imię: (.*)Płeć/';
            preg_match($regex, $frame, $match);
            $doginfo['name'] = $match[1] ? $match[1] : "N/A";
            
            //title
            
            $doginfo['title'] = $doginfo['name'];
            
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
            $doginfo['location'] = $match[1] ? $match[1] : "N/A";
            
            //breed
            
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
            
            //image
            $imgContext = $this->queryCss('#psiak img', $contextNode = null, $dom = $newdom)->item(0)->getAttribute('src');
            $imgUrl = 'http://www.' . $this->host . ltrim($imgContext, '.');
            
            //do tego przydałaby się inna metoda/klasa
            //http://forums.phpfreaks.com/topic/286562-script-to-scrape-images/

            if(!filter_var($imgUrl, FILTER_VALIDATE_URL) === FALSE)
            {
                $name = basename($imgUrl);
                file_put_contents("{$name}", file_get_contents($imgUrl));
            }
            
            //description
            
            $descriptionFrame = $this->queryCss('td[colspan]', $contextNode = null, $dom = $newdom)->item(2)->nodeValue;
            $regex = '/Charakter: (.*)/s';
            preg_match($regex, strip_tags($descriptionFrame), $match);
            if(isset($match[1]))
            {
                $description = preg_replace('/([\r\n\t])/',' ',$match[1]);
            }
            if(isset($description) && strlen($description) < 3000)
            {
                $doginfo['description'] = $description;
            } else {
                $doginfo['description'] = "Brak opisu";
            }
            
            $this->results[] = $doginfo;
        }
    }
    
    private function krakowEadopcjeScrap()
    {
        //body
    }
    
    private function psyScrap()
    {
        $tableContent = $this->queryCss('tbody', null, $this->dom)->item(0);
        $dogWrapper = $this->queryCss('tr', $tableContent, $this->dom);
        $this->psyTodayCount = 0;
        
        foreach($dogWrapper as $dog)
        {
            $doginfo = array();
            $title = $this->queryCss('c-red', $dog, $this->dom)->item(0);
            $doginfo['title'] = $title ? $title->nodeValue : "N/A";

            $link = $this->queryCss('.colD.taC.nB a', $dog, $this->dom)->item(0);
            $link = $link->getAttribute('href');
            
            $file = file_get_contents('http://' . $this->host . $link);

            $file = mb_convert_encoding($file, 'utf-8', mb_detect_encoding($file));   //tysiące obejść, by móc ładować pliki z kodowaniem utf8
            $file = mb_convert_encoding($file, 'html-entities', 'utf-8'); 
            
            $newdom = new \DOMDocument();
            @$newdom->loadHTML($file);
            
            //date
            $date = $this->queryCss('.date.c-lBrown', null, $newdom)->item(0)->nodeValue;
            $time = date('d-m-Y', time() - 60 * 60 * 24);
            $today = date('d-m-Y');
            if($today === $date)
            {
                continue;
            }
            if(!($time === $date))
            {
                $this->psyBreakNumber = 1;
                break;
            }
            $doginfo['name'] = 'N/A';
            
            $doginfo['url'] = 'http://' . $this->host . '/' . $link;
            
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
            
            //age

            $doginfo['age'] = 'N/A';
            
            //location
            
            $location = $this->queryCss('.list4', null, $newdom)->item(0)->nodeValue;
            $nextElementList = array('Telefon', 'Tel.', 'Adres');
            
            foreach($nextElementList as $nextElement)
            {
                $regex = "/Województwo: (.*) {$nextElement}/";
                preg_match($regex, preg_replace('/\s+/', ' ',$location), $match);
                if(isset($match[1]) && !isset($doginfo['location']))
                {
                    $doginfo['location'] = $match[1];
                }
            }
            if(!isset($match[1]))
            {
                $doginfo['location'] = "N/A";
            }
            //description
            
            $description = $this->queryCss('.kernel.distinction.m_0_0_20_0', null, $newdom)->item(0)->nodeValue;
            if(isset($description) && strlen($description) < 3000)
            {
                $description = preg_replace('/\s+/', ' ', $description);
                $doginfo['description'] = $description;
            } else {
                $doginfo['description'] = "N/A";
            }
            
            //breed

            $breed = $this->queryCss('.list4a p', null, $newdom)->item(1);
            $doginfo['breed'] = $breed ? $breed->nodeValue : 'N/A';
            
            //sterilization
            
            $sterilization = $this->queryCss('.list4', null, $newdom)->item(0)->nodeValue;
            $nextElementList = array('Data', 'Kontakt');
            $sterilizationValue = array("Tak", "Nie");
            foreach($nextElementList as $nextElement)
            {
                $regex = "/sterylizowana (.*) {$nextElement}/";
                preg_match($regex, preg_replace('/\s+/', ' ',$sterilization), $match);
                if(isset($match[1]) && !isset($doginfo['sterilization']) && in_array($match[1], $sterilizationValue))
                {
                    $doginfo['sterilization'] = $match[1];
                }
            }
            if(!isset($match[1]))
            {
                $doginfo['sterilization'] = "N/A";
            }
            
            //title
            
            $title = $this->queryCss('.medium', null, $newdom)->item(0);
            $doginfo['title'] = $title ? $title->nodeValue : 'N/A';

            //size
            
            //$size = $this->queryCss('.list4 p', null, $newdom)->item(0);
            //$doginfo['size'] = $size ? $size->nodeValue : 'N/A';

            $this->results[] = $doginfo;
        }
    }
    
    private function queryCss($selector, $contextNode = null, $dom)  //jak tutaj wpisać $dom = $this->dom ?
    {
        $domScraper = new \DOMXPath($dom);
        return $domScraper->query(CssSelector::toXPath($selector), $contextNode);
    }

}