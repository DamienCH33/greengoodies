<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $productsData = [
            [
                'name' => "Kit d'hygiène recyclable",
                'shortDescription' => 'Pour une salle de bain éco-friendly',
                'price' => 24.99,
                'picture' => 'images/products/kit.jpg',
                'fullDescription' => 'Un kit complet d’hygiène personnelle avec des produits recyclables et respectueux de l’environnement, parfait pour adopter des gestes éco-responsables au quotidien.',
            ],
            [
                'name' => "Shot Tropical",
                'shortDescription' => 'Fruits frais, pressés à froid',
                'price' => 4.50,
                'picture' => 'images/products/shot.jpg',
                'fullDescription' => 'Un concentré de fruits exotiques bio, riche en vitamines et antioxydants pour démarrer la journée avec énergie et vitalité.',
            ],
            [
                'name' => "Gourde en bois",
                'shortDescription' => "50cl, bois d'olivier",
                'price' => 16.90,
                'picture' => 'images/products/gourde.jpg',
                'fullDescription' => 'Gourde écologique fabriquée à partir de matériaux naturels, idéale pour emporter vos boissons tout en réduisant les déchets plastiques.',
            ],
            [
                'name' => "Disques Démaquillants x3",
                'shortDescription' => 'Solution efficace pour vous démaquiller en douceur ',
                'price' => 19.99,
                'picture' => 'images/products/coton.jpg',
                'fullDescription' => 'Disques démaquillants réutilisables en coton bio, doux pour la peau et pour la planète, lavables en machine.',
            ],
            [
                'name' => "Bougie Lavande & Patchouli",
                'shortDescription' => 'Cire naturelle',
                'price' => 32,
                'picture' => 'images/products/bougie.jpg',
                'fullDescription' => 'Bougie artisanale aux huiles essentielles de lavande et patchouli, pour créer une atmosphère relaxante et parfumée dans votre intérieur.',
            ],
            [
                'name' => "Brosse à dent",
                'shortDescription' => 'Bois de hêtre rouge issu de forêts gérées durablement',
                'price' => 5.40,
                'picture' => 'images/products/brosse.jpg',
                'fullDescription' => 'Brosse à dents en bois de hêtre rouge, douce pour les dents et respectueuse de l’environnement.',
            ],
            [
                'name' => "Kit de couverts en bois",
                'shortDescription' => 'Revêtement Bio en olivier & sac de transport',
                'price' => 12.30,
                'picture' => 'images/products/couvert.jpg',
                'fullDescription' => "Kit complet de couverts en bois d'olivier, idéal pour les repas à emporter ou les pique-niques, réutilisable et écologique.",
            ],
            [
                'name' => "Nécessaire déodorant bio",
                'shortDescription' => '50ml déodorant à l’eucalyptus',
                'price' => 8.50,
                'picture' => 'images/products/deo.jpg',
                'fullDescription' => "Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante. 
                                    Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. 
                                    Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. 
                                    Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.",
            ],
            [
                'name' => "Savon bio",
                'shortDescription' => 'Thé, Orange & Girofle',
                'price' => 18.90,
                'picture' => 'images/products/savon.jpg',
                'fullDescription' => 'Savon naturel et bio, fabriqué artisanalement pour nettoyer et nourrir la peau en douceur sans agresser l’environnement.',
            ],
        ];

        foreach ($productsData as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setShortDescription($data['shortDescription']);
            $product->setPrice($data['price']);
            $product->setPicture($data['picture']);
            $product->setFullDescription($data['fullDescription']);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
