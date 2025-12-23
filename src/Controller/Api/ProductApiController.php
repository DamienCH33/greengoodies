<?php

namespace App\Controller\Api;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
final class ProductApiController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function products(SerializerInterface $serializer): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();

        $jsonProducts = $serializer->serialize(
            $products,
            'json',
            ['groups' => 'product:read']
        );

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}