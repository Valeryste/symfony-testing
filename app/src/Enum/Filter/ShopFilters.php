<?php

namespace App\Enum\Filter;

enum ShopFilters: string
{
    case WITH_DELETED = 'deletedAt';

    case BY_CITY = 'city';

    case BY_COUNTRY = 'city.country';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_CITY,
            self::BY_COUNTRY
        ];
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox',
            self::BY_CITY, self::BY_COUNTRY => 'select',
        };
    }

    public function outputInTemplate(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_CITY => 'По городу',
            self::BY_COUNTRY => 'По стране'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_CITY => 'int',
            self::BY_COUNTRY => 'county'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_CITY => 'cities',
            self::BY_COUNTRY => 'countries',
            default => '',
        };
    }

}