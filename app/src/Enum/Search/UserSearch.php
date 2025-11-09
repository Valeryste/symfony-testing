<?php

namespace App\Enum\Search;

enum UserSearch: string
{
    case USERNAME = 'username';

    case EMAIL = 'email';

    public static function getSearchCases(): array
    {
        return [
            self::USERNAME->value,
            self::EMAIL->value
        ];
    }
}