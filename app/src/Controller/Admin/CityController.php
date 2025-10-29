<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Enum\CityFilters;
use App\Enum\CitySorts;
use App\Form\Admin\City\CreateCityFormType;
use App\Form\Admin\City\UpdateCityFormType;
use App\Service\CityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class CityController extends AbstractController
{
    private const PATH_TO_TEMPLATES = 'admin/city/';

    public function __construct(
        private readonly CityService $cityService
    ) {
    }

    #[Route('/cities', name: 'admin_cities_index')]
    public function index(Request $request): Response
    {
        $sorts = $request->query->all()['sorts'] ?? [];

        $filters = $request->query->all()['filters'] ?? [];

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'cities' => $this->cityService->getList(
                page: $request->query->getInt('page', 1),
                filters: $filters,
                sorts: $sorts
            ),
            'createForm' => $this->createForm(CreateCityFormType::class),
            'filters' => CityFilters::getFilterCases(),
            'sorts' => CitySorts::getSortCases(),
            'countries' => $this->cityService->findCountriesWithCities()
        ]);
    }

    #[Route('/cities/store', name: 'admin_cities_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateCityFormType::class, $city = new City());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cityService->store($city);

            $this->addFlash('success', 'Сity was successfully created');

            return $this->redirectToRoute('admin_cities_index');
        }

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'cities' => $this->cityService->getList(
                page: $request->query->getInt('page', 1),
            ),
            'createForm' => $this->createForm(CreateCityFormType::class),
            'filters' => CityFilters::getFilterCases(),
            'sorts' => CitySorts::getSortCases(),
            'countries' => $this->cityService->findCountriesWithCities()
        ]);
    }

    #[Route('/cities/{id}/edit', name: 'admin_cities_edit', methods: 'GET')]
    public function edit(City $city): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'city' => $city,
            'updateForm' => $this->createForm(UpdateCityFormType::class, $city)
        ]);
    }

    #[Route('/cities/{id}', name: 'admin_cities_update', methods: ['POST'])]
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

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'city' => $city,
            'updateForm' => $form
        ]);
    }

    #[Route('/cities/{id}/delete', name: 'admin_cities_delete', methods: ['POST'])]
    public function delete(City $city): Response
    {
        $this->cityService->delete($city);

        return $this->redirectToRoute('admin_cities_index');
    }
}