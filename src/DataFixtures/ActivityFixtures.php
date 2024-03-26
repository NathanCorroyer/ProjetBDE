<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ActivityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(5);

        $states = ['En Création' , 'Ouverte' , 'Clôturée' , 'En Cours' , 'Terminée' , 'Annulée' , 'Historisée' ];

        for ( $i = 1 ; $i <= 20 ; $i ++ ) {
            $activity = new Activity() ;
             $activity->setName("Activité {$i}")
                    ->setStartingDateTime($faker->dateTime())
                    ->setDuration($faker->dateTime())
                    ->setInscriptionLimitDate($activity->getStartingDateTime()->modify('-1 week'))
                    ->setMaxInscription($faker->randomNumber(1 , 15))
                    ->setDescription($faker->text())
                    ->setState($faker->randomElement($states))
                    ->setCampus($this->getReference('CAMPUS' . $faker->randomNumber(1, 10)))
                    ->setPlace($this->getReference('LIEU' . $faker->randomNumber(1 , 10)))
                    ->setPlanner($this->getReference('USER' . $faker->randomNumber(1 , 10)));

                for($j=1;$j<=rand(5,10); $j++)
                {
                    $activity->addUser($this->getReference('USER'.rand(1,10)));
                }
            $manager->persist($activity);
            $this->addReference('ACTIVITY'.$i, $activity);
        }


        $manager->flush();
    }

    public function getDependencies()
    {
        return [CampusFixtures::class , UserFixtures::class, CityFixtures::class , PlaceFixtures::class ];
    }
}
