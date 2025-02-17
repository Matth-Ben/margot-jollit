const init = () => {
    document.querySelectorAll('.component-pannel-navigation').forEach( element => {
        const all_items = element.querySelectorAll( '.component-pannel-navigation__items' )
        const all_items_mobile = element.querySelectorAll( '.component-pannel-navigation__items-mobile' )
        const duration = data?.transitions?.default?.duration ? data.transitions.default.duration : 400
        let is_showing = false

        const init_subnavigation = items => {
            items.forEach( items => {
                const parents = items.querySelectorAll( 'li.has-children' )
    
                parents.forEach( parent => {
                    const button = parent.querySelector( 'button' )
    
                    button.addEventListener( 'click', () => {
                        if ( !is_showing ) {
                            is_showing = true
                            items.style.opacity = 0
                        }
    
                        if ( parent.classList.contains( 'active' ) ) {
                            setTimeout( () => {
                                parent.classList.remove( 'active' )
                                element.classList.remove( 'show-subitems' )
                                items.style.opacity = 1
                                is_showing = false
                            }, duration )
                        } else {
                            setTimeout( () => {
                                parents.forEach( p => p.classList.remove( 'active' ) )
                                parent.classList.add( 'active' )
                                element.classList.add( 'show-subitems' )
                                items.style.opacity = 1
                                is_showing = false
                            }, duration )
                        }
                    } )
                } )
            } )
        }

        init_subnavigation( all_items )
        init_subnavigation( all_items_mobile )
    } )
}

document.addEventListener( 'ContentLoaded', init )
