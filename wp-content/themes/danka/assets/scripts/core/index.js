import data from '../../../data.json'
import smoothScroll from './smooth-scroll'
import taxi from './taxi'
import headroom from './headroom'
import timelineAnimation from './timeline-animation'
// import splitText from './split-text'
import parallax from './parallax'
import gridHelper from './grid-helper'
import scrollbarWidth from './scrollbar-width'
import responsiveImages from './responsive-images'
import windowResizeEvent from './window-resize-event'
import refreshBodyClasslist from './refresh-body-classlist'
import fixScrollBehavior from './fix-scroll-behavior'
import quickLinks from './quick-links'
import animateWords from './animate-words'

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
        taxi()
        headroom()
        // splitText()
        animateWords()
        timelineAnimation()
        smoothScroll()
        parallax()
        gridHelper()
        scrollbarWidth()
        responsiveImages()
        fixScrollBehavior()
        quickLinks()
        document.body.classList.add( 'content-loaded' )
    } )
    
    document.addEventListener( 'NewContentLoaded', () => {
        lenis.scrollTo( 0, { immediate: true } )
        refreshBodyClasslist()
        // splitText()
        animateWords()
        timelineAnimation()
        parallax()
        scrollbarWidth()
        responsiveImages()
        fixScrollBehavior()
    } )
    
    document.addEventListener( 'windowResized', () => {
        responsiveImages()
    } )

    document.addEventListener( 'DOMContentLoaded', () => {

        // if ( document.body.classList.contains( 'welcome' ) ) { // pour chaque nouvelle session
            document.dispatchEvent( new CustomEvent( 'Welcome' ) )
            return
        // }

        document.dispatchEvent( new CustomEvent( 'ContentLoaded' ) )
    } )
} )()