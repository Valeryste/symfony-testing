<?php

namespace App\Controller\Web\Admin;

use App\Controller\BaseController;
use App\Entity\Product;
use App\Enum\Filter\ProductFilters;
use App\Enum\Search\ProductSearch;
use App\Enum\Sort\ProductSorts;
use App\Form\Admin\Product\CreateProductFormType;
use App\Form\Admin\Product\UpdateProductFormType;
use App\Service\Web\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/products')]
#[IsGranted('ROLE_ADMIN')]
class ProductController extends BaseController
{
    private const PATH_TO_TEMPLATES = 'admin/product/';

    public function __construct(
        private readonly ProductService $productService,
    ) {
    }


    #[Route(name: 'admin_products_index')]
    public function index(Request $request): Response
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: ProductFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: ProductSearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->render(self:: PATH_TO_TEMPLATES . 'index.html.twig', [
            'products' => $this->productService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            ),
            'filters' => ProductFilters::getFilterCases(),
            'sorts' => ProductSorts::getSortCases(),
            'categories' => $this->productService->findCategoriesWithProducts()
        ]);
    }

    #[Route('/create', name: 'admin_products_create')]
    public function create(): Response
    {
        return $this->render(self:: PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $this->createForm(CreateProductFormType::class),
        ]);
    }


    #[Route('/store', name: 'admin_products_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateProductFormType::class, $product = new Product());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productService->store($product);

            $this->addFlash('success', 'Product was successfully created');

            return $this->redirectToRoute('admin_products_index');
        }

        $this->addFlash('error', 'Validation or create error');

        return $this->render(self::PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_products_edit')]
    public function edit(Product $product): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'product' => $product,
            'updateForm' => $this->createForm(UpdateProductFormType::class, $product)
        ]);
    }

    #[Route('/{id}', name: 'admin_products_update')]
    public function update(Request $request, Product $product): Response
    {
        $form = $this->createForm(UpdateProductFormType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productService->update($product);

            $this->addFlash('success', 'Product was successfully updated');

            return $this->redirectToRoute('admin_products_edit', [
                'id' => $product->getId()
            ]);
        }

        $this->addFlash('error', 'Validation or update error');

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'product' => $product,
            'updateForm' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_products_delete')]
    public function delete(Product $product): Response
    {
        $this->productService->delete($product);

        $this->addFlash('success', 'Product was successfully deleted');

        return $this->redirectToRoute('admin_products_index');
    }

}