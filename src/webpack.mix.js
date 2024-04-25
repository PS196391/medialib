let mix = require('laravel-mix');

mix.js('src/resources/js/app.js', 'dist/js')
    .postCss('src/resources/css/app.css', 'dist/css', [
        require('tailwindcss'),
    ]);
