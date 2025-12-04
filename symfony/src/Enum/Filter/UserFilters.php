<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum UserFilters: string implements FilterEnumInterface
{
    case WITH_DELETED = 'deletedAt';

    case ONLY_ACTIVE = 'isActive';

    case BY_ROLE = 'role';

    public static function getAvailableFilters(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_ACTIVE,
            self::BY_ROLE
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_ACTIVE => 'checkbox',
            self::BY_ROLE => 'select'
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::ONLY_ACTIVE => 'Только активные',
            self::BY_ROLE => 'По роли'
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::ONLY_ACTIVE => 'bool',
            self::BY_ROLE => 'int'
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::ONLY_ACTIVE => 'isActive',
            self::BY_ROLE => 'role'
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
            self::BY_ROLE => 'roles',
            default => '',
        };
    }
}