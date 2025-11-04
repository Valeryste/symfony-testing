<?php

namespace App\Enum\Filter;

enum CategoryFilters: string
{
    case WITH_DELETED = 'deletedAt';

    case ONLY_ACTIVE = 'isActive';

    case BY_PARENT = 'parent';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_ACTIVE,
            self::BY_PARENT
        ];
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_ACTIVE => 'checkbox',
            self::BY_PARENT => 'select'
        };
    }

    public function outputInTemplate(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_PARENT => 'По родителю',
            self::ONLY_ACTIVE => 'Только активные'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_PARENT => 'int',
            self::ONLY_ACTIVE => 'bool',
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_PARENT => 'parentCategories'
        };
    }
}