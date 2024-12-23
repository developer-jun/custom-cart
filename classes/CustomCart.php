<?php

namespace Cart;

use Traits\HelperTrait;
use \Cart\Product;
use \Cart\CartItem;
use \Cart\CartMessages;

trait CartTrait {

}


class CustomCart {	

	use HelperTrait;

	protected array $error;
	protected $customer;
	protected $current_product_id;
	
	protected $ship_id;
	protected $ship_name;
	protected $ship_address;
	protected $ship_pc;
	protected $ship_city;
	protected $ship_country;
	protected $ship_msg;
	
	public array $order_array;

	protected $db;
	public $cart_messages;

	// constructor ...
	public function __construct($db, $customer_no = '0'){
		$this->db = $db;

		$this->error = [];
		$this->order_array = [];
		$this->cart_messages = new CartMessages();
		if (!isset($_SESSION['order_id'])) {
			$this->getOrder($customer_no);
			$this->removeOldOrders(true); // use false if everyting older 1 day should be removed
		}
	}

	// get an existing order for a customer, or insert a new one if none exist
	public function getOrder($customer) {
		$sql = "SELECT id FROM ". ORDERS ." WHERE customer_id = ? AND open = 'y'";
		$param = [$customer];		
		$customer_data = $this->db->fetch($sql, $param);

		if ($customer_data) {
			$_SESSION['order_id'] = $customer_data['id'];
		} else {
			$sql_new_customer = "INSERT INTO ". ORDERS ." (customer_id, order_date) VALUES (?, NOW())";
			$this->db->execute($sql_new_customer, $param);

			if(!$this->db->hasError()) {
				$_SESSION['order_id'] = $this->db->lastInsertId();
			} else {
				$this->error[] = $this->db->getErrors();
			}
		}
	}

	private function getCartItem($product_id): ?CartItem {
		$sql = "SELECT * FROM ". ORDER_ROWS ." WHERE product_id = ? AND order_id = ?";
		$param = [$product_id, $_SESSION['order_id']];
		$cart_item = $this->db->fetch($sql, $param);

		if ($cart_item) {
			return new CartItem($cart_item['id'], $cart_item['order_id'], $cart_item['product_id'], $cart_item['product_name'], $cart_item['price'], $cart_item['vat_perc'], $cart_item['quantity']);
		}

		return null;
	}	

	// get all order rows from the DB and store them in to an array
	public function getCartItems(): ?array {
		$sql = "SELECT r.id, r.product_id, r.product_name, r.price, r.vat_perc, r.quantity FROM ". ORDER_ROWS ." AS r, ". ORDERS ." AS ord 
			WHERE ord.id = r.order_id AND ord.id = ? AND ord.open = 'y'"; //, ORDER_ROWS, ORDERS, $_SESSION['order_id']);
		$param = [$_SESSION['order_id']];
		$orders = $this->db->fetchAll($sql, $param);
		if ($orders) {
			return $orders;
			
		} 
		
		return null;
	}

	// get the number of ordered rows which belong to this customer
	public function getNumberOfItems(): int {
		$sql = "SELECT Count(*) as total FROM ". ORDER_ROWS ." AS cr, ". ORDERS ." AS co  WHERE co.id = cr.order_id AND co.id = ? AND co.open = 'y'"; // order_id = :order_id AND product_id = :product_id"; //, ORDER_ROWS, $_SESSION['order_id'], $product);
		$param = [$_SESSION['order_id']];
		$result = $this->db->fetch($sql, $param);
		var_dump($result);
		if ($result) {
			return $result['total'];
		}		
		return 0;
	}

	public function removeOldOrders($remove_only_zeros = true): void {
		$param = [VALID_UNTIL * 86400];
		if (RECOVER_ORDER) { // false by default
			$sql = "DELETE FROM ". ORDERS ." WHERE open = 'y' AND order_date < NOW() - INTERVAL ? SECOND";
		} else {
			//$sql = "DELETE FROM ". ORDERS ." WHERE open = 'y' AND customer_id = ? AND order_date < NOW() - INTERVAL ? SECOND";
			// $param = [$customer_no, ...$param];
			$sql = "DELETE FROM ". ORDERS ." WHERE open = 'y' AND order_date < NOW() - INTERVAL ? SECOND";
		}
		$sql .= ($remove_only_zeros) ? " AND customer_id = 0" : "";

		$this->db->execute($sql, $param);
	}
	
	// this method will chek if a order row for this product already exist
	public function checkExistingCartItem($product_id): string {
		$sql = "SELECT Count(*) as totalRows FROM ". ORDER_ROWS ." WHERE order_id = ? AND product_id = ?"; //, ORDER_ROWS, $_SESSION['order_id'], $product);
		$param = [$_SESSION['order_id'], $product_id];
		$totalRows_data = $this->db->fetch($sql, $param);

		if ($totalRows_data['totalRows'] > 0) {
			return $totalRows_data['totalRows'];
		} else {
			return false;
		}
	}

