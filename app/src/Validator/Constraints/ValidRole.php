<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidRole extends Constraint
{
    public string $message = 'Such a role does not exist';
}