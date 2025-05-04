import gsap from 'gsap';

const init = () => {
    document.querySelectorAll('.component--hero-front').forEach(element => {
        const wrappers = element.querySelectorAll('.component--hero-front__wrapper');
        const navRight = element.querySelector('.navigation--right');
        const navLeft = element.querySelector('.navigation--left');

        // Initialisation des positions pour chaque wrapper
        wrappers.forEach(wrapper => {
            const images = wrapper.querySelectorAll('.component--hero-front__image');
            const contains = wrapper.querySelectorAll('.component--hero-front__image--contain');

            images.forEach((img, i) => {
                if (i !== 0) gsap.set(img, { xPercent: 100 });
            });

            contains.forEach((cont, i) => {
                if (i !== 0) gsap.set(cont, { xPercent: -100 });
            });
        });

        // Initialisation des numéros et titres
        const numberWrappers = element.querySelectorAll('.component--hero-front__wrapper--title .number-wrapper');
        const titleWrappers = element.querySelectorAll('.component--hero-front__wrapper--title .title-wrapper');

        // Initialisation des numéros
        numberWrappers.forEach(numberWrapper => {
            const numbers = numberWrapper.querySelectorAll('.number');

            numbers.forEach((num, i) => {
                const unitDigit = num.querySelector('.unit-digit');
                if (i !== 0) {
                    gsap.set(unitDigit, { yPercent: 100, opacity: 0 });
                }
            });
        });

        // Initialisation des titres
        titleWrappers.forEach(titleWrapper => {
            const titles = titleWrapper.querySelectorAll('.title');

            titles.forEach((title, i) => {
                if (i !== 0) gsap.set(title, { yPercent: 100, opacity: 0 });
            });
        });

        // Écouteurs d'événements pour la navigation
        navRight.addEventListener('click', () => navigate(1));
        navLeft.addEventListener('click', () => navigate(-1));

        function navigate(direction) {
            // Créer une timeline GSAP
            const tl = gsap.timeline();

            // Obtenir tous les wrappers
            const wrappers = element.querySelectorAll('.component--hero-front__wrapper');

            // Convertir NodeList en Array pour pouvoir inverser l'ordre si nécessaire
            let wrappersArray = Array.from(wrappers);

            // Inverser l'ordre des wrappers si la direction est négative
            if (direction < 0) {
                wrappersArray.reverse();
            }

            // Définir la durée et le délai entre chaque wrapper
            const duration = 0.5;
            const staggerDelay = 0.1; // Délai entre chaque wrapper

            // Parcourir chaque wrapper avec un délai entre eux
            wrappersArray.forEach((wrapper, index) => {
                const images = wrapper.querySelectorAll('.component--hero-front__image');
                const contains = wrapper.querySelectorAll('.component--hero-front__image--contain');
                const activeImage = wrapper.querySelector('.component--hero-front__image.active');
                const activeContain = wrapper.querySelector('.component--hero-front__image--contain.active');
                const activeIndex = Array.from(images).indexOf(activeImage);

                // Utilisation de gsap.utils.wrap pour boucler les index
                const wrapIndex = gsap.utils.wrap(0, images.length);
                const nextIndex = wrapIndex(activeIndex + direction);

                const nextImage = images[nextIndex];
                const nextContain = contains[nextIndex];

                // Calcul du timing pour ce wrapper
                const wrapperDelay = index * staggerDelay;

                // Ajouter les animations à la timeline avec un décalage
                tl.to(activeImage, { xPercent: -100 * direction, duration: duration }, wrapperDelay)
                  .to(activeContain, { xPercent: 100 * direction, duration: duration }, wrapperDelay)
                  .fromTo(nextImage, { xPercent: 100 * direction }, { xPercent: 0, duration: duration }, wrapperDelay)
                  .fromTo(nextContain, { xPercent: -100 * direction }, { xPercent: 0, duration: duration }, wrapperDelay)
                  // Mise à jour des classes actives après l'animation pour ce wrapper
                  .add(() => {
                      activeImage.classList.remove('active');
                      activeContain.classList.remove('active');
                      nextImage.classList.add('active');
                      nextContain.classList.add('active');
                  }, wrapperDelay + duration);
            });

            // Animation des numéros et titres pour chaque wrapper de titres
            const numberWrappers = element.querySelectorAll('.component--hero-front__wrapper--title .number-wrapper');
            const titleWrappers = element.querySelectorAll('.component--hero-front__wrapper--title .title-wrapper');

            numberWrappers.forEach(numberWrapper => {
                const numbers = numberWrapper.querySelectorAll('.number');
                const activeNumber = numberWrapper.querySelector('.number.active');
                const activeIndex = Array.from(numbers).indexOf(activeNumber);

                const wrapIndex = gsap.utils.wrap(0, numbers.length);
                const nextIndex = wrapIndex(activeIndex + direction);

                const nextNumber = numbers[nextIndex];

                const activeUnitDigit = activeNumber.querySelector('.unit-digit');
                const nextUnitDigit = nextNumber.querySelector('.unit-digit');

                // Initialiser le prochain chiffre hors écran
                gsap.set(nextUnitDigit, { yPercent: 100, opacity: 0 });

                // Calcul du timing pour les numéros
                const textDelay = wrappersArray.length * staggerDelay;

                // Ajouter les animations à la timeline
                tl.to(activeUnitDigit, { yPercent: -100, opacity: 0, duration: duration }, textDelay)
                  .to(nextUnitDigit, { yPercent: 0, opacity: 1, duration: duration }, textDelay + duration)
                  .add(() => {
                      activeNumber.classList.remove('active');
                      nextNumber.classList.add('active');
                  }, textDelay + duration * 2);
            });

            titleWrappers.forEach(titleWrapper => {
                const titles = titleWrapper.querySelectorAll('.title');
                const activeTitle = titleWrapper.querySelector('.title.active');
                const activeIndex = Array.from(titles).indexOf(activeTitle);

                const wrapIndex = gsap.utils.wrap(0, titles.length);
                const nextIndex = wrapIndex(activeIndex + direction);

                const nextTitle = titles[nextIndex];

                // Initialiser le prochain titre hors écran
                gsap.set(nextTitle, { yPercent: 100, opacity: 0 });

                // Calcul du timing pour les titres
                const textDelay = wrappersArray.length * staggerDelay;

                // Ajouter les animations à la timeline
                tl.to(activeTitle, { yPercent: -100, opacity: 0, duration: duration }, textDelay)
                  .to(nextTitle, { yPercent: 0, opacity: 1, duration: duration }, textDelay + duration)
                  .add(() => {
                      activeTitle.classList.remove('active');
                      nextTitle.classList.add('active');
                  }, textDelay + duration * 2);
            });
        }
    });
};

document.addEventListener( 'NewContentLoaded', init )
document.addEventListener( 'ContentLoaded', init )