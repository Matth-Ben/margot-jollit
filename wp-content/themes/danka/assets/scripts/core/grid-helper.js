export default function()
{
    // Avec shift + g on affiche les grilles même si on n'est pas en mode debug
    // if ( !document.body.getAttribute( 'wp-class' )?.includes( 'debug' ) ) {
    //     return
    // }

    if ( !document.body.classList.contains( 'grid-helper-init' ) ) {
        document.body.classList.add( 'grid-helper-init' )
        
        window.addEventListener( 'keydown', e => {
            if ( e.key && e.key === "G" && e.shiftKey ) {
                document.querySelectorAll( '.component-grid-helper' ).forEach( element => {
                    element.classList.toggle( 'active' )
                } )
            }
        } )
    }
}
