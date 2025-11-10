<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $roles = [
            'USER'    => 'role_user',
            'MANAGER' => 'role_manager',
            'ADMIN'   => 'role_admin'
        ];

        foreach ($roles as $roleName => $referenceName) {
            $role = new Role();
            $role->setName($roleName);
            $manager->persist($role);
            $this->addReference($referenceName, $role);
        }

        $manager->flush();

    }
}