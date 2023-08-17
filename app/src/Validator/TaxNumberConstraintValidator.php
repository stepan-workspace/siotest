<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNumberConstraintValidator extends ConstraintValidator
{

    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$this->isValid($value, $constraint)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isValid(mixed $value, Constraint $constraint)
    {
        $list = match(true) {
            !empty($constraint->countryCodeAsArray) => join('|', $constraint->countryCodeAsArray),
            default => "",
        };

        $pattern = match(true) {
            !empty($constraint->patternCriteria) => $constraint->patternCriteria,
            default => "/^({$list})[a-z0-9]+$/i",
        };

        return match(true) {
            !empty($list) => preg_match($pattern, $value),
            default => true,
        };
    }
}