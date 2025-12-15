<?php

namespace App\Repository;

use App\Entity\CountHistory;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<CountHistory>
 */
class CountHistoryRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry    $registry,
        PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, $paginator, CountHistory::class);
    }

    public function findLastByProduct(Product $product): ?CountHistory
    {
        return $this->findOneBy(
            ['product' => $product],
            ['createdAt' => 'DESC']
        );
    }

    public function getPaginatedResults(
        int   $page = 1,
        int   $limit = 10,
        array $filters = [],
        array $sorts = [],
        array $search = [],
        Product $product = null
    ) : PaginationInterface
    {
        return $this->paginator->paginate(
            target: $this->getListQuery($filters, $sorts, $search, $product),
            page: $page,
            limit: $limit
        );
    }

    public function getListQuery(array $filters = [], array $sorts = [], array $search = [], Product $product = null): Query
    {
        $query = $this->createQueryBuilder('ch');

        if ($product) {
            $query
                ->leftJoin('ch.product', 'p')
                ->addSelect('p')
                ->andWhere('ch.product = :product')
                ->setParameter('product', $product);
        }

        $this->setFilterInQuery($query, $filters);
        $this->setSortInQuery($query, $sorts);

        return $query->getQuery();
    }
}
