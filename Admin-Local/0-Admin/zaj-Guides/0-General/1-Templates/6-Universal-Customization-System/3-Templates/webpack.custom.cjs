const mix = require('laravel-mix');

 // Custom assets that won't be overwritten by vendor updates
mix.js('resources/Custom/js/app.js', 'public/Custom/js')
   .sass('resources/Custom/css/app.scss', 'public/Custom/css')
   .copy('resources/Custom/images/', 'public/Custom/images/')
   .copy('resources/Custom/fonts/', 'public/Custom/fonts/')
   .version(); // Add versioning for cache busting

// Custom options for better performance
mix.options({
    processCssUrls: false,
    clearConsole: false,
});

// Enable source maps in development
if (!mix.inProduction()) {
    mix.sourceMaps();
}

// Optimize for production
if (mix.inProduction()) {
    mix.version()
       .options({
           terser: {
               terserOptions: {
                   compress: {
                       drop_console: true,
                   },
               },
           },
       });
}