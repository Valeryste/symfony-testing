<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Shop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EmployeeFixtures extends Fixture implements DependentFixtureInterface
{
    private const COUNT_EMPLOYEES = 40;

    public function load(ObjectManager $manager): void
    {
        $shopReference = 0;

        for ($i = 0; $i < self::COUNT_EMPLOYEES; $i++) {
            if($i % 2 == 0 && $i !== 0) {
                $shopReference++;
            }

            $employee = new Employee();

            $samePart = 'employee' . $i;

            $employee->setName($samePart);
            $employee->setSurname($samePart .'surname');
            $employee->setEmail($samePart . '@employee.com');
            $employee->setPhone('+37533111111111');
            $employee->setPosition('salesman');
            $employee->setIsDismissed(false);
            $employee->setShop($this->getReference('shop' . $shopReference, Shop::class));

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