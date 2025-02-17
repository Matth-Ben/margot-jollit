import gsap from 'gsap';

export default function() {
    const container = document.body;

    // Créer le thumb de la scrollbar
    const scrollbarThumb = document.createElement('div');
    scrollbarThumb.className = 'custom-scrollbar-thumb';
    document.body.appendChild(scrollbarThumb);

    let isDragging = false;
    let isHovering = false; // Nouvelle variable pour suivre l'état du survol
    let startY;
    let startScrollTop;
    let hideScrollbarTimeout;

    function updateScrollbar() {
        const containerHeight = window.innerHeight;
        const contentHeight = container.scrollHeight;
        const scrollTop = window.scrollY || window.pageYOffset;

        // Calculer la hauteur du thumb
        const scrollbarThumbHeight = Math.max((containerHeight / contentHeight) * containerHeight, 30); // Hauteur minimale de 30px
        scrollbarThumb.style.height = `${scrollbarThumbHeight}px`;

        // Calculer la position du thumb
        const scrollbarThumbTop = (scrollTop / (contentHeight - containerHeight)) * (containerHeight - scrollbarThumbHeight);
        scrollbarThumb.style.transform = `translateY(${scrollbarThumbTop}px)`;
    }

    function showScrollbar() {
        gsap.to(scrollbarThumb, { opacity: 1, duration: 0.3, ease: 'power2.out' });
        if (hideScrollbarTimeout) {
            clearTimeout(hideScrollbarTimeout);
        }
        hideScrollbarTimeout = setTimeout(() => {
            hideScrollbar();
        }, 1000); // Temps avant de cacher la scrollbar après l'arrêt du scroll
    }

    function hideScrollbar() {
        if (!isDragging && !isHovering) {
            gsap.to(scrollbarThumb, { opacity: 0, duration: 0.3, ease: 'power2.out' });
        }
    }

    let ticking = false;

    function onScroll() {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                updateScrollbar();
                ticking = false;
            });
            ticking = true;
        }
        showScrollbar();
    }

    function onResize() {
        updateScrollbar();
    }

    window.addEventListener('scroll', onScroll);
    window.addEventListener('resize', onResize);

    // Initialiser la scrollbar
    updateScrollbar();
    hideScrollbar();

    // Gestion du drag du thumb
    scrollbarThumb.addEventListener('mousedown', (e) => {
        isDragging = true;
        startY = e.clientY;
        startScrollTop = window.scrollY || window.pageYOffset;
        document.body.classList.add('no-select'); // Empêcher la sélection de texte pendant le drag
        showScrollbar(); // Afficher la scrollbar pendant le drag
        e.preventDefault();
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            const deltaY = e.clientY - startY;
            const containerHeight = window.innerHeight;
            const contentHeight = container.scrollHeight;
            const scrollbarThumbHeight = scrollbarThumb.offsetHeight;

            const maxScrollTop = contentHeight - containerHeight;
            const maxThumbMove = containerHeight - scrollbarThumbHeight;

            const scrollTop = startScrollTop + (deltaY / maxThumbMove) * maxScrollTop;
            window.scrollTo(0, scrollTop);

            // Mettre à jour le thumb pendant le drag
            updateScrollbar();
        }
    });

    document.addEventListener('mouseup', () => {
        if (isDragging) {
            isDragging = false;
            document.body.classList.remove('no-select');
            hideScrollbar(); // Cacher la scrollbar après le drag
        }
    });

    // Afficher la scrollbar lors du survol
    scrollbarThumb.addEventListener('mouseenter', () => {
        isHovering = true;
        showScrollbar();
    });
    scrollbarThumb.addEventListener('mouseleave', () => {
        isHovering = false;
        if (!isDragging) {
            hideScrollbar();
        }
    });

    // Gestion des événements tactiles pour le drag sur mobile
    scrollbarThumb.addEventListener('touchstart', (e) => {
        isDragging = true;
        startY = e.touches[0].clientY;
        startScrollTop = window.scrollY || window.pageYOffset;
        document.body.classList.add('no-select');
        showScrollbar();
        e.preventDefault();
    });
    
    document.addEventListener('touchmove', (e) => {
        if (isDragging) {
            const deltaY = e.touches[0].clientY - startY;
            const containerHeight = window.innerHeight;
            const contentHeight = container.scrollHeight;
            const scrollbarThumbHeight = scrollbarThumb.offsetHeight;

            const maxScrollTop = contentHeight - containerHeight;
            const maxThumbMove = containerHeight - scrollbarThumbHeight;

            const scrollTop = startScrollTop + (deltaY / maxThumbMove) * maxScrollTop;
            window.scrollTo(0, scrollTop);

            // Mettre à jour le thumb pendant le drag
            updateScrollbar();
        }
    });
    
    document.addEventListener('touchend', () => {
        if (isDragging) {
            isDragging = false;
            document.body.classList.remove('no-select');
            hideScrollbar();
        }
    });
}
