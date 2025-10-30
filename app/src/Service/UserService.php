<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\UserFilters;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RoleRepository         $roleRepository
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = []): PaginationInterface
    {
        if(isset($filters['with_deleted']) && $filters['with_deleted'] == 1) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $transformedFilters = array_filter(
            array_map(
                function ($filterCase) use ($filters) {
                    if (isset($filters[$filterCase->value])) {
                        return [$filterCase, $filters[$filterCase->value]];
                    }
                    return null;
                },
                UserFilters::getFilterCases()
            )
        );

        $paginationUsers = $this->userRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $transformedFilters,
            sorts: $sorts
        );

        if(isset($filters['with_deleted']) && $filters['with_deleted'] == 1) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return $paginationUsers;
    }

    public function update(User $user): User
    {
        $user->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $user;
    }

    public function delete(User $user): void
    {
        $user->setIsActive(false);

        $this->entityManager->flush();

        $this->entityManager->remove($user);

        $this->entityManager->flush();

    }

    public function getAllRole(): array
    {
        return $this->roleRepository->findAll();
    }
}