<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Model\PriceHistory\PriceHistoryListResponse;
use App\Service\Api\ApiPriceHistoryService;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Enum\Filter\PriceHistoryFilters;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[IsGranted('ROLE_ADMIN')]
#[Route('api/admin/products/{productId}/price-history')]
#[OA\Tag(name: 'Admin Products Price History')]
#[ForbiddenResponse]
#[UnauthorizedResponse]
class PriceHistoryController extends BaseController
{
    public function __construct(
        private readonly ApiPriceHistoryService $apiPriceHistoryService
    ) {
    }

    #[Route(name: 'api_admin_products_price_history', methods: ['GET'])]
    #[OA\Get(
        description: 'Get history of product price changes with filtering by date range',
        summary: 'Get product price history',
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
            ),
            new OA\Parameter(
                name: 'filters[createdAtFrom]',
                description: 'Filter price changes created after this date (inclusive). Format: YYYY-MM-DD',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    format: 'date',
                    example: '2024-01-01'
                )
            ),
            new OA\Parameter(
                name: 'filters[createdAtTo]',
                description: 'Filter price changes created before this date (inclusive). Format: YYYY-MM-DD',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    format: 'date',
                    example: '2024-12-31'
                )
            ),
        ]
    )]
    #[OA\Parameter(
        name: 'productId',
        description: 'Product ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Price history retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: PriceHistoryListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request, int $productId): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: PriceHistoryFilters::class
        );

        try {
            return $this->json(
                $this->apiPriceHistoryService->getListByProduct(
                    productId: $productId,
                    page: $request->query->getInt('page', 1),
                    filters: $transformedFilters,
                    sorts: $request->query->all()['sorts'] ?? ['createdAt' => 'desc']
                )
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }
}