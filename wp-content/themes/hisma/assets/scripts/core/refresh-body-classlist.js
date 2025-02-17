export default () => {
    document.body.setAttribute( 'wp-class', Array.from( document.getElementById( 'app-content' ).classList ).join( ' ' ) )
}