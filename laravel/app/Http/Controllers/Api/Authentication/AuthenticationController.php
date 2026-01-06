<?php

namespace App\Http\Controllers\Api\Authentication;

use App\DTO\Authentication\LoginDTO;
use App\DTO\Authentication\RegisterDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Models\User;
use App\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends BaseController
{
    public function __construct(
       private readonly AuthenticationService $authenticationService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $registerDTO = new RegisterDTO(...$request->validated());

        return response()->json($this->authenticationService->register($registerDTO), 201);
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $loginDTO = new LoginDTO(...$request->validated());

        return response()->json($this->authenticationService->login($loginDTO));
    }

    public function user(Request $request): User
    {
        return $request->user();
    }

    public function logout(Request $request): Response
    {
        $this->authenticationService->logout($request->user());

        return response()->noContent();
    }
}
