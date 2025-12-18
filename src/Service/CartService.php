<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\OrderProduct;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getCurrentCart(User $user, bool $createIfMissing = true): Order
    {
        $cart = $this->em->getRepository(Order::class)->findOneBy(['user' => $user], ['id' => 'DESC']);

        if (!$cart && $createIfMissing) {
            $cart = new Order();
            $cart->setUser($user)
                ->setTotalPrice(0);
        }

        return $cart;
    }
    public function addProduct(Order $cart, Product $product, int $quantity): void
    {
        if (!$cart->getId()) {
            $this->em->persist($cart);
            $this->em->flush();
        }

        foreach ($cart->getOrderProducts() as $orderProduct) {
            if ($orderProduct->getProduct()->getId() === $product->getId()) {
                $orderProduct->setQuantity($orderProduct->getQuantity() + $quantity);
                $orderProduct->setPrice($product->getPrice());
                $this->calculateTotal($cart);
                $this->em->flush();
                return;
            }
        }

        $orderProduct = new OrderProduct();
        $orderProduct->setProduct($product)
            ->setQuantity($quantity)
            ->setPrice($product->getPrice())
            ->setOrder($cart);

        $cart->addOrderProduct($orderProduct);
        $this->em->persist($orderProduct);
        $this->calculateTotal($cart);
        $this->em->flush();
    }
    public function calculateTotal(Order $cart): float
    {
        $total = 0;
        foreach ($cart->getOrderProducts() as $orderProduct) {
            $total += $orderProduct->getPrice() * $orderProduct->getQuantity();
        }
        $cart->setTotalPrice($total);

        return $total;
    }
    public function clearCart(Order $cart): void
    {
        foreach ($cart->getOrderProducts() as $orderProduct) {
            $this->em->remove($orderProduct);
        }

        $cart->setTotalPrice(0);
        $this->em->flush();
    }
    public function removeProduct(Order $cart, Product $product, int $quantity = 1): bool
    {
        foreach ($cart->getOrderProducts() as $orderProduct) {
            if ($orderProduct->getProduct()->getId() === $product->getId()) {

                if ($orderProduct->getQuantity() <= $quantity) {
                    $cart->removeOrderProduct($orderProduct);
                    $this->em->remove($orderProduct);
                    $this->calculateTotal($cart);
                    return true;
                }

                $orderProduct->setQuantity($orderProduct->getQuantity() - $quantity);
                $this->calculateTotal($cart);
                return false;
            }
        }

        return false;
    }
}
