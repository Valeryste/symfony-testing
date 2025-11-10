<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Shop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ShopFixtures extends Fixture implements DependentFixtureInterface
{
    private const COUNT_SHOPS = 20;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT_SHOPS; $i++) {
            $shop = new Shop();

            $shop->setName('shop' . $i);
            $shop->setAddress($i . ' Test Street, Apt ' . $i);
            $shop->setIsOpen(true);
            $shop->setCity($this->getReference('city' . $i, City::class));

            $this->addReference($shop->getName(), $shop);
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