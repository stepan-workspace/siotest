<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use App\Service\Error\ExceptionError;
use App\Service\Error\FormError;
use App\Service\Error\HandlerErrorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A controller that allows you to add,
 * edit, and delete products.
 */
#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository      $productRepository,
        private readonly HandlerErrorInterface  $handlerError
    )
    {
    }

    /**
     * Listings of all products (GET)
     * @url /api/products
     *
     * @return JsonResponse
     */
    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        $data = $this->productRepository->findAll();
        return $this->json($data);
    }

    /**
     * Adding a product (POST). Request example:
     *  {
     *      "name": "Product one",
     *      "price": "123"
     *  }
     * @url /api/products
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/products', name: 'products_add', methods: ['POST'])]
    public function addProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ProductFormType::class, new Product());
        $form->submit($data);

        if ($form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();
            return $this->json($form->getData());
        }

        return $this->json(
            $this->handlerError->serve(FormError::class, $form)->toArray(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Product editing (PUT). Request example:
     *  {
     *      "name": "edit product one",
     *      "price": "234"
     *  }
     * @url /api/products/1
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'products_edit', methods: ['PUT'])]
    public function editProduct(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                throw new Exception('Product not fount by Id: ' . $id);
            }

            $data = json_decode($request->getContent(), true);
            $form = $this->createForm(ProductFormType::class, $product);
            $form->submit($data);

            if (!$form->isValid()) {
                return $this->json(
                    $this->handlerError->serve(FormError::class, $form)->toArray(),
                    Response::HTTP_BAD_REQUEST
                );
            }

            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();
            return $this->json($form->getData());
        }  catch (Exception $e) {
            return $this->json(
                $this->handlerError->serve(ExceptionError::class, $e)->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Removing a product (DELETE). Request example:
     * @url /api/products/1
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'products_delete', methods: ['DELETE'])]
    public function deleteProduct(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                throw new Exception('Product not fount by Id: ' . $id);
            }
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            return $this->json('Product removed by id = ' . $id);
        } catch (Exception $e) {
            return $this->json(
                $this->handlerError->serve(ExceptionError::class, $e)->toArray(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}