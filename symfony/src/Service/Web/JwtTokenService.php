<?php

namespace App\Service\Web;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JwtTokenService
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager
    ) {}

    public function createToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }

    public function createAuthResponse(User $user): array
    {
        return [
            'token' => $this->createToken($user),
            'user' => $this->getUserData($user)
        ];
    }

    private function getUserData(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];
    }
}