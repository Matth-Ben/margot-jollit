const init = () => {
    document.querySelectorAll( '.component--video' ).forEach( element => {
        const video = element.querySelector( 'video' )
        const button = element.querySelector( 'button' )
        const src = element.querySelector( 'video' )?.getAttribute( 'src' )

        button.addEventListener( 'click', () => {
            element.classList.toggle( 'component--pause' )

            if ( element.classList.contains( 'component--pause' ) ) {
                element.querySelector( 'video' ).pause()
                button.setAttribute( 'aria-label', button.getAttribute( 'data-text-pause' ) )
            } else {
                element.querySelector( 'video' ).play()
                button.setAttribute( 'aria-label', button.getAttribute( 'data-text-play' ) )
            }
        } )

        video.addEventListener( 'loadeddata', () => {
            element.classList.add( 'component--loaded' )
        } )

        if ( src ) {
            video.removeAttribute( 'src' )
            video.setAttribute( 'src', src )
        }
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )