<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    abstract public function getList(array $data);

    public function applySearch(Builder $query, array $fields, ?string $searchTerm): Builder
    {
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
