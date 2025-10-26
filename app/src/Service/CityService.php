<?php

namespace App\Service;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class CityService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CityRepository         $cityRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getList(int $page): PaginationInterface
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $paginationCities = $this->cityRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            withDeletedCountries: true
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
        $this->entityManager->remove($city);

        $this->entityManager->flush();
    }
}