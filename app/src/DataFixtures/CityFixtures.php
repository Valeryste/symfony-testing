<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends BaseFixture implements DependentFixtureInterface
{
    public const COUNT_CITY = 20;

    public const REFERENCE_NAME = 'city';

    public function load(ObjectManager $manager): void
    {
        $countries = [];

        for ($i = 0; $i < CountryFixtures::COUNT_COUNTRY; $i++) {
            $countries[] = $this->getReference(CountryFixtures::REFERENCE_NAME . $i, Country::class);
        }

        for ($i = 0; $i < self::COUNT_CITY; $i++) {
            $city = new City();

            $city->setName($this->faker->city);
            $city->setCountry($this->faker->randomElement($countries));

            $this->addReference(self::REFERENCE_NAME . $i, $city);
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