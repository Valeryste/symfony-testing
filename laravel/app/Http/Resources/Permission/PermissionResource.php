<?php

namespace App\Http\Resources\Permission;

use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'route' => $this->route,
            'role' => new RoleResource($this->role)
        ];
    }
}
