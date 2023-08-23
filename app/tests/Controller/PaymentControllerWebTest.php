<?php

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional test for checking response statuses and
 * content of response REST API of product cost calculation
 * method
 */
final class PaymentControllerWebTest extends WebTestCase
{

    use ProductDataTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * The test is designed to check answers with the success status
     * of the API method responsible for calculating the cost of the product
     *
     * @dataProvider getDataToGetCalculationPriceCheckDataStatusOK
     */
    public function testGetCalculationPriceCheckDataStatusOK(array $requestData, array $responseData): void
    {
        $this->client->request(method: 'POST', uri: '/api/calculation', content: json_encode($requestData));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        foreach ($responseData as $k => $v) {
            $this->assertArrayHasKey($k, $responseContent);
            $this->assertEquals($v, $responseContent[$k]);
        }
    }

    /**
     *The test is designed to check an optional parameter responsible
     * for a discount on a product with a success status
     *
     * @dataProvider getDataToGetCalculationPriceCouponCodeStatusOK
     */
    public function testGetCalculationPriceCouponCodeStatusOK(array $requestData): void
    {
        $this->client->request(method: 'POST', uri: '/api/calculation', content: json_encode($requestData));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('amount', $responseContent);
        $this->assertEquals('119', $responseContent['amount']);
    }

    /**
     * The test is designed to check the error messages of the required
     * parameters, namely: product ID, country tax number, payment method.
     * It also checks the validation of values and the presence of
     * fields. Response status failed.
     *
     * @dataProvider getDataToGetCalculationPriceStatusBAD
     */
    public function testGetCalculationPriceStatusBAD(array $requestData, ?array $errors): void
    {
        $this->client->request(method: 'POST', uri: '/api/calculation', content: json_encode($requestData));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $responseJson = $this->client->getResponse()->getContent();
        if ($errors && is_iterable($errors)) {
            foreach ($errors as $error) {
                $this->assertStringContainsString($error, $responseJson);
            }
        }
        $responseContent = json_decode($responseJson, true);
        $this->assertArrayHasKey('errors', $responseContent);
        $this->assertArrayHasKey('errors', ['errors' => 'errors']);
    }

    /**
     * The test is designed to check the response to banned or
     * non-existent url addresses. Response status failed.
     *
     * @dataProvider getDataToGetCalculationPriceForbiddenStatusBAD
     */
    public function testGetCalculationPriceForbiddenStatusBAD($method): void
    {
        $this->client->request(method: $method, uri: '/api/calculation');
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $responseJson = $this->client->getResponse()->getContent();
        $error = '"forbidden":"This API endpoint does not exist or is not allowed."';
        $this->assertStringContainsString($error, $responseJson);
    }

    private function getDataToGetCalculationPriceCheckDataStatusOK(): Generator
    {
        $this->client = static::createClient();
        $products = $this->getProductDataWithAmount();
        self::tearDown();
        foreach ($products as $v) {
            yield $v;
        }
    }

    private function getDataToGetCalculationPriceCouponCodeStatusOK(): Generator
    {
        yield [[
            'product' => '1',
            'taxNumber' => 'DE123456789',
            'paymentProcessor' => 'paypal'
        ], [
            'product' => '1',
            'taxNumber' => 'DE123456789',
            'couponCode' => '',
            'paymentProcessor' => 'paypal'
        ]];
    }

    private function getDataToGetCalculationPriceStatusBAD(): Generator
    {
        $default = [
            'product' => '1',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'D15',
            'paymentProcessor' => 'paypal'
        ];

        $data = [
            'product' => [
                'UNSET' => '"product":"This value should not be blank."',
                '0' => '"product":"This value should be 1 or more."',
                '5' => '"exception":"Product not found by Id: 5"',
                '' => '"product":"This value should not be blank."'
            ],
            'taxNumber' => [
                'UNSET' => '"taxNumber":"This value should not be blank."',
                'DE123456' => '"exception":"Tax not found by Tax number: DE123456"',
                'D_123456' => '"taxNumber":"This value: D_123456 is not valid."',
                'DE12str789' => '"exception":"Tax not found by Tax number: DE12str789"',
                '0000000' => '"taxNumber":"This value: 0000000 is not valid."',
                '' => '"taxNumber":"This value should not be blank."'
            ],
            'couponCode' => [
                'Dstring' => '"couponCode":"This value is not valid."',
                '12D15' => '"couponCode":"This value is not valid."',
                'E15' => '"couponCode":"This value is not valid."',
                '35' => '"couponCode":"This value is not valid."',
            ],
            'paymentProcessor' => [
                'UNSET' => '"paymentProcessor":"This value should not be blank."',
                '' => '"paymentProcessor":"This value should not be blank."',
            ]
        ];

        foreach ($data as $field => $errors) {
            $tmp = $default;
            foreach ($errors as $value => $error) {
                if ($value === 'UNSET') {
                    unset($tmp[$field]);
                } else {
                    $tmp[$field] = $value;
                }
                yield [$tmp, [$error]];
            }
        }
    }

    private function getDataToGetCalculationPriceForbiddenStatusBAD(): Generator
    {
        yield ['GET', 'POST', 'PUT', 'DELETE'];
    }
}
