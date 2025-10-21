<?php

namespace App\Controller\Api;

use App\DTO\LoginFormDTO;
use App\Service\JwtTokenService;
use App\Service\LoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly LoginService $loginService,
        private readonly JwtTokenService $jwtTokenService
    ){}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->json([
                'error' => 'Username and password are required'
            ], 400);
        }

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