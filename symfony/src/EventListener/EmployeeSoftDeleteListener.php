<?php

namespace App\EventListener;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: 'onPreRemove', entity: Employee::class)]
class EmployeeSoftDeleteListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function onPreRemove(Employee $employee): void
    {
        $employee->setIsDismissed(true);

        $this->entityManager->flush();
    }
}