<?php

namespace App\Service\Web;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Repository\ShopRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class EmployeeService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly EmployeeRepository     $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ShopRepository         $shopRepository
    ) {
    }


    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): PaginationInterface
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationEmployees = $this->employeeRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search:  $search
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return $paginationEmployees;
    }


    public function store(Employee $employee): Employee
    {
        $this->entityManager->persist($employee);

        $this->entityManager->flush();

        return $employee;
    }

    public function update(Employee $employee): Employee
    {
        $employee->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $employee;
    }

    public function delete(Employee $employee): void
    {
        $this->entityManager->remove($employee);

        $this->entityManager->flush();
    }

    public function getShopsHavingEmployee(): array
    {
        return $this->shopRepository->getShopsHavingEmployee();
    }

    public function getShopName(Employee $employee): string
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $shopName = $employee->getShop()->getName();

        $this->entityManager->getFilters()->enable('softdeleteable');

        return $shopName;
    }

}