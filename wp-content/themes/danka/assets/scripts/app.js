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
import './components/accordions'
import './components/pannel'
import './components/alert'


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


// Séparer les fonctionnalités en modules
document.addEventListener( 'ShowElements', data => {
    if (data?.detail?.elements) {
        data.detail.elements.forEach( element => {
            if (element.getAttribute('animate-words') !== null) {
                // console.log('animate words')
                document.dispatchEvent(new CustomEvent('AnimateWords', { detail: { elements: [element] } }))
            }
        } )
    }
} )