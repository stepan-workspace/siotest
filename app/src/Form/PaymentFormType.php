<?php

namespace App\Form;

use App\Repository\CountryRepository;
use App\Validator\TaxNumberConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentFormType extends AbstractType
{
    public function __construct(
        private readonly CountryRepository $countryRepository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, iterable $options): void
    {
        $builder
            ->add('product', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^\d+$/']),
                    new Assert\Range(['min' => 1]),
                ]
            ])
            ->add('taxNumber', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^[a-z]{2}[a-z0-9]+$/i']),
                    new TaxNumberConstraint([
                        'countryCodeAsArray' => $this->countryRepository->getCodeOfAllCountries()
                    ])
                ]
            ])
            ->add('couponCode', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex(['pattern' => '/^[D|P]\d+$/']),
                ]
            ])
            ->add('paymentProcessor', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^[a-z0-9]+$/i']),
                ]
            ]);
    }
}
