<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<City>
 */
class CityRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator
    ) {
        parent::__construct($registry, $paginator, City::class);
    }

    public function getListQuery(bool $withDeletedCountries = false): Query
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC');


        if ($withDeletedCountries) {
            $query->innerJoin('c.country', 'country')
                ->andWhere('country.deletedAt IS NULL');
        }

        return $query->getQuery();
    }

    public function getPaginatedResults(int $page = 1, int $limit = 10, bool $withDeletedCountries = false): PaginationInterface
    {
        return $this->paginator->paginate(
            target: $this->getListQuery($withDeletedCountries),
            page: $page,
            limit: $limit
        );
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
}
