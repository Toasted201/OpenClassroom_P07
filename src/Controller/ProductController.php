<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @Route("/products/{id}", name="product_show", methods={"GET"})
     */
    public function showProduct(Product $product, SerializerInterface $serializer) : JsonResponse
    {
        return new JsonResponse($serializer->serialize($product, 'json'), 200, [], true);
    }


    /**
     * @Route("/products", name="product_list", methods={"GET"})
     */
    public function listProduct(SerializerInterface $serializer, ProductRepository $productRepository, Request $request) : JsonResponse
    {
        $limit = $request->query->get('limit', 10);
        $page = $request->query->get('page', 1);
        $offset = ($page - 1) * $limit;
        $numberOfPages = (int) ceil($productRepository->count([]) / $limit);

        $products = $productRepository->findBy([], ['id' => 'asc'], $limit, $offset);

        $paginated = new PaginatedRepresentation(
            $products,
            'product_list',
            [],
            $page,
            $limit,
            $numberOfPages
        );

        return new JsonResponse($serializer->serialize($paginated, 'json'), 200, [], true);
    }
}
