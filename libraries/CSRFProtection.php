<?php

declare(strict_types=1);
namespace Libraries;

/**
 * Class Security
 *
 * Provides methods that help protect your site against
 * Cross-Site Request Forgery attacks.
 *
 */
class CSRFProtection
{
    protected const CSRF_HASH_BYTES = 32;
    private $hash;
    private $token_name;

    public function __construct() {
        $this->token_name = CSRF_TOKEN_NAME ?? 'csrf_token_name';
        $this->generateHash();
    }
    
    public function generateHash(): void {
        
        if(!isset($_SESSION[$this->token_name])) {
            $this->hash = bin2hex(random_bytes(static::CSRF_HASH_BYTES));
            $_SESSION[$this->token_name] = $this->hash;

            return;
        }
        
        $this->hash = $_SESSION[$this->token_name];
    }

    public function getTokenName(): string {
        return $this->token_name;
    }

    public function getHash(): ?string {
        return $this->hash;
    }    

    public function validateCSRFField(): bool {      
        if(isset($_POST[$this->token_name]) && $_POST[$this->token_name] == $this->hash) {
            return true;
        }

        return false;
    }

    public function validateCSRFAPI(string $request_csrf_token = null): bool {     
        return (isset($request_csrf_token) && $request_csrf_token === $_SESSION[$this->token_name]);
    }   
}
