export class Marquee
{
	index = 0

	speed

	firstElement

	interval

	constructor( element, reverse = false, speed = 0.4 ) {
		const container = element.querySelector( '.component__container' )

		this.element = element
		this.firstElement = container.children[0]
		this.container = container
		this.reverse = reverse
		this.speed = speed

		// min 2 children
		container.insertAdjacentHTML( 'beforeend', this.firstElement.outerHTML )

		// get width of all children
		let totalWidth = 0
		Array.from( container.children ).forEach( child => totalWidth += child.clientWidth )

		let index = 0 // prevent infinite loop

		while ( totalWidth < (innerWidth * 4 + 3000) && index < 100 ) {
			container.insertAdjacentHTML( 'beforeend', this.firstElement.outerHTML )
			totalWidth = 0
			Array.from( container.children ).forEach( child => totalWidth += child.clientWidth )
			Array.from( container.children ).forEach( child => child.setAttribute( 'aria-hidden', true ) )
			Array.from( container.children )[0].setAttribute( 'aria-hidden', false )
			index++
		}

		if ( !reverse ) {
			requestAnimationFrame(this.run)
		} else {
			this.index = this.firstElement.clientWidth
			requestAnimationFrame(this.run_reverse)
		}
	}

	run = () => {
		this.firstElement.style.marginLeft = `-${this.index}px`

		if ( this.index > this.firstElement.clientWidth ) {
			this.index = 0
		}

		if ( !this.element.classList.contains( 'paused' ) ) {
			this.index = this.index + this.speed
		}

		requestAnimationFrame(this.run)
	}

	run_reverse = () => {
		this.firstElement.style.marginLeft = `-${this.index}px`

		if ( this.index < 0 ) {
			this.index = this.firstElement.clientWidth
		}

		if ( !this.element.classList.contains( 'paused' ) ) {
			this.index = this.index - this.speed
		}

		requestAnimationFrame(this.run_reverse)
	}
}

export default Marquee
