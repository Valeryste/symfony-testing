<?php

namespace App\Enum;

enum CityFilters: string
{
    case WITH_DELETED = 'with_deleted';

    case BY_COUNTRY = 'by_country';

    public function getField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::BY_COUNTRY => 'country'
        };
    }

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_COUNTRY
        ];
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox',
            self::BY_COUNTRY => 'select'
        };
    }

    public function translateValueToRu(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_COUNTRY => 'По стране'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_COUNTRY => 'int'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_COUNTRY => 'countries',
            default => '',
        };
    }
}