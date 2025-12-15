<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueEntityField extends Constraint
{
    public string $message = 'This value is already used.';
    public string $entityClass;
    public string $field;

    public function __construct(
        string $entityClass,
        string $field,
        ?string $message = null,
        array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        parent::__construct($options, $groups, $payload);

        $this->entityClass = $entityClass;
        $this->field = $field;

        if ($message) {
            $this->message = $message;
        }
    }

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return UniqueEntityFieldValidator::class;
    }

}