export default () => {
    let resizeTimer

    window.addEventListener( "resize", () => {
        clearTimeout( resizeTimer )
        resizeTimer = setTimeout( () => {
            document.dispatchEvent( new CustomEvent( 'WindowResized' ) )
        }, 200 )
    } )
}