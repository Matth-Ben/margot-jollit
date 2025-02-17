const init = () => {
    document.querySelectorAll( '.acf-component--video' ).forEach( element => {
        const button_play = element.querySelector( '.acf-component__button-play' )

        if ( button_play ) {
            button_play.addEventListener( 'click', () => {
                const iframe = element.querySelector( 'iframe' )

                element.classList.add( 'see-video' )

                if ( iframe ) {
                    iframe.setAttribute( 'src', iframe.src + '&autoplay=1' )
                }
            } )
        }
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )
