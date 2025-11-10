<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public const COUNT_PRODUCT = 10;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT_PRODUCT; $i++) {
            $product = new Product();

            $product->setName('product' . $i);
            $product->setIsActive(true);
            $product->setCount(rand(0, 100000));
            $product->setPrice(rand(0, 100000));

            $this->addReference($product->getName(), $product);
            $manager->persist($product);
        }

        $manager->flush();
    }
}