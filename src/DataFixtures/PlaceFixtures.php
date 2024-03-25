<?php

namespace App\DataFixtures;

use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PlaceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(3);


        for ( $i = 1 ; $i <= 10 ; $i ++ ) {
            $place = ( new Place())
                    ->setName("Lieu {$i}")
                    ->setAdress($faker->address())
                    ->setLatitude($faker->latitude())
                    ->setLongitude($faker->longitude())
                    ->setCity($this->getReference('CITY' . $faker->randomNumber(1, 10)));
            $manager->persist($place);
            $this->addReference('LIEU' . $i , $place);

        }

        $manager->flush();
    }

    public function getDependencies(){
        return [CityFixtures::class];
    }
}
