const mix = require('laravel-mix');


build = [
    {
        'file_path': 'public/assets/general/build/js/toc/main.min.js',
        'files': [
            'packages/dreamteam-base/base/resources/assets/frontend/toc/js/main.js',
        ],
    }
];
buildSass = [
    {
        'file_path': 'public/assets/general/build/css/toc/main.min.css',
        'files': [
            'packages/dreamteam-base/base/resources/assets/frontend/toc/sass/main.scss',
        ],
    },
];
build.map(function(item, index) {
    mix.scripts(item.files, item.file_path);

});
buildSass.forEach(function (value) {
    value.files.map((src, index) => {
        mix.sass(src, value.file_path)
    })
});

const path = require('path')

const source = `packages/dreamteam-base/media`
const dist = `public/vendor/core/core/media`

const sourceBase = `packages/dreamteam-base/base`
const distBase = `public/vendor/core/core/base`

mix
    .sass(`${source}/resources/assets/sass/media.scss`, `${dist}/css`)
    .js(`${source}/resources/assets/sass/js/media.js`, `${dist}/js`)
    .js(`${source}/resources/assets/sass/js/media-setting.js`, `${dist}/js`)
    .js(`${source}/resources/assets/sass/js/integrate.js`, `${dist}/js`)
    .js(`${sourceBase}/resources/assets/plugins/ckeditor5/editor.js`, `${distBase}/plugins/ckeditor5/min`)
    .js(`${sourceBase}/resources/assets/sass/js/functions.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/app.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/core.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/currency.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/login.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/nestable.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/check-version-extension.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/auto-load-job-status.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/setting-email.js`, `${distBase}/js`)
    .js(`${sourceBase}/resources/assets/sass/js/config-theme.js`, `${distBase}/js`)
    .sass(`${sourceBase}/resources/assets/sass/css/style.scss`, `${distBase}/css`)
    .sass(`${sourceBase}/resources/assets/sass/css/nestable.scss`, `${distBase}/css`)
    .sass(`${sourceBase}/resources/assets/sass/css/custom-style.scss`, `${distBase}/css`)
    .sass(`${sourceBase}/resources/assets/sass/css/logs-content.scss`, `${distBase}/css`)
    .sass(`${sourceBase}/resources/assets/sass/css/setting-email.scss`, `${distBase}/css`)
    .sass(`${sourceBase}/resources/assets/sass/css/theme-config.scss`, `${distBase}/css`)
    .sass(`${sourceBase}/resources/assets/sass/css/currency.scss`, `${distBase}/css`)
    .sass(`${sourceBase}/resources/assets/sass/css/setting-group.scss`, `${distBase}/css`);

if (mix.inProduction()) {
    mix
        .copy(`${dist}/css/media.css`, `${source}/resources/assets/css`)
        .copy(`${dist}/js/media.js`, `${source}/resources/assets/js`)
        .copy(`${dist}/js/media-setting.js`, `${source}/resources/assets/js`)
        .copy(`${dist}/js/integrate.js`, `${source}/resources/assets/js`)
        .copy(`${distBase}/plugins/ckeditor5/min/editor.js`, `${sourceBase}/resources/assets/plugins/ckeditor5/min`)
        .copy(`${distBase}/js/functions.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/app.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/core.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/currency.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/login.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/check-version-extension.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/auto-load-job-status.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/setting-email.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/config-theme.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/js/nestable.js`, `${sourceBase}/resources/assets/js`)
        .copy(`${distBase}/css/style.css`, `${sourceBase}/resources/assets/css`)
        .copy(`${distBase}/css/nestable.css`, `${sourceBase}/resources/assets/css`)
        .copy(`${distBase}/css/custom-style.css`, `${sourceBase}/resources/assets/css`)
        .copy(`${distBase}/css/logs-content.css`, `${sourceBase}/resources/assets/css`)
        .copy(`${distBase}/css/setting-email.css`, `${sourceBase}/resources/assets/css`)
        .copy(`${distBase}/css/theme-config.css`, `${sourceBase}/resources/assets/css`)
        .copy(`${distBase}/css/currency.css`, `${sourceBase}/resources/assets/css`)
        .copy(`${distBase}/css/setting-group.css`, `${sourceBase}/resources/assets/css`);
}
