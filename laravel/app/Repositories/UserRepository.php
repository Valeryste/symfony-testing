<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{
    protected function model(): string
    {
        return User::class;
    }

    public function create(array $data): Model|User
    {
        return $this->model
            ->newQuery()
            ->create($data);
    }

    public function findByUsername(string $username): Model|User
    {
        return $this->model
            ->newQuery()
            ->where('username', $username)
            ->firstOrFail();
    }

    public function deleteAccessTokens(User $user): int
    {
        return $user->tokens()
            ->delete();
    }

    public function findById(int $id): Model|User|null
    {
        return $this->model
            ->newQuery()
            ->find($id);
    }

    public function update(User $user, array $data): void
    {
        $user->update($data);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
