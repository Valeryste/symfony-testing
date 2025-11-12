<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<City>
 */
class CityRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry    $registry,
        PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, $paginator, City::class);
    }

    public function getListQuery(array $filters = [], array $sorts = [], array $search = []): Query
    {
        $query = $this->createQueryBuilder('c');

        $this->setSearchInQuery($query, $search);

        $this->setFilterInQuery($query, $filters);

        $this->setSortInQuery($query, $sorts);

        return $query->getQuery();
    }

    public function getCitiesHavingShops(): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.shops', 'shops')
            ->groupBy('s.id')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
