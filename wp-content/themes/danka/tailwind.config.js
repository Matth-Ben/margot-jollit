const fs = require('fs')
let path = require('path')
const plugin = require('tailwindcss/plugin')

let rawdata = fs.readFileSync('data.json');
let data = JSON.parse(rawdata);

let spacings = {}
let defaultSpacings = {}
let screens = {}
let colors = {}
let content = ""

if ( data?.spacings ) {
    for ( const [key, item] of Object.entries( data.spacings ) ) {
        if ( key !== 'desktop' ) {
            spacings[key] = `var(--spacing-${key}) !important`
            content += `w-${key} h-${key} `
            content += `m-${key} mt-${key} mr-${key} mb-${key} ml-${key}  my-${key}  mx-${key} `
            content += `p-${key} pt-${key} pr-${key} pb-${key} pl-${key}  py-${key}  px-${key} `
        }
    }
}

for ( let i = 0; i <= 500; i++ ) {
    defaultSpacings[i] = `${i/10}rem !important`
    defaultSpacings['-' + i] = `-${i/10}rem !important`
}

defaultSpacings.margin = 'var(--margin) !important'
defaultSpacings.gap = 'var(--gap) !important'

if ( data?.breakpoints ) {
    for ( const [key, item] of Object.entries( data.breakpoints ) ) {
        if ( key !== 'desktop' ) {
            screens[key] = `${item}px`
        }

        content += `${key}:block ${key}:hidden ${key}:grid `
    }
}

if ( data?.maxColumns ) {
    for ( const [key, item] of Object.entries( data.maxColumns ) ) {
        content += `grid-cols-${item} `
    }
}

if ( data?.colors ) {
    colors.transparent = 'transparent'
    colors = { ...colors }

    for ( const [key, item] of Object.entries( data.colors ) ) {
        colors[key] = `var(--color-${key})`
    }
    
    for ( const [key, item] of Object.entries( data.colors ) ) {
        content += `text-${key} bg-${key} border-${key} `
    }
}

if ( data?.breakpointDesktop ) {
    screens['d'] = screens[data.breakpointDesktop]
}

fs.appendFile( path.resolve( './views/_data.twig' ), "", () => null )
fs.writeFile( path.resolve( './views/_data.twig' ), content, err => {
    if ( err ) {
        console.error( err )
    }
    // fichier écrit avec succès
} )

console.log({ ...defaultSpacings, ...spacings })

module.exports = {
    important: false,
    content: require( 'fast-glob' ).sync( [
        './data.json',
        './views/*.twig',
        './views/**/*.twig',
    ] ),
    theme: {
        extend: {
            spacing: { ...defaultSpacings, ...spacings },
            padding: { ...defaultSpacings, ...spacings },
            margin: { ...defaultSpacings, ...spacings },
            maxWidth: { ...defaultSpacings, ...spacings },
            minWidth: { ...defaultSpacings, ...spacings },
            screens: screens,
            colors: colors,
            backgroundColor: colors,
            borderRadius: {
                DEFAULT: data?.radius ? data?.radius + 'rem' : '1rem'
            }
        }
    },
    plugins: [
        plugin( function ( { addVariant } ) {
            addVariant( "children", "& *" )
            addVariant( "children-hover", "& *:hover" )
            addVariant( "children-first", "& *:first-child" )
            addVariant( "children-last", "& *:last-child" )
            addVariant( "child", "& > *" )
            addVariant( "child-hover", "& > *:hover" )
            addVariant( "child-first", "& > *:first-child" )
            addVariant( "child-last", "& > *:last-child" )
        } )
    ]
}
