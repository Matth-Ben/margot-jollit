const mix = require('laravel-mix');
const path = require('path');

mix.setPublicPath( path.resolve( "./" ) )

// mix.postCss('assets/styles/app.scss', 'public/css', [
//     require('@tailwindcss/postcss'),
// ]);

mix
    .options({
        postCss: [
            require('@tailwindcss/postcss'),
        ],
    })
    .sass('assets/styles/app.scss', 'assets/build')
    // .postCss('assets/build/css/app.css', 'assets/build', [
    //     require('@tailwindcss/postcss'),
    // ])
    // .js("assets/scripts/app.js", "assets/build")
    // .webpackConfig({
    //     watchOptions: {
    //         aggregateTimeout: 200,
    //         poll: 1000, // Vérifie les fichiers toutes les 1000 ms
    //     },
    //     stats: {
    //         warnings: true, // Assure que les messages de debug et autres s'affichent.
    //     },
    // });


mix.browserSync({
    proxy: "wp-danka.test",
    host: "wp-danka.test",
    injectChanges: false,
    files: ["./assets/build"],
    port: 8080,
    open: false // Mettre à "true" pour ouvrir un onglet automatiquement (http://localhost:8080)
});

if (mix.inProduction()) {
    mix.version();
}



// let mix = require("laravel-mix")
// let path = require("path")

// require('dotenv').config();
// // require( 'laravel-mix-tailwind' )
// // require( 'mix-tailwindcss' ) //

// mix.webpackConfig({
//     // plugins: {
//     //     "@tailwindcss/postcss": {},
//     // },
//     // resolve: {
//     //    ...
//     // },
//     // stats: {
//     //      children: true
//     // }
// });



// // mix.setPublicPath( path.resolve( "./" ) )
// // mix.js("assets/scripts/app.js", "assets/build");
// // mix.sass("assets/styles/app.scss", "assets/build", require);
// // mix.tailwind();

// // mix.browserSync({
// //     proxy: process.env.APP_URL,
// //     host: process.env.APP_URL,
// //     injectChanges: false,
// //     files: ["./assets/build", "./views"],
// //     port: 8080,
// //     open: false // Mettre à "true" pour ouvrir un onglet automatiquement (http://localhost:8080)
// // });
// // mix.version();
