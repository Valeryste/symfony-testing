<?php

namespace App\Repositories;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PermissionRepository extends BaseRepository
{
    protected function model(): string
    {
        return Permission::class;
    }

    public function check(string $routeName, User $user): bool
    {
        $role = $user->role;

        if ($role->name === RoleEnum::ADMIN->value) {
            return true;
        }

        return $role->permissions()
            ->where('route', $routeName)
            ->exists();
    }

    public function findByRoute(string $routeName): Model|Permission|null
    {
        return $this->model
            ->newQuery()
            ->where('route', $routeName)
            ->first();
    }

    public function create(array $data): Model|Permission
    {
        return $this->model
            ->newQuery()
            ->create($data);
    }

    public function update(Permission $permission, array $data): void
    {
        $permission->update($data);
    }
}
