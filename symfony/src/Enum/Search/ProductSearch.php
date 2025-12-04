<?php

namespace App\Enum\Search;

enum ProductSearch: string
{
    case NAME = 'name';

    public static function getSearchCases(): array
    {
        return [
            self::NAME->value
        ];
    }

}
