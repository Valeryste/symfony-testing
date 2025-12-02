<?php

namespace App\Controller\Web\Admin;

use App\Controller\BaseController;
use App\Entity\Shop;
use App\Enum\Filter\ShopFilters;
use App\Enum\Search\ShopSearch;
use App\Enum\Sort\ShopSorts;
use App\Form\Admin\Shop\CreateShopFormType;
use App\Form\Admin\Shop\UpdateShopFormType;
use App\Service\Web\ShopService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/shops')]
#[IsGranted('ROLE_ADMIN')]
class ShopController extends BaseController
{
    private const PATH_TO_TEMPLATES = 'admin/shop/';

    public function __construct(
        private readonly ShopService $shopService,
    ) {
    }


    #[Route(name: 'admin_shops_index')]
    public function index(Request $request): Response
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: ShopFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: ShopSearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->render(self:: PATH_TO_TEMPLATES . 'index.html.twig', [
            'shops' => $this->shopService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            ),
            'filters' => ShopFilters::getAvailableFilters(),
            'sorts' => ShopSorts::getSortCases(),
            'cities' => $this->shopService->getCitiesHavingShops(),
            'countries' => $this->shopService->getCountriesHavingShops()
        ]);
    }

    #[Route('/create', name: 'admin_shops_create')]
    public function create(): Response
    {
        return $this->render(self:: PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $this->createForm(CreateShopFormType::class),
        ]);
    }


    #[Route('/store', name: 'admin_shops_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateShopFormType::class, $shop = new Shop());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->shopService->store($shop);

            $this->addFlash('success', 'Shop was successfully created');

            return $this->redirectToRoute('admin_shops_index');
        }

        $this->addFlash('error', 'Validation or create error');

        return $this->render(self::PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_shops_edit')]
    public function edit(Shop $shop): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'shop' => $shop,
            'updateForm' => $this->createForm(UpdateShopFormType::class, $shop)
        ]);
    }

    #[Route('/{id}', name: 'admin_shops_update')]
    public function update(Request $request, Shop $shop): Response
    {
        $form = $this->createForm(UpdateShopFormType::class, $shop);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->shopService->update($shop);

            $this->addFlash('success', 'Shop was successfully updated');

            return $this->redirectToRoute('admin_shops_edit', [
                'id' => $shop->getId()
            ]);
        }

        $this->addFlash('error', 'Validation or update error');

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'shop' => $shop,
            'updateForm' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_shops_delete')]
    public function delete(Shop $shop): Response
    {
        $this->shopService->delete($shop);

        $this->addFlash('success', 'Shop was successfully deleted');

        return $this->redirectToRoute('admin_shops_index');
    }

}