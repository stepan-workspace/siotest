<?php

namespace App\Controller;

use App\Form\PaymentFormType;
use App\Repository\ProductRepository;
use App\Service\Calculator\PriceBuilderInterface;
use App\Service\Error\ExceptionError;
use App\Service\Error\FormError;
use App\Service\Error\HandlerErrorInterface;
use App\Service\Payment\Providers\PaymentBuilderInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PriceBuilderInterface   $priceBuilder,
        private readonly HandlerErrorInterface   $handlerError,
        private readonly ProductRepository       $productRepository,
        private readonly HttpKernelInterface     $httpKernel,
        private readonly PaymentBuilderInterface $paymentBuilder
    )
    {
    }

    #[Route('/calculation', name: 'payment_calculation', methods: ['POST'])]
    public function getCalculationPrice(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $form = $this->createForm(PaymentFormType::class);
            $form->submit($data);

            if (!$form->isValid()) {
                return $this->json(
                    $this->handlerError->serve(FormError::class, $form)->toArray(),
                    Response::HTTP_BAD_REQUEST
                );
            }

            $amount = $this->priceBuilder
                ->setProductId((int)$form->get('product')->getData())
                ->setTaxNumber((string)$form->get('taxNumber')->getData())
                ->setCouponCode((string)$form->get('couponCode')->getData())
                ->getPrice();

            $product = $this->productRepository->find($form->get('product')->getData());

            $data = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'amount' => $amount
            ];

            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(
                $this->handlerError->serve(ExceptionError::class, $e)->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/pay', name: 'payment_pay', methods: ['POST'])]
    public function getMakePayment(Request $request): JsonResponse
    {
        try {
            $subRequest = Request::create(uri: '/api/calculation', method: 'POST', content: $request->getContent());
            $subRequest->headers->set('Content-Type', 'application/json');
            $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

            $requestContent = json_decode($request->getContent(), true);
            $paymentProcessor = $requestContent['paymentProcessor'];

            $responseContent = json_decode($response->getContent(), true);
            $amount = $responseContent['amount'];

            $provider = $this->paymentBuilder
                ->setPaymentKey($paymentProcessor)
                ->createProvider();

            $data = $responseContent;
            if (!$provider?->processPayment($amount)) {
                $data['pay'] = 'fail';
                $data['error'] = $provider?->getError() ?? '';
                return $this->json(
                    ['errors' => ['pay' => $data]],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $data['pay'] = 'success';
            return $this->json($data);
        } catch (Exception $e) {
            return $this->json(
                $this->handlerError->serve(ExceptionError::class, $e)->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}