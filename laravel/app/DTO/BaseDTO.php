<?php

namespace App\DTO;

abstract class BaseDTO
{
    public function toArray(): array
    {
        return array_filter((array) $this, fn($value) => $value !== null);
    }
}
