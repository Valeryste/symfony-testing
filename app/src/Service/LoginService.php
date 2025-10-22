<?php

namespace App\Service;

use App\DTO\LoginFormDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginService extends BaseService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @throws \Exception
     */
    public function login(LoginFormDTO $loginFormDTO) : User
    {
        $user = $this->userRepository->findOneBy(['username' => $loginFormDTO->username]);

        if (!$user) {
            throw new \Exception('Invalid user');
        }

        if(!$user->isActive()) {
            throw new \Exception('User is blocked');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $loginFormDTO->password)) {
            throw new \Exception('Invalid password');
        }

        return $user;
    }

}