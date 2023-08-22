<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Validator class for checking country tax number.
 * Used in the form during data validation
 */
class TaxNumberConstraint extends Constraint
{
    public string $message = 'This value: {{ value }} is not valid.';

    public iterable $countryCodeAsArray = [];

    public string $patternCriteria = "";
}