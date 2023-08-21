<?php

namespace App\Service\Error;

use Symfony\Component\Form\FormInterface;

class FormError implements ArrayErrorInterface
{
    public function __construct(
        private ?FormInterface $source = null
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
        $errors = [];
        foreach ($this->source->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }
        return ['errors' => $errors];
    }
}
