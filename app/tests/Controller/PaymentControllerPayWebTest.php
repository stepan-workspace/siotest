<?php

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class PaymentControllerPayWebTest extends WebTestCase
{

    use ProductDataTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * This test will check with valid data the API method
     * that initiates payment. Expected response success
     *
     * @dataProvider getDataToGetMakePaymentCheckDataStatusOK
     */
    public function testGetMakePaymentCheckDataStatusOK($requestData, $responseData)
    {
        $this->client->request(method: 'POST', uri: '/api/pay', content: json_encode($requestData));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        foreach ($responseData as $k => $v) {
            $this->assertArrayHasKey($k, $responseContent);
            $this->assertEquals($v, $responseContent[$k]);
        }
    }

    /**
     * This test checks with arbitrary data the API method
     * responsible for making the payment. The expected
     * response depends on the result returned.
     *
     * @dataProvider getDataToGetMakePaymentCheckTaxesAllCounties
     */
    public function testGetMakePaymentCheckTaxesAllCounties($requestData, $paymentProcessor)
    {
        $this->client->request(method: 'POST', uri: '/api/pay', content: json_encode($requestData));
        $responseJson = $this->client->getResponse()->getContent();
        $this->assertJson($responseJson);
        $responseContent = json_decode($responseJson, true);

        $amount = (float)$responseContent['amount'];
        $error = match (true) {
            $amount < 10 && $paymentProcessor === 'stripe' => '"exception":""',
            (int)$amount > 100 && $paymentProcessor === 'paypal' => '"exception":"Too high price"',
            default => ''
        };

        $statusCode = match (true) {
            (bool)$error => (function($error, $responseJson) {
                $this->assertStringContainsString($error, $responseJson);
                return Response::HTTP_BAD_REQUEST;
            })($error, $responseJson),
            default => Response::HTTP_OK
        };

        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * This test checks with invalid data the API method for
     * the correctness of processing the result of the request,
     * which is responsible for making the payment.
     * Expected response fails
     *
     * @dataProvider getDataToGetMakePaymentStatusBAD
     */
    public function testGetMakePaymentStatusBAD(array $requestData, ?array $errors): void
    {
        $this->client->request(method: 'POST', uri: '/api/pay', content: json_encode($requestData));
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
    }

    private function getDataToGetMakePaymentCheckDataStatusOK(): Generator
    {
        yield [[
            'product' => '1',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'D15',
            'paymentProcessor' => 'stripe'
        ], [
            'price' => '100.00',
            'amount' => '101.15',
        ]];

        yield [[
            'product' => '2',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'D15',
            'paymentProcessor' => 'paypal'
        ], [
            'price' => '20.00',
            'amount' => '5.95',
        ]];
    }

    private function getDataToGetMakePaymentStatusBAD(): Generator
    {
        $default = [
            'product' => '2',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'P15',
            'paymentProcessor' => 'paypal'
        ];

        $data = [
            'product' => [
                '1' => '"exception":"Too high price"'
            ],
            'taxNumber' => [
                'INVALID' => '"taxNumber":"This value: INVALID is not valid."',
                '00000' => '"taxNumber":"This value: 00000 is not valid."',
                'DE12345678_' => '"taxNumber":"This value: DE12345678_ is not valid."',
                'DE12345678' => '"exception":"Tax not found by Tax number: DE12345678"'
            ],
            'paymentProcessor' => [
                'UNSET' => '"paymentProcessor":"This value should not be blank."',
                'INVALID' => '"exception":"You have requested a non-existent parameter \u0022INVALID\u0022."',
                '00000' => '"exception":"You have requested a non-existent parameter \u002200000\u0022."',
                '' => '"paymentProcessor":"This value should not be blank."'
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

        yield [[
            'product' => '2',
            'taxNumber' => 'DE123456789',
            'couponCode' => 'D15',
            'paymentProcessor' => 'stripe'
        ], ['"exception":""']];
    }

    private function getDataToGetMakePaymentCheckTaxesAllCounties(): Generator
    {
        /** @var array $data this tax numbers of countries */
        $data = [
            'DE123456789',
            'IT12345678901',
            'GR123456789',
            'FRab123456789'
        ];

        /** @var int $product this products Id */
        foreach ([1, 3] as $product) {
            /** @var string $paymentProcessor this valid payment method */
            foreach (['paypal', 'stripe'] as $paymentProcessor) {
                foreach ($data as $taxNumber) {
                    yield [[
                        'product' => $product,
                        'taxNumber' => $taxNumber,
                        'couponCode' => 'P18',
                        'paymentProcessor' => $paymentProcessor
                    ], $paymentProcessor];
                }
            }
        }
    }
}