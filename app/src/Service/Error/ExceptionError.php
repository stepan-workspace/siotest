<?php

namespace App\Service\Error;

class ExceptionError implements ToArrayErrorInterface
{
    private \Exception $exception;

    public function setObject($object): static
    {
        $this->exception = $object;
        return $this;
    }

    public function toArray(): array
    {
        return ['errors' => ['exception' => $this->exception->getMessage()]];
    }
}