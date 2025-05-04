import data from '../../../data.json'
import smoothScroll from './smooth-scroll'
import page_transition from './page-transition'
import timelineAnimation from './timeline-animation'
import parallax from './parallax'
import gridHelper from './grid-helper'
import scrollbarWidth from './scrollbar-width'
import responsiveImages from './responsive-images'
import windowResizeEvent from './window-resize-event'
import refreshBodyClasslist from './refresh-body-classlist'
import fixScrollBehavior from './fix-scroll-behavior'
import quickLinks from './quick-links'
import animateWords from './animate-words'
import animateLines from './animate-lines'

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
        // taxi()
        page_transition()
        // headroom()
        // splitText()
        animateWords()
        animateLines()
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
        animateWords()
        animateLines()
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
        document.dispatchEvent( new CustomEvent( 'Welcome' ) )
        return
    } )
} )()