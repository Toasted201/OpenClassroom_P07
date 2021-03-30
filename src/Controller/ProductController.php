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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{
    /**
     * @Route("/products/{id}", name="product_show", methods={"GET"}, format="json")
     * @OA\Get(
     *      description="Returns product based on Id",
     *      summary="Find product by Id",
     *      operationId="getProductById",
     *      @OA\Response(
     *          response="200",
     *          description="Return properties of a product",
     *          @OA\JsonContent(ref=@Model(type=Product::class))
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Access token is missing or invalid"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="No product found, check your parameters or Access token"
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The Id of product to use",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      )
     * )
     * @OA\Tag(name="products")
     * @Security(name="Bearer")
     */
    public function showProduct(Product $product, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($product, 'json'), 200, [], true);
    }

    /**
     * @Route("/products", name="product_list", methods={"GET"}, format="json")
     * @OA\Get(
     *      description="Returns all products",
     *      summary="Find products",
     *      @OA\Response(
     *          response="200",
     *          description="Return the list of all products.",
     *          @OA\JsonContent(
     *              @OA\Property(property="page", type="integer"),
     *              @OA\Property(property="limit", type="integer"),
     *              @OA\Property(property="pages", type="integer"),
     *              @OA\Property(
     *                  property="_embedded",
     *                  ref=@Model(type=Product::class)),
     *              @OA\Property(
     *                  property="_links",
     *                  @OA\Property(property="next", type="string"),
     *                  @OA\Property(property="first", type="string"),
     *                  @OA\Property(property="last", type="string"),
     *                  @OA\Property(property="previous", type="string")
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Access token is missing or invalid"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="No product found, check your parameters or Access token "
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="The number of items to return one the page",
     *          required=false,
     *          @OA\Schema(type="integer", default="10")
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="The page to return",
     *          required=false,
     *          @OA\Schema(type="integer", default="1")
     *      )
     * )
     * @OA\Tag(name="products")
     * @Security(name="Bearer")
     */
    public function listProduct(
        SerializerInterface $serializer,
        ProductRepository $productRepository,
        Request $request
    ): JsonResponse {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
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
