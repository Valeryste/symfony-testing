<?php

namespace App\Traits;

trait Searchable
{
    public function getSearchableFields(): array
    {
        if (empty($this->searchableFields)) {
            return $this->getDefaultSearchableFields();
        }

        return $this->searchableFields;
    }

    protected function getDefaultSearchableFields(): array
    {
        return array_filter($this->getFillable(), function ($field) {
            return is_string($field);
        });
    }
}
