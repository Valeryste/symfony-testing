<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Country;
use App\Enum\CountryFilters;
use App\Enum\CountrySorts;
use App\Form\Admin\Country\CreateCountryFormType;
use App\Form\Admin\Country\UpdateCountryFormType;
use App\Service\CountryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/countries')]
#[IsGranted('ROLE_ADMIN')]
class CountryController extends BaseController
{
    private const PATH_TO_TEMPLATES = 'admin/country/';

    public function __construct(
        private readonly CountryService $countryService
    ) {
    }

    #[Route(name: 'admin_countries_index')]
    public function index(Request $request): Response
    {
        $filtersFromRequest = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: CountryFilters::class
        );

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'countries' => $this->countryService->getList(
                page: $request->query->getInt('page', 1),
                filters: $filtersFromRequest,
                sorts: $request->query->all()['sorts'] ?? []
            ),
            'createForm' => $this->createForm(CreateCountryFormType::class),
            'filters' => CountryFilters::getFilterCases(),
            'sorts' => CountrySorts::getSortCases()
        ]);
    }

    #[Route('/store', name: 'admin_countries_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateCountryFormType::class, $country = new Country());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->countryService->store($country);

            $this->addFlash('success', 'Сountry was successfully created');

            return $this->redirectToRoute('admin_countries_index');
        }

        $this->addFlash('error', 'Validation or create error');

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'countries' => $this->countryService->getList(
                page: $request->query->getInt('page', 1),
            ),
            'createForm' => $form,
            'filters' => CountryFilters::getFilterCases(),
            'sorts' => CountrySorts::getSortCases()
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_countries_edit', methods: 'GET')]
    public function edit(Country $country): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'country' => $country,
            'updateForm' => $this->createForm(UpdateCountryFormType::class, $country)
        ]);
    }

    #[Route('/{id}', name: 'admin_countries_update', methods: ['POST'])]
    public function update(Request $request, Country $country): Response
    {
        $form = $this->createForm(UpdateCountryFormType::class, $country);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->countryService->update($country);

            $this->addFlash('success', 'Сountry was successfully updated');

            return $this->redirectToRoute('admin_countries_update', [
                'id' => $country->getId()
            ]);
        }

        $this->addFlash('error', 'Validation or update error');

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'country' => $country,
            'updateForm' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_countries_delete', methods: ['POST'])]
    public function delete(Country $country): Response
    {
        $this->countryService->delete($country);

        return $this->redirectToRoute('admin_countries_index');
    }
}