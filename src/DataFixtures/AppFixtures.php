<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use GMP;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        //ajout des produits
        $productsData=[];
        $productsData=[
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

        foreach ($productsData as $productData){
            $product = new Product();
            $product->setReference($productData['reference'])
                ->setDesignation($productData['designation'])
                ->setBrand($productData['brand'])
                ->setPriceExclTax($productData['priceExclTax']);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
