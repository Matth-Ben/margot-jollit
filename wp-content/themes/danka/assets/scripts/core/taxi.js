import { Core, Transition } from '@unseenco/taxi'

const animation_duration = 320

class CustomTransition extends Transition {
    /**
     * Handle the transition leaving the previous page.
     * @param { { from: HTMLElement, trigger: string|HTMLElement|false, done: function } } props
     */
    onLeave({ from, trigger, done }) {
        
        if ( !document.body.classList.contains( 'page-switching' ) ) {
            document.body.classList.add( 'page-switching' )
            document.dispatchEvent( new CustomEvent( 'NewContentLoading' ) )
            setTimeout( () => {
                // if ( !document.body.classList.contains( 'headroom--top' ) ) {
                //     lenis.scrollTo( 200, { immediate: true } )
                // }
                done()
            }, animation_duration )
        }
    }
    
    /**
     * Handle the transition entering the next page.
     * @param { { to: HTMLElement, trigger: string|HTMLElement|false, done: function } } props
    */
    onEnter({ to, trigger, done }) {
        setTimeout( () => {
            const i = setInterval( () => {
                if ( document.getElementById( 'app-content' ) ) {
                    clearInterval( i )
                    document.body.classList.remove( 'page-switching' )
                    document.dispatchEvent( new CustomEvent( 'NewContentLoaded' ) )
                    done()
                }
            } )
        }, animation_duration )
    }
}

export default () => {
    const options = {
        links: 'a:not([target="_blank"]):not([href^=\\#]):not([data-prevent-taxi]):not([download])',
        transitions: {
            default: CustomTransition
        },
        bypassCache: true
    }

    const taxi = new Core( options )

    window.taxi = taxi
}