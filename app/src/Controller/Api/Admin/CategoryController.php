<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Admin\Category\StoreCategoryDTO;
use App\DTO\Api\Admin\Category\UpdateCategoryDTO;
use App\Entity\Category;
use App\Enum\Filter\CategoryFilters;
use App\Enum\Search\CategorySearch;
use App\Model\Category\CategoryListResponse;
use App\Model\Category\CategoryResponse;
use App\Request\Category\StoreCategoryRequest;
use App\Request\Category\UpdateCategoryRequest;
use App\Service\Api\ApiCategoryService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[IsGranted('ROLE_ADMIN')]
#[Route('api/admin/categories')]
#[OA\Tag(name: 'Admin Categories')]
#[ForbiddenResponse]
#[UnauthorizedResponse]
class CategoryController extends BaseController
{
    public function __construct(
        private readonly ApiCategoryService $apiCategoryService
    ) {
    }

    #[Route(name: 'api_admin_categories_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns paginated list of categories. Supports filtering by various fields, sorting and search.',
        summary: 'Get paginated categories with filtering, sorting and search',
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search term for category name',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'filters[deletedAt]',
                description: 'Filter by deleted status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'filters[parent]',
                description: 'Filter by parent category ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'filters[isActive]',
                description: 'Filter by active status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'sorts[id]',
                description: 'Sort by id (asc/desc)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])
            ),
            new OA\Parameter(
                name: 'sorts[name]',
                description: 'Sort by name (asc/desc)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Categories list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CategoryListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: CategoryFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: CategorySearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->json(
            $this->apiCategoryService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            )
        );
    }

    #[Route(name: 'api_admin_categories_store', methods: ['POST'])]
    #[OA\Post(
        description: 'Adding a new category',
        summary: 'Adding a new category'
    )]
    #[OA\RequestBody(
        description: 'Category data to store',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
                new OA\Property(property: 'parentId', type: 'integer', example: 1, nullable: true)
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Category was stored successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CategoryResponse::class),
            type: 'object'
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->apiCategoryService->store(new StoreCategoryDTO(...$request->toArray()));

            return $this->json(
                $category,
                201
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_categories_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns category details',
        summary: 'Get category details'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Category ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Category details retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CategoryResponse::class),
            type: 'object'
        )
    )]
    public function show(Category $category): JsonResponse
    {
        return $this->json(
            $this->apiCategoryService->show($category)
        );
    }

    #[Route('/{id}', name: 'api_admin_categories_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Update category information. Only provided fields will be updated.',
        summary: 'Update category'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Category ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\RequestBody(
        description: 'Category data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Updated Electronics', nullable: true),
                new OA\Property(property: 'parentId', type: 'integer', example: 2, nullable: true),
                new OA\Property(property: 'isActive', type: 'boolean', example: false, nullable: true)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Category updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'category',
                    ref: new Model(type: CategoryResponse::class)
                )
            ]
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $updatedCategory = $this->apiCategoryService->update(new UpdateCategoryDTO(...$request->toArray()), $category);

            return $this->json([
                'category' => $updatedCategory
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_categories_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Soft delete category by ID.',
        summary: 'Delete category'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Category ID to delete',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Category deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Category deleted successfully')
            ]
        )
    )]
    public function delete(Category $category): JsonResponse
    {
        try {
            $this->apiCategoryService->delete($category);

            return $this->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}