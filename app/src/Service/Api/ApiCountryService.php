<?php

namespace App\Service\Api;

use App\DTO\Api\Admin\Country\StoreCountryDTO;
use App\DTO\Api\Admin\Country\UpdateCountryDTO;
use App\Entity\Country;
use App\Model\Country\CountryListResponse;
use App\Model\Country\CountryResponse;
use App\Repository\CountryRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;

class ApiCountryService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CountryRepository      $countryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getList(int $page, array $filters = [], array $sorts = [], array $search = []): CountryListResponse
    {
        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->disable('softdeleteable');
        }

        $paginationCountries = $this->countryRepository->getPaginatedResults(
            page: $page,
            limit: self::PAGINATION_LIMIT,
            filters: $filters,
            sorts: $sorts,
            search: $search
        );

        if ($this->hasFilter($filters, 'deletedAt', 1)) {
            $this->entityManager->getFilters()->enable('softdeleteable');
        }

        return new CountryListResponse(
            currentPage: $paginationCountries->getCurrentPageNumber(),
            totalCount: $paginationCountries->getTotalItemCount(),
            countries: array_map(
                function ($country) {
                    return self::toResponse($country);
                },
                $paginationCountries->getItems())
        );
    }

    public static function toResponse(Country $country): CountryResponse
    {
        return new CountryResponse(
            id: $country->getId(),
            name: $country->getName(),
            createdAt: $country->getCreatedAt(),
            updatedAt: $country->getUpdatedAt()
        );
    }

    public function show(Country $country): CountryResponse
    {
        return self::toResponse($country);
    }

    public function store(StoreCountryDTO $storeCountryDTO): CountryResponse
    {
        $country = new Country();

        $country->setName($storeCountryDTO->name);

        $this->entityManager->persist($country);

        $this->entityManager->flush();

        return self::toResponse($country);
    }

    public function update(UpdateCountryDTO $updateCountryDTO, Country $country): CountryResponse
    {
        $country->setName($updateCountryDTO->name ?? $country->getName());
        $country->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return self::toResponse($country);
    }

    public function delete(Country $country): void
    {
        $this->entityManager->remove($country);

        $this->entityManager->flush();
    }
}