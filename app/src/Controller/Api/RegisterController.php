<?php

namespace App\Controller\Api;

use App\DTO\RegisterFormDTO;
use App\Repository\UserRepository;
use App\Service\JwtTokenService;
use App\Service\RegistrationService;
use Symfony\Component\HttpFoundation\Request;
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
    public function register(Request $request) : JsonResponse
    {
        $dataRequest = json_decode($request->getContent(), true);

        //Валидацию надо будет сделать какую-то красивую
        if (empty($dataRequest['username']) || empty($dataRequest['email']) || empty($dataRequest['password'])) {
            return $this->json([
                'error' => 'Username, email and password are required'
            ], 400);
        }

        $data = [
            'username' => $dataRequest['username'],
            'plainPassword' => $dataRequest['password'],
            'email' => $dataRequest['email']
        ];

        try {
            //С этим тоже нужно что-то делать
            if ($this->userRepository->findOneBy(['username' => $data['username']])) {
                return $this->json([
                    'error' => 'This username is already taken.'
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