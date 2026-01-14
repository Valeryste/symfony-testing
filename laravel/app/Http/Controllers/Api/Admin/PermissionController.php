<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTO\Common\IndexDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Common\IndexRequest;
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
        $routes = collect(Route::getRoutes()->getRoutes());

        $formattedRoutes = $routes->map(function ($route) {
            return [
                'name' => $route->getName(),
            ];
        })->filter(function ($route) {
            return str_starts_with($route['name'], 'app.');
        })->values();

        return response()->json($formattedRoutes);
    }
}
