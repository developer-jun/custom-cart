<?php

namespace Traits;


trait CookieTrait {

    public function setCookie($cookie_name, $cookie_value, $cookie_expiry = 0) {
        if ($cookie_expiry > 0) {
            setcookie($cookie_name, $cookie_value, $cookie_expiry);
            return;
        } 
        
        setcookie($cookie_name, $cookie_value); // expires after browser is closed
    }

    public function getCookie($cookie_name): ?string {
        if (isset($_COOKIE[$cookie_name])) {
            return $_COOKIE[$cookie_name];
        }
        return false;
    }

    public function deleteCookie($cookie_name): void {
        setcookie($cookie_name, '', time() - 3600);
    }
}