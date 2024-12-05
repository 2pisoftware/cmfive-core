const mix = require("laravel-mix");
const path = require('path');

const { glb } = require('laravel-mix-glob');
const fs = require("fs");

// We need to determine the root directory of the project - since everything is symlinked this requires a bit of thought
let scriptPath = __dirname;
if (scriptPath.includes('cmfive-core')) {
    // System is symlinked outside of boilerplate
    scriptPath = scriptPath.split('cmfive-core')[0];
    if (fs.existsSync(scriptPath + 'cmfive-boilerplate')) {
        scriptPath += 'cmfive-boilerplate/';
    } else {
        throw new Error('Could not determine root directory of project');
    }
} else if (scriptPath.includes('cmfive-boilerplate')) {
    // System is symlinked inside of boilerplate
    scriptPath = scriptPath.split('cmfive-boilerplate')[0] + "cmfive-boilerplate/";
}

console.log("Script Path: " + scriptPath);

process.env.DEBUG = true;

mix.webpackConfig(webpack => {
    return {
        plugins: [
            new webpack.DefinePlugin({ __VUE_PROD_DEVTOOLS__: 'true', }),
        ]
    }
})

async function loadAssets() {
    mix.ts("src/js/app.ts", "")
        .vue()
        .sass("src/scss/app.scss", "")
        .setPublicPath("dist")
        .setResourceRoot("/system/templates/base/dist");

    // Compile vue components separately
    mix
        .ts(glb.src(scriptPath + 'modules/*/assets/ts/*.ts'), 'dist/', null, {
            base: function (file, ext, mm) {
                return 'dist/' + path.dirname(file).split(path.sep).reverse()[2] + '/';
            }
        })
        .sass(glb.src(scriptPath + 'modules/*/assets/scss/*.scss'), 'dist/', {
            sassOptions: {
                includePaths: [scriptPath + 'system/templates/base/src/scss/', scriptPath + 'system/templates/base/node_modules']
            }
        })
        .vue()
        .setPublicPath("dist")
        .setResourceRoot("/system/templates/base/dist")
}

loadAssets();