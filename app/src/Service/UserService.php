<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {

    }

    public function getList(int $page): PaginationInterface
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $paginationUsers = $this->userRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT
        );

        $this->entityManager->getFilters()->enable('softdeleteable');

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
}