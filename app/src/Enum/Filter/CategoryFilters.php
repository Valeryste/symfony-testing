<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum CategoryFilters: string implements FilterEnumInterface
{
    case WITH_DELETED = 'deletedAt';

    case ONLY_ACTIVE = 'isActive';

    case BY_PARENT = 'parent';

    public static function getAvailableFilters(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_ACTIVE,
            self::BY_PARENT
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_ACTIVE => 'checkbox',
            self::BY_PARENT => 'select'
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_PARENT => 'По родителю',
            self::ONLY_ACTIVE => 'Только активные'
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_PARENT => 'int',
            self::ONLY_ACTIVE => 'bool',
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::ONLY_ACTIVE => 'isActive',
            self::BY_PARENT => 'parent'
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
            self::BY_PARENT => 'parentCategories',
            default => ''
        };
    }
}