import Headroom from "headroom.js"

export default function()
{
    const headroom = new Headroom( document.body, {
        offset: {
            up: 100,
            down: 100
        },
        tolerance : {
            up : 5,
            down : 0
        },
        onPin: function() {
            setTimeout( () => {
                document.body.classList.add( 'headroom-pinned' )
                document.body.classList.remove( 'headroom-unpinned' )
            }, 800 )
        },
        // callback when unpinned, `this` is headroom object
        onUnpin: function() {
            setTimeout( () => {
                document.body.classList.add( 'headroom-unpinned' )
                document.body.classList.remove( 'headroom-pinned' )
            }, 800 )
        },
        // callback when above offset, `this` is headroom object
        onTop: function() {
            document.body.classList.add( 'headroom-top' )
            document.body.classList.remove( 'headroom-not-top' )
        },
        // callback when below offset, `this` is headroom object
        onNotTop: function() {
            setTimeout( () => {
                document.body.classList.add( 'headroom-not-top' )
                document.body.classList.remove( 'headroom-top' )
            }, 800 )
        },
    
        // callback when at bottom of page, `this` is headroom object
        onBottom: function () {
            document.body.classList.add('headroom-bottom');
        },
        // callback when moving away from bottom of page, `this` is headroom object
        onNotBottom: function () {
            document.body.classList.remove('headroom-bottom');
        }
    } )

    headroom.init()

    const headroom_element = document.querySelector( '.component--headroom' )

    if ( headroom_element ) {
        const show_duration = headroom_element.getAttribute( 'show-duration' )

        if ( show_duration ) {
            setTimeout( () => {
                headroom_element.classList.add( 'is-shown' )
            }, show_duration )
        }
    }
}