<?php
namespace Models;

class Product {
    public function __construct(
        public $id, 
        public $name, 
        public $description, 
        public $price, 
        public $vat_percentage, 
        public $thumbnail, 
        public $large_image) {}

    public function getData(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'vat_percentage' => $this->vat_percentage,
            'thumbnail' => $this->thumbnail,
            'large_image' => $this->large_image
        ];
    }
}