<?php
namespace Views;

use \Libraries\CSRFProtection;

use \Models\Cart;
use \Models\Notification;

//use \Traits\csrfTrait;

class CartView {
    //use csrfTrait;

    public function __construct() {

    }

    public function cartItemsAsLists(?array $cart_items = []): string {
        if(empty($cart_items)) {
            return '';
        }
        ob_start();
        ?>
        <div class="container text-center" x-data="cartActionHandler()">
            <table class="table">
            <thead>
                <tr>
                    <th scope="col">Art. no.</th>
                    <th scope="col">Product</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Price</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
                <?php foreach ($cart_items as $item) { ?>
                <tr>
                    <td><?= $item['product_id'] ?></td>
                    <td class="text-left"><?= $item['product_name'] ?></td>
                    <td>                     
                        <div class="product-field input-group max-width-100">
                            <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>" />    
                            <input type="number" class="form-control quantity-textbox" name="quantity" size="3" value="<?= $item['quantity'] ?>" />
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>" />
                            <button type="submit" class="btn btn-primary btn-sm flex-btn" title="Update" @click="updateCartQuantity($event)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="icon-3">
                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.15-.043l4.25-5.5Z" clip-rule="evenodd" />
                                </svg>                      
                            </button>
                        </div>               
                    
                    </td>
                    <td><?= formatValue($item['price']) ?></td>
                    <td><?= formatValue($item['price'] * $item['quantity']) ?></td>
                    <td>
                        <a class="text-red-900" title="Remove Item" @click="removeCartItem(<?= $item['id'] ?>)" href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 icon-2">
                        <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" />
                        </svg></a>
                    </td>
                </tr>
                <?php } // end foreach loop ?>
            </table>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function cartBadge(int $total_quantity = 0): string {
        ob_start();
        ?>
        <button type="button" class="btn position-relative cart-header-icon">
            <svg class="bi" width="24" height="24"><use xlink:href="#cart"/></svg>
            <span id="cart-quantity-badge" class="<?= ($total_quantity > 0) ? '' : 'invisible ' ?>position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= $total_quantity ?>
            </span>
        </button>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function cartAsToastContent(array|null $cart_items, Notification $result_notification): string {
        ob_start();
        ?>
        <div class="toast-content">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Your cart:</h5> 
                <a class="link-info link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="<?= APP_URL .'/cart.php' ?>">View Cart</a>
            </div>
            <?= $result_notification->notificationTag(true) ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Price x Qty</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <?php
                $total = 0;
                if($cart_items) {
                    foreach ($cart_items as $item) { 
                        $total += $item['price'] * $item['quantity'];
                        ?>
                        <tr>
                            <td><?= $item['product_name'] ?></td>
                            <td>
                                <?= formatValue($item['price']) ?>             
                                x
                                <?= $item['quantity'] ?>
                            </td>
                            <td><?= formatValue($item['price'] * $item['quantity']) ?></td>
                        </tr>
                        <?php 
                    }
                } else {
                    echo '<tr><td colspan="3">Your cart is empty.</td></tr>';
                }
                ?>
            </table>
            <div class="text-right">
                Total: <strong><?= formatValue($total) ?></strong>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function getCartActionsScript(): string {
        $post_url = APP_URL . '/api.php';
        $csrf = new CSRFProtection();
        $api_csrf_token = htmlspecialchars($csrf->getHash());
        $script = <<<SCRIPT
          function cartActionHandler() {
              const post_url = '$post_url';
              const api_csrf_token = '$api_csrf_token';
              const post_data = {
                action: '', 
                cart_data: {
                    product_id: 0, 
                    quantity: 0,
                    cart_item_id: 0
                }
              }; // structure only
              return {
                  responseMessage: '',
                  async updateCartQuantity(updateCartEvent) {
                      // console.log('updateCartQuantity');
                      const productItemElement = updateCartEvent.target.closest('.product-field');
                      const productId = productItemElement.querySelector('input[name="product_id"]').value;
                      const quantity = productItemElement.querySelector('input[name="quantity"]').value;
                      const cartItemId = productItemElement.querySelector('input[name="cart_item_id"]').value;

                      try {
                          post_data.action = 'update-cart-item';
                          post_data.cart_data = {
                                product_id: productId, 
                                quantity: quantity,
                                cart_item_id: cartItemId
                          };                          
                          const response = await fetch(post_url, {
                              method: 'POST',
                              headers: {
                                  'Content-Type': 'application/json',
                                  'X-CSRF-Token': api_csrf_token
                              },
                              body: JSON.stringify(post_data)
                          });
                          if(response.status !== 200) {
                            if(response.status === 404) {
                                throw new Error('The API is unreachable, either the server is down or the API endpoint is incorrect.');
                            } 
                            
                            // general error
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                          }
                          
                          const data = await response.json();
                          processUpdateCartResult(data);    
                      } catch (error) {
                          console.log(error);
                          processFailedNotification({title: 'Update Cart Failed', content: 'Error: ' + error.message });
                      }
                  },
                  async removeCartItem(cartItemId) {
                      post_data.action = 'remove-cart-item';
                      post_data.cart_data = { cart_item_id: cartItemId };
                      try {                          
                          const response = await fetch(post_url, {
                              method: 'POST',
                              headers: {
                                  'Content-Type': 'application/json',
                                  'X-CSRF-Token': api_csrf_token
                              },
                              body: JSON.stringify(post_data)
                          });
                          if(response.status !== 200) {
                            if(response.status === 404) {
                                throw new Error('The API is unreachable, either the server is down or the API endpoint is incorrect.');
                            } 
                            
                            // general error
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                          }
                          
                          const data = await response.json();
                          processRemoveCartItemResult(data);    
                      } catch (error) {
                          console.log(error);
                          processFailedNotification({title: 'Remove Cart Item Failed', content: 'Error: ' + error.message });
                      }
                  }
              } // return
          }          
        SCRIPT;
  
        return $script;
      }
}