	// insert new cart item
	public function addItemToCart(Product $p, $quantity) {
		$sql = "INSERT INTO ". ORDER_ROWS ." (order_id, product_id, product_name, price, vat_perc, quantity) 
			VALUES (?, ?, ?, ?, ?, ?)";
		$params = [
			$_SESSION['order_id'], 
			$p->id, 
			$p->name, 
			$p->price, 
			$p->vat_perc ?? VAT_VALUE, 
			$quantity
		];
		$isInsertSuccess = $this->db->execute($sql, $params);
		if ($isInsertSuccess) {			
			$this->cart_messages->setMessage('success', 'Successfully added item to cart.');
		} else {			
			$this->cart_messages->setMessage('error', 'An error occured while trying to add item to cart.');
		}	
	}

	// update existing cart item's quantity
	public function updateCartItem(CartItem $cart_item, $quantity) {
		$sql = "UPDATE ". ORDER_ROWS ." SET quantity = ? WHERE id = ? AND order_id = ?";
		$params = [$cart_item->quantity + intval($quantity), $cart_item->id, $cart_item->order_id];
		
		$isUpdateSuccess = $this->db->execute($sql, $params);
		if ($isUpdateSuccess) {
			$this->cart_messages->setMessage('success', 'Successfully updated cart items.');
		} else {
			$this->cart_messages->setMessage('error', 'An error occured while trying to update cart items.');
		}
	}

	//
	public function deleteCartItem($cart_item_id = 0): bool {
		$sql = "DELETE FROM ". ORDER_ROWS ." WHERE id = ? AND order_id = ?";
		$params = [$cart_item_id, $_SESSION['order_id']];
		$isDeleteSuccess = $this->db->execute($sql, $params);
		if ($isDeleteSuccess) {
			//$this->error[] = 'Delete Success'; // $this->messages(15);
			return true;
		}
		
		return false;
	}

	// handle a order row while using the methodes above
	public function addToCart(Product $p, $quantity) {
		if (!intval($quantity)) {
			$this->cart_messages->setMessage('error', 'Please enter a valid quantity');
			return;
		}
		$cart_item = $this->getCartItem($p->id);
		if ($cart_item) {
			$this->updateCartItem($cart_item, $quantity);
		} else {
			$this->addItemToCart($p, $quantity);
		} 
	}	

	// show total value of the current cart
	public function showTotalValue() {
		$sql = "SELECT SUM(quantity * price) AS total FROM ". ORDER_ROWS ." WHERE order_id = ?"; //, ORDER_ROWS, $_SESSION['order_id']);
		$param = [$_SESSION['order_id']];
		
		$result = $this->db->fetch($sql, $param);

		if ($result) {
			return $result['total'];
		}
		
		return 0;		
	}

	// calculate VAT, switch between true and false to handle netto or brutto prices
	public function createTotalVAT() {
		$sql = "SELECT price, vat_perc, quantity FROM ". ORDER_ROWS ." WHERE order_id = ?";
		$param = [$_SESSION['order_id']];
		
		$result = $this->db->fetch($sql, $param);

		if ($result) {
			$vat = 0;
			$vat_dec = $result['vat_perc'] / 100;
			if (INCLUDE_VAT) {
				$vat = $vat + ($result['price'] * $result['quantity']) / (1 + $vat_dec) * $vat_dec;
			} else {
				$vat = $vat + ($result['price'] * $result['quantity']) * $vat_dec;
			}
			return $vat;
		}

		return 0;
	}

