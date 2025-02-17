
const init = () => {
    document.body.addEventListener( 'click', event => {
        if ( event.target.getAttribute( 'data-show-search' ) !== null || event.target.closest( '[data-show-search]' ) ) {
            document.dispatchEvent( new CustomEvent( 'ShowSearch' ) )
        }
    } )

    document.querySelectorAll( '.component--form-search' ).forEach( element => {
        const input = element.querySelector( 'input' )
        const link = element.querySelector( 'a' )
        const base_url = new URL( window.location.href )
        const button_close = element.querySelector( '.component__button-close button' )

        const update_link = () => {
            if ( input.value.length > 0 ) {
                console.log(base_url)
                link.href = base_url.origin + '?s=' + input.value
            }
        }

        document.addEventListener( 'ShowSearch', () => {
            element.classList.add( 'is-shown' )
            document.body.classList.add( 'show-form-search' )
            setTimeout( () => input.focus(), data?.animations?.default?.duration ?? 0 )
        } )

        button_close.addEventListener( 'click', () => {
            element.classList.remove( 'is-shown' )
            document.body.classList.remove( 'show-form-search' )
        } )

        input.addEventListener( 'change', () => {
            if ( input.value.length > 0 ) {
                console.log(base_url)
                link.href = base_url.origin + '?s=' + input.value
            }
        } )
        element.addEventListener( 'submit', event => {
            event.preventDefault()
            
            if ( input.value.length > 0 ) {
                console.log(base_url)
                link.href = base_url.origin + '?s=' + input.value
            }
            
            link.click()
            button_close.click()
        } )
    } )
}

document.addEventListener( 'ContentLoaded', init )