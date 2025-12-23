<?php

namespace App\Controller\User;

use App\Entity\Cart;
use App\Entity\Product;
use App\Exception\CartException;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
final class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartService $cartService
    ) {}

    #[Route('/', name: 'app_cart', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function currendCart(): Response
    {
        $user = $this->getUser();
        $cart = $this->cartService->getCurrentCart($user, true);

        $total = $this->cartService->calculateTotal($cart);

        if (!$cart->getId()) {
            $cart->setUser($user);
            $cart->setTotalPrice(0);
        }

        return $this->render('user/cart.html.twig', [
            'cartProducts' => $cart->getCartProducts(),
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(Product $product, Request $request): Response
    {
        $quantity = (int)$request->request->get('quantity', 1);
        $user = $this->getUser();
        $cart = $this->cartService->getCurrentCart($user);

        $this->cartService->addProduct($cart, $product, $quantity);
        $this->em->flush();

        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function clear(): Response
    {
        $user = $this->getUser();
        $cart = $this->cartService->getCurrentCart($user);

        $this->cartService->clearCart($cart);
        $this->addFlash('success', 'Panier vidé !');

        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/checkout', name: 'app_cart_checkout', methods: ['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkout(): Response
    {
        try {
            $this->cartService->checkout();
        } catch (CartException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_cart');
        }

        $this->addFlash('success', 'Votre commande a été validée.');

        return $this->redirectToRoute('app_account');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function remove(Product $product, Request $request): Response
    {
        $quantity = (int)$request->request->get('quantity', 1);
        $user = $this->getUser();
        $cart = $this->cartService->getCurrentCart($user);

        $productRemoved = $this->cartService->removeProduct($cart, $product, $quantity);
        $this->em->flush();

        if ($productRemoved) {
            $this->addFlash('danger', 'Produit supprimé du panier.');
        } else {
            $this->addFlash('info', 'Quantité mise à jour.');
        }

        return $this->redirectToRoute('app_cart');
    }
}
