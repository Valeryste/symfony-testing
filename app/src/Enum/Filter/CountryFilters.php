<?php

namespace App\Enum\Filter;

enum CountryFilters: string
{
    case WITH_DELETED = 'deletedAt';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED
        ];
    }

    public function outputInTemplate(): string
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
