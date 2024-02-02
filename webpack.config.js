var Encore = require('@symfony/webpack-encore');
var webpack = require('webpack');
var path = require('path');

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create public/build/app.js and public/build/app.css
    .addEntry('app', './assets/js/app.js')

    .addStyleEntry('main', './assets/css/main.scss')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()

    // allow sass/scss files to be processed
    .enableSassLoader()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // enable compilation of .vue file
    //.enableVueLoader()

    .addLoader({
        test: /\.svelte$/,
        loader: 'svelte-loader',
    })

    // disable CSS extraction in .css file ( styles will be injected by JS in -hot- dev mode)
    //.disableCssExtraction()

    // copy images to ./public
    .copyFiles({
        from: './assets/images',
        to: '../images/[path][name].[ext]',
    })

    // This line can replace a file by another one. This can be useful to extend comportement of a library
    //.addPlugin(new webpack.NormalModuleReplacementPlugin(
    //  /(.*)\/UcsSelection.svelte/,
    //  path.resolve(__dirname, './assets/js/CustomUcsSelection.svelte')
    //));


//Watching Issues
Encore.configureWatchOptions(watchOptions => {
    watchOptions.poll = 250; // check for changes every 250 milliseconds
});

// export the final configuration
//module.exports = Encore.getWebpackConfig();

let config = Encore.getWebpackConfig();
config.resolve.mainFields = ['svelte', 'browser', 'module', 'main'];
config.resolve.extensions = ['.mjs', '.js', '.svelte'];

let svelte = config.module.rules.pop();
config.module.rules.unshift(svelte);

module.exports = config;