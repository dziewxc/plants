<?php
namespace Cloudy\Bundle\CrudBundle\DataFixtures\ORM\PlantFixtures;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Cloudy\Bundle\CrudBundle\Entity\Plant;

class PlantFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $plant = new Plant();
        $plant->setLocation('Warszawa');
        $plant->setFlatSurface(234);
        $plant->setResidentsCount(4);
        $plant->setIfGasStove(true);
        $plant->setElectronicsAmount(5);
        $plant->setIfGarage(true);
        $manager->persist($plant);
        
        $plant = new Plant();
        $plant->setLocation('Kraków');
        $plant->setFlatSurface(123);
        $plant->setResidentsCount(4);
        $plant->setIfGasStove(false);
        $plant->setElectronicsAmount(7);
        $plant->setIfGarage(true);
        $manager->persist($plant);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 3;
    }
}