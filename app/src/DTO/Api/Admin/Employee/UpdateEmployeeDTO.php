<?php

namespace App\DTO\Api\Admin\Employee;

use App\DTO\BaseDTO;

class UpdateEmployeeDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $surname,
        public readonly ?string $phone,
        public readonly ?string $position,
        public readonly ?string $email,
        public readonly ?bool $isDismissed,
        public readonly ?int $shopId
    ) {
    }
}