<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;

class UpdateDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $email = null,
        public readonly ?int $is_active = null,
        public readonly ?int $role_id = null
    ) {
    }
}
