<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator
    ) {
        parent::__construct($registry, $paginator, Country::class);
    }

    public function getListQuery(array $filters = [], array $sorts = [], array $search = []): Query
    {
        $query = $this->createQueryBuilder('c');

        $this->setSearchInQuery($query, $search);
        $this->setFilterInQuery($query, $filters);
        $this->setSortInQuery($query, $sorts);

        return $query->getQuery();
    }

    public function findCountriesWithCities(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.cities', 'cities')
            ->groupBy('c.id')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getCountriesHavingShops(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.cities', 'city')
            ->innerJoin('city.shops', 'shop')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

    }

}
