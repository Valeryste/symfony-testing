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

class AuthenticationController extends BaseController
{
    public function __construct(
        private readonly AuthenticationService $authenticationService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $registerDTO = new RegisterDTO(...$request->validated());

            return response()->json($this->authenticationService->register($registerDTO), 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $loginDTO = new LoginDTO(...$request->validated());

            return response()->json($this->authenticationService->login($loginDTO));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
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
