<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class RequiresHTTPValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint):void
    {
        if (!$constraint instanceof RequiresHTTP) {
            throw new UnexpectedTypeException($constraint, RequiresHTTP::class);
        }


        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        if (str_starts_with(strtoupper($value), 'HTTP://') || str_starts_with(strtoupper($value), 'HTTPS://')) {
            return;
        }

        $this->context->buildViolation($constraint->message)->setParameter('{{ url }}', $value)->addViolation();



    }
}