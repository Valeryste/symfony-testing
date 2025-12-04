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
            $operator = $filter['operator'];

            if (empty($value)) {
                continue;
            }

            $this->applyFilter(
                queryBuilder: $queryBuilder,
                alias: $alias,
                field: $field,
                fieldType: $fieldType,
                value: $value,
                operator: $operator
            );
        }
    }

    private function applyFilter(
        QueryBuilder $queryBuilder,
        string $alias,
        string $field,
        string $fieldType,
        mixed $value,
        string $operator
    ): void
    {
        if ($fieldType === 'datetime') {
            $this->applyDateTimeFilter($queryBuilder, $alias, $field, $value, $operator);

            return;
        }

        if ($fieldType === 'array' || count(explode('.', $field)) > 1) {
            $this->applyRelationFilter($queryBuilder, $alias, $field, $value, $operator);

            return;
        }

        $queryBuilder
            ->andWhere("$alias.$field $operator :{$alias}_{$field}_value")
            ->setParameter("{$alias}_{$field}_value", $value);
    }

    private function applyRelationFilter(
        QueryBuilder $queryBuilder,
        string $alias,
        string $field,
        mixed $value,
        string $operator
    ): void
    {
        $fields = explode('.', $field);

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

    private function applyDateTimeFilter(
        QueryBuilder $queryBuilder,
        string $alias,
        string $field,
        mixed $value,
        string $operator
    ): void
    {
        if(in_array((int)$value, [0, 1])){
            $queryBuilder->andWhere("$alias.$field $operator");

            return;
        }

        try {
            $value = new \DateTime($value);

            if($operator === '<=') {
                $value->setTime(23, 59, 59);
            }

            $queryBuilder
                ->andWhere("$alias.$field $operator :{$alias}_{$field}_value")
                ->setParameter("{$alias}_{$field}_value", $value);

        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid date format ' . $value, 400);
        }
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