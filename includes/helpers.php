<?php

function getCategoryCheckoutPageSettings(bool $use_stock = true): array {
    $settings = [
        'catalog' => "custom_cart_stock_example.php", 
        'checkout' => "custom_cart_stock_example.php", 
        'confirm' => "custom_cart_stock_confirm.php"
    ];   
    if (!$use_stock) {
        $settings['catalog'] = "custom_cart_example.php";
        $settings['checkout'] = "custom_cart_checkout_example.php";
        $settings['confirm'] = "custom_cart_confirm.php";
    }

    return $settings;
}

function formatValue($value, $encoding = true) {
    if ($encoding) {
        $curr = (ord(CURRENCY) == "128") ? "&#8364;" : htmlentities(CURRENCY);
    } else {
        $curr= CURRENCY;
    }
    $formatted = $curr.number_format($value, 2, ".", ",");
    return $formatted;
}