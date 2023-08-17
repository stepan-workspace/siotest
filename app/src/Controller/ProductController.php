<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository      $productRepository
    )
    {
    }

    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        $data = $this->productRepository->findAll();
        return $this->json($data);
    }

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

        $errors = $form->getConfig()->getType()->getInnerType()->processFormErrors($form);
        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/products/{id}', name: 'products_edit', methods: ['PUT'])]
    public function editProduct(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json('Product not found by id = ' . $id, Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ProductFormType::class, $product);
        $form->submit($data);

        if ($form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();
            return $this->json($form->getData());
        }

        $errors = $form->getConfig()->getType()->getInnerType()->processFormErrors($form);
        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/products/{id}', name: 'products_delete', methods: ['DELETE'])]
    public function deleteProduct(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json('Product not found by id = ' . $id, Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->json('Product removed by id = ' . $id);
    }
}
