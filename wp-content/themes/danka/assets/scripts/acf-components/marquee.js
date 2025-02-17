import Marquee from "../class/class-marquee"

const init = () => {
    document.querySelectorAll( '.acf-component--marquee' ).forEach( element => {
        if ( element.classList.contains( 'init' ) ) {
            return
        }

        const button_pause = element.querySelector( 'button' )
        const elements = element.querySelectorAll( '.acf-component__text > div' )

        element.classList.add( 'init' )
        elements.forEach( ( e, index ) => {
            new Marquee( e, (index % 2 == 0 ? false : true) )
        } )

        button_pause.addEventListener( 'click', () => {
            elements.forEach( e => { e.classList.toggle( 'pause' ) } )
            element.classList.toggle( 'paused' )
        } )
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )
