const fs = require('fs')
let path = require('path')

let rawdata = fs.readFileSync('data.json');
let data = JSON.parse(rawdata);
let breakpoint_desktop = data.breakpointDesktop
let breakpoint_desktop_value = data.breakpoints[breakpoint_desktop]
let utilities = ''
let global_css_variables = []
let desktop_global_css_variables = []
let responsive_css_variables = {}
let custom_css = ''
let content = ''
const remDefault    = parseFloat(data.rem.default);    // ex. 10
const xxlBreakpoint = data.breakpoints.xxl;            // ex. 1440
const maxViewport   = 1920;
const maxFont       = (remDefault * maxViewport / xxlBreakpoint).toFixed(3);  // "13.333"

// Breakpoints
if ( data?.breakpoints ) {
    responsive_css_variables['default'] = []
    responsive_css_variables['default'].push(`--breakpoint-d: ${data.breakpoints[breakpoint_desktop]}px;`)

    for ( const [key, item] of Object.entries( data.breakpoints ) ) {
        responsive_css_variables[key] = []
        responsive_css_variables['default'].push(`--breakpoint-${key}: ${item}px;`)
    }
}

// Margins
if ( data?.margins ) {
    for (const [key, item] of Object.entries(data.margins)) {
        responsive_css_variables[key].push(`--spacing-margin: ${item}rem;`)
    }
}

// Gaps
if ( data?.gaps ) {
    for (const [key, item] of Object.entries(data.gaps)) {
        responsive_css_variables[key].push(`--spacing-gap: ${item}rem;`)
    }
}

// maxColumns
if ( data?.maxColumns ) {
    for (const [key, item] of Object.entries(data.maxColumns)) {
        responsive_css_variables[key].push(`--max-columns: ${item};`)
    }
}

// Spacings
if ( data?.spacings ) {
    for ( const [key, item] of Object.entries( data.spacings ) ) {
        if ( key !== 'desktop' ) {
            responsive_css_variables['default'].push(`--spacing-${key}: ${item}rem;`)
        } else {
            for ( const [k, i] of Object.entries( item ) ) {
                responsive_css_variables[breakpoint_desktop].push(`--spacing-${k}: ${i}rem;`)
            }
        }
    }
}

// Colors
if ( data?.colors ) {
    for ( const [key, item] of Object.entries( data.colors ) ) {
        responsive_css_variables['default'].push(`--color-${key}: ${item};`)
    }
}

// Transitions
if ( data?.transitions ) {
    for ( const [key, item] of Object.entries( data.transitions ) ) {
        if (key === 'default') {
            responsive_css_variables['default'].push(`--default-transition-duration: ${item?.duration}ms;`)
            responsive_css_variables['default'].push(`--default-transition-timing-function: ${item?.timing};`)
            global_css_variables.push(`--transition-duration: ${item?.duration}ms;`)
            global_css_variables.push(`--transition-timing-function: ${item?.timing};`)
            global_css_variables.push(`--transition: var(--transition-duration) var(--transition-timing-function);`)
            utilities += `\n@utility transition {\n  transition: var(--transition);\n}`
        } else {
            global_css_variables.push(`--transition-${key}-duration: ${item?.duration}ms;`)
            global_css_variables.push(`--transition-${key}-timing-function: ${item?.timing};`)
            global_css_variables.push(`--transition-${key}: var(--transition-${key}-duration) var(--transition-${key}-timing-function);`)
            utilities += `\n@utility transition-${key} {\n  transition: var(--transition-${key});\n}`
        }
    }
}

// Text styles
if ( data?.textStyles ) {
    for ( const [key, item] of Object.entries( data.textStyles ) ) {
        if ( key !== 'desktop' ) {
            let css = ''
            for ( const [k, i] of Object.entries( item ) ) {
                css += `  ${k}: var(--ts-${key}-${k});\n`
                if (k !== 'font-family')  {
                    global_css_variables.push(`--ts-${key}-${k}: ${i};`)
                } else {
                    if (data.fonts?.[i] && data.fonts?.[i]?.name && data.fonts?.[i]?.serif) {
                        global_css_variables.push(`--ts-${key}-${k}: ${data.fonts[i].name}, ${data.fonts[i].serif};`)
                    }
                }
            }
            utilities += `\n@utility ts-${key} {\n${css}}`
            custom_css += `\n\n${key} {\n  @apply ts-${key};\n}`
        } else {
            for ( const [k, i] of Object.entries( item ) ) {
                for ( const [kk, ii] of Object.entries( i ) ) {
                    desktop_global_css_variables.push(`--ts-${k}-${kk}: ${ii};`)
                }
            }
        }
    }
}

// Custom
if ( data?.custom ) {
    for ( const [key, item] of Object.entries( data.custom ) ) {
        for ( const [k, i] of Object.entries( item) ) {
            if (k == "default") {
                responsive_css_variables["default"].push(`--${key}: ${i};`)
            }
            if (k == "desktop") {
                responsive_css_variables[breakpoint_desktop].push(`--${key}: ${i};`)
            }
        }
    }
}

// Others
for (let index = 1; index <= 300; index++) {
    responsive_css_variables['default'].push(`--spacing-${index}: ${index / 10}rem;`)
}
for (let index = 1; index <= 12; index++) {
    responsive_css_variables['default'].push(`--spacing-col-${index}: calc( ((var(--layout-width) - (var(--spacing-gap) * (var(--max-columns) - 1))) / var(--max-columns)) * ${index} + (var(--spacing-gap) * (${index} - 1)) );`)
    global_css_variables.push(`--spacing-col-${index}: calc( ((var(--layout-width) - (var(--spacing-gap) * (var(--max-columns) - 1))) / var(--max-columns)) * ${index} + (var(--spacing-gap) * (${index} - 1)) );`)
}
global_css_variables.push('--layout-width: calc(100vw - 2 * var(--spacing-margin));')

// Custom CSS
custom_css += `\n\nhtml {\n  font-size: 10px;\n}`
if (data.rem?.xxl === 'scale') {
    custom_css += `\n\n@media screen and (min-width: ${xxlBreakpoint}px) {\n  html {\n    font-size: clamp(\n      ${remDefault}px,\n      calc(100vw * ${remDefault} / ${xxlBreakpoint}),\n      ${maxFont}px\n    );\n  }\n}`;
}
utilities += `\n@utility container {\n  width:100%; max-width: 100%; padding: 0 var(--spacing-margin)\n}`
utilities += `\n@utility gap {\n gap: var(--spacing-gap); \n}`


for ( const [key, item] of Object.entries( responsive_css_variables ) ) {
    if (key === 'default') {
        content += `@theme {\n  ${item.join('\n  ')}\n}`
    } else {
        if (item.length > 0) {
            content += `\n\n@media screen and (min-width: ${data.breakpoints[key]}px) {\n  @theme {\n    ${item.join('\n    ')}\n  }\n}`
        }
    }
}

content += `\n\n${utilities}`
content += `\n\n:root {\n  ${global_css_variables.join('\n  ')}\n}`
content += `\n\n@media screen and (min-width: ${breakpoint_desktop_value}px) {\n  :root {\n    ${desktop_global_css_variables.join('\n    ')}\n  }\n}`
content += `\n\n${custom_css}`

fs.appendFile( path.resolve( './assets/styles/core/data.css' ), "", () => null )
fs.writeFile( path.resolve( './assets/styles/core/data.css' ), content, err => {
    if ( err ) {
        console.error( err )
    }
    // fichier écrit avec succès
} )
