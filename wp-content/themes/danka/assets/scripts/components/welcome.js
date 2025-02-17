document.addEventListener( 'Welcome', () => {
    const loading_elements = document.querySelectorAll( '[data-loading]' )

    document.dispatchEvent( new CustomEvent( 'PreContentLoaded' ) )

    if ( loading_elements )  {
        const i = setInterval( () => {
            let is_ready = true

            loading_elements.forEach( element => {
                if ( element.getAttribute( 'data-loading' ) != 'true' ) {
                    is_ready = false
                    return
                }
            } )

            if ( is_ready ) {
                clearInterval( i )
                document.body.classList.add( 'show-content-loaded' )
                document.dispatchEvent( new CustomEvent( 'ContentLoaded' ) )
            }
        }, 100 )
    } else {
        document.body.classList.add( 'show-content-loaded' )
        document.dispatchEvent( new CustomEvent( 'ContentLoaded' ) )
    }
} )