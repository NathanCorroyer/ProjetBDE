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


        $names = array(
            "Parc de la Tête d'Or",
            "Librairie Mollat",
            "Conservatoire National Supérieur de Musique et de Danse de Paris",
            "Jardin des Plantes",
            "Parc national des Calanques",
            "Cinéma Le Grand Rex",
            "Atelier des Sens",
            "Atelier du Pommier",
            "Studio Yoga République",
            "Le Dernier Bar avant la Fin du Monde",
            "Basic-Fit",
            "Jardin du Luxembourg",
            "Camping Les Mimosas",
            "Musée du Louvre",
            "Centre Bouddhiste Kadampa",
            "Stade Roland-Garros",
            "Alliance Française",
            "Montmartre",
            "Piscine Molitor",
            "Leroy Merlin"
        );

        $cities = array(
            "Lyon",
            "Bordeaux",
            "Paris",
            "Paris",
            "Marseille",
            "Paris",
            "Paris",
            "Aix-en-Provence",
            "Paris",
            "Paris",
            "Lyon",
            "Paris",
            "Portiragnes",
            "Paris",
            "Paris",
            "Paris",
            "Nice",
            "Paris",
            "Paris",
            "Toulouse"
        );
        $adresses=array(
            "Parc de la tête d'or",
            "15, rue Vital Carles",
            "209, Avenue Jean Jaurès",
            "57, rue Cuvier",
            "Parc national des Calanques",
            "1, Boulevard Poissonière",
            "10, rue du Bourg l'Abbé",
            "8 Rue des Artistes",
            "21 rue Béranger",
            "19, Avenue Victoria",
            "9 Rue de la Forme",
            "Jardin du luxembourg",
            "Port Cassafières",
            "Musée du Louvre",
            "7, rue de l'Aqueduc",
            "2, Avenue Gordon Bennett",
            "2, rue de Paris",
            "18e arrondissement de Paris",
            "2, avenue de la Porte Molitor",
            "14, Avenue Jean René Lagasse"
        );
        $latitudes = array(
            45.7756,
            44.8412,
            48.8499,
            48.8432,
            43.2165,
            48.8709,
            48.8601,
            43.5297,
            48.8698,
            48.8645,
            45.7521,
            48.8462,
            43.2734,
            48.8606,
            48.8504,
            48.8462,
            43.7009,
            48.8867,
            48.8469,
            43.6047
        );
        $longitudes = array(
            4.8536,
            -0.5800,
            2.3570,
            2.3563,
            5.3698,
            2.3440,
            2.3507,
            5.4474,
            2.3607,
            2.3535,
            4.8583,
            2.3372,
            3.3100,
            2.3376,
            2.3763,
            2.2530,
            7.2714,
            2.3431,
            2.2520,
            1.4499
        );
        for ( $i = 0 ; $i <= 19 ; $i ++ ) {
            $place = ( new Place())
                    ->setName($names[$i])
                    ->setAdress($adresses[$i])
                    ->setLatitude($latitudes[$i])
                    ->setLongitude($longitudes[$i])
                    ->setCity($this->getReference($cities[$i]));
            $manager->persist($place);
            $this->addReference('LIEU' . $i , $place);

        }

        $manager->flush();
    }

    public function getDependencies(){
        return [CityFixtures::class];
    }
}
