<?php

namespace App\Tests\Controller;

use App\Repository\ProductRepository;

trait ProductDataTrait
{
    private array $amountDataDefault = [
        '100.00' => '101.15',
        '20.00' => '5.95',
        '10.00' => '-5.95'
    ];

    public function getProductRepository(): ProductRepository
    {
        return $this->client->getContainer()->get(ProductRepository::class);
    }

    public function getProductAmountByPrice($price, array|null $amountData = null)
    {
        $amountData ??= $this->amountDataDefault;
        return $amountData[$price] ?? '0';
    }

    public function getProductDataWithAmount(array|null $amountData = null): array
    {
        $products = $this->getProductRepository()->findAll();
        $data = [];
        foreach ($products as $product) {
            $requestData = [
                'product' => $product->getId(),
                'taxNumber' => 'DE123456789',
                'couponCode' => 'D15',
                'paymentProcessor' => 'paypal'
            ];
            $price = $product->getPrice();
            $responseData = (array)$product->jsonSerialize();
            $responseData['amount'] = $this->getProductAmountByPrice($price, $amountData);
            $data[] = [
                $requestData,
                $responseData
            ];
        }
        return $data;
    }
}