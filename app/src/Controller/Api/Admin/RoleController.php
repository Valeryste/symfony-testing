<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Service\Api\ApiRoleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/roles')]
#[IsGranted('ROLE_ADMIN')]
class RoleController extends BaseController
{

    public function __construct(
        private readonly ApiRoleService $apiRoleService
    ) {

    }

    #[Route(name: 'api_admin_roles')]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->apiRoleService->getList()
        );
    }

}