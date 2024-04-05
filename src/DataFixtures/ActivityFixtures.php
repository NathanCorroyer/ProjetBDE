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
        $activity = new Activity() ;
        $activity->setName("Activité créée par Admin")
                ->setStartingDateTime($faker->dateTimeBetween('+1 week', '+2 week'))
            ->setDuration($faker->dateTime())
            ->setMaxInscription($faker->randomNumber(1 , 15))
            ->setDescription($faker->text())
            ->setState(State::Ongoing)
            ->setCampus($this->getReference('CAMPUS' . $faker->randomNumber(1, 10)))
            ->setPlace($this->getReference('LIEU' . $faker->randomNumber(1 , 10)))
            ->setPlanner($this->getReference('ADMIN'));
        $startingDate = new \DateTime(($activity->getStartingDateTime())->format('Y-m-d H:i:s'));
        $activity->setInscriptionLimitDate($startingDate -> modify(' -1 week'));
        $manager -> persist($activity);

        $activites = array(
            "Faire du vélo",
            "Lire un livre",
            "Jouer d'un instrument de musique",
            "Faire du jardinage",
            "Faire de la randonnée",
            "Regarder un film",
            "Faire de la cuisine",
            "Faire de la peinture/dessin",
            "Faire du yoga",
            "Jouer à des jeux de société",
            "Aller à la salle de sport",
            "Écrire dans un journal",
            "Faire du camping",
            "Visiter un musée",
            "Faire de la méditation",
            "Jouer au tennis",
            "Apprendre une nouvelle langue",
            "Faire de la photographie",
            "Faire de la natation",
            "Faire du bricolage"
        );



        //$faker->seed(5);

        $states = [State::Creation , State::Open , State::Closed , State::Ongoing , State::Finished, State::Archived
            ,State::Cancelled];

        for ( $i = 0 ; $i <= 19 ; $i ++ ) {
            $index = array_rand($states);
            $state = $states[$index];
            $activity = new Activity() ;
             $activity->setName($activites[$i])
                    ->setStartingDateTime($faker->dateTimeBetween('-1 week', '+1 week'))
                    ->setDuration($faker->dateTime())
                    ->setMaxInscription($faker->randomNumber(1 , 15))
                    ->setDescription($faker->text())
                    ->setState($state)
                    ->setCampus($this->getReference('CAMPUS' . $faker->randomNumber(1, 10)))
                    ->setPlace($this->getReference('LIEU' . $i))
                    ->setPlanner($this->getReference('USER' . $faker->randomNumber(1 , 10)));
             $startingDate = new \DateTime(($activity->getStartingDateTime())->format('Y-m-d H:i:s'));
             $activity->setInscriptionLimitDate($startingDate -> modify(' -1 week'));

                for($j=1;$j<=rand(1,$activity->getMaxInscription()); $j++)
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
