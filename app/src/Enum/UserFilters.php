<?php

namespace App\Enum;

use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManager;

enum UserFilters: string
{
    case WITH_DELETED = 'with_deleted';

    case ONLY_ACTIVE = 'only_active';

    case BY_ROLE = 'by_role';

    public function getField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::ONLY_ACTIVE => 'isActive',
            self::BY_ROLE => 'role'
        };
    }

    public static function getFilterCases(): array
    {
        return [
            self::WITH_DELETED,
            self::ONLY_ACTIVE,
            self::BY_ROLE
        ];
    }

    public function translateValueToRu(): string
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