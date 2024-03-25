<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(1);

        for($i = 1; $i <= 10; $i++)
        {
            $campus = new Campus();
            $campus->setName($faker->city());
            $manager->persist($campus);
            $this->addReference('CAMPUS'. $i, $campus);
        }
        $manager->flush();
    }
}
