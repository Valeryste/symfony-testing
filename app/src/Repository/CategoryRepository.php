<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Category>
 */
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

    //    /**
    //     * @return Category[] Returns an array of Category objects
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

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getListQuery(array $filters = [], array $sorts = []): Query
    {
        return new Query($this->getEntityManager());
    }
}
