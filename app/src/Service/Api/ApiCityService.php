<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\City\StoreCityDTO;
use App\DTO\Api\Admin\City\UpdateCityDTO;
use App\Entity\City;
use App\Model\City\CityListResponse;
use App\Model\City\CityResponse;
use App\Model\Country\CountryResponse;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class ApiCityService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CityRepository         $cityRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly CountryRepository $countryRepository
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): CityListResponse
    {
        if($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationCities = $this->cityRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search: $search
        );

        if($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return new CityListResponse(
            currentPage: $paginationCities->getCurrentPageNumber(),
            totalCount: $paginationCities->getTotalItemCount(),
            countries: array_map(
                function ($city) {
                    return $this->getCityToResponse($city);
                },
                $paginationCities->getItems())
        );
    }

    public function getCityToResponse(City $city): CityResponse
    {
        return new CityResponse(
            id: $city->getId(),
            name: $city->getName(),
            country: new CountryResponse(
                id: $city->getCountry()->getId(),
                name: $city->getCountry()->getName(),
                createdAt: $city->getCountry()->getCreatedAt(),
                updatedAt: $city->getCountry()->getUpdatedAt()
            ),
            createdAt: $city->getCreatedAt(),
            updatedAt: $city->getUpdatedAt()
        );
    }

    public function store(StoreCityDTO $storeCityDTO): CityResponse
    {
        $city = new City();

        $city->setName($storeCityDTO->name);
        $city->setCountry($this->countryRepository->find($storeCityDTO->countryId));

        $this->entityManager->persist($city);

        $this->entityManager->flush();

        return $this->getCityToResponse($city);
    }

    public function update(UpdateCityDTO $updateCityDTO, City $city): CityResponse
    {
        $city->setName($updateCityDTO->name ?? $city->getName());

        if (isset($updateCityDTO->countryId)) {
            if ($country = $this->countryRepository->find($updateCityDTO->countryId)) {
                $city->setCountry($country);
            }
        }

        $city->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $this->getCityToResponse($city);
    }

    public function delete(City $city): void
    {
        $this->entityManager->remove($city);

        $this->entityManager->flush();
    }
}