<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum CityFilters: string implements FilterEnumInterface
{
    case WITH_DELETED = 'deletedAt';

    case BY_COUNTRY = 'country';

    public static function getAvailableFilters(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_COUNTRY
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox',
            self::BY_COUNTRY => 'select'
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_COUNTRY => 'По стране'
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_COUNTRY => 'int'
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::BY_COUNTRY => 'country'
        };
    }

    public function getOperator(mixed $value = null): string
    {
        return match($this) {
            self::WITH_DELETED => (int) $value == 1 ? 'IS NOT NULL' : 'IS NULL',
            default => '='
        };
    }

    public function getTemplateVariableName(): ?string
    {
        return match($this) {
            self::BY_COUNTRY => 'countries',
            default => '',
        };
    }
}