export default () => {
    document.querySelectorAll( 'img[data-sizes]' ).forEach( element => {
        const current_attr_sizes = parseInt( element.getAttribute( 'sizes' ) )
        let bigger_side = element.clientWidth > element.clientHeight ? 'width' : 'height'
        let width = 0
        let height = 0

        const sizes = element.getAttribute( 'data-sizes' ).split( ',' )

        if ( bigger_side === 'width' ) {
            let index = 0

            while ( width < element.clientWidth && sizes[index] ) {
                width = parseInt( sizes[index].split( '-' )[0] )
                height = parseInt( sizes[index].split( '-' )[1] )
                index++
            }
        }
       
        if ( bigger_side === 'height' ) {
            let index = 0

            while ( height < element.clientHeight && sizes[index] ) {
                width = parseInt( sizes[index].split( '-' )[0] )
                height = parseInt( sizes[index].split( '-' )[1] )
                index++
            }
        }

        if ( width && width !== current_attr_sizes  && width > current_attr_sizes ) {
            element.setAttribute( 'sizes', width + 'px' )
        }
    } )
}
