document.addEventListener( 'Welcome', () => {
    document.dispatchEvent( new CustomEvent( 'ContentLoaded' ) )
} )