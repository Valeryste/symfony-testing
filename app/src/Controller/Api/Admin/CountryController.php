<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Admin\Country\StoreCountryDTO;
use App\DTO\Api\Admin\Country\UpdateCountryDTO;
use App\Entity\Country;
use App\Enum\Filter\CountryFilters;
use App\Enum\Search\CountrySearch;
use App\Model\Country\CountryListResponse;
use App\Model\Country\CountryResponse;
use App\Request\Country\StoreCountryRequest;
use App\Request\Country\UpdateCountryRequest;
use App\Service\Api\ApiCountryService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/countries')]
#[IsGranted('ROLE_ADMIN')]
#[OA\Tag(name: 'Admin Countries')]
#[UnauthorizedResponse]
#[ForbiddenResponse]
class CountryController extends BaseController
{
    public function __construct(
        private readonly ApiCountryService $apiCountryService
    ) {
    }

    #[Route(name: 'api_admin_countries_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns paginated list of countries. Supports filtering by various fields, sorting and search.',
        summary: 'Get paginated countries with filtering, sorting and search',
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
        description: 'Countries list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CountryListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: CountryFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: CountrySearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->json(
            $this->apiCountryService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            )
        );
    }

    #[Route(name: 'api_admin_countries_store', methods: ['POST'])]
    #[OA\Post(
        description: 'Adding a new country',
        summary: 'Adding a new country'
    )]
    #[OA\RequestBody(
        description: 'Country data to store',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Belarus'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Country was store successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CountryResponse::class),
            type: 'object'
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function store(StoreCountryRequest $request): JsonResponse
    {
        try {
            return $this->json([
                    $this->apiCountryService->store(new StoreCountryDTO(...$request->toArray()))
                ]
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_countries_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns country details',
        summary: 'Get country details'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Country ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Country details retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: CountryResponse::class),
            type: 'object'
        )
    )]
    public function show(Country $country): JsonResponse
    {
        return $this->json(
            $this->apiCountryService->show($country)
        );
    }

    #[Route('/{id}', name: 'api_admin_countries_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Update country information. Only provided fields will be updated.',
        summary: 'Update country'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Country ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\RequestBody(
        description: 'Country data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Norway', nullable: true),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Country updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Country updated successfully'),
                new OA\Property(
                    property: 'country',
                    ref: new Model(type: CountryResponse::class)
                )
            ]
        )
    )]
    #[ValidationErrorResponse(field: 'name')]
    public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        try {
            return $this->json([
                'country' => $this->apiCountryService->update(
                    new UpdateCountryDTO(...$request->toArray()),
                    $country)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_countries_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Soft delete country by ID.',
        summary: 'Delete country'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Country ID to delete',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Country deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Country deleted successfully')
            ]
        )
    )]
    public function delete(Country $country): JsonResponse
    {
        try{
            $this->apiCountryService->delete($country);

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