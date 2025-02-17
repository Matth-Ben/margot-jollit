export default class CookiesManager
{
    element

    main

    title

    description

    privacy_policy

    preferences

    services

    all_services

    button_personalize

    button_decline

    button_accept

    button_confirm

    buttons_back

    buttons_consent

    cookie

    cookie_name = 'app_cookie'

    data

    constructor( element, data ) {
        this.element = element
        this.data = data

        // text
        this.title = element.querySelector( '.component__title' )
        this.description = element.querySelector( '.component__description' )
        this.privacy_policy = element.querySelector( '.component__privacy-policy' )

        // buttons
        this.button_personalize = element.querySelector( '.component__button-personalize' )
        this.button_decline = element.querySelector( '.component__button-decline' )
        this.button_accept = element.querySelector( '.component__button-accept' )
        this.button_confirm = element.querySelector( '.component__button-confirm' )
        this.buttons_back = element.querySelectorAll( '.component__back'  )

        // pannels
        this.main = element.querySelector( '.component__main' )
        this.preferences = element.querySelector( '.component__preferences' )

        // services container
        this.services = element.querySelector( '.component__services' )

        // cookie
        this.cookie = this.get_cookie( this.cookie_name )

        this.create_services()
        this.set_text()
        
        // services
        this.all_services = element.querySelectorAll( '.component__service' )

        // consent buttons
        this.buttons_consent = element.querySelectorAll( '.component__service button' )
        
        this.refresh_states()
        this.create_events()
        this.update_youtube_iframe()
        this.update_script()
    }

    set_text() {
        if ( this?.data?.text ) {

            if ( this.data.text.title && this.title ) {
                this.title.innerHTML = this.data.text.title
            }

            if ( this.data.text.description && this.description ) {
                this.description.innerHTML = this.data.text.description
            }

            if ( this.data.text.link && this.privacy_policy ) {
                this.privacy_policy.setAttribute( 'href', this.data.text.link )
            }
            else if ( this.privacy_policy ) {
                this.privacy_policy.remove()
            }
        }
    }
    
    create_services() {
        for ( const [key, item] of Object.entries(this.data) ) {

            if ( item.is_active ) {
                this.services.innerHTML += `<div class="component__service" data-name="${key}"><span>${item.name}</span><button type="button"></button></div>`
            }
        }
    }
    
    refresh_states() {
        const real_services = this.services.querySelectorAll( '.component__service:not([data-name="necessary"])' )

        if ( real_services.length > 0 || true ) {
            if ( this.services.querySelectorAll( '.component__service:not([data-name="necessary"])' ) && this.cookie === '' ) {
                this.element.classList.add( 'active' )
            }
            else {
                this.cookie.split( ',' ).forEach( item => {
                    const item_splited = item.split( '!' )
                    const name = item_splited[0]
                    const is_active = parseInt( item_splited[1] )
                    const service = Array.from( this.all_services ).find( i => i.getAttribute( 'data-name' ) === name )
        
                    if ( service && is_active === 1 ) {
                        service.classList.add( 'active' )
                    }
                } )
            }
        }
    }

    get_cookie( cname ) {
        let name = cname + "="
        let decodedCookie = decodeURIComponent( document.cookie )
        let ca = decodedCookie.split( ';' )

        for ( let i = 0; i < ca.length; i++ ) {
            let c = ca[i]
    
            while ( c.charAt(0) == ' ' ) {
                c = c.substring(1)
            }
            if ( c.indexOf( name ) == 0 ) {
                return c.substring( name.length, c.length )
            }
        }
    
        return ''
    }

    create_events() {
        this.button_personalize.addEventListener( 'click', () => this.select_pannel( this.preferences ) )

        this.buttons_back.forEach( item => {
            item.addEventListener( 'click', () => this.select_pannel( this.main ) )
        } )

        this.buttons_consent.forEach( item => {
            item.addEventListener( 'click', () => {
                const service = item.closest( '.component__service' )
                const service_name = service.getAttribute( 'data-name' )

                if ( service_name !== 'necessary' ) {
                    service.classList.toggle( 'active' )
                }
            } )
        } )

        this.button_decline.addEventListener( 'click', () => {

            this.all_services.forEach( item => {
                if ( item.getAttribute( 'data-name' ) !== 'necessary' ) {
                    item.classList.remove( 'active' )
                }
            } )

            this.save_preferences()
            this.element.classList.remove( 'active' )
        } )
        
        this.button_accept.addEventListener( 'click', () => {
            this.all_services.forEach( item => item.classList.add( 'active' ) )
            this.save_preferences()
            this.element.classList.remove( 'active' )
        } )
        
        this.button_confirm.addEventListener( 'click', () => {
            this.save_preferences()
            this.element.classList.remove( 'active' )
            this.select_pannel( this.main )
        } )

        document.body.addEventListener( 'click', event => {
            let href = event.target.getAttribute( 'href' )
            
            if ( !href && event.target.closest( '[href]' ) ) {
                href = event.target.closest( '[href]' ).getAttribute( 'href' )
            }

            if ( href && href.includes( '#show-cookies-pannel' ) ) {
                event.preventDefault()
                this.element.classList.add( 'active' )
            }
            
            if ( href && href.includes( '#active-cookies-youtube' ) ) {
                event.preventDefault()
                this.active_service( 'youtube' )
                this.update_youtube_iframe()
            }
        } )

        document.addEventListener( 'NewContentLoaded', () => this.update_youtube_iframe() )
    }

    select_pannel( pannel ) {
        this.main.classList.remove( 'active' )
        this.preferences.classList.remove( 'active' )
        pannel.classList.add( 'active' )
    }

    save_preferences() {
        let date = new Date(),
            active_services = []

        this.all_services.forEach( item => {
            const name = item.getAttribute( 'data-name' )

            if ( name && name !== 'necessary' ) {
                active_services.push( name + '!' + ( item.classList.contains( 'active' ) ? 1 : 0 ) )
            }
        } )
        
        date.setMonth(date.getMonth() + 10)
        document.cookie = `${this.cookie_name}=${active_services.join( ',' )}; expires=${date.toUTCString()}; path=/; samesite`
        this.update_youtube_iframe()
        this.update_script()
    }

    active_service( name ) {
        this.all_services.forEach( item => {

            if ( item.getAttribute( 'data-name' ) == name ) {
                item.classList.add( 'active' )
            }
        } )

        this.save_preferences()
    }

    service_is_active( name ) {
        let is_active = false

        this.get_cookie( this.cookie_name ).split( ',' ).forEach( item => {
            let item_splited = item.split( '!' )

            if ( item_splited[0] === name && item_splited[1] == '1' ) {
                is_active = true
                return
            }
        } )

        return is_active
    }

    update_youtube_iframe() {

        if ( this.service_is_active( 'youtube' ) ) {
            document.querySelectorAll( '.component--youtube-manager' ).forEach( item => {
                if ( !item.classList.contains( 'active' ) ) {
                    item.innerHTML = item.getAttribute( 'data-iframe' )
                    item.classList.add( 'active' )
                }
            } )
        }
        else {
            document.querySelectorAll( '.component--youtube-manager' ).forEach( item => {
                item.innerHTML = `
                    <div class="flex flex-col items-center justify-center gap-l">
                        <p class="component__text text-light">Vous devez accepter les cookies (témoins) du site pour pouvoir lire le contenu</p>
                        <a class="component--button component__blue component__small" href="#active-cookies-youtube"><span class="component__text">Activer les cookies</span></a>
                    </div>
                `
                item.classList.remove( 'active' )
            } )
        }
    }

    update_script() {
        this.all_services.forEach( item => {
            const name = item.getAttribute( 'data-name' )

            if ( name && this.service_is_active( name ) ) {
                if ( this.data?.[name] && this.data?.[name]?.script ) {
                    let uniqid = this.uniqid()
                    document.head.innerHTML += `<div class="script-${uniqid}">${this.data?.[name]?.script}</div>`
                    let script = document.querySelector( `.script-${uniqid} script` )
        
                    if ( script ) {
                        eval( script.innerHTML )
                    }
                }
            }
        } )
    }

    uniqid( prefix = "", random = false ) {
        const sec = Date.now() * 1000 + Math.random() * 1000;
        const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");
        return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}`:""}`;
    }
}
