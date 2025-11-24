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

        return new EmployeeListResponse(
            currentPage: $paginationEmployees->getCurrentPageNumber(),
            totalCount: $paginationEmployees->getTotalItemCount(),
            employees: array_map(
                function ($employee) {
                    return self::toResponse($employee);
                },
                $paginationEmployees->getItems()
            )
        );
    }

    public static function toResponse(Employee $employee): EmployeeResponse
    {
        return new EmployeeResponse(
            id: $employee->getId(),
            name: $employee->getName(),
            surname: $employee->getSurname(),
            phone: $employee->getPhone(),
            position: $employee->getPosition(),
            email: $employee->getEmail(),
            isDismissed: $employee->isDismissed(),
            shop: ApiShopService::toResponse($employee->getShop()),
            createdAt: $employee->getCreatedAt(),
            updatedAt: $employee->getUpdatedAt()
        );
    }

    public function show(Employee $employee): EmployeeResponse
    {
        return self::toResponse($employee);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreEmployeeDTO $storeEmployeeDTO): EmployeeResponse
    {
        $employee = new Employee();

        $employee->setName($storeEmployeeDTO->name);
        $employee->setEmail($storeEmployeeDTO->email);
        $employee->setSurname($storeEmployeeDTO->surname);
        $employee->setPhone($storeEmployeeDTO->phone);
        $employee->setPosition($storeEmployeeDTO->position);
        $this->setEmployeeShop($employee, $storeEmployeeDTO->shopId);

        $this->entityManager->persist($employee);

        $this->entityManager->flush();

        return self::toResponse($employee);
    }

    /**
     * @throws \Exception
     */
    public function update(UpdateEmployeeDTO $updateEmployeeDTO, Employee $employee): EmployeeResponse
    {
        $employee->setName($updateEmployeeDTO->name ?? $employee->getName());
        $employee->setEmail($updateEmployeeDTO->email ?? $employee->getEmail());
        $employee->setSurname($updateEmployeeDTO->surname ?? $employee->getSurname());
        $employee->setPhone($updateEmployeeDTO->phone ?? $employee->getPhone());
        $employee->setPosition($updateEmployeeDTO->position ?? $employee->getPosition());
        $employee->setUpdatedAt(new \DateTime());
        if(!empty($updateEmployeeDTO->shopId)) {
            $this->setEmployeeShop($employee, $updateEmployeeDTO->shopId);
        }

        $this->entityManager->flush();

        return self::toResponse($employee);
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