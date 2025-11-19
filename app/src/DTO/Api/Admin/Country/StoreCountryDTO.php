<?php

namespace App\DTO\Api\Admin\Country;

use App\DTO\BaseDTO;

class StoreCountryDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}