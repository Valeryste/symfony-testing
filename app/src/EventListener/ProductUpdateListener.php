<?php

namespace App\EventListener;

use App\Entity\CountHistory;
use App\Entity\PriceHistory;
use App\Entity\Product;
use App\Repository\CountHistoryRepository;
use App\Repository\PriceHistoryRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

#[AsEntityListener(event: Events::postUpdate, method: 'onPostUpdate', entity: Product::class)]
class ProductUpdateListener
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CountHistoryRepository $countHistoryRepository,
        private readonly PriceHistoryRepository $priceHistoryRepository
    ) {
    }

    public function onPostUpdate(Product $product, PostUpdateEventArgs $event): void
    {
        $changeSet = $event->getObjectManager()->getUnitOfWork()->getEntityChangeSet($product);

        $this->changeCount($changeSet, $product);

        $this->changePrice($changeSet, $product);

        $this->entityManager->flush();
    }

    private function changeCount(PersistentCollection|array $changeSet, Product $product): void
    {
        if (isset($changeSet['count'])) {
            $newCount = $changeSet['count'][1];

            $lastHistory = $this->countHistoryRepository->findLastByProduct($product);
            $lastHistory?->setUpdatedAt(new \DateTime());

            $countHistory = new CountHistory();
            $countHistory->setCount($newCount);
            $countHistory->setProduct($product);

            $this->entityManager->persist($countHistory);
        }
    }

    private function changePrice(PersistentCollection|array $changeSet, Product $product): void
    {
        if (isset($changeSet['price'])) {
            $oldPrice = (float)$changeSet['price'][0];
            $newPrice = (float)$changeSet['price'][1];

            if ($oldPrice != $newPrice) {
                $lastHistory = $this->priceHistoryRepository->findLastByProduct($product);
                $lastHistory?->setUpdatedAt(new \DateTime());

                $priceHistory = new PriceHistory();
                $priceHistory->setPrice($newPrice);
                $priceHistory->setProduct($product);

                $this->entityManager->persist($priceHistory);
            }
        }
    }
}