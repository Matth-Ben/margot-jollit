import data from '../../../data.json'
import smoothScroll from './smooth-scroll'
import headroom from './headroom'
import timelineAnimation from './timeline-animation'
// import splitText from './split-text'
import parallax from './parallax'
import gridHelper from './grid-helper'
import scrollbarWidth from './scrollbar-width'
import responsiveImages from './responsive-images'
import windowResizeEvent from './window-resize-event'
import scrollbar from './scrollbar'

/*
Transition de page:
- cacher contenu
- scroller vers le haut ?
- changer le body class
- afficher contenu
*/

export default ( () => {
    window.data = data

    document.addEventListener( 'ContentLoaded', () => {
        windowResizeEvent()
        headroom()
        // splitText()
        timelineAnimation()
        smoothScroll()
        parallax()
        gridHelper()
        scrollbarWidth()
        scrollbar()
        responsiveImages()
        document.body.classList.add( 'content-loaded' )
    } )
    
    document.addEventListener( 'windowResized', () => {
        responsiveImages()
    } )

    document.addEventListener( 'DOMContentLoaded', () => {

        if ( document.body.classList.contains( 'welcome' ) ) {
            document.dispatchEvent( new CustomEvent( 'Welcome' ) )
            return
        }

        document.dispatchEvent( new CustomEvent( 'ContentLoaded' ) )
    } )
} )()