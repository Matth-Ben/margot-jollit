const { default: taxi } = require("../core/taxi")

const init = () => {
    document.querySelectorAll( '.component-pannel-filters' ).forEach( element => {
        const navigation_buttons = element.querySelectorAll( '.component-pannel-filters__navigation button' )
        const reset_button = element.querySelector( '.component-pannel-filters__reset-button' )
        const apply_button = element.querySelector( '.component-pannel-filters__apply-button' )
        
        const pannels_taxonomy = element.querySelectorAll( '.component-pannel-filters__filter-taxonomy' ) // Panneaux des taxonomies
        const date_buttons = element.querySelectorAll( '.component-pannel-filters__filter-dates button' ) // Panneau des dates

        let current_open_button = null

        // Navigation
        if ( navigation_buttons.length > 0 ) {
            navigation_buttons.forEach( button => {
                button.addEventListener( 'click', () => {
                    const target = button.getAttribute( 'data-filter-target' )
    
                    navigation_buttons.forEach( b => b.classList.remove( 'active' ) ) // Réinitialisation des boutons
                    button.classList.add( 'active' ) // Activation du bouton
                    element.querySelectorAll( '.component-pannel-filters__content > div' ).forEach( pannel => pannel.classList.remove( 'active' ) ) // Réinitialisation des panneaux
                    element.querySelector( '[data-filter="' + target + '"]' ).classList.add( 'active' ) // Activation du panneau
                } )
            } )
        }

        // Taxonomies
        pannels_taxonomy.forEach( pannel => {
            const buttons = pannel.querySelectorAll( 'button' )

            buttons.forEach( button => {
                button.addEventListener( 'click', () => {
                    buttons.forEach( button => button.classList.remove( 'active' ) ) // Réinitialisation des boutons
                    button.classList.add( 'active' ) // Activation du bouton
                } )
            } )
        } )

        // Dates
        date_buttons.forEach( button => {
            button.addEventListener( 'click', () => {
                if ( button.classList.contains( 'active' ) ) {
                    button.classList.remove( 'active' ) // Désactivation du bouton
                    return
                }
                date_buttons.forEach( button => button.classList.remove( 'active' ) ) // Réinitialisation des boutons
                button.classList.add( 'active' ) // Activation du bouton

                // Ajouter la période dans l'URL
                // const url = new URL( window.location.href )
                // const search_params = url.searchParams
                // search_params.set( 'period', button.getAttribute( 'data-period' ) )
                // window.history.pushState( {}, '', url )
            } )
        } )

        // Réinitialisation
        reset_button.addEventListener( 'click', () => {
            document.dispatchEvent( new CustomEvent( 'ClosePannel' ) )
            setTimeout( () => {
                // Recharger la page sans les paramètres de l'URL
                const url = new URL( window.location.href )
                window.taxi.navigateTo( url.origin + url.pathname )
            }, data?.transitions?.secondary?.duration ? data.transitions.secondary.duration : 1400 )
        } )

        // Application
        apply_button.addEventListener( 'click', () => {
            const period_active = element.querySelector( '[data-period].active' )
            const museum_active = element.querySelector( '.component__pannel-museums button.active' )
            const url = new URL( window.location.href )
            const search_params = url.searchParams

            // Ajouter les données des taxonomies dans l'URL
            let taxonomies_parameter = []

            pannels_taxonomy.forEach( pannel => {
                const active = pannel.querySelector( 'button.active:not([data-term="all"])' )
                
                if ( active ) {
                    taxonomies_parameter.push( pannel.getAttribute( 'data-filter' ) + ',' + active.getAttribute( 'data-term' ) )
                }
            } )

            if ( taxonomies_parameter.length > 0 ) {
                search_params.set( 'taxonomies', taxonomies_parameter.join( '|' ) )
            } else {
                search_params.delete( 'taxonomies' )
            }


            // Ajouter les données de la période dans l'URL
            if ( period_active ) {
                search_params.set( 'period', period_active ? period_active.getAttribute( 'data-period' ) : '' )
            } else {
                search_params.delete( 'period' )
            }

            // Ajouter les données du musée dans l'URL
            if ( museum_active ) {
                search_params.set( '_museum', museum_active ? museum_active.getAttribute( 'data-museum' ) : '' )
            } else {
                search_params.delete( '_museum' )
            }

            document.dispatchEvent( new CustomEvent( 'ClosePannel' ) )

            setTimeout( () => {
                window.history.pushState( {}, '', url )
                window.taxi.navigateTo( url.href )
            }, data?.transitions?.secondary?.duration ? data.transitions.secondary.duration : 1400 )
        } )

        // Défaut
        if ( navigation_buttons.length > 0 ) {
            navigation_buttons[0].classList.add( 'active' )
            element.querySelector( '[data-filter="' + navigation_buttons[0].getAttribute( 'data-filter-target' ) + '"]' ).classList.add( 'active' )
        } else {
            element.querySelector( '[data-filter]' ).classList.add( 'active' )
        }
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )