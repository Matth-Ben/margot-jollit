
import Marquee from "../class/class-marquee"

const init= () => {
    document.querySelectorAll( '.component--marquee-logos' ).forEach( marquee => {
        const button_pause = marquee.querySelector( 'button' )

        new Marquee( marquee, false, 0.8 )

        if ( button_pause ) {
            button_pause.addEventListener( 'click', () => {
                marquee.classList.toggle( 'paused' )
            } )
        }
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )