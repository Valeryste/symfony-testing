<?php

namespace App\EventListener;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'onPreUpdate', entity: Category::class)]
#[AsDoctrineListener(event: Events::postFlush)]
class CategoryUpdateListener
{
    private static ?Category $updateCategory = null;

    private bool $processing = true;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function onPreUpdate(Category $category): void
    {
        if (!$category->isActive() && $this->hasActiveChildren($category)) {
            self::$updateCategory = $category;
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!$this->processing || !isset(self::$updateCategory)) {
            return;
        }

        $this->processing = false;

        $this->deactivateCategoryTree(self::$updateCategory);

        $this->entityManager->flush();

        self::$updateCategory = null;
    }

    private function deactivateCategoryTree(Category $category): void
    {
        $children = $category->getChildren();

        foreach ($children as $child) {
            $child->setIsActive(false);

            $this->deactivateCategoryTree($child);
        }
    }

    private function hasActiveChildren(Category $category): bool
    {
        foreach ($category->getChildren() as $child) {
            if ($child->isActive() === true) {
                return true;
            }
        }

        return false;
    }
}