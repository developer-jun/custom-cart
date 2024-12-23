<?php

namespace Models;

class Alert {
    public function __construct(public string $type, public string $message){}

    public function alertTag(): string {
        return `
            <div class="alert alert-{$this->type}" role="alert">
                <strong>{$this->message}</strong>
            </div>
        `;
    }
}