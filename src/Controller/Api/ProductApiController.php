<?php

namespace App\Controller\Api;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class ProductApiController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function products(): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();

        return  $this->json($products, Response::HTTP_OK, [], ['groups' => 'product:read']);
    }
}
