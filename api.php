<?php
require('config.php');
require('includes/helpers.php');

use \Libraries\DB;
use \Libraries\Messages;
use \Libraries\CSRFProtection;

use \Services\CartServices;
use \Services\ProductServices;
use \Services\CustomerServices;

use \Models\Product;
use \Models\Customer;
use \Models\CartApiResponse;
use \Models\Notification;
use \Models\Status;

//use \Views\PageView;
//use \Views\ProductView;
use \Views\CartView;

$CART_RESPONSE_DEFAULT = new CartApiResponse(
    status_notification: new Notification(
        title: 'Cart Notification',
        type: '',
        message: '', 
    ),
    cart_summary: '',
    total_quantity: 0,
    total_amount: 0,
);

// require('_api.php');
function securityCheck($csrfApiToken): void {
    $csrf = new CSRFProtection();
    if(!$csrf->validateCSRFAPI($csrfApiToken)) {
        
        
        $response = [
            'title' => 'API Request is invalid',
            'cart_total_quantity' => -1,
            'result' => [
                'type' => 'error', 
                'content' => 'CSRF Security Token is invalid. Please refresh the page and try again.',
                'message' => '',
            ]
        ];
        echo json_encode($response);
        die();

        // for future exapansions, use the swith default case to handle this type of error by manually modify the action (saving request to database, etc)
        
    }
}

function processRemoveCartItem($cart_obj, $cart_item_id, $cart_view): CartResponse {
    $cart_response = new CartApiResponse(
        type: '',
        message: '', 
        title: 'Cart Notification',
        cart_total_quantity: 0, 
        cart_total_amount: 0,
    );
    $result = [];
    if($cart_obj->deleteCartItem($cart_item_id)) {
        $result = [
            'type' => 'success', 
            'message' => 'Successfully Removed Item from Cart',
            'content' => $cart_view->cartItemsAsLists($cart_obj->getCartItems()),
        ];
    } else {
        $response['result'] = [
            'type' => 'error', 
            'message' => 'An error occured while trying to remove item from cart',
            'content' => '',
        ];
    }
    $cart_response->result = $result;
    $cart_response->cart_total_quantity = $cart_obj->getTotalCartQuantity();
    $cart_response->cart_total_amount = formatValue($cart_obj->computeCartTotal());

    return $cart_response;
}

