<?php

namespace App\Services;

use App\DTO\Common\IndexDTO;
use App\DTO\Permission\StoreDTO;
use App\DTO\Permission\UpdateDTO;
use App\Http\Resources\Permission\PermissionCollection;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Permission;
use App\Repositories\PermissionRepository;

class PermissionService extends BaseService
{
    public function __construct(
       private readonly PermissionRepository $permissionRepository
    ) {
    }

    public function getList(IndexDTO $indexDTO): PermissionCollection
    {
        return new PermissionCollection($this->permissionRepository->getList($indexDTO->toRepositoryParams()));
    }

    public function store(StoreDTO $storeDTO): PermissionResource
    {
        return new PermissionResource($this->permissionRepository->create((array) $storeDTO));
    }

    public function update(Permission $permission, UpdateDTO $updateDTO): PermissionResource
    {
        $this->permissionRepository->update($permission, (array) $updateDTO);

        return new PermissionResource($permission);
    }
}
