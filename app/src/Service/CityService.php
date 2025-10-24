<?php

namespace App\Service;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class CityService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CityRepository         $cityRepository,
        private readonly PaginatorInterface     $paginator,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getList(int $page): PaginationInterface
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $paginationCities = $this->paginator->paginate(
            target: $this->cityRepository->getListQueryWithSoftDelete(),
            page: $page,
            limit: self::PAGINATION_LIMIT
        );

        $this->entityManager->getFilters()->enable('softdeleteable');

        return $paginationCities;
    }

    public function store(City $city): City
    {
        $this->entityManager->persist($city);

        $this->entityManager->flush();

        return $city;
    }


    public function update(City $city): City
    {
        $city->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $city;
    }

    public function delete(City $city): void
    {
        $city->setDeletedAt(new \DateTime());

        $this->entityManager->flush();
    }
}