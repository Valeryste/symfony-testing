<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\UpdateUserDTO;
use App\Entity\User;
use App\Model\RoleResponse;
use App\Model\UserListResponse;
use App\Model\UserResponse;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class ApiUserService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RoleRepository         $roleRepository
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): UserListResponse
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationUsers = $this->userRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search: $search
        );

        $response = new UserListResponse(
            currentPage: $paginationUsers->getCurrentPageNumber(),
            totalCount: $paginationUsers->getTotalItemCount(),
            users: array_map(
                function ($user) {
                    return $this->getUser($user);
                },
                $paginationUsers->getItems())
        );


        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return $response;
    }

    public function getUser(User $user): UserResponse
    {
        return new UserResponse(
            id: $user->getId(),
            username: $user->getUsername(),
            email: $user->getEmail(),
            isActive: $user->isActive(),
            role: new RoleResponse(
                id: $user->getRole()->getId(),
                name: $user->getRole()->getName(),
            ),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }

    public function update(UpdateUserDTO $updateUserDTO, User $user): UserResponse
    {
        $user->setEmail($updateUserDTO->email ?? $user->getEmail());
        $user->setUsername($updateUserDTO->username ?? $user->getUsername());
        $user->setIsActive($updateUserDTO->isActive ?? $user->isActive());

        if ($updateUserDTO->roleId !== null) {
            $role = $this->roleRepository->findOneBy(['id' => $updateUserDTO->roleId]);
            if ($role) {
                $user->setRole($role);
            }
        }
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $this->getUser($user);
    }

    public function delete(User $user): void
    {
        $user->setIsActive(false);

        $this->entityManager->flush();

        $this->entityManager->remove($user);

        $this->entityManager->flush();
    }
}