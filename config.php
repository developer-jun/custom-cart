<?php 
error_reporting(E_ALL);
session_start();

$checkout  = "custom_cart_checkout.php";
$confirm   = "custom_cart_confirm.php";

// db connection
// prioritize existing DB constants
if (!defined("DB_SERVER") || !define("DB_NAME")){
	define("DB_SERVER", "localhost");
	define("DB_NAME", "beta");
	define("DB_USER", "root");
	define("DB_PASSWORD", "");
}

// APP
define("APP_DIR", dirname(__FILE__));
define("APP_URL", dirname($_SERVER['PHP_SELF']));
define("PUBLIC_DIR", APP_URL.'/public/');

define("CSRF_TOKEN_NAME", 'csrf_protection_token');

// db tables
define("PRODUCTS_TABLE", "custom_cart_products");
define("CUSTOMERS_TABLE", "custom_cart_customers");
define("ORDERS_TABLE", "custom_cart_orders");
define("ORDERS_CART_ITEMS_TABLE", "custom_cart_orders_cart_items");
define("SHIP_ADDRESS", "custom_cart_shipment");

define("CURRENCY", "$");
define("INCLUDE_VAT", true);
define("VAT_VALUE", 19); 
define("SITE_MASTER", "Custom Cart shop");
define("SITE_MASTER_MAIL", "webmaster@site.com");
define("MAIL_ENCODING", "iso-8859-1");
define("DATE_FORMAT", "d-m-Y");
define("RECOVER_ORDER", false); 
define("VALID_UNTIL", 7 * 86400); // default 7 days

define("CHECKOUT", $checkout);
define("CONFIRM", $confirm); 

require_once('includes/autoload.php');