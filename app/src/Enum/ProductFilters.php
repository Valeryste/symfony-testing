<?php

namespace App\Enum;

enum ProductFilters: string
{
    case WITH_DELETED = 'with_deleted';

    case BY_CATEGORY = 'by_category';

    public function getField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::BY_CATEGORY => 'categories'
        };
    }

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_CATEGORY
        ];
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox',
            self::BY_CATEGORY => 'select'
        };
    }

    public function translateValueToRu(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_CATEGORY => 'По категориям'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_CATEGORY => 'array'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_CATEGORY => 'categories'
        };
    }
}