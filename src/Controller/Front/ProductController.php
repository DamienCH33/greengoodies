<?php

namespace App\Controller\Front;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ProductController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/product/{id}', name: 'app_product_detail')]
    public function showDetailProduct(int $id): Response
    {
        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Produit introvable.');
        }

        return $this->render('front/productDetail.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/products', name: 'app_product_list')]
    #[IsGranted('ROLE_USER')]
    public function showProductList(): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();

        return $this->render('front/productList.html.twig', [
            'products' => $products,
        ]);
    }    
}
