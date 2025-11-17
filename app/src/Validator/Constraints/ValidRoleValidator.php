<?php

namespace App\Validator\Constraints;

use App\Entity\Role;
use App\Validator\Constraints\ValidRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidRoleValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidRole) {
            throw new UnexpectedTypeException($constraint, ValidRole::class);
        }

        if (null === $value) {
            return;
        }

        $role = $this->entityManager->getRepository(Role::class)->find($value);

        if (!$role) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}