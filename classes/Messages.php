<?php
namespace Cart;

class Messages {
    private $messages = [];

    public function __construct() {
        // general error messages
        $this->messages[1] = "Unknown database error, please try it again.";
        $this->messages[2] = "Unknown application error, please contact the system administrator.";
        $this->messages[3] = "Unknown database error, but the order is send by e-mail.";
        // messages related to cart activity
        $this->messages[11] = "Added the product to your cart.";
        $this->messages[12] = "Updated the order row in your cart.";
        $this->messages[13] = "Can't add/update the product, please try it again.";
        $this->messages[14] = "Add some products to the cart...";
        $this->messages[15] = "Removed the order row from your cart.";
        // checkout related messages
        $this->messages[21] = "Shipment address is successfully modified.";
        $this->messages[22] = "Your order is processed and a copy is send to you by e-mail.";		
        // messages used inside the mail
        $this->messages[51] = "Order posted via ".$_SERVER['HTTP_HOST']." on ".date(DATE_FORMAT);
        $this->messages[52] = "Hello,\r\n\r\nyou ordered the following products:\r\n\r\n";
        //$this->messages[53] = "\r\nThe total amount from your order is: ".$this->formatValue($this->show_total_value(), false);
        //$this->messages[53] .= (INCL_VAT) ? " (incl. VAT)" : "";
        //$this->messages[54] = "\r\nThe VAT amount for your order is: ".$this->formatValue($this->create_total_VAT(), false)."\r\n\r\n";
        $this->messages[55] = "\r\n\r\n_______________________________________\r\nkind regards\r\n".SITE_MASTER."\r\n\r\n";
        // if you change the format next then change the values in the mail method, too.
        $this->messages[56] = sprintf("\t%-10s  %-30s  %-20s  %-12s\r\n", "Quantity", "Description", "Art. no.", "Single price"); 
        $this->messages[57] = "Shipment to / ordered by:\r\n";
        //$this->messages[57] .= $this->ship_name."\r\n".$this->ship_address."\r\n".$this->ship_pc." ".$this->ship_city."\r\n".$this->ship_country;
        //$this->messages[58] = ($this->ship_msg != "") ? "\r\n\r\nMessage:\r\n".$this->ship_msg."\r\n" : ""; 
    }    

    public function formatValue($value, $encoding = true) {
		if ($encoding) {
			$curr = (ord(CURRENCY) == "128") ? "&#8364;" : htmlentities(CURRENCY);
		} else {
			$curr= CURRENCY;
		}
		$formatted = $curr." ".number_format($value, 2, ",", ".");
		return $formatted;
	}

    public function getMessage($number): string {
        if(isset($this->messages[$number])) {
            return $msg[$number];
        } 

        return "Error number [$number] is not defined.";
    }
}