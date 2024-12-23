<?php
  require('_shop.php') ?>

<?php 
  include_once('./includes/header.php') ?>
  <main>
    <div class="container-xl p-10">
        <h2>Products</h2>
        <p>Select from our selection of products.</p>
        <p style="color:#FF0000;font-weight:bold;margin:10px 0;"><?= $cart_services->getMessages(); ?></p>
        <?= $product_view->productAsLists(
          $product_services->getProducts(), 
          'card'
        ); ?>       
    </div>
  </main>
  <script>
  

  </script>
  <?= $toast_notification->toastNotification(); ?>
  <?php
    $page_view->addScript($product_view->getAddToCartScript());
  ?>
  <?php 
    include_once('./includes/footer.php') ?>