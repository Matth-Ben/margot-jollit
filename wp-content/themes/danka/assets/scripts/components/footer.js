import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin( ScrollTrigger )

const init = () => {
    document.querySelectorAll( '.component--footer' ).forEach( element => {

        if ( element.classList.contains( 'is-init' ) ) {
            return
        } else {
            element.classList.add( 'is-init' )
        }

        const element_secondary = element.querySelector( '.component__secondary' )
        
        const instance = gsap.from( element_secondary, {
            ease: "none",
            scrollTrigger: {
                trigger: document.body,
                start: () => `+=${window.scrollY + element_secondary.getBoundingClientRect().top - window.innerHeight}`,
                end: () => `+=${element_secondary.clientHeight}`,
                invalidateOnRefresh: true,
                // scrub: true,
                // markers: true,
                onUpdate: self => {
                    element_secondary.style.setProperty( '--progress', self.progress )
                }
            }
        } )
    } )
}

document.addEventListener( 'ContentLoaded', init )
document.addEventListener( 'NewContentLoaded', init )