<?php

namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityFieldValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntityField) {
            throw new UnexpectedTypeException($constraint, UniqueEntityField::class);
        }

        if (empty($value)) {
            return;
        }

        $repository = $this->entityManager->getRepository($constraint->entityClass);

        $result = $repository->findOneBy([$constraint->field => $value ]);

        if ($result) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}