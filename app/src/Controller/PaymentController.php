<?php

namespace App\Controller;

use App\Form\PaymentFormType;
use App\Service\Calculator\PriceBuilderInterface;
use App\Service\Error\ExceptionError;
use App\Service\Error\FormError;
use App\Service\Error\HandlerErrorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PriceBuilderInterface $priceBuilder,
        private readonly HandlerErrorInterface $handlerError
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

            $price = $this->priceBuilder
                ->setProductId((int)$form->get('product')->getData())
                ->setTaxNumber((string)$form->get('taxNumber')->getData())
                ->setCouponCode((string)$form->get('couponCode')->getData())
                ->getPrice();

            return $this->json(['price' => $price]);
        } catch (\Exception $e) {
            return $this->json(
                $this->handlerError->serve(ExceptionError::class, $e)->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}