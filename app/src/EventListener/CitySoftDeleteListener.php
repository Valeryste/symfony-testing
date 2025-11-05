<?php

namespace App\EventListener;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: 'onPreRemove', entity: City::class)]
class CitySoftDeleteListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function onPreRemove(City $city): void
    {
        if ($city->getDeletedAt() === null) {
            foreach ($city->getShops() as $shop) {
                $this->entityManager->remove($shop);
            }
        }
    }
}