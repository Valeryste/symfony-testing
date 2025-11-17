<?php

namespace App\DTO\Api\Admin\User;

use App\DTO\BaseDTO;

class UpdateUserDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $username,
        public readonly ?string $email,
        public readonly ?int $roleId,
        public readonly ?bool $isActive
    ) {

    }
}