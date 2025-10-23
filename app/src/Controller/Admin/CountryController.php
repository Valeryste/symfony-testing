<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Form\Admin\Country\CreateFormType;
use App\Form\Admin\Country\UpdateFormType;
use App\Service\CountryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class CountryController extends AbstractController
{
    private const PATH_TO_TEMPLATES = 'admin/country/';

    public function __construct(
        private readonly CountryService $countryService
    )
    {
    }

    #[Route('/countries', name: 'admin_countries_index')]
    public function index(Request $request): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'countries' => $this->countryService->getAllPaginate($request),
            'createForm' => $this->createForm(CreateFormType::class)
        ]);
    }

    #[Route('/countries/store', name: 'admin_countries_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateFormType::class, $country = new Country());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->countryService->store($country);

            $this->addFlash('success', 'Сountry was successfully created');

            return $this->redirectToRoute('admin_countries_index');
        }

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'countries' => $this->countryService->getAllPaginate($request),
            'createForm' => $form
        ]);
    }

    #[Route('/countries/{id}/edit', name: 'admin_countries_edit', methods: 'GET')]
    public function edit(Country $country): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'country' => $country,
            'updateForm' => $this->createForm(UpdateFormType::class, $country)
        ]);
    }

    #[Route('/countries/{id}', name: 'admin_countries_update', methods: ['POST'])]
    public function update(Request $request, Country $country): Response
    {
        $form = $this->createForm(UpdateFormType::class, $country);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->countryService->update($country);

            $this->addFlash('success', 'Сountry was successfully updated');

            return $this->redirectToRoute('admin_countries_update', [
                'id' => $country->getId()
            ]);
        }

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'country' => $country,
            'updateForm' => $form
        ]);
    }

    #[Route('/countries/{id}/delete', name: 'admin_countries_delete', methods: ['POST'])]
    public function delete(Country $country): Response
    {
        $this->countryService->delete($country);

        return $this->redirectToRoute('admin_countries_index');
    }

}