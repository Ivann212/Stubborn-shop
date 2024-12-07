<?php

namespace App\Service;

use App\Entity\Product;

class CartService
{
    private $cart = [];

    public function addProduct(Product $product, int $quantity = 1)
    {
        $this->cart[] = [
            'product' => $product,
            'quantity' => $quantity,
        ];
    }

    public function removeProduct(Product $product)
    {
        foreach ($this->cart as $key => $item) {
            if ($item['product'] === $product) {
                unset($this->cart[$key]);
                return;
            }
        }
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }
}
