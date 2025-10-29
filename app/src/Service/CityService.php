<?php

namespace App\Service;

use App\Entity\City;
use App\Enum\CityFilters;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class CityService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CityRepository         $cityRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CountryRepository      $countryRepository
    ) {
    }

    public function getList(int $page, array $filters, array $sorts): PaginationInterface
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $transformedFilters = array_filter(
            array_map(
                function ($filterCase) use ($filters) {
                    if (isset($filters[$filterCase->value])) {
                        return [$filterCase, $filters[$filterCase->value]];
                    }
                    return null;
                },
                CityFilters::getFilterCases()
            )
        );

        $paginationCities = $this->cityRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $transformedFilters,
            sorts: $sorts
        );

        $this->entityManager->getFilters()->enable('softdeleteable');

        return $paginationCities;
    }

    public function findCountriesWithCities(): array
    {
        return $this->countryRepository->findCountriesWithCities();
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

    public function getCountryName(City $city): string
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $name = $city->getCountry()->getName();

        $this->entityManager->getFilters()->enable('softdeleteable');

        return $name;
    }
}