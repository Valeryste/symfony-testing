<?php

namespace App\Validator\Constraints;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidCity
{
    public string $message = 'Such a city does not exist';
}