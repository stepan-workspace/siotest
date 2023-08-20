<?php

namespace App\Service\Resolver;

interface ResolverArrayInterface
{
    public function addConform(string|int|float $target, bool|string|int|float $factual = null): static;

    public function getContract(): array;
}