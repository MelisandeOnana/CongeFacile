<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PasswordMatch) {
            throw new \InvalidArgumentException(sprintf(
                'La contrainte doit être une instance de %s.',
                PasswordMatch::class
            ));
        }

        if ($value['newPassword'] !== $value['confirmPassword']) {
            $this->context->buildViolation($constraint->message)
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }
}
