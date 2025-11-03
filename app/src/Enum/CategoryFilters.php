<?php

namespace App\Enum;

enum CategoryFilters: string
{
    case WITH_DELETED = 'with_deleted';

    case BY_PARENT = 'by_parent';

    public function getField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::BY_PARENT => 'parent'
        };
    }

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_PARENT
        ];
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox',
            self::BY_PARENT => 'select'
        };
    }

    public function translateValueToRu(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_PARENT => 'По родителю'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_PARENT => 'int'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_PARENT => 'parentCategories'
        };
    }
}