<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Admin\Shop\StoreShopDTO;
use App\DTO\Api\Admin\Shop\UpdateShopDTO;
use App\Entity\Shop;
use App\Enum\Filter\ShopFilters;
use App\Enum\Search\ShopSearch;
use App\Model\Shop\ShopListResponse;
use App\Model\Shop\ShopResponse;
use App\Request\Shop\StoreShopRequest;
use App\Request\Shop\UpdateShopRequest;
use App\Service\Api\ApiShopService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[IsGranted('ROLE_ADMIN')]
#[Route('api/admin/shops')]
#[OA\Tag(name: 'Admin Shops')]
#[ForbiddenResponse]
#[UnauthorizedResponse]
class ShopController extends BaseController
{
    public function __construct(
        private readonly ApiShopService $apiShopService
    ) {
    }

    #[Route(name: 'api_admin_shops_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns paginated list of shops. Supports filtering by various fields, sorting and search.',
        summary: 'Get paginated shops with filtering, sorting and search',
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
                description: 'Search term for name',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'filters[deletedAt]',
                description: 'Filter by deleted',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'filters[city]',
                description: 'Filter by city ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'filters[city.country]',
                description: 'Filter by city->country',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'filters[isOpen]',
                description: 'Filter by country ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
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
        description: 'Shops list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: ShopListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: ShopFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: ShopSearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->json(
            $this->apiShopService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            )
        );
    }

    #[Route(name: 'api_admin_shops_store', methods: ['POST'])]
    #[OA\Post(
        description: 'Adding a new shop',
        summary: 'Adding a new shop'
    )]
    #[OA\RequestBody(
        description: 'Shop data to store',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Main Shop'),
                new OA\Property(property: 'address', type: 'string', example: '123 Main Street'),
                new OA\Property(property: 'cityId', type: 'integer', example: 1),
                new OA\Property(property: 'isOpen', type: 'boolean', example: true),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Shop was stored successfully',
        content: new OA\JsonContent(
            ref: new Model(type: ShopResponse::class),
            type: 'object'
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function store(StoreShopRequest $request): JsonResponse
    {
        try {
            $shop = $this->apiShopService->store(new StoreShopDTO(...$request->toArray()));

            return $this->json(
                $shop,
                201
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/{id}/show', name: 'api_admin_shops_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns shop details',
        summary: 'Get shop details'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Shop ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Shop details retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: ShopResponse::class),
            type: 'object'
        )
    )]
    public function show(Shop $shop): JsonResponse
    {
        return $this->json(
            $this->apiShopService->getShopToResponse($shop)
        );
    }

    #[Route('/{id}', name: 'api_admin_shops_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Update shop information. Only provided fields will be updated.',
        summary: 'Update shop'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Shop ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\RequestBody(
        description: 'Shop data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Updated Shop Name'),
                new OA\Property(property: 'address', type: 'string', example: '456 Updated Street'),
                new OA\Property(property: 'cityId', type: 'integer', example: 2),
                new OA\Property(property: 'isOpen', type: 'boolean', example: false),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Shop updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Shop updated successfully'),
                new OA\Property(
                    property: 'shop',
                    ref: new Model(type: ShopResponse::class)
                )
            ]
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function update(UpdateShopRequest $request, Shop $shop): JsonResponse
    {
        try {
            $updatedShop = $this->apiShopService->update(new UpdateShopDTO(...$request->toArray()), $shop);

            return $this->json([
                'message' => 'Shop updated successfully',
                'shop' => $updatedShop
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/{id}', name: 'api_admin_shops_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Soft delete shop by ID.',
        summary: 'Delete shop'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Shop ID to delete',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Shop deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Shop deleted successfully')
            ]
        )
    )]
    public function delete(Shop $shop): JsonResponse
    {
        try {
            $this->apiShopService->delete($shop);

            return $this->json([
                'message' => 'Shop deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}