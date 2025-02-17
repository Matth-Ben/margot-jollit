import Swiper from "swiper"

const uniqid = () => {
    return Math.random().toString( 36 ).substr( 2, 9 )
}

const init = () => {
    document.querySelectorAll( ".component-some-events" ).forEach( element => {
        const carousel = element.querySelector( ".swiper" )
        const previous = element.querySelector( ".component-some-events__previous" )
        const next = element.querySelector( ".component-some-events__next" )
        const list_name = element.getAttribute( 'data-post-type' ) ? element.getAttribute( 'data-post-type' ) : 'secondary'
        const list = data.lists[list_name] ? data.lists[list_name] : data.lists['default']
    
        if ( carousel ) {
            const responsive = {}
            const id = '_' + uniqid()
            carousel.setAttribute( 'id', id )

            if ( data ) {
                for ( const [size, breakpoint] of Object.entries( data.breakpoints ) ) {
                    if ( list && list[size] ) {
                        responsive[breakpoint] = {}
                        responsive[breakpoint].slidesPerView = list[size]
                    }
                }
            }

            const swiper = new Swiper( `#${id}`, {
                autoHeight: true,
                grabCursor: true,
                breakpoints: { ...responsive },
            } )

            swiper.on('slideChange', function (e) {
                previous.classList.remove( 'disabled' )
                next.classList.remove( 'disabled' )

                if ( e.progress === 0 ) {
                    previous.classList.add( 'disabled' )
                }
                else if ( e.progress === 1 ) {
                    next.classList.add( 'disabled' )
                }
            } )
            
            previous.classList.add( 'disabled' )
            previous.addEventListener( 'click', () => swiper.slidePrev( 300 ) )
            next.addEventListener( 'click', () => swiper.slideNext( 300 ) )
        }
    } )
}

document.addEventListener( 'NewContentLoaded', init )
document.addEventListener( 'ContentLoaded', init )