<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    private const SEARCH_FIELDS = [
        'email',
        'username'
    ];

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

    public function getList(array $data): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $query = $this->applySearch(
            query: $query,
            fields: self::SEARCH_FIELDS,
            searchTerm:  $data['search']
        );

        $query = $this->applySort(
            query: $query,
            sortBy: $data['sortBy'],
            sortOrder: $data['sortOrder']
        );

        return $query->paginate(
            perPage: $data['perPage'],
            page: $data['page']
        );
    }
}
