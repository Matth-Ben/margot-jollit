const init = () => {
    document.querySelectorAll( '.component--hero-front-page' ).forEach( element => {
        const previous_button = element.querySelector( '.component__previous' )
        const next_button = element.querySelector( '.component__next' )
        const control = element.querySelector( '.component__control' )
        const items = element.querySelectorAll( '.component__item' )
        const pause_buttons = element.querySelectorAll( '.component__pause' )
        const videos = element.querySelectorAll( 'video' )
        const duration = data?.animations?.secondary?.duration || 400
        const interval = 6000

        let index = 0
        let old_index = 0
        let wait = false
        let i = setInterval( () => {}, interval )

        items[index].classList.add( 'active' )

        const hide_elements = index => {
            items[index].removeAttribute( 'show-elements' )
            items[index].querySelectorAll( '[hero-show-element]' ).forEach( ( e, index ) => {
                e.removeAttribute( 'show-element' )
            } )
            items[index].querySelectorAll( '.is-in-view' ).forEach( e => e.classList.remove( 'is-in-view' ) )
        }

        const show_elements = index => {
            items[index].setAttribute( 'show-elements', '' )
            items[index].querySelectorAll( '[hero-show-element]' ).forEach( ( e, index ) => {
                e.setAttribute( 'show-element', '' )
            } )
            document.dispatchEvent( new CustomEvent( 'ShowElements', { detail: { elements: [items[index]] } } ) )
        }

        const run = () => {
            wait = true
            items.forEach( item => item.classList.remove( 'active' ) )
            items[index].classList.add( 'active' )
            show_elements( index )
            clearInterval( i )
            control.innerHTML = `${index + 1}`
            setTimeout( () => {
                // i = setInterval( next, interval )
                hide_elements( old_index )
                wait = false
            }, duration )
        }

        const next = () => {
            if ( wait ) return
            old_index = index
            index = index < items.length - 1 ? index + 1 : 0
            run()
        }
       
        const previous = () => {
            if ( wait ) return
            old_index = index
            index = index > 0 ? index - 1 : items.length - 1
            run()
        }

        pause_buttons.forEach( button => {
            button.addEventListener( 'click', () => {
                const item = button.closest( '.component__item' )
                const video = item.querySelector( 'video' )
                item.classList.toggle( 'pause' )

                if ( item.classList.contains( 'pause' ) ) {
                    video.pause()
                    button.setAttribute( 'aria-label', button.getAttribute( 'data-text-pause' ) )
                } else {
                    video.play()
                    button.setAttribute( 'aria-label', button.getAttribute( 'data-text-play' ) )
                }
            } )
        } )

        if ( videos ) {
            videos[0].addEventListener( 'loadeddata', () => {
                console.log('loadeddata')
                element.setAttribute( 'data-loading', true )
            } )
            videos.forEach( video => {
                video.setAttribute( 'src', video.getAttribute( 'data-src' ) )
                video.play()
            } )
        } else {
            element.setAttribute( 'data-loading', true )
        }

        // i = setInterval( next, interval )
        show_elements( index )

        next_button.addEventListener( 'click', next )
        previous_button.addEventListener( 'click', previous )
    } )
}

document.addEventListener( 'PreContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )