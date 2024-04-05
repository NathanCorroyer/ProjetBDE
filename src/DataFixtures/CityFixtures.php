<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(4);

        $cities = array(
            "Lyon",
            "Bordeaux",
            'Paris',
            'Marseille',
            "Aix-en-Provence",
            "Portiragnes",
            'Nice',
            'Toulouse'
        );
        foreach ($cities as $cityName){
            $city = ( new City())
                    ->setName($cityName)
                    ->setZipCode($faker->postcode());

            $manager->persist($city);

            $this->addReference($city->getName() , $city );
        }

        $manager->flush();
    }
}
