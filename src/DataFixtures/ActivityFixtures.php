<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ActivityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
//        $faker->seed(5);

        $states = [State::Creation , State::Open , State::Closed , State::Ongoing , State::Finished, State::Archived
            ,State::Cancelled];

        for ( $i = 1 ; $i <= 20 ; $i ++ ) {
            $index = array_rand($states);
            $state = $states[$index];
            $activity = new Activity() ;
             $activity->setName("Activité {$i}")
                    ->setStartingDateTime($faker->dateTimeBetween('+1 week', '+2 week'))
                    ->setDuration($faker->dateTime())
                    ->setMaxInscription($faker->randomNumber(1 , 15))
                    ->setDescription($faker->text())
                    ->setState($state)
                    ->setCampus($this->getReference('CAMPUS' . $faker->randomNumber(1, 10)))
                    ->setPlace($this->getReference('LIEU' . $faker->randomNumber(1 , 10)))
                    ->setPlanner($this->getReference('USER' . $faker->randomNumber(1 , 10)));
             $startingDate = new \DateTime(($activity->getStartingDateTime())->format('Y-m-d H:i:s'));
             $activity->setInscriptionLimitDate($startingDate -> modify(' +1 week'));

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
