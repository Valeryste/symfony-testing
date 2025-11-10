<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const COUNT_CATEGORY = 10;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT_CATEGORY; $i++) {
            $category = new Category();

            $category->setName('category' . $i);
            $category->setIsActive(true);

            $this->addReference($category->getName(), $category);
            $manager->persist($category);
        }

        $manager->flush();
    }
}