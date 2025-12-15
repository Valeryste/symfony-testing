<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum CountryFilters: string implements FilterEnumInterface
{
    case WITH_DELETED = 'deletedAt';

    public static function getAvailableFilters(): array
    {
        return [
            self::WITH_DELETED
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'checkbox'
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime'
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt'
        };
    }

    public function getOperator(mixed $value = null): string
    {
        return match($this) {
            self::WITH_DELETED => (int) $value == 1 ? 'IS NOT NULL' : 'IS NULL',
        };
    }

    public function getTemplateVariableName(): ?string
    {
        return match($this) {
            default => '',
        };
    }
}
