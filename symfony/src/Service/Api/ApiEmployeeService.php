<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\Employee\StoreEmployeeDTO;
use App\DTO\Api\Admin\Employee\UpdateEmployeeDTO;
use App\Entity\Employee;
use App\Model\Employee\EmployeeListResponse;
use App\Model\Employee\EmployeeResponse;
use App\Repository\EmployeeRepository;
use App\Repository\ShopRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class ApiEmployeeService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly EmployeeRepository     $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ShopRepository         $shopRepository
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): EmployeeListResponse
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationEmployees = $this->employeeRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search: $search
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return EmployeeListResponse::fromPagination($paginationEmployees);
    }

    public function show(Employee $employee): EmployeeResponse
    {
        return EmployeeResponse::fromEntity($employee);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreEmployeeDTO $storeEmployeeDTO): EmployeeResponse
    {
        $employee = new Employee();

        $employee
            ->setName($storeEmployeeDTO->name)
            ->setEmail($storeEmployeeDTO->email)
            ->setSurname($storeEmployeeDTO->surname)
            ->setPhone($storeEmployeeDTO->phone)
            ->setPosition($storeEmployeeDTO->position);

        $this->setEmployeeShop($employee, $storeEmployeeDTO->shopId);

        $this->entityManager->persist($employee);

        $this->entityManager->flush();

        return EmployeeResponse::fromEntity($employee);
    }

    /**
     * @throws \Exception
     */
    public function update(UpdateEmployeeDTO $updateEmployeeDTO, Employee $employee): EmployeeResponse
    {
        $employee
            ->setName($updateEmployeeDTO->name ?? $employee->getName())
            ->setEmail($updateEmployeeDTO->email ?? $employee->getEmail())
            ->setSurname($updateEmployeeDTO->surname ?? $employee->getSurname())
            ->setPhone($updateEmployeeDTO->phone ?? $employee->getPhone())
            ->setPosition($updateEmployeeDTO->position ?? $employee->getPosition())
            ->setUpdatedAt(new \DateTime());

        if(!empty($updateEmployeeDTO->shopId)) {
            $this->setEmployeeShop($employee, $updateEmployeeDTO->shopId);
        }

        $this->entityManager->flush();

        return EmployeeResponse::fromEntity($employee);
    }

    public function delete(Employee $employee): void
    {
        $this->entityManager->remove($employee);

        $this->entityManager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    private function setEmployeeShop(Employee $employee, int $shopId): void
    {
        if (!($shop = $this->shopRepository->find($shopId))) {
            throw new EntityNotFoundException('Shop with ID: ' . $shopId . ' not found', 404);
        }
        $employee->setShop($shop);
    }
}