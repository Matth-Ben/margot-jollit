const init = () => {
    document.querySelectorAll( '.component--headroom' ).forEach( element => {
        const buttonBurger = element.querySelector( '.component__menu-burger' )
        const buttonModalId = buttonBurger.getAttribute( 'data-modal' )
        const burger = element.querySelector( '.component__button--burger' )
        const modal = document.getElementById( buttonModalId )
        const modalOverlay = modal.querySelector( '.component__modal--overlay' )
        
        buttonBurger.addEventListener( 'click', () => {
            element.classList.toggle('active');
            burger.classList.toggle('active');
            burger.classList.toggle('not-active');
        });

        modalOverlay.addEventListener( 'click', () => {
            element.classList.remove('active');
            burger.classList.remove('active');
            burger.classList.add('not-active');
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                element.classList.remove('active');
                burger.classList.remove('active');
                burger.classList.add('not-active');
            }
        });
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )