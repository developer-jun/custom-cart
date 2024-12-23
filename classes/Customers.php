<?php

namespace Cart;

use \Cart\Customer;
use \Traits\CookieTrait;

class Customers {

    use CookieTrait;

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function checkCustomer(): ?Customer {
        $customer = null;
        // check if session already exists
        if (isset($_SESSION['customer_id'])) {
            echo '<br /> SESSION CUSTOMER ID: ' . $_SESSION['customer_id'];
            //$customer = $this->retrieveCustomer($_SESSION['customer_id']);
            if($customer = $this->getCustomer($_SESSION['customer_id'])){
                return $customer;
            }          
        } 

        // try to get customer from cookie
        if($customer = $this->getCustomerFromCookie()) {
            echo '<br /> Cookie ID: ' . $customer->id;
            return $customer;
        }
        
        // create a new customer
        $customer = $this->generateNewCustomer();
        $this->setCustomerCookie($customer);

        // set session
        $_SESSION['customer_id'] = $customer->id;
        return $customer;    
    }   

    private function setCustomerCookie(Customer $customer) {        
        $this->setCookie('customer_id', $customer->id, time() + 3600 * 24 * 7);
        $this->setCookie('customer_data', json_encode($customer), time() + 3600 * 24 * 7);
    }

    public function generateNewCustomer(): Customer {
        $customer =  new Customer(0, bin2hex(random_bytes(6)), '', 'guest-' . random_int(1000, 9999), '');
        $customer_id = $this->createCustomer($customer);
        $customer->id = $customer_id;
        return $customer;
    }

    private function getCustomerFromCookie(): ?Customer {
        $customer_data = $this->getCookie('customer_data');
        if ($customer_data) {
            $customer = json_decode($customer_data);
            if ($customer) {
                return new Customer($customer->id, $customer->uid, $customer->email, $customer->name, $customer->address);
            }
        }
        
        unset($_SESSION['customer_id']);
        return null;
    }

    public function createCustomer($customer_data): ?int {
        $sql = "INSERT INTO custom_cart_customers (uid, name, email, address) VALUES (?, ?, ?, ?)";
        $params = [
            $customer_data->uid,
            $customer_data->name,
            $customer_data->email,
            $customer_data->address
        ];
        $isInsertSuccess = $this->db->execute($sql, $params);
        if ($isInsertSuccess) {
            return $this->db->lastInsertId();
        } 
        // else { $this->db->hasError(); }

        return false;
    }

    public function getCustomer($customer_id = 0): ?Customer {
        $sql = "SELECT * FROM custom_cart_customers WHERE id = ?";
        $param = [$customer_id];
        $cust_result = $this->db->fetch($sql, $param);
        if ($cust_result) {
            return new Customer($cust_result['id'], $cust_result['uid'], $cust_result['email'], $cust_result['name'], $cust_result['address']);
        }
        return false;        
    }

    public function isCustomer($customer_id = 0): bool {
        $sql = "SELECT COUNT(*) as total FROM custom_cart_customers WHERE id = ?";
        $param = [$customer_id];
        $cust_result = $this->db->fetch($sql, $param);

        if ($cust_result) {
            return $cust_result['total'] > 0;
        }
        return false;
    }


}