<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class EmployeeRepository extends BaseRepository
{
    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator
    ) {
        parent::__construct($registry, $paginator, Employee::class);
    }

    public function getListQuery(array $filters = [], array $sorts = [], array $search = []): Query
    {
        $query = $this->createQueryBuilder('e');

        $this->setSearchInQuery($query, $search);

        $this->setFilterInQuery($query, $filters);

        $this->setSortInQuery($query, $sorts);

        return $query->getQuery();
    }
}
