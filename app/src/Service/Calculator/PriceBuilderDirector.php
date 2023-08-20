<?php

namespace App\Service\Calculator;

use App\Repository\CountryTaxRepository;
use App\Repository\ProductRepository;
use App\Service\Register\RegisterInterface;
use App\Service\Resolver\ResolverArrayInterface;
use Exception;

class PriceBuilderDirector implements PriceBuilderDirectorInterface
{
    public function __construct(
        private PriceBuilderInterface              $builder,
        private readonly RegisterInterface         $register,
        private readonly ProductRepository         $productRepository,
        private readonly DiscountProviderInterface $discountProvider,
        private readonly CountryTaxRepository      $countryTaxRepository
    )
    {
    }

    public function setBuilder(PriceBuilderInterface $builder): static
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function buildComplete(array $data, ResolverArrayInterface $dataResolver): float
    {
        foreach ($dataResolver->getContract() as $target => $factual) {
            $value = key_exists($factual, $data) ? $data[$factual] : null;
            $this->register->setParameter($target, $value);
        }

        $price = $this->productRepository->find(
            $this->register->getParameter('productId')
        )?->getPrice();

        if (is_null($price)) {
            throw new Exception('Product no found by Id: ' . $this->register->getParameter('productId'));
        }

        $discount = $this->discountProvider->getDiscount(
            $this->register->getParameter('couponCode'),
            $price
        );

        $tax = $this->countryTaxRepository->getTaxCountryByTaxNumber(
            $this->register->getParameter('taxNumber')
        );

        return $this->builder
            ->setPrice($price)
            ->setDiscount($discount)
            ->setTax($tax)
            ->build();
    }
}