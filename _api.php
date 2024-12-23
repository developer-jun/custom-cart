<?php
use \Libraries\CSRFProtection;


function APISecurityCheck($csrfApiToken): mixed {
    $csrf = new CSRFProtection();
    if(!$csrf->validateCSRFAPI($csrfApiToken)) {
        return [
            'title' => 'API Request is invalid',
            'cart_total_quantity' => -1,
            'result' => [
                'type' => 'error', 
                'content' => 'CSRF Security Token is invalid. Please refresh the page and try again.',
                'message' => '',
            ]
        ];
    }

    return true;    
}


function removeCartItem($cart_obj, $cart_item_id) {
    $cart_obj->deleteCartItem($cart_item_id);

    return [
        'type' => 'success', 
        'message' => 'Successfully Removed Item from Cart',
        'content' => $cart_view->cartAsToastContent($cart_obj->getCartItems()),
    ];
}