<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;


class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProductController.php',
        ]);
    }

    /**
     * @Route("/products/{id}", name="product_show", methods={"GET"})
     */
    public function showProduct(Product $product, SerializerInterface $serializer)
    {       
        $data = $serializer->serialize($product, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    
    /**
     * @Route("/products", name="product_list", methods={"GET"})
     */
    public function listProduct(SerializerInterface $serializer, ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        $data = $serializer->serialize($products, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
