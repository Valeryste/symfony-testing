<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum EmployeeFilters: string implements FilterEnumInterface
{
    case WITH_DELETED = 'deletedAt';

    case BY_SHOP = 'shop';

    case ONLY_DISMISSED = 'isDismissed';

    public static function getAvailableFilters(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_DISMISSED,
            self::BY_SHOP
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_DISMISSED => 'checkbox',
            self::BY_SHOP => 'select'
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::BY_SHOP => 'По магазину',
            self::ONLY_DISMISSED => 'Только уволенные'
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_SHOP =>  'int',
            self::ONLY_DISMISSED => 'bool'
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::BY_SHOP => 'shop',
            self::ONLY_DISMISSED => 'isDismissed'
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
            self::BY_SHOP => 'shops',
            default => '',
        };
    }
}