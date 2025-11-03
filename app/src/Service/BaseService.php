<?php

namespace App\Service;

abstract class BaseService
{
    protected function hasFilter(array $filters, string $fieldName, $value = null): bool
    {
        foreach ($filters as $filter) {
            if ($filter['field'] === $fieldName) {
                return isset($value) || $filter['value'] == $value;
            }
        }
        return false;
    }
}