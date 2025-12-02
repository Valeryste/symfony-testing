<?php

namespace App\Controller\Web\Admin;

use App\Controller\BaseController;
use App\Entity\Category;
use App\Enum\Filter\CategoryFilters;
use App\Enum\Search\CategorySearch;
use App\Enum\Sort\CategorySorts;
use App\Form\Admin\Category\CreateCategoryFormType;
use App\Form\Admin\Category\UpdateCategoryFormType;
use App\Service\Web\CategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends BaseController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    private const PATH_TO_TEMPLATES = 'admin/category/';

    #[Route(name: 'admin_categories_index')]
    public function index(Request $request): Response
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: CategoryFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: CategorySearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
            'categories' => $this->categoryService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            ),
            'filters' => CategoryFilters::getAvailableFilters(),
            'sorts' => CategorySorts::getSortCases(),
            'parentCategories' => $this->categoryService->getAllParents()
        ]);
    }

    #[Route('/create', name: 'admin_categories_create')]
    public function create(): Response
    {
        return $this->render(self:: PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $this->createForm(CreateCategoryFormType::class)
        ]);
    }

    #[Route('/store', name: 'admin_categories_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateCategoryFormType::class, $category = new Category());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->store($category);

            $this->addFlash('success', 'Category was successfully created');

            return $this->redirectToRoute('admin_categories_index');
        }

        $this->addFlash('error', 'Validation or create error');


        return $this->render(self:: PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categories_edit')]
    public function edit(Category $category): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'category' => $category,
            'updateForm' => $this->createForm(UpdateCategoryFormType::class, $category, [
                'availableParents' => $this->categoryService->getAllExcept($category->getId())
            ])
        ]);
    }

    #[Route('/{id}', name: 'admin_categories_update')]
    public function update(Request $request, Category $category): Response
    {
        $form = $this->createForm(UpdateCategoryFormType::class, $category, [
            'availableParents' => $this->categoryService->getAllExcept($category->getId())
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->update($category);

            $this->addFlash('success', 'Category was successfully updated');

            return $this->redirectToRoute('admin_categories_edit', [
                'id' => $category->getId()
            ]);

        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        $this->addFlash('error', 'Validation or create error');

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'category' => $category,
            'updateForm' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_categories_delete')]
    public function delete(Category $category): Response
    {
        $this->categoryService->delete($category);

        $this->addFlash('success', 'Category was successfully deleted');

        return $this->redirectToRoute('admin_categories_index');
    }
}