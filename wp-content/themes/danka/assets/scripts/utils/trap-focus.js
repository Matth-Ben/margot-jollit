
const focusableElementsString = 'a[href], area[href], input:not([disabled]):not([type="hidden"]), select:not([disabled]), ' +
    'textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex]:not([tabindex="-1"]), [contenteditable]';

const trap_focus = ( element, escape_function ) => {
    // console.log(focusableElementsString)

    const focusableElements = element.querySelectorAll(focusableElementsString);

    if (focusableElements.length === 0) {
        element.setAttribute('tabindex', '-1');
        element.focus();
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
            } else if (event.key === 'Escape' && escape_function) {
                escape_function()
            }
        }

        element.addEventListener('keydown', handleFocusTrap);
        element._handleFocusTrap = handleFocusTrap;
    }
}

export { trap_focus, focusableElementsString }