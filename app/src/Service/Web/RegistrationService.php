<?php

namespace App\Service\Web;

use App\DTO\Web\RegisterFormDTO;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Service\BaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService extends BaseService
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RoleRepository              $roleRepository
    ) {}

    public function register(
        RegisterFormDTO $registerFormDTO
    ): User
    {
        $user = new User();

        $user
            ->setUsername($registerFormDTO->username)
            ->setEmail($registerFormDTO->email)
            ->setPlainPassword($registerFormDTO->plainPassword)
            ->setPassword(
                $this->passwordHasher->hashPassword($user, $registerFormDTO->plainPassword)
            );

        $user->setRole($this->roleRepository->findOneBy(['name' => 'Guest']));

        $user->eraseCredentials();

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return $user;
    }
}