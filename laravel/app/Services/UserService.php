<?php

namespace App\Services;

use App\DTO\Common\IndexDTO;
use App\Http\Resources\User\UserCollection;
use App\Repositories\UserRepository;

class UserService extends BaseService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function getList(IndexDTO $indexDTO): UserCollection
    {
        return new UserCollection($this->userRepository->getList($indexDTO->toRepositoryParams()));
    }
}
