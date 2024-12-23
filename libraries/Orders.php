<?php

namespace Libraries;

use \Libraries\Database;
use \Models\Order;

class Orders {

	protected $db;
	protected $orders;
	protected $order;

	// constructor ...
	public function __construct() {
		$this->db = new Database();
		$this->orders = new Order();
	}

	// get all orders from the DB
	public function getOrders(): array {
		$sql = "SELECT * FROM ". ORDERS_TABLE ." WHERE open = 'y'";
		$orders = $this->db->fetchAll($sql);
		if ($orders) {
			return $orders;
		} 
		return [];
	}

	// get an order by id
	public function getOrder($order_id): Order {
		$sql = "SELECT * FROM ". ORDERS_TABLE ." WHERE id = ? AND open = 'y'";
		$param = [$order_id];
		$order = $this->db->fetch($sql, $param);
		if ($order) {
			return new Order($order['id'], $order['customer_id'], $order['order_date'], $order['open'], $order['processed_on']);
		}
		return null;
	}

	// get all orders from the DB and store them in to an array
	public function getOrdersArray(): array {
		$sql = "SELECT * FROM ". ORDERS_TABLE ." WHERE open = 'y'";
		$orders = $this->db->fetchAll($sql);
		if ($orders) {
			return $orders;
		} 
		return [];
	}

	// get the number of orders which belong to this customer
	public function getNumberOfOrders(): int {
		$sql = "SELECT Count(*) as total FROM ". ORDERS_TABLE ." WHERE open = 'y'";
		$result = $this->db->fetch($sql);
		if ($result) {
			return $result['total'];
		}		
		return 0;
	}

	// get the number of orders which belong to this customer
	public function getNumberOfOrdersByCustomer($customer_id): int {
		$sql = "SELECT Count(*) as total FROM ". ORDERS_TABLE ." WHERE open = 'y' AND customer_id = ?";
		$param = [$customer_id];
		$result = $this->db->fetch($sql, $param);
		if ($result) {
			return $result['total'];
		}		
		return 0;
	}

	// get the number of orders which belong to this customer
	public function getNumberOfOrdersByCustomerArray($customer_id): int {
		$sql = "SELECT Count(*) as total FROM ". ORDERS_TABLE ." WHERE open = 'y' AND customer_id = ?";
		$param = [$customer_id];
		$result = $this->db->fetch($sql, $param);
		if ($result) {
			return $result['total'];
		}		
		return 0;
	}
}