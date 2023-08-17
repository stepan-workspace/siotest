<?php

namespace App\Controller;

use App\Form\PaymentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class PaymentController extends AbstractController
{
    #[Route('/calculation', name: 'payment_calculation', methods: ['POST'])]
    public function getCalculationPrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(PaymentFormType::class);
        $form->submit($data);

        if ($form->isValid()) {
            // TODO need service for calculate payment
            return $this->json($form->getData());
        }

        $errors = $form->getConfig()->getType()->getInnerType()->processFormErrors($form);
        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}