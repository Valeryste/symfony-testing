<?php

namespace App\Enum\Search;

enum EmployeeSearch: string
{
    case NAME = 'name';

    case SURNAME = 'surname';

    case EMAIL = 'email';

    case POSITION = 'position';

    public static function getSearchCases(): array
    {
        return [
            self::NAME->value,
            self::SURNAME->value,
            self::EMAIL->value,
            self::POSITION->value
        ];
    }

}