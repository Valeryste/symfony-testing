<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class UserService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly PaginatorInterface     $paginator,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getAllPaginate(Request $request): PaginationInterface
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $query = $this->userRepository->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->getQuery();

        $paginationUser = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PAGINATION_LIMIT
        );

        $this->entityManager->getFilters()->enable('softdeleteable');

        return $paginationUser;
    }

    public function update(User $user): User
    {
        $this->entityManager->flush();

        return $user;
    }

    public function delete(User $user): void
    {
        $user->setIsActive(false);

        $user->setDeletedAt(new \DateTime());

        $this->entityManager->flush();
    }
}