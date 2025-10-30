<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Country;

#[AsEntityListener(event: Events::preRemove, method: 'onPreRemove', entity: Country::class)]
class CountrySoftDeleteListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function onPreRemove(Country $country): void
    {
        if ($country->getDeletedAt() === null) {
            foreach ($country->getCities() as $city) {
                $this->entityManager->remove($city);
            }
        }
    }
}