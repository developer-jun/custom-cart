<?php
namespace Cart;

class CartMessages {
    private $messages = [];

    public function __construct($messages = []) {
        $this->messages = $messages;
    }

    public function setMessage($type, $message) {
        if($message != '') {
            $this->messages[] = ['type' => $type, 'message' => $message];
        }
    }

    public function getMessages(): array {
        return $this->messages;
    }

    public function hasCartMessages(): bool {
        return !empty($this->messages);
    }
}