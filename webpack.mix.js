const mix = require('laravel-mix');

mix
    .setPublicPath('public/build')
    .setResourceRoot('/build/')
    .js('resources/js/app.js', 'js')
    .less('resources/less/app.less', 'css')
    .version();
