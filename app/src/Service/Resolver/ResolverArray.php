<?php

namespace App\Service\Resolver;

class ResolverArray implements ResolverArrayInterface
{
    private array $contract = [];

    public function addConform(string|int|float $target, bool|string|int|float $factual = null): static
    {
        $this->contract[$target] = is_null($factual) ? $target : $factual;
        return $this;
    }

    public function getContract(): array
    {
        return $this->contract;
    }
}