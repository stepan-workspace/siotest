<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNumberConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private ?Constraint $constraint
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->constraint = $constraint;
        if (!$this->isValid($value) && $value) {
            $this->context->buildViolation($this->constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isValid(mixed $value): bool
    {
        $list = match (true) {
            !empty($this->constraint->countryCodeAsArray) => join('|', $this->constraint->countryCodeAsArray),
            default => ""
        };

        $pattern = match (true) {
            !empty($this->constraint->patternCriteria) => $this->constraint->patternCriteria,
            default => "/^({$list})[a-z0-9]+$/i"
        };

        return (bool)match (true) {
            !empty($list) => preg_match($pattern, $value),
            default => true
        };
    }
}