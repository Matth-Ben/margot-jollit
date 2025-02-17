import Swiper from 'swiper'

export default () => {
    document.querySelectorAll( '.swiper' ).forEach( element => {
        const swiper_element = element
        const button_prev = element.querySelector( '.swiper-previous' )
        const button_next = element.querySelector( '.swiper-next' )

        var swiper = new Swiper( swiper_element, {
            slidesPerView: 1,
            grabCursor: true,
            speed: 600,
            spaceBetween: 24,
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 24,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
            }
        } )

        button_prev.addEventListener( 'click', () => {
            swiper.slidePrev()
        } )
        button_next.addEventListener( 'click', () => {
            swiper.slideNext()
        } )
    } )
}