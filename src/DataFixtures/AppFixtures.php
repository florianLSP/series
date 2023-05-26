<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->addSeries($manager);
        $this->addUsers($manager);
    }



    public function addSeries(ObjectManager $manager){
        $generator = Factory::create('fr_FR');

        for($i = 0 ; $i < 50 ; $i++){
            $serie = new Serie();
            $serie
                ->setBackdrop("backdrop.png")
                ->setDateCreated($generator->dateTimeBetween("-20 year"))
                ->setGenres($generator->randomElement(["Western", "SF", "Drama", "Comedy"]))
                ->setName($generator->word.$i)
                ->setFirstAirDate($generator->dateTimeBetween("-10 year", "-1 year"))
                ->setLastAirDate(new \DateTime("-2 month"))
                ->setPopularity($generator->numberBetween(0, 1000))
                ->setPoster("poster.png")
                ->setStatus("Canceled")
                ->setTmdbId(123456)
                ->setVote(5);
            $manager->persist($serie);
        }
        $manager->flush();
    }

    private function addUsers(ObjectManager $manager)
    {
        $generator = Factory::create('fr_FR');

        for($i = 0; $i < 10; $i++){
            $user = new User();
            $user
                -> setEmail($generator->email)
                -> setFirstname($generator->firstName)
                -> setLastName($generator->lastName)
                -> setRoles(['ROLES_USER'])
                -> setPassword(
                    $this->hasher->hashPassword($user, '123'));

            $manager->persist($user);
        }
    $manager->flush();

    }
}
