<?php

namespace App\DTO\Api\Admin\Country;

use App\DTO\BaseDTO;

class UpdateCountryDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
    ) {
    }
}