// modal.js
import gsap from 'gsap';

let isModalOpen = false;
let lastFocusedElement = null;

export let currentOpenModal = null;

function setCurrentOpenModal(modal) {
    currentOpenModal = modal;
}

export function closeModal(modal) {
    if (!modal) return;

    window.lenis?.start();

    // Mettre aria-expanded à false sur le bouton déclencheur
    if (lastFocusedElement) {
        lastFocusedElement.setAttribute('aria-expanded', 'false');
    }

    // Retourner le focus à l'élément déclencheur
    if (lastFocusedElement) {
        lastFocusedElement.focus();
        lastFocusedElement = null;
    }

    // Réactiver le focus sur le contenu sous-jacent
    document.querySelectorAll('body > *:not(#' + modal.id + ')').forEach((el) => {
        el.removeAttribute('aria-hidden');
        el.removeAttribute('tabindex');
    });

    const modalWrapper = modal.querySelector('.component__modal--wrapper, .component__modal-fromTop--wrapper, .component__modal-fromLeft--wrapper, .component__modal-fromBottom--wrapper');
    const modalOverlay = modal.querySelector('.component__modal--overlay, .component__modal-fromTop--overlay, .component__modal-fromLeft--overlay, .component__modal-fromBottom--overlay');

    gsap.fromTo(modalOverlay, {
        opacity: 0.2,
    }, {
        opacity: 0,
        duration: 0.5,
        ease: 'power2.out',
    });

    let animationDirection = 'right'; // par défaut
    if (modal.classList.contains('component__modal-fromTop')) {
        animationDirection = 'top';
    } else if (modal.classList.contains('component__modal-fromLeft')) {
        animationDirection = 'left';
    } else if (modal.classList.contains('component__modal-fromBottom')) {
        animationDirection = 'bottom';
    }

    const fromVars = {};
    const toVars = {
        duration: 0.5,
        ease: 'power2.out',
        onComplete: () => {
            gsap.set(modal, { visibility: 'hidden' });
        },
    };

    if (animationDirection === 'left') {
        fromVars.xPercent = 100;
        toVars.xPercent = 0;
    } else if (animationDirection === 'top') {
        fromVars.yPercent = 100;
        toVars.yPercent = 0;
    } else if (animationDirection === 'right') {
        fromVars.xPercent = -100;
        toVars.xPercent = 0;
    } else if (animationDirection === 'bottom') {
        fromVars.yPercent = -100;
        toVars.yPercent = 0;
    }

    gsap.fromTo(modalWrapper, fromVars, toVars);

    // Retirer les classes "active" et "not-active" du bouton correspondant
    const buttonBurger = document.querySelector(`[data-modal="${modal.id}"]`);
    if (buttonBurger) {
        const burger = buttonBurger.closest('.component--headroom').querySelector('.component__button--burger');
        if (burger) {
            burger.classList.remove('active');
            burger.classList.add('not-active');
        }
    }

    if (currentOpenModal === modal) {
        setCurrentOpenModal(null); // Réinitialise la variable
        isModalOpen = false;
    }
}


export const destroyModals = () => {
    document.querySelectorAll('.component__modal').forEach(modal => {
        modal.removeAttribute('aria-hidden');
        modal.removeAttribute('tabindex');

        const modalOverlay = modal.querySelector('.component__modal--overlay');
        if (modalOverlay) {
            modalOverlay.removeEventListener('click', closeModal.bind(null, modal));
        }
    });
};

