import Swup from 'swup'
import gsap from 'gsap'

export default () => {
    const swup = new Swup();
    window.swup = swup

    swup.hooks.replace('animation:out:await', async () => {
        await gsap.to('#swup', { opacity: 0, duration: 0.25 });
    });
    
    swup.hooks.replace('animation:in:await', async () => {
        lenis.scrollTo(0, { immediate: true });
        await gsap.fromTo('#swup', { opacity: 0 }, { opacity: 1, duration: 0.25 });
    });
    
    swup.hooks.on('page:view', () => {
        document.dispatchEvent( new CustomEvent( 'NewContentLoaded' ) )
    });
}
