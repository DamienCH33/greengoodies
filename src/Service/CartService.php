<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\User;
use App\Exception\CartException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CartService
{
    public function __construct(private EntityManagerInterface $em, private Security $security) {}

    public function getCurrentCart(User $user, bool $createIfMissing = true): Cart
    {
        $cart = $this->em->getRepository(Cart::class)->findOneBy(['user' => $user], ['id' => 'DESC']);

        if (!$cart && $createIfMissing) {
            $cart = new Cart();
            $cart->setUser($user)
                ->setTotalPrice(0);
        }

        return $cart;
    }
    public function addProduct(Cart $cart, Product $product, int $quantity): void
    {
        if (!$cart->getId()) {
            $this->em->persist($cart);
            $this->em->flush();
        }

        foreach ($cart->getCartProducts() as $cartProduct) {
            if ($cartProduct->getProduct()->getId() === $product->getId()) {
                $cartProduct->setQuantity($cartProduct->getQuantity() + $quantity);
                $cartProduct->setPrice($product->getPrice());
                $this->calculateTotal($cart);
                $this->em->flush();
                return;
            }
        }

        $cartProduct = new CartProduct();
        $cartProduct->setProduct($product)
            ->setQuantity($quantity)
            ->setPrice($product->getPrice())
            ->setCart($cart);

        $cart->addCartProduct($cartProduct);
        $this->em->persist($cartProduct);
        $this->calculateTotal($cart);
        $this->em->flush();
    }
    public function calculateTotal(Cart $cart): float
    {
        $total = 0;
        foreach ($cart->getCartProducts() as $cartProduct) {
            $total += $cartProduct->getPrice() * $cartProduct->getQuantity();
        }
        $cart->setTotalPrice($total);

        return $total;
    }
    public function clearCart(Cart $cart): void
    {
        foreach ($cart->getCartProducts() as $cartProduct) {
            $this->em->remove($cartProduct);
        }

        $this->em->remove($cart);
        $this->em->flush();
    }
    public function removeProduct(Cart $cart, Product $product, int $quantity = 1): bool
    {
        foreach ($cart->getCartProducts() as $cartProduct) {
            if ($cartProduct->getProduct()->getId() === $product->getId()) {

                if ($cartProduct->getQuantity() <= $quantity) {
                    $cart->removeCartProduct($cartProduct);
                    $this->em->remove($cartProduct);
                    $this->calculateTotal($cart);
                    return true;
                }

                $cartProduct->setQuantity($cartProduct->getQuantity() - $quantity);
                $this->calculateTotal($cart);
                return false;
            }
        }

        return false;
    }

    public function checkout()
    {
        $user = $this->getUser();
        $cart = $this->getCurrentCart($user);

        if ($cart->getCartProducts()->isEmpty()) {
           throw new CartException('Votre panier est vide');
        }
        $order = Order::createFromCart($cart);

        $this->clearCart($cart);
        
        $this->em->persist($order);
        $this->em->flush();
    }

    private function getUser()
    {
        return $this->security->getUser();
    }
}
