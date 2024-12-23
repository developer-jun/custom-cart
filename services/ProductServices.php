<?php
namespace Services;
use \Models\Product;

class ProductServices {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getProducts() {
        $sql = "SELECT * FROM ". PRODUCTS_TABLE;
        $prod_result = $this->db->fetchAll($sql, []);
        return $prod_result;        
    }

    public function getProduct($product_id = 0) {
        $sql = "SELECT * FROM ". PRODUCTS_TABLE ." WHERE id = ?";
        $param = [$product_id];
        $prod_result = $this->db->fetch($sql, $param);
        if ($prod_result) {
            return new Product(
                id: $prod_result['id'], 
                name: $prod_result['name'], 
                description: $prod_result['description'], 
                price: $prod_result['price'],                
                vat_percentage: $prod_result['vat_percentage'],
                thumbnail: $prod_result['thumbnail'],
                large_image: $prod_result['large_image']
            );                
        }
        return false;        
    }

    public function isProduct($product_id = 0): bool {
        $sql = "SELECT COUNT(*) as total FROM ". PRODUCTS_TABLE ." WHERE id = ?";
        $param = [$product_id];
        $prod_result = $this->db->fetch($sql, $param);

        if ($prod_result) {
            return $prod_result['total'] > 0;
        }
        return false;
    }
}