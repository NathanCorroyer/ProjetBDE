<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\fr_FR\PhoneNumber;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {

    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new PhoneNumber($faker));

        for ($i = 1; $i <= 10; $i++){
            $user = new User();
            $user ->setEmail( $faker->unique()->Email())
                ->setLastName($faker->lastName())
                ->setFirstName($faker->firstName())
                ->setPassword($this->hasher->hashPassword($user, $faker->password()))
                ->setPhone($faker->serviceNumber())
                ->setCampus($this->getReference('CAMPUS'.$faker->randomNumber(1,10)));

            $manager->persist($user);
            $this->addReference('USER' . $i , $user);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [CampusFixtures::class];
    }
}
