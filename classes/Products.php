<?php
namespace Cart;
use \Cart\Product;

class Products {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getProducts() {
        $sql = "SELECT * FROM custom_cart_example_products";
        $prod_result = $this->db->fetchAll($sql, []);
        return $prod_result;        
    }

    public function getProduct($product_id = 0) {
        $sql = "SELECT * FROM custom_cart_example_products WHERE id = ?";
        $param = [$product_id];
        $prod_result = $this->db->fetch($sql, $param);
        if ($prod_result) {
            return new Product($prod_result['id'], $prod_result['name'], $prod_result['description'], $prod_result['price'], $prod_result['art_no'], $prod_result['vat_perc']);
        }
        return false;        
    }

    public function isProduct($product_id = 0): bool {
        $sql = "SELECT COUNT(*) as total FROM custom_cart_example_products WHERE id = ?";
        $param = [$product_id];
        $prod_result = $this->db->fetch($sql, $param);

        if ($prod_result) {
            return $prod_result['total'] > 0;
        }
        return false;
    }
}