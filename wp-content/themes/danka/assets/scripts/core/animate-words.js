import gsap from 'gsap'
import splitText from '../utils/split-text'

const animateWords = ( element, delay = 100 ) => {
    splitText( element )
    const wordElements = element.querySelectorAll( '.word span' )

    if ( wordElements.length === 0 ) {
        return
    }

    const timeline = gsap.timeline( {
        paused: true,
        onComplete: () => {
            element.classList.add( 'visible' )
            wordElements.forEach( wordElement => wordElement.parentElement.style.clipPath = "" )
        },
        onReverseComplete: () => {
            element.classList.remove( 'visible' )
        }
    } )

    wordElements.forEach( ( wordElement, index ) => {
        wordElement.parentElement.style.clipPath = 'polygon(0 0, 100% 0, 100% 100%, 0 100%)'
        wordElement.style.display = 'inline-flex'
        timeline.fromTo(
            wordElement,
            { y: "120%" },
            {
                y: 0,
                duration: 1.4,
                ease: 'power3.out',
                onComplete: () => {
                    wordElement.parentElement.classList.add( 'visible' )
                },
                onReverseComplete: () => {
                    wordElement.parentElement.classList.remove( 'visible' )
                },
            },
            index * delay / 1000
        )
    } )

    document.addEventListener('AnimateWords', data => {
        if (data?.detail?.elements) {
            data.detail.elements.forEach( e => {
                if (element == e) {
                    timeline.play();
                }
            } )
        }
    } )
    
    document.addEventListener('AnimateWordsReverse', data => {
        if (data?.detail?.elements) {
            data.detail.elements.forEach( e => {
                if (element == e) {
                    timeline.reverse();
                }
            } )
        }
    } )
}

export default () => {
    document.querySelectorAll( '[animate-words]' ).forEach( element => {
        if (!element.classList.contains('animate-words--init')) {
            element.classList.add('animate-words--init')
            animateWords(element)
        }
    } ) 
}

// document.addEventListener('ContentLoaded', init)
// document.addEventListener('NewContentLoaded', init)