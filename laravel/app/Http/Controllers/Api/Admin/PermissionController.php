<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTO\Common\IndexDTO;
use App\DTO\Permission\StoreDTO;
use App\DTO\Permission\UpdateDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Common\IndexRequest;
use App\Http\Requests\Permission\StoreRequest;
use App\Http\Requests\Permission\UpdateRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

class PermissionController extends BaseController
{
    public function __construct(
        private readonly PermissionService $permissionService
    ) {
    }

    public function index(IndexRequest $request): JsonResponse
    {
        $indexDTO = new IndexDTO($request->validated());

        return response()->json($this->permissionService->getList($indexDTO));
    }

    public function getListRoute(): JsonResponse
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->map(fn($route) => ['name' => $route->getName()])
            ->filter(fn($route) => $route['name'] && str_starts_with($route['name'], 'app.'))
            ->values();

        return response()->json($routes);
    }

    public function store(StoreRequest $storeRequest): JsonResponse
    {
        try{
            $storeDTO = new StoreDTO(...$storeRequest->validated());

            return response()->json($this->permissionService->store($storeDTO), 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Permission $permission, UpdateRequest $updateRequest): JsonResponse
    {
        try{
            $updateDTO = new UpdateDTO(...$updateRequest->validated());

            return response()->json($this->permissionService->update($permission, $updateDTO));

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
