import Plyr from 'plyr';

export default () => {
    document.querySelectorAll('.video-wrapper').forEach(wrapper => {
        const playerElement = wrapper.querySelector('.plyr__video-embed, .js-player');
        const poster = wrapper.querySelector('.video-poster');

        // Initialiser Plyr
        const player = new Plyr(playerElement, {
            youtube: {
                noCookie: true,
                rel: 0,
                showinfo: 0
            },
            vimeo: {
                controls: false,
                dnt: true,
                byline: false,
                portrait: false,
                title: false,
                transparent: false
            },
            controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen'],
        });

        if (poster) {
            // Cacher la vidéo jusqu'à ce que l'utilisateur clique sur le poster
            playerElement.style.pointerEvents = 'none';

            // Lorsque l'utilisateur clique sur le poster
            poster.addEventListener('click', () => {
                poster.style.display = 'none';
                playerElement.style.pointerEvents = 'auto';
                player.play();
            });

            // Variable pour stocker le timeout
            let pauseTimeout;

            player.on('pause', () => {
                // Effacer tout timeout existant
                clearTimeout(pauseTimeout);

                // Démarrer un timeout pour afficher le poster après 200ms
                pauseTimeout = setTimeout(() => {
                    // Vérifier si la vidéo est toujours en pause
                    if (player.paused) {
                        poster.style.display = 'block';
                        playerElement.style.pointerEvents = 'none';
                    }
                }, 200);
            });

            player.on('play', () => {
                // Effacer le timeout si la vidéo reprend avant la fin du délai
                clearTimeout(pauseTimeout);
                poster.style.display = 'none';
                playerElement.style.pointerEvents = 'auto';
            });

            // Afficher le poster lorsque la vidéo est terminée
            player.on('ended', () => {
                // Effacer le timeout par précaution
                clearTimeout(pauseTimeout);
                poster.style.display = 'block';
                playerElement.style.pointerEvents = 'none';
            });
        } else {
            // Si aucun poster n'est présent, s'assurer que la vidéo est interactive
            playerElement.style.pointerEvents = 'auto';
        }

        // Intersection Observer pour détecter la visibilité
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    // L'élément n'est plus visible
                    if (!player.paused) {
                        player.pause();
                    }
                }
            });
        }, {
            threshold: 0.5
        });

        observer.observe(wrapper);
    });
}