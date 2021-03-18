<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        //ajout des produits
        $productsData = [];
        $productsData = [
            ['reference' => 'SM-A515FZBVEUB',
            'designation' => 'Galaxy A51',
            'brand' => 'Samsung',
            'priceExclTax' => '250.00'],

            ['reference' => 'SM-A326BZKVEUH',
            'designation' => 'Galaxy A32 5GMP',
            'brand' => 'Samsung',
            'priceExclTax' => '273.08'],

            ['reference' => 'MT9K2ZD/A',
            'designation' => 'iPhone XS (256Go) - Or',
            'brand' => 'Apple',
            'priceExclTax' => '985.26']
        ];

        foreach ($productsData as $productData) {
            $product = new Product();
            $product->setReference($productData['reference'])
                ->setDesignation($productData['designation'])
                ->setBrand($productData['brand'])
                ->setPriceExclTax($productData['priceExclTax']);
            $manager->persist($product);
        }
        $manager->flush();

        //ajout des clients
        $clientsData = [];
        $clientsData = [
            ['title' => 'BestOfTel'],
            ['title' => 'ThereIsTel'],
        ];

        foreach ($clientsData as $clientData) {
            $client = new Client();
            $client->setTitle($clientData['title']);
            $manager->persist($client);
        }
        $manager->flush();



        //ajout des users
        /** @var ClientRepository */
        $clientRepository = $manager->getRepository(Client::class);
        $userClient = $clientRepository->findOneBy(['id' => '1']);

        $usersData = [];
        $usersData = [
            ['firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'JohnDoe@email.com',
            'client' => $userClient],
            ['firstName' => 'Bob',
            'lastName' => 'Doe',
            'email' => 'BobDoe@email.com',
            'client' => $userClient],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setFirstName($userData['firstName'])
                ->setLastName($userData['lastName'])
                ->setEmail($userData['email'])
                ->setClient($userData['client']);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
