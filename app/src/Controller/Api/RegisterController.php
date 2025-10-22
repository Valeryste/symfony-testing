<?php

namespace App\Controller\Api;

use App\DTO\RegisterFormDTO;
use App\Repository\UserRepository;
use App\Request\RegistrationRequest;
use App\Service\JwtTokenService;
use App\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly RegistrationService $registerService,
        private readonly JwtTokenService $jwtTokenService,
        private readonly UserRepository $userRepository
    ) {}

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(RegistrationRequest $request) : JsonResponse
    {
        $data = [
            'username' => $request->getUsername(),
            'plainPassword' => $request->getPassword(),
            'email' => $request->getEmail()
        ];

        try {
            if ($this->userRepository->findOneBy(['username' => $data['username']])) {
                return $this->json([
                    'error' => 'This username is already taken.'
                ], 400);
            }

            if ($this->userRepository->findOneBy(['email' => $data['email']])) {
                return $this->json([
                    'error' => 'This email is already taken.'
                ], 400);
            }

            $user = $this->registerService->register(new RegisterFormDTO(...$data));

            return $this->json([
                'message' => 'Registration successful',
                ... $this->jwtTokenService->createAuthResponse($user)
            ], 201);

        } catch (\Exception $e)
        {
            return $this->json([
                'error' => $e->getMessage()
            ], 401);
        }
    }

}