<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends AbstractController
{
    /**
     * @Route("/products/{id}", name="product_show", methods={"GET"})
     */
    public function showProduct(Product $product, SerializerInterface $serializer)
    {       
        return new JsonResponse($serializer->serialize($product, 'json'),200,[],true);
    }

    
    /**
     * @Route("/products", name="product_list", methods={"GET"})
     */
    public function listProduct(SerializerInterface $serializer, ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();
        return new JsonResponse($serializer->serialize($products, 'json'),200,[],true);
    }
}