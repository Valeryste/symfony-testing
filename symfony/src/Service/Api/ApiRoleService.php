<?php

namespace App\Service\Api;

use App\Model\Role\RoleResponse;
use App\Repository\RoleRepository;
use App\Service\BaseService;

class ApiRoleService extends BaseService
{
    public function __construct(
        private readonly RoleRepository $roleRepository
    ) {
    }

    public function getList(): array
    {
        return array_map(
            function ($role) {
                return RoleResponse::fromEntity($role);
            },
            $this->roleRepository->findAll()
        );
    }
}