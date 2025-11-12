<?php

namespace App\Enum\Filter;

enum EmployeeFilters: string
{
    case WITH_DELETED = 'deletedAt';

    case BY_SHOP = 'shop';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_SHOP
        ];
    }

    public function outputInTemplate(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_SHOP => 'По магазину'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_SHOP =>  'int'
        };
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox',
            self::BY_SHOP => 'select'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_SHOP => 'shops',
            default => '',
        };
    }

}