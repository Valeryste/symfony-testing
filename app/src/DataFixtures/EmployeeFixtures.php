<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Shop;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EmployeeFixtures extends BaseFixture implements DependentFixtureInterface
{
    private const COUNT_EMPLOYEES = 40;

    private const POSITIONS = [
        'salesman',
        'manager',
        'loader',
        'director',
        'driver'
    ];

    public function load(ObjectManager $manager): void
    {
        $shops = [];

        for ($i = 0; $i < ShopFixtures::COUNT_SHOPS; $i++) {
            $shops[] = $this->getReference(ShopFixtures::REFERENCE_NAME . $i, Shop::class);
        }

        for ($i = 0; $i < self::COUNT_EMPLOYEES; $i++) {
            $employee = new Employee();

            $employee->setName($this->faker->firstName);
            $employee->setSurname($this->faker->lastName);
            $employee->setEmail($this->faker->email);
            $employee->setPhone($this->faker->phoneNumber);
            $employee->setPosition($this->faker->randomElement(self::POSITIONS));
            $employee->setIsDismissed($this->faker->boolean(80));
            $employee->setShop($this->faker->randomElement($shops));

            $manager->persist($employee);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ShopFixtures::class
        ];
    }
}