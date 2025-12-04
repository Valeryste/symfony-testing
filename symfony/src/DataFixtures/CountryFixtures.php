<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends BaseFixture
{
    public const COUNT_COUNTRY = 10;

    public const REFERENCE_NAME = 'country';

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT_COUNTRY; $i++) {
            $country = new Country();

            $country->setName($this->faker->country);

            $this->addReference(self::REFERENCE_NAME . $i, $country);
            $manager->persist($country);
        }

        $manager->flush();
    }
}