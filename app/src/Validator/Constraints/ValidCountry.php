<?php

namespace App\Validator\Constraints;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidCountry
{
    public string $message = 'Such a country does not exist';
}