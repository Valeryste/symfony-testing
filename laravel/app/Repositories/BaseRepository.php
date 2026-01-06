<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

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
}