export default () => {
    // Fonction générique pour gérer les modals
    function setupModal(buttonClass, modalClass, wrapperClass, overlayClass, closeButtonClass, animationDirection) {
        document.querySelectorAll(buttonClass).forEach(element => {
            if (element.hasAttribute('data-setup')) return; // Évite les doublons

            const buttonModalId = element.getAttribute('data-modal');
            const modal = document.getElementById(buttonModalId);
            if (!modal) return; // Si la modal n'existe pas

            const modalWrapper = modal.querySelector(wrapperClass);
            const modalOverlay = modal.querySelector(overlayClass);
            const closeButton = modal.querySelector(closeButtonClass);

            element.addEventListener('click', () => {
                lastFocusedElement = element;

                if (isModalOpen && currentOpenModal !== modal) {
                    closeModal(currentOpenModal);
                }
                if (isModalOpen && currentOpenModal === modal) {
                    closeModal(modal);
                } else {
                    openModal(modal, modalWrapper, modalOverlay, animationDirection);
                }
            });

            if (closeButton) {
                closeButton.addEventListener('click', (event) => {
                    event.stopPropagation();
                    closeModal(modal);
                });
            }

            modalOverlay.addEventListener('click', () => {
                closeModal(modal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && isModalOpen && currentOpenModal === modal) {
                    closeModal(modal);
                }
            });

            element.setAttribute('data-setup', 'true'); // Marque cet élément comme configuré
        });
    }

    function openModal(modal, modalWrapper, modalOverlay, animationDirection) {
        if (isModalOpen && currentOpenModal !== modal) {
            // Ferme la modal actuellement ouverte si elle est différente
            closeModal(currentOpenModal);
        }

        // Mettre aria-expanded à true sur le bouton déclencheur
        if (lastFocusedElement) {
            lastFocusedElement.setAttribute('aria-expanded', 'true');
        }

        // Rendre le contenu sous-jacent inert ou désactivé
        document.querySelectorAll('body > *:not(#' + modal.id + ')').forEach((el) => {
            el.setAttribute('aria-hidden', 'true');
            el.setAttribute('tabindex', '-1');
        });

        window.lenis?.stop();

        gsap.set(modal, {
            visibility: 'visible',
        });

        const fromVars = {};
        const toVars = {
            duration: 0.5,
            ease: 'power2.out',
            onComplete: () => {
                trapFocus(modal);
            },
        };

        if (animationDirection === 'right') {
            fromVars.xPercent = 0;
            toVars.xPercent = -100;
        } else if (animationDirection === 'top') {
            fromVars.yPercent = 0;
            toVars.yPercent = 100;
        } else if (animationDirection === 'left') {
            fromVars.xPercent = 0;
            toVars.xPercent = 100;
        } else if (animationDirection === 'bottom') {
            fromVars.yPercent = 0;
            toVars.yPercent = -100;
        }

        gsap.fromTo(modalWrapper, fromVars, toVars);

        gsap.fromTo(modalOverlay, {
            opacity: 0,
        }, {
            opacity: 0.2,
            duration: 0.5,
            ease: 'power2.out',
        });

        setCurrentOpenModal(modal); // Mets à jour la variable
        isModalOpen = true;
    }

    function trapFocus(modal) {
        const focusableElementsString = 'a[href], area[href], input:not([disabled]):not([type="hidden"]), select:not([disabled]), ' +
        'textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex]:not([tabindex="-1"]), [contenteditable]';
        const focusableElements = modal.querySelectorAll(focusableElementsString);

        if (focusableElements.length === 0) {
            modal.setAttribute('tabindex', '-1');
            modal.focus();
        } else {
            const firstFocusableElement = focusableElements[0];
            const lastFocusableElement = focusableElements[focusableElements.length - 1];

            firstFocusableElement.focus();

            function handleFocusTrap(event) {
                if (event.key === 'Tab') {
                    if (event.shiftKey) {
                        if (document.activeElement === firstFocusableElement) {
                            event.preventDefault();
                            lastFocusableElement.focus();
                        }
                    } else {
                        if (document.activeElement === lastFocusableElement) {
                            event.preventDefault();
                            firstFocusableElement.focus();
                        }
                    }
                } else if (event.key === 'Escape') {
                    closeModal(modal);
                }
            }

            modal.addEventListener('keydown', handleFocusTrap);
            modal._handleFocusTrap = handleFocusTrap;
        }
    }

    // Appels des différentes modals
    setupModal(
        '.component__button--modal',
        '.component__modal',
        '.component__modal--wrapper',
        '.component__modal--overlay',
        '.component__modal--close',
        'right'
    );

    setupModal(
        '.component__button--modal-fromTop',
        '.component__modal-fromTop',
        '.component__modal-fromTop--wrapper',
        '.component__modal-fromTop--overlay',
        '.component__modal-fromTop--close',
        'top'
    );

    setupModal(
        '.component__button--modal-fromLeft',
        '.component__modal-fromLeft',
        '.component__modal-fromLeft--wrapper',
        '.component__modal--overlay',
        '.component__modal-fromLeft--close',
        'left'
    );

    setupModal(
        '.component__button--modal-fromBottom',
        '.component__modal-fromBottom',
        '.component__modal-fromBottom--wrapper',
        '.component__modal-fromBottom--overlay',
        '.component__modal-fromBottom--close',
        'bottom'
    );
}
