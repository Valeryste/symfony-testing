<?php

namespace App\DTO\Api\Admin\City;

use App\DTO\BaseDTO;

final class UpdateCityDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?int $countryId,
    ) {
    }
}