function processUpdateCartItem($cart_data, $cart_services, $product_services, $cart_view): CartApiResponse {
    $result_notification = new Notification(
        title: 'Cart Notification', 
        message: 'An error occured while trying to insert item to cart', 
        type: 'error'
    ); // optimistic default

    // diffirentiate ADD OR UPDATE
    if(isset($cart_data['cart_item_id'])) {
        $cart_item = $cart_services->getCartItem(intval($cart_data['cart_item_id']));
        if($cart_item) {
            // UPDATE
            if($cart_services->updateCart($cart_item->id, intval($cart_data['quantity']))) {                
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        }         
    } else {
        if(isset($post_data['item_id']) 
            && $cart_item = $cart_services->getCartItem($cart_data['item_id'])) {
            if($cart_services->updateCart($cart_item->id, intval($cart_data['quantity']))) {
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        }
    }
   
    return new CartApiResponse(
        status_notification: $result_notification,        
        total_quantity     : $cart_services->getTotalCartQuantity(),
        total_amount       : $cart_services->computeCartTotal(),
        cart_summary       : $cart_view->cartItemsAsLists($cart_services->getCartItems()),
    ); 
}

function processUpdateCart($cart_data, $cart_services, $product_services, $cart_view): CartApiResponse {
    /*
    if(empty($cart_data)) {
        $cart_response->result = [
            'type' => 'error', 
            'content' => 'Cart Data is missing',
            'message' => '',
        ];
    }
    */
    
    // SOP: do some simple validation, don't trust request hence we need to verify that the data exists from the database along with user's session id.
    /*
    $cart_item = $cart_services->getCartItem(intval($cart_data['cart_item_id']));            
    
    if($cart_services->updateCart($cart_data['cart_item_id'], intval($cart_data['quantity']))) {
        $result_message = $cart_services->messages->getMessages();                
        $response['result'] = [
            'type' => $result_message[0]['type'], 
            'message' => $result_message[0]['message'],
            'content' => $cart_view->cartItemsAsLists($cart_services->getCartItems()),
        ];                
    } else {
        $response['result'] = [
            'type' => 'error', 
            'message' => 'An error occured while trying to update item in cart',
            'content' => '',
        ];
    }
    $response['cart_total_quantity'] = $cart_services->getTotalCartQuantity();
    $response['cart_total_amount'] = formatValue($cart_services->computeCartTotal());
    
    */

    $result_notification = new Notification(
        title: 'Cart Notification', 
        message: 'An error occured while trying to insert item to cart', 
        type: 'error'
    ); // optimistic default

    // diffirentiate ADD OR UPDATE
    if(isset($cart_data['cart_item_id'])) {
        $cart_item = $cart_services->getCartItem(intval($cart_data['cart_item_id']));
        if($cart_item) {
            // UPDATE
            if($cart_services->updateCart($cart_item->id, $cart_item->quantity + intval($cart_data['quantity']))) {                
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        } 
        
        /*else {
            // INSERT
            $product = $product_services->getProduct($post_data['product_id']);
            if($product && $cart_services->addToCart($product, abs(intval($post_data['quantity'])))) {
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Inserted Item to Cart';
            }
        }*/
    } else {
        if(isset($post_data['item_id']) 
            && $cart_item = $cart_services->getCartItem($cart_data['item_id'])) {
            if($cart_services->updateCart($cart_item->id, intval($cart_data['quantity']))) {
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        }
    }
    
            
    /*
    $product = $product_obj->getProduct($post_data['product_id'])) {
        if(!$cart_obj->addToCart($product, intval($post_data['quantity']))) {
            $result_notification->status->type = 'error'; 
            $result_notification->status->message = 'An error occured while trying to insert item to cart';
        }
    } else {
        if(isset($post_data['item_id']) 
            && $cart_item = $cart_obj->getCartItem($post_data['item_id'])) {
            if($cart_obj->updateCart($cart_item, intval($post_data['quantity']))) {
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        }
    }
    */

    return new CartApiResponse(
        status_notification: $result_notification,        
        total_quantity     : $cart_services->getTotalCartQuantity(),
        total_amount       : $cart_services->computeCartTotal(),
        cart_summary       : $cart_view->cartItemsAsLists($cart_services->getCartItems()),
    ); 
}

function processAddToCart($post_data, $cart_services, $product_services, $cart_view): CartApiResponse {
    $result_notification = new Notification(
        title: 'Cart Notification', 
        message: 'An error occured while trying to insert item to cart', 
        type: 'error'
    ); // optimistic default

    // diffirentiate ADD OR UPDATE
    if(isset($post_data['product_id'])) {
        $cart_item = $cart_services->getCartItemByProduct($post_data['product_id']);
        if($cart_item) {
            // UPDATE
            if($cart_services->updateCart($cart_item->id, $cart_item->quantity + intval($post_data['quantity']))) {                
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        } else {
            // INSERT
            $product = $product_services->getProduct($post_data['product_id']);
            if($product && $cart_services->addToCart($product, abs(intval($post_data['quantity'])))) {
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Inserted Item to Cart';
            }
        }
    } else {
        if(isset($post_data['item_id']) 
            && $cart_item = $cart_services->getCartItem($post_data['item_id'])) {
            if($cart_services->updateCart($cart_item->id, intval($post_data['quantity']))) {
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        }
    }
    
            
    /*
    $product = $product_obj->getProduct($post_data['product_id'])) {
        if(!$cart_obj->addToCart($product, intval($post_data['quantity']))) {
            $result_notification->status->type = 'error'; 
            $result_notification->status->message = 'An error occured while trying to insert item to cart';
        }
    } else {
        if(isset($post_data['item_id']) 
            && $cart_item = $cart_obj->getCartItem($post_data['item_id'])) {
            if($cart_obj->updateCart($cart_item, intval($post_data['quantity']))) {
                $result_notification->status->type = 'success'; 
                $result_notification->status->message = 'Successfully Updated Item from Cart';
            } else {
                $result_notification->status->type = 'error'; 
                $result_notification->status->message = 'An error occured while trying to update item in cart';
            }
        }
    }
    */

    return new CartApiResponse(
        status_notification: $result_notification,        
        total_quantity     : $cart_services->getTotalCartQuantity(),
        total_amount       : $cart_services->computeCartTotal(),
        cart_summary       : $cart_view->cartAsToastContent($cart_services->getCartItems(), $result_notification),
    ); 
}


$response = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = json_decode(file_get_contents('php://input'), true);   
    // a must have check
    securityCheck($_SERVER['HTTP_X_CSRF_TOKEN']);

    // Step 1:
    $db = new DB(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

    // Step 2:
    

    // Step 3:
    $customer_services = new CustomerServices($db);
    $customer_data = $customer_services->checkCustomer();

    // Step 4:
    $cart_services = new CartServices($db, $customer_data->number ?? 0);

    // Step 5
    $cart_view = new CartView();
    $data = [];
    switch($post_data['action']) {
        case 'add-to-cart':
            $cart_response = processAddToCart($post_data, $cart_services, new ProductServices($db), $cart_view);
            break;
        case 'update-cart':
            $cart_response = processUpdateCart($post_data['cart_data'] ?? [], $cart_services, new ProductServices($db), $cart_view);
            break;
        case 'update-cart-item':
            $cart_response = processUpdateCartItem($post_data['cart_data'] ?? [], $cart_services, new ProductServices($db), $cart_view);
            break;
        case 'remove-cart-item':
            $cart_item_id = 0;
            if($post_data['cart_data']) {
                $cart_item_id = $post_data['cart_data']['cart_item_id'] ?? 0;
            } 
            if($cart_item_id === 0) {
                $response['result'] = [
                    'type' => 'error', 
                    'content' => 'Cart item id is missing',
                    'message' => '',
                ];
                break;
            }

            $cart_response = processRemoveCartItem($cart_services, $cart_item_id, $cart_view);
            //echo json_encode($cart_response);
            //die();
            break;
        default:
            $cart_response = ['type' => 'success', 'title' => 'No Action taken', 'content' => ''];
            break;
    }
    // $response['form'] = $_POST;
}

echo json_encode($cart_response);