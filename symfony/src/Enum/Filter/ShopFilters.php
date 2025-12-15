<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum ShopFilters: string implements FilterEnumInterface
{
    case WITH_DELETED = 'deletedAt';

    case BY_CITY = 'city';

    case BY_COUNTRY = 'city.country';

    case ONLY_OPEN = 'isOpen';

    public static function getAvailableFilters(): array
    {
        return [
            self::WITH_DELETED,
            self::BY_CITY,
            self::BY_COUNTRY,
            self::ONLY_OPEN
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::WITH_DELETED, self::ONLY_OPEN => 'checkbox',
            self::BY_CITY, self::BY_COUNTRY => 'select',
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::WITH_DELETED => 'Удаленные',
            self::ONLY_OPEN => 'Только открытые',
            self::BY_CITY => 'По городу',
            self::BY_COUNTRY => 'По стране'
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::WITH_DELETED => 'datetime',
            self::BY_CITY => 'int',
            self::BY_COUNTRY => 'country',
            self::ONLY_OPEN => 'bool'
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::WITH_DELETED => 'deletedAt',
            self::BY_CITY => 'city',
            self::BY_COUNTRY => 'city.country',
            self::ONLY_OPEN => 'isOpen'
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
            self::BY_CITY => 'cities',
            self::BY_COUNTRY => 'countries',
            default => '',
        };
    }
}