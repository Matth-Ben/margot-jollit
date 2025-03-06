import gsap from 'gsap';
import { trap_focus, focusableElementsString } from '../utils/trap-focus';

const init_pannel = () => {

    // Créer un overlay
    const overlay = document.createElement( 'div' )
    overlay.classList.add( 'component-pannel-overlay' )
    document.body.appendChild( overlay )

    // Fermer le pannel en cliquant sur l'overlay
    document.body.addEventListener( 'click', event => {
        if ( event.target.classList.contains( 'component-pannel-overlay' ) ) {
            document.dispatchEvent( new CustomEvent( 'ClosePannel' ) )
        }
    } )

    // Instancier chaque pannel
    document.querySelectorAll( '.component-pannel' ).forEach( element => {
        if ( element.classList.contains( 'component-pannel--init' ) ) {
            return
        } else {
            element.classList.add( 'component-pannel--init' )
        }

        // Récupérer l'id du pannel
        const pannel_id = element.getAttribute( 'id' )

        // Si l'id n'existe pas, ne rien faire
        if ( !pannel_id ) return

        let openButton // Dernier bouton cliqué
        let toggleButtons = document.querySelectorAll( `[data-pannel-target="${pannel_id}"]` ) // Tous les boutons liés au pannel
        
        const firstFocusableElement = element.querySelector( '[data-focus]' ) ?? element.querySelector( focusableElementsString )
        const timeline = gsap.timeline( {
            paused: true,
            onStart: () => element.classList.add( 'is-visible' ),
            onReverseComplete: () => element.classList.remove( 'is-visible' )
        } )

        const from_parameters = {} // Dépend de la position du pannel
        const parameters = {
            duration: data?.transitions?.secondary?.duration ? data.transitions.secondary.duration / 1000 : 1.4,
            ease: 'power2.inOut',
            onComplete: () => {},
            onReverseComplete: () => {},
        }

        // Définir les paramètres de l'animation en fonction de la position du pannel
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

        // Créer l'animation
        timeline.fromTo( element, from_parameters, parameters )

        // Gérer le focus
        trap_focus( element, close )
        
        // Gérer l'ouverture et la fermeture du pannel sur tous les clics
        document.addEventListener( 'click', event => {
            let button, id
            
            // Trouver le bouton qui a été cliqué
            if ( event.target.closest( '[data-pannel-target]' ) ) {
                button = event.target.closest( '[data-pannel-target]' )
            }
            else if ( event.target.getAttribute( 'data-pannel-close' ) !== null ) {
                button = event.target
            }

            id = button?.getAttribute( 'data-pannel-target' )

            // Si le bouton cliqué n'est pas lié à ce pannel, ne rien faire
            if ( id !== pannel_id ) {
                return
            }

            // Réatribuer les boutons
            toggleButtons = document.querySelectorAll( `[data-pannel-target="${pannel_id}"]` )

            // Ouvrir ou fermer le pannel
            element.classList.toggle( 'active' )
            
            if ( element.classList.contains( 'active' ) ) {
                document.dispatchEvent( new CustomEvent( 'ClosePannel' ), { detail: { pannel: element } } ) // Fermer tous les pannels
                element.classList.add( 'active' ) // Ajouter la classe active
                open( button ) // Ouvrir le pannel
            } else {
                close()
            }
        } )

        // Fermer le pannel à l'événnement 'ClosePannel'
        document.addEventListener( 'ClosePannel', () => {
            if ( element.classList.contains( 'active' ) ) {
                close()
                toggleButtons.forEach( button => button.classList.remove( 'active' ) )
                element.classList.remove( 'active' )
            }
        } )

        // Rafrachir l'animation à chaque redimensionnement de la fenêtre
        document.addEventListener( 'WindowResized', () => {
            timeline.invalidate()
        } )
    } )
}

document.addEventListener( 'ContentLoaded', init_pannel )
document.addEventListener( 'NewContentLoaded', init_pannel )