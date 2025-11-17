<?php

namespace App\Controller\Api\Authentication;

use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Authentication\LoginFormDTO;
use App\Request\Authentication\LoginRequest;
use App\Service\JwtTokenService;
use App\Service\LoginService;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly LoginService $loginService,
        private readonly JwtTokenService $jwtTokenService
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    #[OA\Tag(name: 'Authentication')]
    #[Security([])]
    #[OA\RequestBody(
        description: 'Login credentials',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'username', type: 'string', example: 'user123'),
                new OA\Property(property: 'password', type: 'string', example: 'password123')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Login successful',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...'),
                new OA\Property(property: 'user', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'username', type: 'string', example: 'user123'),
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'role', type: 'string', example: 'ROLE_ADMIN')
                ], type: 'object')
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Invalid password|username')
            ]
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'User is blocked',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'User is no active')

            ]
        )
    )]
    #[ValidationErrorResponse]
    public function login(LoginRequest $request): JsonResponse
    {
        $data = [
            'username' => $request->getUsername(),
            'password' => $request->getPassword()
        ];

        try {
            $user = $this->loginService->login(new LoginFormDTO(...$data));

            return $this->json($this->jwtTokenService->createAuthResponse($user));

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}