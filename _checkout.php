<?php
require('config.php');
require('includes/helpers.php');

//use \Cart\Database;

use \Libraries\DB;
use \Libraries\Carts;
use \Libraries\Products;
use \Libraries\Messages;
use \Libraries\Customers;

use \Models\Product;
use \Models\Customer;

use \Views\ProductView;
use \Views\CartView;

// Step 1: create a DB object
// Step 2: Retrieve Products for display
//   - set list it to a local variable
// Step 3: Check customer session/cookie
//   - if not set, create a temporary customer info
//   - then create a 
// Step 2: create new Customers object
//	- 
// Step 3: create new Products object
// Step 4: create new Carts object
//	- Retrieve cart data from DB using customer id and order id
//	- catch Cart related Actions: Add, Update, and Delete
// Step 5: Create the related objects to display our relivant contents
//	- Products
//	- Cart


// Step 1:
$db = new DB(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

// Step 2:
$product_obj = new Products($db);

// Step 3:
$customer_obj = new Customers($db);
$customer_data = $customer_obj->checkCustomer();

// Step 4:
$cart_obj = new Carts($db, $customer_data->number ?? 0);


// Step 5
$productView = new ProductView();
$cartView = new CartView();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if(isset($_POST['prod_id']) && $product = $product_obj->getProduct($_POST['prod_id'])) {
		$cart_obj->addToCart($product, intval($_POST['quantity']));
	} else {
		if(isset($_POST['item_id']) && $cart_item = $cart_obj->getCartItem($_POST['item_id'])) {
			$cart_obj->updateCart($cart_item, intval($_POST['quantity']));
		}
	}
}

if(isset($_GET['cart_action']) && $_GET['cart_action'] == "remove") {
	$cart_obj->deleteCartItem($_GET['id']);
}

if (isset($_GET['action']) && $_GET['action'] == "checkout") {
	if ($cart_obj->getNumberOfRecords() > 0) {
		header("Location: ". CHECKOUT); // change the file name if you need
	} else {
		$cart_obj->error = "Your cart is currently empty!";
	}
}
