<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->makeModel();
    }

    /**
     * @return string
     */
    abstract protected function model(): string;

    /**
     * @return Model
     */
    final public function makeModel(): Model
    {
        return app($this->model());
    }
    public function getList(array $data): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $this->applySearch(
            query: $query,
            searchTerm: $data['search']
        );

        $this->applySort(
            query: $query,
            sortBy: $data['sortBy'],
            sortOrder: $data['sortOrder']
        );

        return $query->paginate(
            perPage: $data['perPage'],
            page: $data['page']
        );
    }

    public function applySearch(Builder $query, ?string $searchTerm): Builder
    {
        $fields = $query->getModel()->getSearchableFields();

        return $query->when(
            value: !empty($searchTerm),
            callback: function ($query) use ($searchTerm, $fields) {
                $query->where(function ($query) use ($searchTerm, $fields) {
                    foreach ($fields as $field) {
                        $query->orWhere($field, 'like', "%{$searchTerm}%");
                    }
                });
            }
        );
    }

    public function applySort(Builder $query, string $sortBy, string $sortOrder): Builder
    {
        $table = $query->getModel()->getTable();
        $columns = Schema::getColumnListing($table);

        if (!in_array($sortBy, $columns)) {
            return $query->orderBy('created_at', 'desc');
        }

        return $query->orderBy($sortBy, $sortOrder);
    }
}
