<?php
namespace Libraries;

class Messages {
    //private $messages = [];

    public function __construct(private $messages = []) {
        //$this->messages = $messages;
    }

    public function setMessage($type, $message) {
        if($message != '') {
            $this->messages[] = ['type' => $type, 'message' => $message];
        }
    }

    public function getMessages(): array {
        return $this->messages;
    }

    public function setMessages($messages = []) {
        if($messages) {
            foreach($messages as $message) {
                $this->messages[] = ['type' => $message['type'], 'message' => $message['message']];
            }
        }
    }

    public function hasCartMessages(): bool {
        return !empty($this->messages);
    }
}