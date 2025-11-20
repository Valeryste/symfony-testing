<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Admin\City\StoreCityDTO;
use App\DTO\Api\Admin\City\UpdateCityDTO;
use App\Entity\City;
use App\Enum\Filter\CityFilters;
use App\Enum\Search\CitySearch;
use App\Model\City\CityListResponse;
use App\Model\City\CityResponse;
use App\Request\City\StoreCityRequest;
use App\Request\City\UpdateCityRequest;
use App\Service\Api\ApiCityService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[IsGranted('ROLE_ADMIN')]
#[Route('api/admin/cities')]
#[OA\Tag(name: 'Admin Cities')]
#[ForbiddenResponse]
#[UnauthorizedResponse]
class CityController extends BaseController
{
    public function __construct(
        private readonly ApiCityService $apiCityService
    ) {
    }

    #[Route(name: 'api_admin_cities_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns paginated list of cities. Supports filtering by various fields, sorting and search.',
        summary: 'Get paginated cities with filtering, sorting and search',
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
                name: 'filters[country]',
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
            ),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Cities list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CityListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: CityFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: CitySearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->json(
            $this->apiCityService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            )
        );
    }

    #[Route(name: 'api_admin_cities_store', methods: ['POST'])]
    #[OA\Post(
        description: 'Adding a new city',
        summary: 'Adding a new city'
    )]
    #[OA\RequestBody(
        description: 'City data to store',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Grodno'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'City was store successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CityResponse::class),
            type: 'object'
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function store(StoreCityRequest $request): JsonResponse
    {
        try {
            return $this->json([
                    $this->apiCityService->store(new StoreCityDTO(...$request->toArray()))
                ]
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 401);
        }
    }

    #[Route('/{id}/show', name: 'api_admin_cities_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns city details',
        summary: 'Get city details'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'City ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'City details retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CityResponse::class),
            type: 'object'
        )
    )]
    public function show(City $city): JsonResponse
    {
        return $this->json(
            $this->apiCityService->getCityToResponse($city)
        );
    }

    #[Route('/{id}', name: 'api_admin_cities_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Update city information. Only provided fields will be updated.',
        summary: 'Update city'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'City ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\RequestBody(
        description: 'City data to store',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Grodno',  nullable: true),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'City updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'City updated successfully'),
                new OA\Property(
                    property: 'city',
                    ref: new Model(type: CityResponse::class)
                )
            ]
        )
    )]
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        try{
            return $this->json([
                'message' => 'User updated successfully',
                'user' => $this->apiCityService->update(new UpdateCityDTO(...$request->toArray()), $city)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 401);
        }
    }

    #[Route('/{id}', name: 'api_admin_cities_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Soft delete city by ID.',
        summary: 'Delete city'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'City ID to delete',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'City deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'City deleted successfully')
            ]
        )
    )]
    public function delete(City $city): JsonResponse
    {
        try{
            $this->apiCityService->delete($city);

            return $this->json([
                'message' => 'City deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}