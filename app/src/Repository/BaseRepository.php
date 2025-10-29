<?php

namespace App\Repository;

use App\Trait\PaginationTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

abstract class BaseRepository extends ServiceEntityRepository
{
    use PaginationTrait;

    public function __construct(
        ManagerRegistry    $registry,
        PaginatorInterface $paginator,
        string             $entityClass
    )
    {
        parent::__construct($registry, $entityClass);
        $this->setPaginator($paginator);
    }

    protected function setFilterInQuery(QueryBuilder $queryBuilder, ?array $filters): void
    {
        $alias = $queryBuilder->getRootAliases()[0];

        foreach ($filters as $filter) {
            $field = $filter[0]->getField();

            $value = $filter[1];

            if ($value === '' || $value === null) {
                continue;
            }

            if ($filter[0]->getFieldType() === 'datetime') {
                $condition = $value == 1 ? 'IS NOT NULL' : 'IS NUll';

                $queryBuilder->andWhere("$alias.$field $condition");
                continue;
            }

            $queryBuilder
                ->andWhere("$alias.$field =:filter_value")
                ->setParameter('filter_value', $value);
        }

    }

    protected function setSortInQuery(QueryBuilder $queryBuilder, ?array $sorts): void
    {
        $alias = $queryBuilder->getRootAliases()[0];

        foreach ($sorts as $field => $direction) {
            if ($direction === '' || $direction === null) {
                continue;
            }

            $queryBuilder->orderBy("$alias.$field", strtoupper($direction));
        }

        if (empty($sorts)) {
            $queryBuilder->orderBy("$alias.id", 'ASC');
        }
    }
}