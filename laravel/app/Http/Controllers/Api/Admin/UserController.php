<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTO\Common\IndexDTO;
use App\DTO\User\UpdateDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Common\IndexRequest;
use App\Http\Requests\User\UpdateRequest;
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

    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->userService->get($id));

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function update(UpdateRequest $updateRequest, int $id): JsonResponse
    {
        try {
            $updateDTO = new UpdateDTO(...$updateRequest->validated());

            return response()->json($this->userService->update($id, $updateDTO));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->userService->delete($id);

            return response()->json();
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
