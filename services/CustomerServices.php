<?php
namespace Services;

use \Models\Customer;
use \Traits\CookieTrait;

class CustomerServices {
    use CookieTrait;

    public function __construct(private $db) {}

    public function checkCustomer(): ?Customer {
        $customer = null;

        if (isset($_SESSION['customer_id'])) {
            if($customer = $this->getCustomer($_SESSION['customer_id'])) {
                return $customer;
            }          
        } 

        if($customer = $this->getCustomerFromCookie()) {
            $_SESSION['customer_id'] = $customer->id;
            return $customer;
        }
        
        $customer = $this->generateNewCustomer();
        $this->setCustomerCookie($customer);

        $_SESSION['customer_id'] = $customer->id;
        return $customer;    
    }     

    public function generateNewCustomer(): Customer {        
        $customer = new Customer(
            id: 0, 
            uid: bin2hex(random_bytes(6)), 
            email: '', 
            name: 'guest-' . random_int(1000, 9999), 
            address: ''
        ); // default values
        $sql = "INSERT INTO ". CUSTOMERS_TABLE ." (uid, name, email, address, created_on) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $customer->uid,
            $customer->name,
            $customer->email,
            $customer->address,
            date("Y-m-d H:i:s")
        ];

        if ($isInsertSuccess = $this->db->execute($sql, $params)) {
            $customer->id = $this->db->lastInsertId();
        }

        return $customer;
    }    

    public function getCustomer($customer_id = 0): ?Customer {
        $sql = "SELECT * FROM ". CUSTOMERS_TABLE ." WHERE id = ?";
        $param = [$customer_id];
        $cust_result = $this->db->fetch($sql, $param);
        if ($cust_result) {
            return new Customer($cust_result['id'], $cust_result['uid'], $cust_result['email'], $cust_result['name'], $cust_result['address']);
        }
        return null;        
    }

    public function isCustomer($customer_id = 0): bool {
        $sql = "SELECT COUNT(*) as total FROM ". CUSTOMERS_TABLE ." WHERE id = ?";
        $param = [$customer_id];
        $cust_result = $this->db->fetch($sql, $param);

        if ($cust_result) {
            return $cust_result['total'] > 0;
        }
        return false;
    }

    private function setCustomerCookie(Customer $customer) {        
        $this->setCookie('customer_id', $customer->id, time() + 3600 * 24 * 7);
        $this->setCookie('customer_data', json_encode($customer), time() + 3600 * 24 * 7);
    }

    private function getCustomerFromCookie(): ?Customer {
        if ($customer_data = $this->getCookie('customer_data')) {
            if ($customer = json_decode($customer_data)) {
                return new Customer($customer->id, $customer->uid, $customer->email, $customer->name, $customer->address);
            }
        }
        
        unset($_SESSION['customer_id']);
        return null;
    }
}