<?php
    require('_cart.php') ?>

<?php 
  include_once('./includes/header.php') ?>  
<main>
    <div class="container-xl p-10">
        <h2>Cart Items</h2>
        <p>These are the items you have selected so far.</p>       

        <?php
        if ($cart_services->getNumberOfItems() > 0) { 
          echo '<div id="cart-items">'. $cart_view->cartItemsAsLists($cart_services->getCartItems()) .'</div>';
          ?>
          <div class="text-right">
            <p>Total Amount: <strong id="cart-total-value"><?= formatValue($cart_services->computeCartTotal()); ?></strong></p>
            <p>Total VAT: <strong id="cart-total-vat"><?= formatValue($cart_services->createTotalVAT()); ?></strong></p>
            <p>
              <a class="btn btn-info text-nowrap" role="button" href="#">
                Checkout
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                </svg>
              </a>
            </p>
            <p>
              <a class="icon-link icon-link-hover link-success link-underline-success link-underline-opacity-25" href="#">
                Icon link
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                </svg>
              </a>
            </p>
          </div>
          <?php 
        } else {
          ?>
          <div class="alert alert-light" role="alert">
          <p class="text-center">Your cart is currently empty, <a href="shop.php">click here to view our store</a></p>
          </div>          
          <?php
        }
        ?>
    </div>
</main>
<?= $toast_notification->toastNotification(); ?>
<?php
    $page_view->addScript($cart_view->getCartActionsScript());
  ?>
<?php 
    include_once('./includes/footer.php') ?>