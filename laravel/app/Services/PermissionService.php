<?php

namespace App\Services;

use App\DTO\Common\IndexDTO;
use App\Http\Resources\Permission\PermissionCollection;
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
}
