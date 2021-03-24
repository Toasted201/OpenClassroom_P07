<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * Throws an exception unless the attribute is granted against the current authentication token
     * with an 404 error
     *
     * @throws AccessDeniedException
     */
    protected function denyAccessUnlessGranted(
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
     */
    public function showUser(User $user, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('link', $user);
        return new JsonResponse($serializer->serialize($user, 'json'), 200, [], true);
    }


    /**
     * @Route("/users", name="user_list", methods={"GET"}, format="json")
     */
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
            $users,
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
     */
    public function deleteUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('link', $user);

        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse('', 204, [], true);
    }
}
