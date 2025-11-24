<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\User\UpdateUserDTO;
use App\Entity\User;
use App\Model\Role\RoleResponse;
use App\Model\User\UserListResponse;
use App\Model\User\UserResponse;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

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

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return new UserListResponse(
            currentPage: $paginationUsers->getCurrentPageNumber(),
            totalCount: $paginationUsers->getTotalItemCount(),
            users: array_map(
                function ($user) {
                    return $this->toResponse($user);
                },
                $paginationUsers->getItems()
            )
        );
    }

    public function toResponse(User $user): UserResponse
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

    public function show(User $user): UserResponse
    {
        return self::toResponse($user);
    }

    /**
     * @throws \Exception
     */
    public function update(UpdateUserDTO $updateUserDTO, User $user): UserResponse
    {
        $user->setEmail($updateUserDTO->email ?? $user->getEmail());
        $user->setUsername($updateUserDTO->username ?? $user->getUsername());
        $user->setIsActive($updateUserDTO->isActive ?? $user->isActive());
        $user->setUpdatedAt(new \DateTime());
        if (!empty($updateUserDTO->roleId)) {
           $this->setRoleUser($user, $updateUserDTO->roleId);
        }

        $this->entityManager->flush();

        return $this->toResponse($user);
    }

    public function delete(User $user): void
    {
        $user->setIsActive(false);

        $this->entityManager->flush();

        $this->entityManager->remove($user);

        $this->entityManager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    private function setRoleUser(User $user, int $roleId): void
    {
        if (!($role = $this->roleRepository->find($roleId))) {
            throw new EntityNotFoundException('Role with ID: ' . $roleId . ' not found', 404);
        }

        $user->setRole($role);
    }
}