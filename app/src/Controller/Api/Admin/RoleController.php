<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Model\RoleListResponse;
use App\Service\Api\ApiRoleService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[Route('/api/admin/roles')]
#[IsGranted('ROLE_ADMIN')]
#[OA\Tag(name: 'Admin Roles')]
#[UnauthorizedResponse]
#[ForbiddenResponse]
class RoleController extends BaseController
{

    public function __construct(
        private readonly ApiRoleService $apiRoleService
    ) {
    }

    #[Route(name: 'api_admin_roles', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns list of roles',
        summary: 'Get list of roles',
    )]
    #[OA\Response(
        response: 200,
        description: 'Roles list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: RoleListResponse::class),
            type: 'object'
        )
    )]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->apiRoleService->getList()
        );
    }
}