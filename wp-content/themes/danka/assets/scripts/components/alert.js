const init = () => {
    document.querySelectorAll( '.component--alert' ).forEach( element => {
        const button = element.querySelector( 'button' )
        const link = element.querySelector( 'a' )

        setTimeout( () => {
            document.body.classList.add( 'show-alert' )
            document.documentElement.style.setProperty( '--alert-height', element.clientHeight + 'px' ) // add css variable
        }, 1000 )
        
        button.addEventListener( 'click', () => {
            document.body.classList.remove( 'show-alert' )
        } )
        
        link.addEventListener( 'click', () => {
            document.body.classList.remove( 'show-alert' )
        } )

        document.addEventListener( 'WindowResized', () => {
            document.documentElement.style.setProperty( '--alert-height', element.clientHeight + 'px' ) // refresh css variable
        } )
    } )
}

document.addEventListener( 'ContentLoaded', init )
// document.addEventListener( 'NewContentLoaded', init )