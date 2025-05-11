
const mix = require('laravel-mix');
const glob = require('glob')

mix.options({
    processCssUrls: false,
    clearConsole: true,
    terser: {
        extractComments: false,
    },
    manifest: false
});

mix.disableSuccessNotifications();
// mix.setPublicPath('public/assets');
// Run all webpack.mix.js in app
glob.sync('./packages/*/webpack.mix.js').forEach(item => require(__dirname + '/' + item));
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.sass('resources/assets/sass/app.scss', 'public/assets/css')
    .sass('resources/assets/sass/base.scss', 'public/assets/css')
    .js('resources/assets/js/main.js', 'public/assets/js');