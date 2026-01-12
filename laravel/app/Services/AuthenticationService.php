<?php

namespace App\Services;

use App\DTO\Authentication\LoginDTO;
use App\DTO\Authentication\RegisterDTO;
use App\Enums\RoleEnum;
use App\Http\Resources\Authentication\LoginResource;
use App\Http\Resources\Authentication\RegisterResource;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationService extends BaseService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RoleRepository $roleRepository
    ) {
    }

    public function register(RegisterDTO $registerDTO): array
    {
        $user = DB::transaction(function () use ($registerDTO) {
            $user = $this->userRepository->create((array)$registerDTO);

            $userRole = $this->roleRepository->findByName(RoleEnum::USER);
            $user->role()->associate($userRole);

            $user->save();

            return $user;
        });

        return [
            'token' => $user->createToken('auth-token')->plainTextToken,
            'user' => new RegisterResource($user)
        ];
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginDTO $loginDTO): array
    {
        $user = $this->userRepository->findByUsername($loginDTO->username);

        if(!Hash::check($loginDTO->password, $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'password' => ['Password is incorrect'],
            ]);
        }

       return [
           'token' => $user->createToken('auth-token')->plainTextToken,
           'user' => new LoginResource($user)
       ];
    }

    public function logout(User $user): int
    {
        return $this->userRepository->deleteAccessTokens($user);
    }
}
