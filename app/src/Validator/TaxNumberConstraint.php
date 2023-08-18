<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class TaxNumberConstraint extends Constraint
{
    public string $message = 'This value: {{ value }} is not valid.';

    public iterable $countryCodeAsArray = [];

    public string $patternCriteria = "";
}