<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\AssignOp\Mod;

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
}
