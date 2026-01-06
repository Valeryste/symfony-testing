<?php

namespace App\Http\Middleware;

use App\Repositories\PermissionRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        private readonly PermissionRepository $permissionRepository
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if(!$this->permissionRepository->findByRoute($request->route()->getName())) {
            return $next($request);
        }

        if(!$this->permissionRepository->check($request->route()->getName(), $request->user()))
        {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return $next($request);
    }
}
