class CarouselBasic
{
    items
    
    previous
    
    next

    index = 0

    constructor( element )
    {
        this.items = element.querySelectorAll( '.carousel__item' )
        this.previous = element.querySelector( '.carousel__previous' )
        this.next = element.querySelector( '.carousel__next' )

        if ( this.items && this.previous && this.next ) {
            this.init_events_listeners()
        }
    }

    init_events_listeners()
    {
        this.previous.addEventListener( 'click', () => {
            let index = this.index - 1 < 0 ? this.items.length - 1 : this.index - 1
            this.show_item( index )
        } )
        
        this.next.addEventListener( 'click', () => {
            let index = this.index + 1 > this.items.length - 1 ? 0 : this.index + 1
            this.show_item( index )
        } )
    }

    show_item( index )
    {
        this.index = index
        this.items.forEach( item => item.classList.remove( 'active' ) )
        this.items[index].classList.add( 'active' )
    }
}

export default CarouselBasic