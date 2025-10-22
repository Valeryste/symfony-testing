<?php

namespace App\Controller\Api;

use App\DTO\LoginFormDTO;
use App\Request\LoginRequest;
use App\Service\JwtTokenService;
use App\Service\LoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly LoginService $loginService,
        private readonly JwtTokenService $jwtTokenService
    ){}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
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
            ], 401);
        }
    }
}