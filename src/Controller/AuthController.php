<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
 {

    /**
     * @Route("/register", name="client_register", methods={"POST"})
     */
  public function register(
      Request $request, 
      UserPasswordEncoderInterface $encoder, 
      EntityManagerInterface $entityManager, 
      SerializerInterface $serializer,
      ValidatorInterface $validator)
  {
    $data = [];
    $data = $serializer->deserialize($request->getContent(), 'array', 'json');
   
    $client = new Client();

    $client->setTitle($data['title'])
        ->setPassword($encoder->encodePassword($client, $data['password']));

    $violations = $validator->validate($client);

    if (count($violations) > 0) {
        $message = [];
            foreach ($violations as $violation) {              
                $message[] = sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            return new JsonResponse($serializer->serialize($message, 'json'), 422, [], true);
        } 
        
        if (count($violations) == 0) {       
            $entityManager->persist($client);
            $entityManager->flush();
            return new JsonResponse("Nouveau Client enregistré %s: %s ".$client->getTitle(), 201, [], true);
        }    
  }

  /**
    * @Route("/login_check", name="login_check", methods={"POST"})
   * @param UserInterface $client
   * @param JWTTokenManagerInterface $JWTManager
   * @return JsonResponse
   */
  public function getTokenUser(UserInterface $client, JWTTokenManagerInterface $jwtManager)
  {
   return new JsonResponse(['token' => $jwtManager->create($client)]);
  }

 }