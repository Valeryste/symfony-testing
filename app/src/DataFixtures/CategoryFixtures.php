<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends BaseFixture
{
    public const COUNT_CATEGORY = 10;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT_CATEGORY; $i++) {
            $category = new Category();

            $category->setName('Категория ' . $this->faker->unique()->word);
            $category->setIsActive($this->faker->boolean(80));

            $this->addReference('category' . $i, $category);
            $manager->persist($category);
        }

        $manager->flush();
    }
}