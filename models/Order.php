<?php

namespace Models;

// use \Models\Model;

class Order { //extends Model {

	public $id;
	public $customer_id;
	public $order_date;
	public $open;
	public $processed_on;

	public function __construct($id = 0, $customer_id = 0, $order_date = 0, $open = 'y', $processed_on = 0) {
		$this->id = $id;
		$this->customer_id = $customer_id;
		$this->order_date = $order_date;
		$this->open = $open;
		$this->processed_on = $processed_on;
	}

	public function getData(): array {
		return [
			'id' => $this->id,
			'customer_id' => $this->customer_id,
			'order_date' => $this->order_date,
			'open' => $this->open,
			'processed_on' => $this->processed_on
		];
	}

}