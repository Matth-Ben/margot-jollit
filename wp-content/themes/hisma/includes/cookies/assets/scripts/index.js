import CookiesManager from "./CookiesManager.js"

export default ( () => {
    document.addEventListener( 'ContentLoaded', () => {
        document.querySelectorAll( '.component--cookies-manager'  ).forEach( element => {
            new CookiesManager( element, cookies_data )
        } )
    } )
} )()
