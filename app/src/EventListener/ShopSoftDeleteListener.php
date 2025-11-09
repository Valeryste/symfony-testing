<?php

namespace App\EventListener;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: 'onPreRemove', entity: Shop::class)]
class ShopSoftDeleteListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function onPreRemove(Shop $shop): void
    {
        if (!empty($shop->getDeletedAt())) {
            foreach ($shop->getEmployees() as $employee) {
                $employee->setIsDismissed(true);
                $this->entityManager->flush();
            }
        }
    }
}