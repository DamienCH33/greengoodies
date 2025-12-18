<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
final class ApiController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/auth', name: 'api_login', methods: ['POST'])]    
    public function login(): JsonResponse
    {
        throw new \Exception('This should never be reached!');
    }
    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function products(SerializerInterface $serializer): Response
    {
        $products = $this->em->getRepository('App:Product')->findAll();
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);
        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}
