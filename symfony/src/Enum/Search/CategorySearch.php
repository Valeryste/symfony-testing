<?php

namespace App\Enum\Search;

enum CategorySearch: string
{
    case NAME = 'name';

    public static function getSearchCases(): array
    {
        return [
            self::NAME->value
        ];
    }

}
