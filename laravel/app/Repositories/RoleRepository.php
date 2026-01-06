<?php

namespace App\Repositories;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleRepository extends BaseRepository
{
    protected function model(): string
    {
        return Role::class;
    }

    public function findByName(RoleEnum $name):  Model|Role
    {
        return $this->model
            ->newQuery()
            ->where('name', $name)
            ->firstOrFail();
    }
}
