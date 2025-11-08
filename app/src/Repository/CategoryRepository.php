<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class CategoryRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator
    ) {
        parent::__construct($registry, $paginator, Category::class);
    }

    public function findCategoriesWithProducts(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.products', 'p')
            ->distinct()
            ->getQuery()
            ->getResult();

    }

    public function getListQuery(array $filters = [], array $sorts = [], array $search = []): Query
    {
        $query = $this->createQueryBuilder('c');

        $this->setSearchInQuery($query, $search);

        $this->setFilterInQuery($query, $filters);

        $this->setSortInQuery($query, $sorts);

        return $query->getQuery();
    }

    public function getAllParents(): array
    {
        return $this->createQueryBuilder('c')
            ->where('SIZE(c.children) > 0')
            ->getQuery()
            ->getResult();
    }

    public function getAllExcept(int $id): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.id != :id')
            ->setParameter('id', $id)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
