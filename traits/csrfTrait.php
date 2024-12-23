<?php

namespace Traits;

trait csrfTrait {
    private $csrf;    
    public function generateCSRFField(): string {
        $csrf = new \Libraries\CSRFProtection();
        return '<input type="hidden" name="'. $csrf->getTokenName() .'" value="'. $csrf->getHash() .'">';
    }

    public function validateCSRF(): string {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $csrf = new \Libraries\CSRFProtection();        
        return $csrf->validateCSRFField(CSRF_TOKEN_NAME);
    }

    public function validateCSRFAPI(): string {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $csrf = new \Libraries\CSRFProtection();        
        return $csrf->validateCSRFField(CSRF_TOKEN_NAME);
    }

    private function isPost(): bool {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            return true;
        }

        return false;
    }
}