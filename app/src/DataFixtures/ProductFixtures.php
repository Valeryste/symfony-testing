<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends BaseFixture implements DependentFixtureInterface
{
    public const COUNT_PRODUCT = 10;

    public function load(ObjectManager $manager): void
    {
        $categories = [];

        for ($i = 0; $i < CategoryFixtures::COUNT_CATEGORY; $i++) {
            $categories[] = $this->getReference('category' . $i, Category::class);
        }

        for ($i = 0; $i < self::COUNT_PRODUCT; $i++) {
            $product = new Product();

            $product->setName(ucfirst($this->faker->words(2, true)));
            $product->setIsActive($this->faker->boolean(80));
            $product->setCount($this->faker->numberBetween(0, 10000));
            $product->setPrice($this->faker->randomFloat(2, 0, 100000));

            $selectedCategories = $this->faker->randomElements($categories, rand(1, 3));

            foreach ($selectedCategories as $category) {
                $product->addCategory($category);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];
    }
}