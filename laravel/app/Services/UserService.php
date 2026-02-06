<?php

namespace App\Services;

use App\DTO\Common\IndexDTO;
use App\DTO\User\UpdateDTO;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Repositories\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function get(int $id): UserResource
    {
        if($this->userRepository->findById($id) === null) {
            throw new NotFoundHttpException(message: "Not found user with {$id} id", code: 404);
        }

        return new UserResource($this->userRepository->findById($id));
    }

    public function update(int $id, UpdateDTO $updateDTO): UserResource
    {
        $user = $this->userRepository->findById($id);

        if($user === null) {
            throw new NotFoundHttpException(message: "Not found user with {$id} id", code: 404);
        }

        $this->userRepository->update($user, $updateDTO->toArray());

        return new UserResource($user);
    }

    public function delete(int $id): void
    {
        $user = $this->userRepository->findById($id);

        if($user === null) {
            throw new NotFoundHttpException(message: "Not found user with {$id} id", code: 404);
        }

        $this->userRepository->delete($user);
    }
}
