<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    private const COUNT_COUNTRY = 10;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT_COUNTRY; $i++) {
            $country = new Country();

            $country->setName('country' . $i);

            $this->addReference($country->getName(), $country);
            $manager->persist($country);
        }

        $manager->flush();
    }
}