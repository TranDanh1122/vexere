const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `packages/${directory}`
const dist = `public/vendor/core/core/${directory}`

mix
    .js(`${source}/resources/assets/js/language.js`, `${dist}/build/js/language.js`)
    .js(`${source}/resources/assets/js/translation.js`, `${dist}/build/js`)
    .sass(`${source}/resources/assets/sass/language.scss`, `${dist}/build/css`)
    .sass(`${source}/resources/assets/sass/translation.scss`, `${dist}/build/css`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/build/js/language.js`, `${source}/resources/assets/build/js`)
        .copy(`${dist}/build/js/translation.js`, `${source}/resources/assets/build/js`)
        .copy(`${dist}/build/css/language.css`, `${source}/resources/assets/build/css`)
        .copy(`${dist}/build/css/translation.css`, `${source}/resources/assets/build/css`)
}
