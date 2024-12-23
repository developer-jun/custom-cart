<?php
namespace Cart;

class Product {
    public $id;
    public $name;
    public $description;
    public $price; 
    public $art_no;
    public $vat_perc;

    public function __construct($id, $name, $description, $price, $art_no, $vat_perc) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->art_no = $art_no;
        $this->vat_perc = $vat_perc;
    }    
}