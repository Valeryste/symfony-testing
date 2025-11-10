<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $products = [];
        $categories = [];

        for ($i = 0; $i < ProductFixtures::COUNT_PRODUCT; $i++) {
            $products[] = $this->getReference('product' . $i, Product::class);
        }

        for ($j = 0; $j < CategoryFixtures::COUNT_CATEGORY; $j++) {
            $categories[] = $this->getReference('category' . $j, Category::class);
        }

        foreach ($products as $product) {
            $selectedCategories = array_rand($categories,  rand(1, 4));

            if (!is_array($selectedCategories)) {
                $selectedCategories = [$selectedCategories];
            }

            foreach ($selectedCategories as $categoryIndex) {
                $product->addCategory($categories[$categoryIndex]);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
            CategoryFixtures::class,
        ];
    }
}