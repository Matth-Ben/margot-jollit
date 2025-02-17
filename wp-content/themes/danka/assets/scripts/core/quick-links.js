export default function()
{
    const quick_links = document.getElementById( 'quick-links' )
    
    quick_links.querySelectorAll( 'a' ).forEach( element => {
        element.addEventListener( 'focusout', () => {
            document.body.classList.remove( 'show-quick-links' )
        } )
        element.addEventListener( 'focus', () => {
            document.body.classList.add( 'show-quick-links' )
        } )
    } )
}