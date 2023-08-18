<?php

namespace App\Service\Error;

use Symfony\Component\Form\FormInterface;

class FormError implements ToArrayErrorInterface
{

    private FormInterface $form;
    public function setObject($object): static
    {
        $this->form = $object;
        return $this;
    }

    public function toArray(): array
    {
        $errors = [];
        foreach ($this->form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }
        return ['errors' => $errors];
    }
}
