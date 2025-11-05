<?php

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class ShopRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry    $registry,
        PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, $paginator, Shop::class);
    }

    public function getListQuery(array $filters = [], array $sorts = [], array $search = []): Query
    {
        $query = $this->createQueryBuilder('s');

        $this->setSearchInQuery($query, $search);

        $this->setFilterInQuery($query, $filters);

        $this->setSortInQuery($query, $sorts);

        return $query->getQuery();
    }
}
