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

    public function getProductDataWithAmount(array|null $amountData = null): array
    {
        $amountData ??= $this->amountDataDefault;

        $products = $this->getProductRepository()->findAll();
        $data = [];
        foreach ($products as $product) {
            $requestData = [
                'product' => $product->getId(),
                'taxNumber' => 'DE123456789',
                'couponCode' => 'D15',
                'paymentProcessor' => 'paypal'
            ];
            $responseData = (array)$product->jsonSerialize();
            $responseData['amount'] = $amountData[$product->getPrice()] ?? '0';
            $data[] = [
                $requestData,
                $responseData
            ];
        }
        return $data;
    }
}