	// check if already an shipment record exist, if yes return the data
	public function checkReturnShipment(): bool {
		$sql = "SELECT COUNT(*) AS total FROM ". SHIP_ADDRESS ." WHERE order_id = ?"; //, SHIP_ADDRESS, $_SESSION['order_id']);
		$param = [$_SESSION['order_id']];
		$result = $this->db->fetch($sql, $param);

		if ($result) {
			return $result['total'] > 0;
		}
		return false;		
	}
	/*
	// read the current shipment data and set the variabels
	public function set_shipment_data() {
		if (!$this->check_return_shipment()) { // create an empty record if there is no shipment data
			$this->insert_new_shipment();
		}
		$sql = sprintf("SELECT * FROM %s WHERE order_id = %d", SHIP_ADDRESS, $_SESSION['order_id']);
		if ($result = mysql_query($sql)) {
			$obj = mysql_fetch_object($result);
			$this->ship_name = $obj->name;
			$this->ship_address = $obj->address;
			$this->ship_pc = $obj->postal_code;
			$this->ship_city = $obj->place;
			$this->ship_country = $obj->country;
			$this->ship_msg = $obj->message;
		} else {
			$this->error = $this->messages(1);
		}
	}
	// function to insert a new shipment record
	public function insert_new_shipment() {
		$sql = sprintf("INSERT INTO %s (order_id, name, address, postal_code, place, country) VALUES (%d, %s, %s, %s, %s, %s)",
			SHIP_ADDRESS, 
			$_SESSION['order_id'],
			$this->prepare_string_value($this->ship_name), 
			$this->prepare_string_value($this->ship_address), 
			$this->prepare_string_value($this->ship_pc), 
			$this->prepare_string_value($this->ship_city), 
			$this->prepare_string_value($this->ship_country));
		if (!mysql_query($sql)) {
			$this->error = $this->messages(1);
		}
	}
	// insert/update the shipment address vor the current order
	public function update_shipment($name, $address, $postal, $city, $country, $msg) {
		if ($this->check_return_shipment()) {
			$sql = sprintf("UPDATE %s SET name = %s, address = %s, postal_code = %s, place = %s, country = %s, message = %s WHERE order_id = %d", 
				SHIP_ADDRESS, 
				$this->prepare_string_value($name), 
				$this->prepare_string_value($address), 
				$this->prepare_string_value($postal), 
				$this->prepare_string_value($city), 
				$this->prepare_string_value($country),
				$this->prepare_string_value($msg),
				$_SESSION['order_id']);
			if (mysql_query($sql)) {
				$this->error = $this->messages(21);
			} else {
				$this->error = $this->messages(1);
			}
		}	
	}
		*/
	// this method will delete all records for the current order and the visitor is redirect to the main page
	public function cancelOrder() {
		$err_level = 0;
		if (!mysql_query(sprintf("DELETE FROM %s WHERE order_id = %d", SHIP_ADDRESS, $_SESSION['order_id']))) $err_level++;
		if (!mysql_query(sprintf("DELETE FROM %s WHERE order_id = %d", ORDER_ROWS, $_SESSION['order_id']))) $err_level++;
		if (!mysql_query(sprintf("DELETE FROM %s WHERE id = %d", ORDERS, $_SESSION['order_id']))) $err_level++;
		if ($err_level > 0) {
			$this->error[] = $this->messages(1);
		} else {
			unset($_SESSION['order_id']);
			header("Location: ".PROD_IDX);
		}	
	}
	// use the property $show_address to show/hide the shipment in the confirmation mail
	public function checkOut($mailto, $show_address = true) {
		$this->set_shipment_data();
		if ($this->mail_order($mailto, $show_address)) {
			header("Location: ".CONFIRM);
		} else {
			$this->error[] = $this->messages(2);
		}
	}
	/*
	public function mail_order($to, $show_shipment) { 
		$header = "From: \"".SITE_MASTER."\" <".SITE_MASTER_MAIL.">\r\n";
		$header .= "Cc: ".SITE_MASTER_MAIL."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/plain; charset=\"".MAIL_ENCODING."\"\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n";
		$subject =  $this->messages(51); //your mailsubject incl. current date
		$body = $this->messages(52); // the body begin text
		$this->show_ordered_rows(); // rows in the order_array
		// next all order rows
		$body .= $this->messages(56); // the table header
		foreach ($this->order_array as $val) {
			$body .= sprintf("\t%-10s  %-30s  %-20s  %-12s\r\n", $val['quantity'], $val['product_name'], $val['product_id'], $this->format_value($val['price'], false));
		}
		$body .= $this->messages(53); // the total amount
		$body .= $this->messages(54); // the VAT amount
		$body .= ($show_shipment) ? $this->messages(57) : ""; // shipment adres...
		$body .= $this->messages(58); // the message.
		$body .= $this->messages(55); // kind regards...
		if (mail($to, $subject, $body, $header)) {
			return true;
		} else {
			return false;
		}
	}
		*/
	public function closeOrder() {
		$sql = sprintf("UPDATE %s SET processed_on = NOW(), open = 'n' WHERE id = %d", ORDERS, $_SESSION['order_id']);
		if (mysql_query($sql)) {
			$this->error[] = $this->messages(22);
			unset($_SESSION['order_id']);
		} else {
			$this->error[] = $this->messages(1);
		}
	}

	public function getErrors(): string {
		$error_string = '';
		foreach($this->error as $error) {
			$error_string .= '<li>'. $error .'</li>';
		}
		if($error_string != '') {
			$error_string .= '<ul>'. $error_string .'</ul>';
		}
        return $error_string;
    }
	

}