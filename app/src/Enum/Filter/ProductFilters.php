<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum ProductFilters: string implements FilterEnumInterface
{
    case WITH_DELETED = 'deletedAt';

    case BY_CATEGORIES = 'categories';

    case ONLY_ACTIVE = 'isActive';

    public static function getAvailableFilters(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_ACTIVE,
            self::BY_CATEGORIES
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_ACTIVE => 'checkbox',
            self::BY_CATEGORIES => 'select'
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_CATEGORIES => 'По категориям',
            self::ONLY_ACTIVE => 'Только активные'
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_CATEGORIES => 'array',
            self::ONLY_ACTIVE => 'bool'
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'createdAt',
            self::BY_CATEGORIES => 'categories',
            self::ONLY_ACTIVE => 'isActive'
        };
    }

    public function getOperator(mixed $value = null): string
    {
        return match($this) {
            self::WITH_DELETED => (int) $value == 1 ? 'IS NOT NULL' : 'IS NULL',
            self::BY_CATEGORIES => 'IN',
            default => '='
        };
    }

    public function getTemplateVariableName(): ?string
    {
        return match($this) {
            self::BY_CATEGORIES => 'categories'
        };
    }
}