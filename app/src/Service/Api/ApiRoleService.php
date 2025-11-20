<?php

namespace App\Service\Api;

use App\Entity\Role;
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
                return $this->toResponse($role);
            },
            $this->roleRepository->findAll()
        );
    }

    public function toResponse(Role $role): RoleResponse
    {
        return new RoleResponse(
            id: $role->getId(),
            name: $role->getName()
        );
    }
}