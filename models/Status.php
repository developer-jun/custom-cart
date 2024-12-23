<?php

namespace Models;

class Status {
    public function __construct(
        public string $type, 
        public string $message){}
    

    public function messageTag(): string {
        return '<div>'.$this->message.'</div>';
    }
    
    public function toArray(): array {
        return [
            'type' => $this->type,
            'message' => $this->message,
        ];
    }
}