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

    protected function setFilterInQuery(QueryBuilder $queryBuilder, array $filters = []): void
    {
        $alias = $queryBuilder->getRootAliases()[0];

        foreach ($filters as $filter) {
            $field = $filter['field'];
            $value = $filter['value'];
            $fieldType = $filter['fieldType'];

            if (empty($value)) {
                continue;
            }

            $this->applyFilter(
                queryBuilder: $queryBuilder,
                alias: $alias,
                field: $field,
                fieldType: $fieldType,
                value: $value
            );
        }
    }

    private function applyFilter(
        QueryBuilder $queryBuilder,
        string $alias,
        string $field,
        string $fieldType,
        $value,
    ): void
    {
        if ($fieldType === 'datetime' && !in_array($value, [0, 1], true)) {
            $condition = (int)$value === 1 ? 'IS NOT NULL' : 'IS NULL';

            $queryBuilder->andWhere("$alias.$field $condition");
            return;
        }

        if ($fieldType === 'array' || count(explode('.', $field)) > 1) {
            $this->applyRelationFilter(
                queryBuilder: $queryBuilder,
                alias: $alias,
                field: $field,
                fieldType: $fieldType,
                value: $value
            );
            return;
        }

        $queryBuilder
            ->andWhere("$alias.$field = :{$alias}_{$field}_value")
            ->setParameter("{$alias}_{$field}_value", $value);
    }

    private function applyRelationFilter(
        QueryBuilder $queryBuilder,
        string $alias,
        string $field,
        string $fieldType,
        $value
    ): void
    {
        $fields = explode('.', $field);

        $operator = $fieldType === 'array' ? 'IN' : '=';

        $currentAlias = $alias;

        foreach ($fields as $field) {
            $lastAlias = $currentAlias;
            $currentAlias = "{$currentAlias}_$field";
            $queryBuilder->join("$lastAlias.$field", $currentAlias);
        }

        $queryBuilder
            ->andWhere("$currentAlias.id $operator (:{$currentAlias}_value)")
            ->setParameter("{$currentAlias}_value", $value);
    }

    protected function setSortInQuery(QueryBuilder $queryBuilder, array $sorts = []): void
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

    protected function setSearchInQuery(QueryBuilder $queryBuilder, array $search = []): void
    {
        $alias = $queryBuilder->getRootAliases()[0];

        if (empty($search)) {
            return;
        }

        $searchQuery = '%' . $search['value'] . '%';

        foreach ($search['fields'] as $field) {
            $queryBuilder->orWhere(
                "$alias.$field LIKE :search"
            );
        }

        $queryBuilder->setParameter('search', $searchQuery);
    }
}