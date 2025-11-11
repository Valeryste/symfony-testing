<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Shop;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ShopFixtures extends BaseFixture implements DependentFixtureInterface
{
    public const COUNT_SHOPS = 20;

    public function load(ObjectManager $manager): void
    {
        $cities = [];

        for ($i = 0; $i < CityFixtures::COUNT_CITY; $i++) {
            $cities[] = $this->getReference('city' . $i, City::class);
        }

        for ($i = 0; $i < self::COUNT_SHOPS; $i++) {
            $shop = new Shop();

            $shop->setName($this->faker->company);
            $shop->setAddress($this->faker->address);
            $shop->setIsOpen($this->faker->boolean(80));
            $shop->setCity($this->faker->randomElement($cities));

            $this->addReference('shop' . $i, $shop);
            $manager->persist($shop);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class
        ];
    }
}