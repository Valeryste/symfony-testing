<?php

namespace App\Controller\Web\Admin;

use App\Controller\BaseController;
use App\Entity\City;
use App\Enum\Filter\CityFilters;
use App\Enum\Search\CitySearch;
use App\Enum\Sort\CitySorts;
use App\Form\Admin\City\CreateCityFormType;
use App\Form\Admin\City\UpdateCityFormType;
use App\Service\Web\CityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/cities')]
#[IsGranted('ROLE_ADMIN')]
class CityController extends BaseController
{
    private const PATH_TO_TEMPLATES = 'admin/city/';

    public function __construct(
        private readonly CityService $cityService
    ) {
    }

    #[Route(name: 'admin_cities_index')]
    public function index(Request $request): Response
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: CityFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: CitySearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'cities' => $this->cityService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            ),
            'createForm' => $this->createForm(CreateCityFormType::class),
            'filters' => CityFilters::getAvailableFilters(),
            'sorts' => CitySorts::getSortCases(),
            'countries' => $this->cityService->findCountriesWithCities()
        ]);
    }

    #[Route('/store', name: 'admin_cities_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateCityFormType::class, $city = new City());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cityService->store($city);

            $this->addFlash('success', 'Сity was successfully created');

            return $this->redirectToRoute('admin_cities_index');
        }

        $this->addFlash('error', 'Validation or create error');

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'cities' => $this->cityService->getList(
                page: $request->query->getInt('page', 1),
            ),
            'createForm' => $form,
            'filters' => CityFilters::getFilterCases(),
            'sorts' => CitySorts::getSortCases(),
            'countries' => $this->cityService->findCountriesWithCities()
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cities_edit', methods: 'GET')]
    public function edit(City $city): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'city' => $city,
            'updateForm' => $this->createForm(UpdateCityFormType::class, $city)
        ]);
    }

    #[Route('/{id}', name: 'admin_cities_update', methods: ['POST'])]
    public function update(Request $request, City $city): Response
    {
        $form = $this->createForm(UpdateCityFormType::class, $city);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cityService->update($city);

            $this->addFlash('success', 'Сity was successfully updated');

            return $this->redirectToRoute('admin_cities_update', [
                'id' => $city->getId()
            ]);
        }

        $this->addFlash('error', 'Validation or update error');

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'city' => $city,
            'updateForm' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_cities_delete', methods: ['POST'])]
    public function delete(City $city): Response
    {
        $this->cityService->delete($city);

        $this->addFlash('success', 'City was successfully deleted');

        return $this->redirectToRoute('admin_cities_index');
    }
}