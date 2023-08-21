<?php

namespace App\Service\Error;

class ExceptionError implements ArrayErrorInterface
{
    public function __construct(
        private ?object $source = null
    )
    {
    }

    public function setSource($source): static
    {
        $this->source = $source;
        return $this;
    }

    public function toArray(): array
    {
        return ['errors' => ['exception' => $this->source->getMessage()]];
    }
}