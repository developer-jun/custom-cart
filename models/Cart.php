<?php
namespace Models;

class Cart {
    public int $id;
    public int $order_id;
    public int $product_id;
    public string $product_name;
    public float $price;
    public float $vat_perc;
    public int $quantity;    

    public function __construct($id, $order_id, $product_id, $product_name, $price, $vat_perc, $quantity) {        
        $this->id           = $id;
        $this->order_id     = $order_id;
        $this->product_id   = $product_id;
        $this->product_name = $product_name;
        $this->price        = $price;
        $this->vat_perc     = $vat_perc;
        $this->quantity     = $quantity;
    }

    public function toArray(): array {
        return [
            'id'            => $this->id,
            'order_id'      => $this->order_id,
            'product_id'    => $this->product_id,
            'product_name'  => $this->product_name,
            'price'         => $this->price,
            'vat_perc'      => $this->vat_perc,
            'quantity'      => $this->quantity
        ];
    }
}