document.addEventListener( 'click', event => {
    let header = event.target.classList.contains( '.accordion__header' ) ? event.target : null
        header = header === null && event.target.closest( '.accordion__header' ) ? event.target.closest( '.accordion__header' ) : null

    if ( header ) {
        const component = event.target.closest( '.component--accordions' )
        const current_accordion = event.target.closest( '.accordion' )
        const is_open = current_accordion && current_accordion.classList.contains( 'active' ) ? true : false

        if ( component && component.classList.contains( 'one-by-one' ) ) {
            component.querySelectorAll( '.accordion' ).forEach( item => {
                item.classList.remove( 'active' )
            } )
        }

        if ( !is_open ) {
            current_accordion.classList.add( 'active' )
        }
        else {
            current_accordion.classList.remove( 'active' )
        }
    }
} )
