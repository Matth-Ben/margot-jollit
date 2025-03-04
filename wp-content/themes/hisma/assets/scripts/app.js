import '../styles/app.scss'

// app.js
// import Swup from 'swup';
import gsap from 'gsap';
import uniqid from 'uniqid';

import './core';

// import timelineAnimation from './core/timeline-animation';
// import splitText from './core/split-text';
// import parallax from './core/parallax';
// import scrollbarWidth from './core/scrollbar-width';
// import responsiveImages from './core/responsive-images';
// import refreshBodyClasslist from './core/refresh-body-classlist';
// import scrollbar from './core/scrollbar';

// // components
// import plyr from './components/plyr-init';
// import modal, { destroyModals, closeModal, currentOpenModal } from './components/modal';

// // utils
// import swiper from './utils/swiper';

// document.addEventListener('ContentLoaded', () => {
//     init_custom_checkbox();
//     init_custom_select();

//     plyr();
//     modal();
//     swiper();
// });

// // Initialisation de Swup
// const swup = new Swup();

// const initComponents = () => {
//     plyr();
//     modal();
//     splitText();
//     timelineAnimation();
//     parallax();
//     scrollbarWidth();
//     scrollbar();
//     swiper();
//     responsiveImages();
// };

// const destroyComponents = () => {
//     if (currentOpenModal) {
//         closeModal(currentOpenModal); // Ferme la modal ouverte
//     }
//     destroyModals();
//     // Ajoute ici d'autres destructions de composants si nécessaire
// };

// // Swup hooks
// swup.hooks.replace('animation:out:start', () => {
//     destroyComponents();
// });

// swup.hooks.replace('animation:out:await', async () => {
//     await gsap.to('.transition-fade', { opacity: 0, duration: 0.25 });
// });

// swup.hooks.replace('animation:in:await', async () => {
//     lenis.scrollTo(0, { immediate: true });
//     document.querySelector('.component--headroom.active')?.classList.remove('active');
//     refreshBodyClasslist();
//     initComponents();

//     await gsap.fromTo('.transition-fade', { opacity: 0 }, { opacity: 1, duration: 0.25 });
// });

// //// custom input checkbox
// const init_custom_checkbox = () => {
//     document.querySelectorAll('input[type="checkbox"]:not(.custom)').forEach(checkbox => {
//         let span = document.createElement('span');
//         span.classList.add('custom-checkbox');

//         span.innerHTML = `
//             <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
//                 <path d="M14.7812 3.21875C15.0625 3.53125 15.0625 4 14.7812 4.28125L6.53125 12.5312C6.21875 12.8438 5.75 12.8438 5.46875 12.5312L1.21875 8.28125C0.90625 8 0.90625 7.53125 1.21875 7.25C1.5 6.9375 1.96875 6.9375 2.25 7.25L5.96875 10.9688L13.7188 3.21875C14 2.9375 14.4688 2.9375 14.75 3.21875H14.7812Z" fill="currentColor"/>
//             </svg>
//         `;

//         span.addEventListener('click', () => {
//             checkbox.checked = !checkbox.checked;
//         });

//         checkbox.after(span);
//         checkbox.classList.add('custom');
//     });
// };
// ////

// //// custom select
// const init_custom_select = () => {
//     document.querySelectorAll('select:not(.custom)').forEach(select => {
//         let container = document.createElement('div');
//         container.classList.add('custom-select');

//         select.after(container);
//         container.appendChild(select);
//         select.classList.add('custom');
//     });
// };
// ////


const splitText = ( element ) => {
    const text = element.textContent

    // Vide le conteneur et divise le texte en mots
    element.innerHTML = ''
    const words = text.split(/\s+/)

    // Ajoute chaque mot dans un élément span
    words.forEach( ( word, index ) => {
        const span = document.createElement( 'span' )
        const child = document.createElement( 'span' )
        span.className = 'word'
        child.innerHTML = word
        span.appendChild( child )
        element.appendChild( span )

        if ( index < words.length - 1 ) {
            const space = document.createTextNode( ' ' )
            element.appendChild( space )
        }
    } )
}




function animateWords( element, delay = 100 ) {
    element.setAttribute( 'data-id', uniqid() )
    splitText( element )
    const wordElements = element.querySelectorAll( '.word span' )

    if ( wordElements.length === 0 ) {
        return
    }

    const timeline = gsap.timeline( {
        paused: true,
        onComplete: () => {
            element.classList.add( 'visible' )
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
            index * 0.02
        )
    } )

    timeline.play();

    ////
    document.getElementById('text-show').addEventListener('click', () => {
        timeline.play();
    } )
    document.getElementById('text-hide').addEventListener('click', () => {
        timeline.reverse();
    } )
    ////
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.split-test').forEach(element => {
        animateWords(element);
    } )
});



/*

*/