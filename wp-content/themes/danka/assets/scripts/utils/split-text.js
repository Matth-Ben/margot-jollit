export default function splitText( element ) {
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