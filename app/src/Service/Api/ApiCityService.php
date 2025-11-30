<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\City\StoreCityDTO;
use App\DTO\Api\Admin\City\UpdateCityDTO;
use App\Entity\City;
use App\Model\City\CityListResponse;
use App\Model\City\CityResponse;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

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

        return CityListResponse::fromPagination($paginationCities);
    }

    public function show(City $city): CityResponse
    {
        return CityResponse::fromEntity($city);
    }

    /**
     * @throws \Exception
     */
    public function store(StoreCityDTO $storeCityDTO): CityResponse
    {
        $city = new City();

        $city->setName($storeCityDTO->name);
        $this->setCountryCity($city, $storeCityDTO->countryId);

        $this->entityManager->persist($city);

        $this->entityManager->flush();

        return CityResponse::fromEntity($city);
    }

    /**
     * @throws \Exception
     */
    public function update(UpdateCityDTO $updateCityDTO, City $city): CityResponse
    {
        $city->setName($updateCityDTO->name ?? $city->getName())
            ->setUpdatedAt(new \DateTime());

        if (!empty($updateCityDTO->countryId)) {
            $this->setCountryCity($city, $updateCityDTO->countryId);
        }

        $this->entityManager->flush();

        return CityResponse::fromEntity($city);
    }

    public function delete(City $city): void
    {
        $this->entityManager->remove($city);

        $this->entityManager->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    private function setCountryCity(City $city, int $countryId): void
    {
        if (!($country = $this->countryRepository->find($countryId))) {
            throw new EntityNotFoundException('Country with ID: ' . $countryId . ' not found', 404);
        }

        $city->setCountry($country);
    }
}