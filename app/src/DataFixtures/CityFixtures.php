<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture implements DependentFixtureInterface
{
    private const COUNT_CITY = 20;

    public function load(ObjectManager $manager): void
    {
        $countryReference = 0;

        for ($i = 0; $i < self::COUNT_CITY; $i++) {
            if($i % 2 == 0 && $i !== 0) {
                $countryReference++;
            }

            $city = new City();

            $city->setName('city' . $i);
            $city->setCountry($this->getReference('country' . $countryReference, Country::class));

            $this->addReference($city->getName(), $city);
            $manager->persist($city);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class
        ];
    }
}