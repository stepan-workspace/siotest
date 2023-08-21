<?php

namespace App\Service\Calculator;

use App\Service\Resolver\ResolverArrayInterface;

interface PriceBuilderDirectorInterface
{
    public function setBuilder(PriceBuilderInterface $builder): static;

    public function buildComplete(array $data, ResolverArrayInterface $dataResolver): float;
}