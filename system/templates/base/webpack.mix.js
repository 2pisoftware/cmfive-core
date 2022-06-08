const mix = require("laravel-mix");
// const fs = require("fs");
const path = require('path');

const MixGlob = require('laravel-mix-glob');
const mixGlob = new MixGlob({
    mix,
    mapping: {
        base: {
            byFunc: {
                scss: { ext: "css" },
                sass: { ext: "css" },
                css: { ext: "css" },
            }
        }
    }
});

process.env.DEBUG = true;

async function loadAssets() {
    mix
        .ts("src/js/app.ts", "")
        .vue()
        .sass("src/scss/app.scss", "")
        .setPublicPath("dist")
        .setResourceRoot("/system/templates/base/dist");

    // Compile vue components separately
    mixGlob
        // .ts('src/js/components/*.vue', 'components/')
        .ts('../../../../../../../modules/*/assets/ts/*.ts', 'dist/', null, {
            base: function (file, ext, mm) { // mm => micromatch instance
                return 'dist/' + path.dirname(file).split(path.sep).reverse()[2] + '/';
            }
        })
        .sass('../../../../../../../modules/*/assets/scss/*', 'dist/', {
            sassOptions: {
                includePaths: [__dirname + '/src/scss/', __dirname + '/node_modules']
            }
        })
        .vue()
        .setPublicPath("dist")
        .setResourceRoot("/system/templates/base/dist")
}

loadAssets();
