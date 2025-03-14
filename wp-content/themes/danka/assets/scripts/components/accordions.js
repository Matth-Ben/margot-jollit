document.addEventListener( 'click', event => {
    let header = event.target.classList.contains( '.component-accordions__accordion-header' ) ? event.target : null
        header = header === null && event.target.closest( '.component-accordions__accordion-header' ) ? event.target.closest( '.component-accordions__accordion-header' ) : null

    if ( header ) {
        const component = event.target.closest( '.component-accordions' )
        const current_accordion = event.target.closest( '.component-accordions__accordion' )
        const is_open = current_accordion && current_accordion.classList.contains( 'active' ) ? true : false

        if ( component && component.classList.contains( 'component-accordions--one-by-one' ) ) {
            component.querySelectorAll( '.component-accordions__accordion' ).forEach( item => {
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
