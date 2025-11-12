<?php

namespace App\Enum\Search;

enum ShopSearch: string
{
    case NAME = 'name';

    case ADDRESS = 'address';

    public static function getSearchCases(): array
    {
        return [
            self::NAME->value,
            self::ADDRESS->value
        ];
    }

}