function getCoords(elem) { // crossbrowser version
    var box = elem.getBoundingClientRect();

    var body = document.body;
    var docEl = document.documentElement;

    var scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
    var scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;

    var clientTop = docEl.clientTop || body.clientTop || 0;
    var clientLeft = docEl.clientLeft || body.clientLeft || 0;

    var top  = box.top +  scrollTop - clientTop;
    var left = box.left + scrollLeft - clientLeft;

    return { top: Math.round(top), left: Math.round(left) };
}

class Parallax
{
    constructor() {
        if ( !document.body.classList.contains( 'parallax-init' ) ) {
            window.addEventListener( 'scroll', this.observe )
            document.body.classList.add( 'parallax-init' )
        }
        this.observe()
    }

    observe() {
        document.querySelectorAll( '[parallax]' ).forEach( element => {
            const speed = element.getAttribute( 'parallax' ) ? element.getAttribute( 'parallax' ) / 10 : .05
            const scrollPosition = window.scrollY - getCoords(element).top
            let value = scrollPosition * speed

            if ( element.getAttribute( 'parallax-down' ) !== null ) {
                value = window.scrollY * speed
            }
            
            element.style.transform = 'translateY(' + value + 'px)';
        } )
    }
}

export default () =>
{
    new Parallax()
}