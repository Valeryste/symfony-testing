<?php

namespace App\Enum;

enum CountryFilters: string
{
    case WITH_DELETED = 'with_deleted';

    public function getField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt'
        };
    }

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED
        ];
    }

    public function translateValueToRu(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime'
        };
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            default => '',
        };
    }
}
