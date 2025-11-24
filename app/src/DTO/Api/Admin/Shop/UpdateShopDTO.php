<?php

namespace App\DTO\Api\Admin\Shop;

use App\DTO\BaseDTO;

class UpdateShopDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?int $isOpen,
        public readonly ?string $address,
        public readonly ?int $cityId
    ) {
    }
}