<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Admin\Product\StoreProductDTO;
use App\DTO\Api\Admin\Product\UpdateProductDTO;
use App\Entity\Product;
use App\Enum\Filter\ProductFilters;
use App\Enum\Search\ProductSearch;
use App\Model\Product\ProductListResponse;
use App\Model\Product\ProductResponse;
use App\Request\Product\StoreProductRequest;
use App\Request\Product\UpdateProductRequest;
use App\Service\Api\ApiProductService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')"))]
#[Route('api/admin/products')]
#[OA\Tag(name: 'Admin Products')]
#[ForbiddenResponse]
#[UnauthorizedResponse]
class ProductController extends BaseController
{
    public function __construct(
        private readonly ApiProductService $apiProductService
    ) {
    }

    #[Route(name: 'api_admin_products_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns paginated list of products. Supports filtering by various fields, sorting and search.',
        summary: 'Get paginated products with filtering, sorting and search',
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
                description: 'Search term for product name',
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
                name: 'filters[categories]',
                description: 'Filter by category IDs',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer')
                ),
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
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Products list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: ProductListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: ProductFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: ProductSearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->json(
            $this->apiProductService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            )
        );
    }

    #[Route(name: 'api_admin_products_store', methods: ['POST'])]
    #[OA\Post(
        description: 'Adding a new product',
        summary: 'Adding a new product'
    )]
    #[OA\RequestBody(
        description: 'Product data to store',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'iPhone 15'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 999.99),
                new OA\Property(property: 'count', type: 'integer', example: 50),
                new OA\Property(
                    property: 'categoryIds',
                    type: 'array',
                    items: new OA\Items(type: 'integer'),
                    example: [1, 2, 3]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Product was stored successfully',
        content: new OA\JsonContent(
            ref: new Model(type: ProductResponse::class),
            type: 'object'
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->apiProductService->store(new StoreProductDTO(...$request->toArray()));

            return $this->json(
                $product,
                201
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_products_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns product details',
        summary: 'Get product details'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Product ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Product details retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: ProductResponse::class),
            type: 'object'
        )
    )]
    public function show(Product $product): JsonResponse
    {
        return $this->json(
            $this->apiProductService->show($product)
        );
    }

    #[Route('/{id}', name: 'api_admin_products_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Update product information. Only provided fields will be updated.',
        summary: 'Update product'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Product ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\RequestBody(
        description: 'Product data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'iPhone 15 Pro', nullable: true),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 1199.99, nullable: true),
                new OA\Property(property: 'count', type: 'integer', example: 25, nullable: true),
                new OA\Property(property: 'isActive', type: 'boolean', example: false, nullable: true),
                new OA\Property(
                    property: 'categoryIds',
                    type: 'array',
                    items: new OA\Items(type: 'integer'),
                    example: [2, 4],
                    nullable: true
                )
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Product updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'product',
                    ref: new Model(type: ProductResponse::class)
                )
            ]
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            $updatedProduct = $this->apiProductService->update(new UpdateProductDTO(...$request->toArray()), $product);

            return $this->json([
                'product' => $updatedProduct
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_products_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Soft delete product by ID.',
        summary: 'Delete product'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Product ID to delete',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Product deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Product deleted successfully')
            ]
        )
    )]
    public function delete(Product $product): JsonResponse
    {
        try {
            $this->apiProductService->delete($product);

            return $this->json([
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}