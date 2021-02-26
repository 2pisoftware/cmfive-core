let mix = require('laravel-mix');

mix
    .ts('src/js/app.ts', '').vue()
    .sass('src/scss/app.scss', '')
    .setPublicPath('dist')
    .setResourceRoot('/system/templates/base/dist');