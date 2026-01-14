<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTO\User\IndexDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\User\IndexRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function index(IndexRequest $request): JsonResponse
    {
        $indexDTO = new IndexDTO($request->validated());

        return response()->json($this->userService->getList($indexDTO));
    }
}
