<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PasswordMatch extends Constraint
{
    public string $message = 'Les mots de passe ne correspondent pas.';
}
