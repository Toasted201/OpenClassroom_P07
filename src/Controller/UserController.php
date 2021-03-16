<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @Route("/users", name="user_add", methods={"POST"})
     */
    public function addUser(SerializerInterface $serializer, Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository)
    {             
        $data = [];
        $data = $serializer->deserialize($request->getContent(), 'array', 'json');

        $user = new User();
        $userForm = $this->createForm(UserFormType::class, $user);
        $userForm->submit($data);
        
        //J'associe le client n°1 pour les tests
        //TODO : associer le client authentifié
        $user->setClient($clientRepository->find(1));

        if ($userForm->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();
        } else {
            var_dump($userForm->getErrors(true));
            die;
        }

        return new JsonResponse($serializer->serialize($user, 'json'),201,[],true);

    }
}