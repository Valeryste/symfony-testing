<?php

namespace App\Enum\Filter;

use App\Interface\FilterEnumInterface;

enum CountHistoryFilters: string implements FilterEnumInterface
{
    case DATE_FROM  = 'createdAtFrom';

    case DATE_TO = 'createdAtTo';

    public static function getAvailableFilters(): array
    {
        return [
            self::DATE_FROM,
            self::DATE_TO
        ];
    }

    public function getInputType(): string
    {
        return match($this) {
            self::DATE_FROM, self::DATE_TO => 'date'
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::DATE_FROM => 'С даты',
            self::DATE_TO => 'По дату'
        };
    }

    public function getEntityFieldType(): string
    {
        return match($this) {
            self::DATE_FROM, self::DATE_TO => 'datetime'
        };
    }

    public function getEntityField(): string
    {
        return match($this) {
            self::DATE_FROM, self::DATE_TO => 'createdAt'
        };
    }

    public function getOperator(mixed $value = null): string
    {
        return match($this) {
            self::DATE_FROM => '>=',
            self::DATE_TO => '<='
        };
    }

    public function getTemplateVariableName(): ?string
    {
        return match($this) {
            default => ''
        };
    }
}