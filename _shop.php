<?php
require('config.php');
require('includes/helpers.php');

use \Libraries\DB;
use \Libraries\Messages;
use \Libraries\ToastNotifications;

use \Services\CustomerServices;
use \Services\ProductServices;
use \Services\CartServices;

use \Models\Product;
use \Models\Customer;

use \Views\PageView;
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

// Step 0:
$page_view = new PageView();
$page_view->addScriptFile('public/js/custom-script.js');
$page_view->addScriptFile('https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js');

// Step 1:
$db = new DB(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);

// Step 2:
$product_services = new ProductServices($db);

// Step 3:
$customer_services = new CustomerServices($db);
$customer_data = $customer_services->checkCustomer();

// Step 4:
$cart_services = new CartServices($db, $customer_data->number ?? 0);

// Step 5
$product_view = new ProductView();
$cart_view = new CartView();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {	
	if(isset($_POST['prod_id']) 
		&& $product = $product_services->getProduct($_POST['prod_id'])) {
		$cart_services->addToCart($product, intval($_POST['quantity']));
	} else {
		if(isset($_POST['item_id']) 
			&& $cart_item = $cart_services->getCartItem($_POST['item_id'])) {
			$cart_services->updateCart($cart_item, intval($_POST['quantity']));
		}
	}
}

if(isset($_GET['cart_action']) && $_GET['cart_action'] == "remove") {
	$cart_services->deleteCartItem($_GET['id']);
}

if (isset($_GET['action']) && $_GET['action'] == "checkout") {
	if ($cart_services->getNumberOfRecords() > 0) {
		header("Location: ". CHECKOUT); // change the file name if you need
	} 
	
	$cart_services->error = "Your cart is currently empty!";	
}

$toast_notification = new ToastNotifications();

