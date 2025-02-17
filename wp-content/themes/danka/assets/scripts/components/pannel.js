const init = () => {
    document.addEventListener( 'click', event => {
        const target = event.target.getAttribute( 'data-pannel' ) !== null ? event.target : event.target.closest( '[data-pannel]' )
        const data = target ? target.getAttribute( 'data-pannel' ) : null
        const id = target ? target.getAttribute( 'data-pannel-id' ) : null
        
        if ( target && id ) {
            const pannel = document.getElementById( id )

            if ( pannel ) {
                pannel.children[0].innerHTML = data
                pannel.classList.add( 'active' )
                document.body.setAttribute( 'data-lenis-prevent', '' )
                document.body.style.overflow = 'hidden'
            }
        }
    } )
    
    document.addEventListener( 'click', event => {
        const target = event.target.classList.contains( 'close-pannel' ) ? event.target : event.target.closest( '.close-pannel' )
        const pannel = target ? target.closest( '.component--pannel' ) : null

        if ( pannel ) {
            pannel.classList.remove( 'active' )
            document.body.removeAttribute( 'data-lenis-prevent' )
            document.body.style.overflow = ''
        }
    } )
}

document.addEventListener( 'ContentLoaded', init )
