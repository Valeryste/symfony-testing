<?php

namespace App\DTO\Api\Admin\Country;

use App\DTO\BaseDTO;

final class UpdateCountryDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
    ) {
    }
}