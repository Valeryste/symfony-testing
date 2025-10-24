<?php

namespace App\Service;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class CountryService extends BaseService
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        private readonly CountryRepository      $countryRepository,
        private readonly PaginatorInterface     $paginator,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getList(int $page): PaginationInterface
    {
        $this->entityManager->getFilters()->disable('softdeleteable');

        $paginationCountries = $this->paginator->paginate(
            target: $this->countryRepository->getListQuery(),
            page: $page,
            limit: self::PAGINATION_LIMIT
        );

        $this->entityManager->getFilters()->enable('softdeleteable');

        return $paginationCountries;
    }

    public function store(Country $country): Country
    {
        $this->entityManager->persist($country);

        $this->entityManager->flush();

        return $country;
    }


    public function update(Country $country): Country
    {
        $country->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $country;
    }

    public function delete(Country $country): void
    {
        $this->entityManager->remove($country);

        $this->entityManager->flush();
    }
}