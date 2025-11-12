<?php

namespace App\Enum\Filter;

use phpDocumentor\Reflection\Types\Self_;

enum EmployeeFilters: string
{
    case WITH_DELETED = 'deletedAt';

    case BY_SHOP = 'shop';

    case ONLY_DISMISSED = 'isDismissed';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_DISMISSED,
            self::BY_SHOP
        ];
    }

    public function outputInTemplate(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_SHOP => 'По магазину',
            self::ONLY_DISMISSED => 'Только уволенные'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_SHOP =>  'int',
            self::ONLY_DISMISSED => 'bool'
        };
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_DISMISSED => 'checkbox',
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