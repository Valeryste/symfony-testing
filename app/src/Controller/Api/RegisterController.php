<?php

namespace App\Controller\Api;

use App\DTO\RegisterFormDTO;
use App\Request\RegistrationRequest;
use App\Service\JwtTokenService;
use App\Service\RegistrationService;
use Nelmio\ApiDocBundle\Attribute\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly RegistrationService $registrationService,
        private readonly JwtTokenService     $jwtTokenService
    ) {
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    #[OA\Tag(name: 'Authentication')]
    #[Security([])]
    #[OA\RequestBody(
        description: 'Register credentials',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'username', type: 'string', example: 'user123'),
                new OA\Property(property: 'password', type: 'string', example: 'password123'),
                new OA\Property(property: 'email', type: 'string', example: 'test@tewyyyrqoirwst.com')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Registration successful',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Registration successful'),
                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...'),
                new OA\Property(property: 'user', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'username', type: 'string', example: 'user123'),
                    new OA\Property(property: 'email', type: 'string', example: 'test@tewyyyrqoirwst.com'),
                    new OA\Property(property: 'role', type: 'string', example: 'ROLE_ADMIN')
                ], type: 'object')
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'validation failed'),
                new OA\Property(
                    property: 'errors',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'property', type: 'string', example: 'username'),
                            new OA\Property(property: 'value', type: 'string', example: ''),
                            new OA\Property(property: 'message', type: 'string', example: 'field username is required')
                        ],
                        type: 'object'
                    )
                )
            ]
        )
    )]
    public function register(RegistrationRequest $request): JsonResponse
    {
        $data = [
            'username' => $request->getUsername(),
            'plainPassword' => $request->getPassword(),
            'email' => $request->getEmail()
        ];

        try {
            $user = $this->registrationService->register(new RegisterFormDTO(...$data));

            return $this->json([
                'message' => 'Registration successful',
                ... $this->jwtTokenService->createAuthResponse($user)
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 401);
        }
    }

}