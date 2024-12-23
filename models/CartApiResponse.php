<?php
namespace Models;

use \Models\Status;
use \Models\Notification;

class CartApiResponse {
    public function __construct(
        public Notification $status_notification,
        public int $total_quantity = 0,
        public float $total_amount = 0.0,
        public string $cart_summary = ''
    ) {}

    public function toArray(): array {
        return [
            'status_notification'   => $this->status_notification->toArray(),
            'total_quantity'        => $this->total_quantity,
            'total_amount'          => $this->total_amount,
            'cart_summary'          => $this->cart_summary,
        ];
    }

    public function jsonSerialize(): array {
        return $this->toArray();
    }
}

/*
class CartApiResponse extends Alert {
    // Alert { type, message }
    public $title;
    public $cart_content;
    public $cart_total_quantity;
    public $cart_total_amount;

    public function __construct($type, $message, $title, $cart_content, $cart_total_quantity, $cart_total_amount) {
        parent::__construct($type, $message);
        
        $this->title = $title;        
        $this->cart_content = $cart_content;        
        $this->cart_total_quantity = $cart_total_quantity;
        $this->cart_total_amount = $cart_total_amount;
    }
}
    */