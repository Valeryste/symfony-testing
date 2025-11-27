<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Query;

class ProductRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator
    ) {
        parent::__construct($registry, $paginator, Product::class);
    }

    public function getListQuery(array $filters = [], array $sorts = [], array $search = []): Query
    {
        $query = $this->createQueryBuilder('p');

        $this->setSearchInQuery($query, $search);
        $this->setFilterInQuery($query, $filters);
        $this->setSortInQuery($query, $sorts);

        return $query->getQuery();
    }
}
