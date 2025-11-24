<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Admin\User\UpdateUserDTO;
use App\Entity\User;
use App\Enum\Filter\UserFilters;
use App\Enum\Search\UserSearch;
use App\Model\User\UserListResponse;
use App\Model\User\UserResponse;
use App\Request\User\UpdateUserRequest;
use App\Service\Api\ApiUserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/users')]
#[IsGranted('ROLE_ADMIN')]
#[OA\Tag(name: 'Admin Users')]
#[UnauthorizedResponse]
#[ForbiddenResponse]
class UserController extends BaseController
{
    public function __construct(
        private readonly ApiUserService $apiUserService,
    ) {
    }

    #[Route(name: 'api_admin_users_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns paginated list of users. Supports filtering by various fields, sorting and search.',
        summary: 'Get paginated list of users with filtering, sorting and search',
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
                description: 'Search term for username or email',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'filters[role]',
                description: 'Filter by role ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'filters[deletedAt]',
                description: 'Filter by deleted',
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
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Users list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: UserListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: UserFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: UserSearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->json(
            $this->apiUserService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            )
        );
    }


    #[Route('/{id}', name: 'api_admin_users_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns user details for editing form',
        summary: 'Get user details for editing'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'User ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'User details retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: UserResponse::class),
            type: 'object'
        )
    )]
    public function show(User $user): JsonResponse
    {
        return $this->json([
            $this->apiUserService->show($user)
        ]);
    }

    #[Route('/{id}', name: 'api_admin_users_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Update user information. Only provided fields will be updated.',
        summary: 'Update user'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'User ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\RequestBody(
        description: 'User data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'username', type: 'string', example: 'new_username', nullable: true),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'new@example.com', nullable: true),
                new OA\Property(property: 'isActive', type: 'boolean', example: true, nullable: true),
                new OA\Property(property: 'roleId', type: 'integer', example: 2, nullable: true),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'User updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'User updated successfully'),
                new OA\Property(
                    property: 'user',
                    ref: new Model(type: UserResponse::class)
                )
            ]
        )
    )]
    #[ValidationErrorResponse]
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            return $this->json([
                'message' => 'User updated successfully',
                'user' => $this->apiUserService->update(
                    new UpdateUserDTO(...$request->toArray()),
                    $user
                )
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_users_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Soft delete user by ID.',
        summary: 'Delete user',
        tags: ['Admin Users']
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'User ID to delete',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'User deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'User deleted successfully')
            ]
        )
    )]
    public function delete(User $user): JsonResponse
    {
        try {
            $this->apiUserService->delete($user);

            return $this->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}