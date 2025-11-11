<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends BaseFixture implements DependentFixtureInterface
{
    private const COUNT_USERS = 10;

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setUsername('admin');
        $user->setPassword(password_hash('admin', PASSWORD_DEFAULT));
        $user->setRole($this->getReference('role_admin', Role::class));
        $user->setEmail('admin@example.com');

        $manager->persist($user);

        for($i = 0; $i < self::COUNT_USERS; $i++) {
            $user = new User();

            $user->setUsername($this->faker->userName);
            $user->setPassword(password_hash('test', PASSWORD_DEFAULT));
            $user->setRole($this->getReference('role_user', Role::class));
            $user->setIsActive($this->faker->boolean(80));
            $user->setEmail($this->faker->email);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixtures::class
        ];
    }
}