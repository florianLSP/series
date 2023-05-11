<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->addSeries($manager);
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
}
