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

    //    /**
    //     * @return City[] Returns an array of City objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?City
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /*private function setSortInQuery(QueryBuilder $queryBuilder, array $sorting): void
    {
        if ($sorting['value'] === 'NOT NULL' || $sorting['value'] === 'NULL') {
            $queryBuilder->andWhere("c.{$sorting['field']} IS " . $filter['value']);
        } else {
            $queryBuilder
                ->andWhere("c.{$filter['field']} = :filter_value")
                ->setParameter('filter_value', $filter['value']);
        }
    }*/
}
