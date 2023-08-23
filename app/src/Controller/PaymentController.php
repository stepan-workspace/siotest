<?php

namespace App\Controller;

use App\Form\PaymentFormType;
use App\Repository\ProductRepository;
use App\Service\Calculator\PriceBuilderDirectorInterface;
use App\Service\Error\ExceptionError;
use App\Service\Error\FormError;
use App\Service\Error\HandlerErrorInterface;
use App\Service\Payment\PaymentBuilderDirectorInterface;
use App\Service\Resolver\ResolverArrayInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A controller used to calculate the cost of an
 * item and pay for that cost. The cost of a product
 * consists of price, discount and tax.
 */
#[Route('/api', name: 'api_')]
class PaymentController extends AbstractController
{
    public function __construct(
        private readonly HandlerErrorInterface           $handlerError,
        private readonly ProductRepository               $productRepository,
        private readonly HttpKernelInterface             $httpKernel,
        private readonly ResolverArrayInterface          $resolverArray,
        private readonly PriceBuilderDirectorInterface   $priceBuilderDirector,
        private readonly PaymentBuilderDirectorInterface $paymentBuilderDirector
    )
    {
    }

    /**
     * REST API method for calculating the cost of a product.
     * Request example (POST):
     *  {
     *      "product": "1",
     *      "taxNumber": "DE123456789",
     *      "couponCode": "D15",
     *      "paymentProcessor": "paypal"
     *  }
     * In the event of a hurry, the answer is 200,
     * failure, the answer is 400
     * @url /api/calculation
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/calculation', name: 'payment_calculation', methods: ['POST'])]
    public function getCalculationPrice(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $form = $this->createForm(PaymentFormType::class);
            $form->submit($data);

            // Request validation form is used
            if (!$form->isValid()) {
                return $this->json(
                    $this->handlerError->serve(FormError::class, $form)->toArray(),
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Data matching for the calculator
            $this->resolverArray
                ->addConform('productId', 'product')
                ->addConform('taxNumber')
                ->addConform('couponCode');

            // final cost of the product
            $amount = $this->priceBuilderDirector
                ->buildComplete($form->getData(), $this->resolverArray);

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

    /**
     * REST API method for paying for the cost of goods. This
     * method uses the REST API method to calculate the cost
     * Request example (POST):
     *   {
     *       "product": "1",
     *       "taxNumber": "DE123456789",
     *       "couponCode": "D15",
     *       "paymentProcessor": "paypal"
     *   }
     * In the event of a hurry, the answer is 200,
     * failure, the answer is 400
     * @url /api/pay
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/pay', name: 'payment_pay', methods: ['POST'])]
    public function getMakePayment(Request $request): JsonResponse
    {
        try {
            // API request that will return the final cost of the product
            $subRequest = Request::create(uri: '/api/calculation', method: 'POST', content: $request->getContent());
            $subRequest->headers->set('Content-Type', 'application/json');
            $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

            $requestContent = json_decode($request->getContent(), true);
            $responseContent = json_decode($response->getContent(), true);

            // If it fails, throw an exception with code 400
            if ($response->getStatusCode() !== Response::HTTP_OK) {
                return $this->json($responseContent, Response::HTTP_BAD_REQUEST);
            }

            $paymentProcessor = $requestContent['paymentProcessor'];
            $amount = $responseContent['amount'];

            // We pay for the product
            $this->paymentBuilderDirector
                ->buildComplete($paymentProcessor, $amount);

            return $this->json($responseContent);
        } catch (Exception $e) {
            return $this->json(
                ($responseContent ?? []) + $this->handlerError->serve(ExceptionError::class, $e)->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * REST API method that closes incoming requests for
     * unresolved routes and sends an error message
     * and a 400 response code
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/{any}', name: 'payment_catch_all', methods: ['GET', 'PUT', 'DELETE'])]
    public function catchAll(Request $request): JsonResponse
    {
        $response = [
            'errors' => ['forbidden' => 'This API endpoint does not exist or is not allowed.'],
        ];
        return $this->json($response, Response::HTTP_BAD_REQUEST);
    }
}
