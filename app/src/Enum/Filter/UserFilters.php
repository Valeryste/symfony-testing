<?php

namespace App\Enum\Filter;

enum UserFilters: string
{
    case WITH_DELETED = 'deletedAt';

    case ONLY_ACTIVE = 'isActive';

    case BY_ROLE = 'role';

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_ACTIVE,
            self::BY_ROLE
        ];
    }

    public function outputInTemplate(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::ONLY_ACTIVE => 'Только активные',
            self::BY_ROLE => 'По роли'
        };
    }

    public function getFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::ONLY_ACTIVE => 'bool',
            self::BY_ROLE => 'int'
        };
    }

    public function getType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_ACTIVE => 'checkbox',
            self::BY_ROLE => 'select'
        };
    }

    public function getTemplateVariable(): string
    {
        return match($this) {
            self::BY_ROLE => 'roles',
            default => '',
        };
    }
}