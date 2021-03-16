<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users/{id}", name="user_show", methods={"GET"})
     */
    public function showUser(User $user, SerializerInterface $serializer)
    {       
        return new JsonResponse($serializer->serialize($user, 'json'),200,[],true);
    }

    
    /**
     * @Route("/users", name="user_list", methods={"GET"})
     */
    public function listUser(SerializerInterface $serializer, UserRepository $userRepository, Request $request)
    {
        $limit = $request->query->get('limit', 10);
        $page=$request->query->get('page', 1);
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

        return new JsonResponse($serializer->serialize($paginated, 'json'),200,[],true);
    }

}
