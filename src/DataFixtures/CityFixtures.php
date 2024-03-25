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

        for ( $i = 1 ; $i <= 10 ; $i ++){
            $city = ( new City())
                    ->setName($faker->city())
                    ->setZipCode($faker->postcode());

            $manager->persist($city);

            $this->addReference('CITY' . $i , $city );
        }

        $manager->flush();
    }
}
