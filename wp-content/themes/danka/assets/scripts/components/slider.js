const init= () => {
    document.querySelectorAll( '.component-slider' ).forEach( element => {
        const items = element.querySelectorAll( '.component-slider__item' )
        const previous_button = element.querySelector( '.component-slider__previous' )
        const next_button = element.querySelector( '.component-slider__next' )

        let current = 0

        const show = index => {
            items.forEach( item => item.classList.remove( 'active' ) )
            items[index].classList.add( 'active' )
        }

        show( current )

        previous_button.addEventListener( 'click', () => {
            current = current === 0 ? items.length - 1 : current - 1
            show( current )
        } )

        next_button.addEventListener( 'click', () => {
            current = current === items.length - 1 ? 0 : current + 1
            show( current )
        } )
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )
