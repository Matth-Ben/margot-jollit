// Rafraîchir les fragments de WooCommerce
window.danka_refreshWooCommerceFragments = async ( fragments = null ) => {
    
    if ( fragments === null ) {
        const data = new FormData()
        data.append( "action", "get_refreshed_fragments" )

        const request = await fetch( woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' ), { // "/?wc-ajax=%%endpoint%%"
            method: 'post',
            body: data
        } )
        const response = await request.json()

        if ( response.fragments ) {
            fragments = response.fragments
        }
    }

    if ( fragments ) {
        for ( const [key, value] of Object.entries( fragments ) ) {
            const element = document.querySelector( key )
            
            if ( element ) {
                element.outerHTML = value
            }
        }

        document.dispatchEvent( new CustomEvent( 'FragmentsRefreshed' ) )
    }

    document.body.classList.remove( 'cart-updating' )
}

// select variation
document.addEventListener( 'click', event => {
    let button_product_variation = null
    
    if ( event.target.getAttribute( 'product-variation' ) !== null ) (
        button_product_variation = event.target
    )

    else if ( event.target.closest( '[product-variation]' ) ) {
        button_product_variation = event.target.closest( '[product-variation]' )
    }

    if ( button_product_variation ) {
        const id = button_product_variation.getAttribute( 'product-variation' )
        const product_id = button_product_variation.getAttribute( 'product' )
        const button_target_id = button_product_variation.getAttribute( 'button-target' )
        const button_add_to_cart = button_target_id ? document.getElementById( button_target_id ) : null

        if ( button_add_to_cart && id ) {
            button_add_to_cart.setAttribute( 'add-to-cart', id )
            button_add_to_cart.removeAttribute( 'disabled' )
            
            document.querySelectorAll( 'button[product="' + product_id + '"]' ).forEach( element => {
                element.classList.remove( 'active' )
            } )
            button_product_variation.classList.add( 'active' )
        }
    }
} )

// add to cart
document.addEventListener( 'click', async event => {
    let button_add_to_cart = null
    
    if ( event.target.getAttribute( 'add-to-cart' ) !== null ) (
        button_add_to_cart = event.target
    )

    else if ( event.target.closest( '[add-to-cart]' ) ) {
        button_add_to_cart = event.target.closest( '[add-to-cart]' )
    }

    if ( button_add_to_cart ) {
        const product_id = button_add_to_cart.getAttribute( 'add-to-cart' )
        const quantity = button_add_to_cart.getAttribute( 'quantity' )

        if ( product_id ) {
            const action = 'add_to_cart'
            const data = new FormData()

            data.append( "action", action )
            data.append( "product_id", product_id )
            data.append( "quantity", quantity ? quantity : 1 )

            document.body.classList.add( 'cart-updating' )

            const request = await fetch( woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', action ), {
                method: 'post',
                body: data
            } )
            const response = await request.text()

            window.danka_refreshWooCommerceFragments( response?.fragments ? response.fragments : null )
        }
    }
} )

// remove from cart
document.addEventListener( 'click', async event => {
    let button_remove_from_cart = null
    
    if ( event.target.getAttribute( 'remove-from-cart' ) !== null ) (
        button_remove_from_cart = event.target
    )

    else if ( event.target.closest( '[remove-from-cart]' ) ) {
        button_remove_from_cart = event.target.closest( '[remove-from-cart]' )
    }

    if ( button_remove_from_cart ) {
        const product_key = button_remove_from_cart.getAttribute( 'remove-from-cart' )
        const action = 'remove_from_cart'

        if ( product_key !== null ) {
            const data = new FormData()

            data.append( "action", action )
            data.append( "cart_item_key", product_key )

            document.body.classList.add( 'cart-updating' )

            const request = await fetch(  woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', action ), {
                method: 'post',
                body: data
            } )
            const response = await request.json()
            
            window.danka_refreshWooCommerceFragments( response?.fragments ? response.fragments : null )
        }
    }
} )

// apply coupon
document.addEventListener( 'click', async event => {
    let button_apply_coupon = null
    
    if ( event.target.getAttribute( 'apply-coupon' ) !== null ) (
        button_apply_coupon = event.target
    )

    else if ( event.target.closest( '[apply-coupon]' ) ) {
        button_apply_coupon = event.target.closest( '[apply-coupon]' )
    }

    if ( button_apply_coupon ) {
        const input = document.getElementById( button_apply_coupon.getAttribute( 'target' ) )
        const coupon_code = input ? input.value : null
        const wpnonce = button_apply_coupon.getAttribute( 'nonce' )
        const action = 'apply_coupon'

        if ( coupon_code !== null && wpnonce ) {
            const data = new FormData()

            data.append( "action", action )
            data.append( "coupon_code", coupon_code )
            data.append( "_wpnonce", wpnonce )

            document.body.classList.add( 'cart-updating' )

            const request = await fetch(  woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', action ), {
                method: 'post',
                body: data
            } )
            const response = await request.text()

            window.danka_refreshWooCommerceFragments( response?.fragments ? response.fragments : null )
        }
    }
} )

// remove coupon
document.addEventListener( 'click', async event => {
    let button_remove_coupon = null
    
    if ( event.target.getAttribute( 'remove-coupon' ) !== null ) (
        button_remove_coupon = event.target
    )

    else if ( event.target.closest( '[remove-coupon]' ) ) {
        button_remove_coupon = event.target.closest( '[remove-coupon]' )
    }

    if ( button_remove_coupon ) {
        const coupon_code = button_remove_coupon.getAttribute( 'remove-coupon' )
        const wpnonce = button_remove_coupon.getAttribute( 'nonce' )
        const action = 'remove_coupon'

        if ( coupon_code !== null && wpnonce ) {
            const data = new FormData()

            data.append( "action", action )
            data.append( "coupon", coupon_code )
            data.append( "_wpnonce", wpnonce )

            document.body.classList.add( 'cart-updating' )

            const request = await fetch(  woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', action ), {
                method: 'post',
                body: data
            } )
            const response = await request.text()

            console.log(response)

            window.danka_refreshWooCommerceFragments( response?.fragments ? response.fragments : null )
        }
    }
} )

// update quantity
document.addEventListener( 'change', async event => {
    const select_quantity = event.target.getAttribute( 'update-quantity' ) !== null ? event.target : null

    if ( select_quantity ) {
        const quantity = select_quantity.value
        const key = select_quantity.getAttribute( 'key' )
        const action = 'danka_update_cart_quantity'
    
        if ( quantity && key ) {
            const data = new FormData()
    
            data.append( "action", action )
            data.append( "hash", key )
            data.append( "quantity", quantity )

            document.body.classList.add( 'cart-updating' )
    
            const request = await fetch(  app_shop.ajax_url, {
                method: 'post',
                body: data
            } )
            const response = await request.text()
    
            console.log(response)
    
            window.danka_refreshWooCommerceFragments()
        }
    }
} )
