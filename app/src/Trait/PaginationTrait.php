<?php

namespace App\Trait;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

trait PaginationTrait
{
    protected PaginatorInterface $paginator;

    public function setPaginator(PaginatorInterface $paginator): void
    {
        $this->paginator = $paginator;
    }

    public function getPaginatedResults(int $page = 1, int $limit = 10): PaginationInterface
    {
        return $this->paginator->paginate(
            target: $this->getListQuery(),
            page: $page,
            limit: $limit
        );
    }

    abstract public function getListQuery(): Query;

}