export default () => {
    if (typeof lenis === 'undefined') return

    const scroll_to_anchor = () => {
        const href = link?.getAttribute( 'href' )
        const anchor = href?.split( '#' )[ 1 ]
        if ( !anchor ) return

        const target = document.getElementById( anchor )

        if ( target )  {
            lenis.scrollTo( target )
        }
    }

    document.addEventListener( 'click', event => {
        let link
        
        if ( event.target ) {
            if ( event.target.closest( 'a' ) ) {
                link = event.target.closest( 'a' )
            }
            else if ( event.target.tagName === 'A' ) {
                link = event.target
            }
        }

        const href = link?.getAttribute( 'href' )
        const anchor = href === '#' ? href : href?.split( '#' )[ 1 ]
        if ( !anchor ) return

        if ( anchor === '#' ) {
            lenis.scrollTo( 0 )
        } else {
            const target = document.getElementById( anchor )
    
            if ( target )  {
                lenis.scrollTo( target )
            }
        }
    } )

    document.addEventListener( 'NewContentLoaded', () => {
        const url = window.location.href
        const anchor = url?.split( '#' )[ 1 ]
        if ( !anchor ) return

        const target = document.getElementById( anchor )

        if ( target )  {
            lenis.scrollTo( target, { immediate: true } )
        }
    } )

    // document.querySelectorAll('#app a[href^="#"]').forEach((el) => {
    //     el.addEventListener('click', (e) => {
    //         e.preventDefault()
    //         const id = el.getAttribute('href')?.slice(1)
    //         if (!id) return
    //         const target = document.getElementById(id)
    //         if (target) {
    //             target.scrollIntoView({ behavior: 'smooth' })
    //             lenis.scrollTo(target)

    //             // add anchor to url
    //             if (history.pushState) {
    //                 history.pushState(null, null, `#${id}`)
    //             } else {
    //                 location.hash = `#${id}`
    //             }
    //         }
    //     })
    // })
}
