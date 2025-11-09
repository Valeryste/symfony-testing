<?php

namespace App\Enum\Filter;

enum ProductFilters: string
{
    case WITH_DELETED = 'deletedAt';

    case BY_CATEGORIES = 'categories';

    case ONLY_ACTIVE = 'isActive';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_ACTIVE,
            self::BY_CATEGORIES
        ];
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_ACTIVE => 'checkbox',
            self::BY_CATEGORIES => 'select'
        };
    }

    public function outputInTemplate(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_CATEGORIES => 'По категориям',
            self::ONLY_ACTIVE => 'Только активные'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_CATEGORIES => 'array',
            self::ONLY_ACTIVE => 'bool'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_CATEGORIES => 'categories'
        };
    }
}