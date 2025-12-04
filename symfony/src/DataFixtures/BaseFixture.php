<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixture extends Fixture
{
    /**
     * @var Generator
     */
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

}