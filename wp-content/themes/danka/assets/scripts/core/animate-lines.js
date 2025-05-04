import gsap from 'gsap'
import ScrollTrigger from 'gsap/ScrollTrigger';
import splitLine from '../utils/split-line'

gsap.registerPlugin(ScrollTrigger);

const animateLines = ( element, delay = 100 ) => {
    splitLine( element )
    const lineElements = element.querySelectorAll( '._y .line' )
    
    if ( lineElements.length === 0 ) {
        return
    }
    
    // Positionner initialement chaque ligne en dessous de sa position d'origine
    gsap.set(lineElements, { yPercent: 100 });
    
    // Animation : chaque ligne passe de yPercent: 100 à yPercent: 0
    gsap.to(lineElements, {
        yPercent: 0,
        duration: 0.8,
        ease: 'power3.out',
        stagger: 0.05,
        scrollTrigger: {
            trigger: element,      // L'élément à observer
            start: 'top 60%',      // Déclenche l'animation quand le haut de l'élément atteint 80% de la hauteur de viewport
            toggleActions: 'play none none none', // Joue l'animation quand l'élément entre dans le viewport
        },
    });
}

export default () => {
    document.querySelectorAll( '[animate-lines]' ).forEach( element => {
        if (!element.classList.contains('animate-lines--init')) {
            element.classList.add('animate-lines--init')
            animateLines(element)
        }
    } ) 
}

// document.addEventListener('ContentLoaded', init)
// document.addEventListener('NewContentLoaded', init)