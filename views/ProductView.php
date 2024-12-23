<?php
namespace Views;

use \Libraries\Products;
use \Models\Product;
use \Libraries\CSRFProtection;

class ProductView {    
    public function __construct() {}

    public function productAsLists($products = [], $format = 'list'): string {
        if(empty($products)) {
          return '';
        }

        $contents = '';
        foreach($products as $product) {
          if($format == 'list') {
              $contents .= $this->useListFormat($product);
          } else {
              $contents .= '<div class="col">'. $this->useCardFormat($product) .'</div>';
          }
        }

        if($format != 'list') {
          $contents = '<div class="row">'. $contents .'</div>';
        }

        //$contents .= $this->generateCSRFField();
        
        return '<div class="container text-center" x-data="cartHandler()">'. $contents . '</div>';        
    }
    
    private function useListFormat($product): string {
      ob_start();
      ?>
      <form class="product">
        <div class="row">
          <div class="col-sm-4">
            <label for="prod_<?= $product['id'] ?>">
                <strong><?= $product['name'] ?></strong><br>
                <?= $product['description'] ." - price: ". formatValue($product['price']) ?>
            </label>
          </div>
          <div class="col">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />			
            <div class="input-group max-w-5">            
                <input type="text" class="form-control" name="quantity" size="5" value="1" />
                <button type="button" class="add-to-cart btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="icon-2">
                        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="icon-3">
                      <path d="M1.75 1.002a.75.75 0 1 0 0 1.5h1.835l1.24 5.113A3.752 3.752 0 0 0 2 11.25c0 .414.336.75.75.75h10.5a.75.75 0 0 0 0-1.5H3.628A2.25 2.25 0 0 1 5.75 9h6.5a.75.75 0 0 0 .73-.578l.846-3.595a.75.75 0 0 0-.578-.906 44.118 44.118 0 0 0-7.996-.91l-.348-1.436a.75.75 0 0 0-.73-.573H1.75ZM5 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM13 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                    </svg>                             
                </button>
            </div>
          </div>
        </div>               
      </form>
      <?php
      $content = ob_get_contents();
      ob_end_clean();
      return $content;
    }

    private function useCardFormat($product): string {
      ob_start();
      ?>
      <div class="card product" style="width: 18rem;">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
          <img src="<?= $product['thumbnail'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
          <div class="card-body">
            <h5 class="card-title"><?= $product['name'] ?></h5>
            <p class="card-text">
              <?= $product['description'] ?>
              <br /><strong>Price: </strong> <span><?= formatValue($product['price']) ?></span>
            </p>           
            <div class="input-group mb-3">
              <span class="input-group-text">Qty:</span>
              <input type="number" class="form-control w-5" name="quantity" size="3" value="1" />
              <button type="submit" class="btn btn-primary" @click="addToCart($event)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-plus-fill" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M10.5 3.5a2.5 2.5 0 0 0-5 0V4h5zm1 0V4H15v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4h3.5v-.5a3.5 3.5 0 1 1 7 0M8.5 8a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V12a.5.5 0 0 0 1 0v-1.5H10a.5.5 0 0 0 0-1H8.5z"/>
                </svg>
                Add to Cart
              </button>
            </div>
          </div>
      </div>
      <?php
      $content = ob_get_contents();
      ob_end_clean();
      return $content;
    }

    public function ProductAsListsItem(Product $product = null): string {

    }

    public function getAddToCartScript(): string {
      $post_url = APP_URL . '/api.php';
      $csrf = new CSRFProtection();
      $api_csrf_token = htmlspecialchars($csrf->getHash());
      $script = <<<SCRIPT
        function cartHandler() {            
            return {
                responseMessage: '',
                async addToCart(event) {
                    const productSelector = event.target.closest('.product');
                    const productId = productSelector.querySelector('input[name="product_id"]').value;
                    const quantity = productSelector.querySelector('input[name="quantity"]').value;
                    try {
                        let post_data = {
                          action: 'add-to-cart', 
                          product_id: productId, 
                          quantity: quantity
                        };                          
                        const response = await fetch('$post_url', {
                            method: 'POST',
                            headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-Token': '$api_csrf_token'
                            },
                            body: JSON.stringify(post_data)
                        });
                        console.log('Error:', response);                
                        if(response.status !== 200) {
                            if(response.status === 404) {
                                throw new Error('The API is unreachable, either the server is down or the API endpoint is incorrect.<br /><br />Please try again in a few minutes, if issue persists, don\'t forget to notify our web administrator.');
                            }                            
                            
                            // general error
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                        }
                        const data = await response.json();                        
                        console.log('Data:', data);
                        processAddToCartResult(data);
                        
                    } catch (error) {
                        console.log('Error thrown');
                        console.log(error);
                        setToastNotification({title: 'Add to Cart Failed', result: {type: 'error', content: '<strong>Error Message: </strong>' + error.message }});
                    }
                }
            }
        }        
      SCRIPT;

      return $script;
    }
}