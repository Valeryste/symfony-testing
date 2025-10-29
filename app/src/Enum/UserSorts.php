<?php

namespace App\Enum;

enum UserSorts: string
{
    case ID = 'id';

    public static function getSortCases(): array
    {
        return [
            self::ID
        ];
    }

    public function translateValueToRu(): string
    {
        return match($this) {
            self::ID => 'По id'
        };
    }

    public function getDirectionToRu(): array
    {
        return match($this) {
            self::ID => ['по возрастанию', 'по убыванию']
        };
    }

    public function getDirection(): array
    {
        return match($this) {
            self::ID => ['ASC', 'DESC']
        };
    }
}
