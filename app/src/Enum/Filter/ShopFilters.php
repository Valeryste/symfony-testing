<?php

namespace App\Enum\Filter;

enum ShopFilters: string
{
    case WITH_DELETED = 'deletedAt';

    case BY_CITY = 'city';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_CITY
        ];
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox',
            self::BY_CITY => 'select'
        };
    }

    public function outputInTemplate(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_CITY => 'По городу'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_CITY => 'int'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_CITY => 'cities',
            default => '',
        };
    }

}