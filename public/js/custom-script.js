function setToastNotification({title, status}) {
    console.log("Toast Notification");
    console.log(title);
    console.log(status);
    const toastTarget = document.getElementById('toastTarget');
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastTarget);     

    if(status.type === 'success') {      
        toastTarget.classList.remove('text-bg-danger');
        toastTarget.classList.add('text-bg-success');
    } else {
        toastTarget.classList.remove('text-bg-success');
        toastTarget.classList.add('text-bg-danger');
    }
    
    toastTarget.querySelector('.message-title').innerHTML = title;
    toastTarget.querySelector('.toast-body').innerHTML = status.content;
    toastBootstrap.show();
}

/*function toggleClass(element, className) {
    if(element.classList.contains(className)) {
        element.classList.remove(className);
    } else {
        element.classList.add(className);
    }
}*/

function updateCartNumberBadge(quantity) {
    let cartQuantityBadge = document.getElementById('cart-quantity-badge');
    if(cartQuantityBadge) {
        let className = 'invisible';
        cartQuantityBadge.innerHTML = quantity;

        if(quantity > 0) {            
            cartQuantityBadge.classList.remove(className);
        } else {
            cartQuantityBadge.classList.add(className);
        } 
    }     
}

function updateCartListAndTotalAmount(cart_total_amount, cart_items_content = '') {
    let formattedCurrency = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    });
    document.getElementById('cart-total-value').innerHTML = formattedCurrency.format(cart_total_amount);
    document.getElementById('cart-items').innerHTML = cart_items_content;
}

function processRemoveCartItemResult(response) {  
    console.log('Result:', response);


    setToastNotification({title: response.title, status: { type: response.result.type, content: response.result.message }});
    if(response.result.type === 'success') {
        updateCartListAndTotalAmount(response.cart_total_amount, response.result.content);
    }
      
    updateCartNumberBadge(parseInt(response.cart_total_quantity));  
}

function processUpdateCartResult(response) {  
    let { title, status } = response.status_notification;
    // let status = notification.status;
    console.log('Result:', response);
    setToastNotification({title: title, status: { type: status.type, content: status.message }});
    if(status.type === 'success') {
        updateCartListAndTotalAmount(response.total_amount, response.cart_summary);
    }
      
    updateCartNumberBadge(parseInt(response.total_quantity));  
}

function processAddToCartResult(response) {
    console.log('Result:', response);
    setToastNotification({
        title: response.status_notification.title, 
        status: {
            type: response.status_notification.status.type,
            content: response.cart_summary
        }
    });  
    updateCartNumberBadge(parseInt(response.total_quantity));
}

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.add-to-cart');

    buttons.forEach(button => {
        button.addEventListener('click', async function() {
            const form = this.closest('form.product');
            const formData = new FormData(form);
            formData.append('action', 'add-to-cart');

            try {
                const response = await fetch('./api.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const response_data = await response.json();
                processAddToCartResult(response_data);                
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
});