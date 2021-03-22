<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/users/{id}", name="user_show", methods={"GET"})
     */
    public function showUser(User $user, SerializerInterface $serializer)
    {
        return new JsonResponse($serializer->serialize($user, 'json'), 200, [], true);
    }


    /**
     * @Route("/users", name="user_list", methods={"GET"})
     */
    public function listUser(SerializerInterface $serializer, UserRepository $userRepository, Request $request) : JsonResponse
    {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;
        $numberOfPages = (int) ceil($userRepository->count([]) / $limit);

        $users = $userRepository->findBy([], ['id' => 'asc'], $limit, $offset);

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
     * @Route("/users", name="user_add", methods={"POST"})
     */
    public function addUser(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager,
        ClientRepository $clientRepository,
        ValidatorInterface $validator
    ) : JsonResponse
    {
        $data = [];
        $data = $serializer->deserialize($request->getContent(), 'array', 'json');

        $user = new User();

        $user->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setEmail($data['email']);

        $client=$this->getUser()->getId();
        $user->setClient($clientRepository->find($client));

        $violations = $validator->validate($user);

        if (count($violations) > 0) {
            $message = [];
            foreach ($violations as $violation) {              
                $message[] = sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            return new JsonResponse($serializer->serialize($message, 'json'), 422, [], true);
        } 
        
        if (count($violations) == 0) {       
            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse($serializer->serialize($user, 'json'), 201, [], true);
        }
    }

    /**
     * @Route("/users/{id}", name="user_delete", methods={"DELETE"})
     */
    public function deleteUser(User $user, EntityManagerInterface $entityManager) : JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse('delete done', 200, [], true);
    }
}
