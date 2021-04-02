<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    /**
     * Throws an exception unless the attribute is granted against the current authentication token
     * with an 404 error
     *
     * @throws AccessDeniedException
     */
    protected function notFoundUnlessGranted(
        $attribute,
        $subject = null,
        string $message = 'Resource Not Found'
    ): void {
        if (!$this->isGranted($attribute, $subject)) {
            throw new NotFoundHttpException($message);
        }
    }

    /**
     * @Route("/users/{id}", name="user_show", methods={"GET"}, format="json")
     * @OA\Get(
     *      description="Returns user based on Id",
     *      summary="Find user by Id",
     *      operationId="getUserById",
     *      @OA\Response(
     *          response="200",
     *          description="Return properties of an user",
     *          @OA\JsonContent(ref=@Model(type=User::class))
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Access token is missing or invalid"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="No user found, check your parameters or Access token"
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The Id of the user to use",
     *          required="true",
     *          @OA\Schema(type="integer")
     *      )
     *)
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
    **/
    public function showUser(User $user, SerializerInterface $serializer): JsonResponse
    {
        $this->notFoundUnlessGranted('link', $user);
        return new JsonResponse($serializer->serialize($user, 'json'), 200, [], true);
    }


    /**
     * @Route("/users", name="user_list", methods={"GET"}, format="json")
     * @OA\Get(
     *      description="Returns all users linked to the authentificated's client",
     *      summary="Find users",
     *      @OA\Response(
     *          response="200",
     *          description="Returns the list of all users linked to the authentificated's client",
     *          @OA\JsonContent(
     *              @OA\Property(property="page", type="integer"),
     *              @OA\Property(property="limit", type="integer"),
     *              @OA\Property(property="pages", type="integer"),
     *              @OA\Property(
     *                  property="_links",
     *                  @OA\Property(property="self", type="string"),
     *                  @OA\Property(property="first", type="string"),
     *                  @OA\Property(property="last", type="string"),
     *                  @OA\Property(property="next", type="string"),
     *                  @OA\Property(property="previous", type="string")
     *              ),
     *              @OA\Property(
     *                  property="_embedded",
     *                  @OA\Property(
     *                      property="items",
     *                      type="array",
     *                      @OA\Items(ref=@Model(type=User::class))
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Access token is missing or invalid"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="No user found, check your parameters or Access token"
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="The number of items to return ont the page",
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
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     **/
    public function listUser(
        SerializerInterface $serializer,
        UserRepository $userRepository,
        Request $request
    ): JsonResponse {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;
        $numberOfPages = (int) ceil($userRepository->count([]) / $limit);

        $client = $this->getUser();
        $users = $userRepository->findBy(['client' => $client], ['id' => 'asc'], $limit, $offset);

        $paginated = new PaginatedRepresentation(
            new CollectionRepresentation($users),
            'user_list',
            [],
            $page,
            $limit,
            $numberOfPages
        );

        return new JsonResponse($serializer->serialize($paginated, 'json'), 200, [], true);
    }

    /**
     * @Route("/users", name="user_add", methods={"POST"}, format="json")
     * @OA\Post(
     *      description="Adds a new User",
     *      summary="Creates a new user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref=@Model(type=User::class, groups={"add_user"})))
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="A new user created.",
     *          @OA\JsonContent(ref=@Model(type=User::class)))
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Access token is missing or invalid"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="No user found, check your parameters or Access token"
     *      )
     *)
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function addUser(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = [];
        $data = $serializer->deserialize($request->getContent(), 'array', 'json');

        $user = new User();

        $user->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setEmail($data['email'])
            ->setClient($this->getUser());

        $violations = $validator->validate($user);

        if (count($violations) > 0) {
            return new JsonResponse($serializer->serialize($violations, 'json'), 422, [], true);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse($serializer->serialize($user, 'json'), 201, [], true);
    }

    /**
     * @Route("/users/{id}", name="user_delete", methods={"DELETE"}, format="json")
     * @OA\Delete(
     *      description="Delete an user based on Id",
     *      summary="Deletes user by Id",
     *      operationId="deleteUserById",
     *      @OA\Response(
     *          response="204",
     *          description="User deleted."
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Access token is missing or invalid"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="No user found, check your parameters  or Access token"
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The Id of the user to delete",
     *          required="true",
     *          @OA\Schema(type="integer")
     *      )
     *)
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function deleteUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->notFoundUnlessGranted('link', $user);

        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse('', 204, [], true);
    }
}
