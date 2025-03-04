import '../styles/app.css'

import './core'

// history.scrollRestoration = "manual"

// components
import './components/welcome'
import './components/some-discoveries'
import './components/slider'
import './components/marquee-logos'
import './components/pannel-navigation'
import './components/pannel-filters'
// import './components/modal'
// import './components/filters'
// import './components/video'
import './components/accordions'
// import './components/marquee'
// import './components/header-mobile'
// import './components/header-desktop'
// import './components/hero-front-page'
import './components/alert'
// import './components/video-or-embed'
// import './components/pannel'
// import './components/form-search'
// import './components/marquee-images'
// import './components/header-desktop'

// acf components
// import './acf-components/studies'
// import './acf-components/video'
// import './acf-components/map-structures'

// layouts
// import './layouts/SingleCollection'

import gsap from 'gsap';
// import modal, { destroyModals, closeModal, currentOpenModal } from './components/modal';

// document.addEventListener('ContentLoaded', () => {
//     modal();
// });



//// custom input checkbox
const init_custom_checkbox = () => {
    document.querySelectorAll( 'input[type="checkbox"]:not(.custom)' ).forEach( checkbox => {
        let span = document.createElement( 'span' )
            span.classList.add( 'custom-checkbox' )

        span.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.7812 3.21875C15.0625 3.53125 15.0625 4 14.7812 4.28125L6.53125 12.5312C6.21875 12.8438 5.75 12.8438 5.46875 12.5312L1.21875 8.28125C0.90625 8 0.90625 7.53125 1.21875 7.25C1.5 6.9375 1.96875 6.9375 2.25 7.25L5.96875 10.9688L13.7188 3.21875C14 2.9375 14.4688 2.9375 14.75 3.21875H14.7812Z" fill="currentColor"/>
            </svg>
        `
        
        span.addEventListener( 'click', () => {
            checkbox.checked = !checkbox.checked
        } )

        checkbox.after( span )
        checkbox.classList.add( 'custom' )
    } )
}

document.addEventListener( 'ContentLoaded', init_custom_checkbox )
document.addEventListener( 'NewContentLoaded', init_custom_checkbox )
////

//// custom select
const init_custom_select = () => {
    document.querySelectorAll( 'select:not(.custom)' ).forEach( select => {
        let container = document.createElement( 'div' )
            container.classList.add( 'custom-select' )
        
        select.after( container )
        container.appendChild( select )
        select.classList.add( 'custom' )        
    } )
}

document.addEventListener( 'ContentLoaded', init_custom_select )
document.addEventListener( 'NewContentLoaded', init_custom_select )
////

//// header height
const set_headroom_height = () => {
    const headroom = document.querySelector( '.component-headroom' )
    document.documentElement.style.setProperty( '--headroom-height', headroom.clientHeight + 'px' )
} // refresh css variable
document.addEventListener( 'WindowResized', set_headroom_height )
document.addEventListener( 'ContentLoaded', set_headroom_height )
////

//// custom headroom
// calculer
// voir si il y a une animation
// recalculer à la fin de l'animation

// let animate = false
// let position = 'top'
// let state = 'pinned'
// let lastScrollTop = 0
// let lastState = 'pinned'
// let lastPosition = 'top'


// const run_headroom = () => {
//     const scroll = window.scrollY
//     const limit = window.innerHeight

//     if ( animate ) {
//         return
//     }

//     if ( scroll > limit ) {
//         position = 'not-top'
//     } else {
//         position = 'top'
//     }

//     if ( scroll > lastScrollTop && scroll > limit ) {
//         state = 'unpinned'
//     }
//     else if ( scroll < lastScrollTop && scroll > limit ) {
//         state = 'pinned'
//     }

//     document.documentElement.classList.remove( 'headroom--pinned', 'headroom--unpinned', 'headroom--top', 'headroom--not-top' )
//     document.documentElement.classList.add( `headroom--${state}`, `headroom--${position}` )
    
//     if ( state !== lastState || position !== lastPosition ) {
//         animate = true
//         setTimeout( () => {
//             animate = false
//             run_headroom()
//         }, 400 )
//     }

//     lastScrollTop = scroll
//     lastState = state
//     lastPosition = position
// }

// window.addEventListener( 'scroll', run_headroom )
////








const focusableElementsString = 'a[href], area[href], input:not([disabled]):not([type="hidden"]), select:not([disabled]), ' +
    'textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex]:not([tabindex="-1"]), [contenteditable]';

const trapFocus = ( element, escape_function ) => { ////
    const focusableElements = element.querySelectorAll(focusableElementsString);

    if (focusableElements.length === 0) {
        element.setAttribute('tabindex', '-1');
        element.focus();
    } else {
        const firstFocusableElement = focusableElements[0];
        const lastFocusableElement = focusableElements[focusableElements.length - 1];

        firstFocusableElement.focus();

        function handleFocusTrap(event) {
            if (event.key === 'Tab') {
                if (event.shiftKey) {
                    if (document.activeElement === firstFocusableElement) {
                        event.preventDefault();
                        lastFocusableElement.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusableElement) {
                        event.preventDefault();
                        firstFocusableElement.focus();
                    }
                }
            } else if (event.key === 'Escape' && escape_function) {
                escape_function()
            }
        }

        element.addEventListener('keydown', handleFocusTrap);
        element._handleFocusTrap = handleFocusTrap;
    }
}



////
const init_pannel = () => {

    // create overlay
    const overlay = document.createElement( 'div' )
    overlay.classList.add( 'component-pannel-overlay' )
    document.body.appendChild( overlay )

    document.body.addEventListener( 'click', event => {
        if ( event.target.classList.contains( 'component-pannel-overlay' ) ) {
            document.dispatchEvent( new CustomEvent( 'ClosePannel' ) )
        }
    } )

    document.querySelectorAll( '.component-pannel' ).forEach( element => {
        if ( element.classList.contains( 'component-pannel--init' ) ) {
            return
        } else {
            element.classList.add( 'component-pannel--init' )
        }

        const pannel_id = element.getAttribute( 'id' )
        
        if ( !pannel_id ) return

        let openButton
        let toggleButtons = document.querySelectorAll( `[data-pannel-target="${pannel_id}"]` )
        
        const firstFocusableElement = element.querySelector( '[data-focus]' ) ?? element.querySelector( focusableElementsString )
        const timeline = gsap.timeline( {
            paused: true,
            onStart: () => element.classList.add( 'is-visible' ),
            onReverseComplete: () => element.classList.remove( 'is-visible' )
        } )
        const from_parameters = {}
        const parameters = {
            duration: data?.transitions?.secondary?.duration ? data.transitions.secondary.duration / 1000 : 1.4,
            ease: 'power2.inOut',
            onComplete: () => {},
            onReverseComplete: () => {},
        }
    
        const open = toggleButton => {
            timeline.play()
            overlay.classList.add( 'is-visible' )
            toggleButtons.forEach( button => button.classList.add( 'active' ) )
            openButton = toggleButton
            openButton.setAttribute( 'aria-expanded', 'true' )
            
            if ( firstFocusableElement ) {
                setTimeout( () => firstFocusableElement.focus(), 10 )
            }
        }
    
        const close = () => {
            timeline.reverse()
            overlay.classList.remove( 'is-visible' )
            toggleButtons.forEach( button => button.classList.remove( 'active' ) )

            if ( openButton ) {
                openButton.focus()
                openButton.setAttribute( 'aria-expanded', 'false' )
            }
        }

        if ( element.classList.contains( 'component-pannel--right' ) ) {
            parameters.right = "0"
            from_parameters.right = () => element.clientWidth * -1
        }
        else if ( element.classList.contains( 'component-pannel--left' ) ) {
            parameters.left = "0"
            from_parameters.left = () => element.clientWidth * -1
        }
        else if ( element.classList.contains( 'component-pannel--top' ) ) {
            parameters.top = "0"
            from_parameters.top = () => element.clientHeight * -1
        }
        else if ( element.classList.contains( 'component-pannel--bottom' ) ) {
            parameters.bottom = "0"
            from_parameters.bottom = () => element.clientHeight * -1
        }

        timeline.fromTo( element, from_parameters, parameters )
        trapFocus( element, close )
        
        document.addEventListener( 'click', event => {
            let button, id
            
            if ( event.target.closest( '[data-pannel-target]' ) ) {
                button = event.target.closest( '[data-pannel-target]' )
            }
            else if ( event.target.getAttribute( 'data-pannel-close' ) !== null ) {
                button = event.target
            }

            id = button?.getAttribute( 'data-pannel-target' )

            if ( id !== pannel_id ) {
                return
            }

            toggleButtons = document.querySelectorAll( `[data-pannel-target="${pannel_id}"]` )
            element.classList.toggle( 'active' )
            
            if ( element.classList.contains( 'active' ) ) {
                document.dispatchEvent( new CustomEvent( 'ClosePannel' ) ) // close other pannels
                element.classList.add( 'active' ) // add active class
                open( button )
            } else {
                close()
            }
        } )

        document.addEventListener( 'ClosePannel', () => {
            if ( element.classList.contains( 'active' ) ) {
                close()
                toggleButtons.forEach( button => button.classList.remove( 'active' ) )
                element.classList.remove( 'active' )
            }
        } )

        document.addEventListener( 'WindowResized', () => {
            timeline.invalidate()
        } )
    } )
}

document.addEventListener( 'ContentLoaded', init_pannel )
document.addEventListener( 'NewContentLoaded', init_pannel )