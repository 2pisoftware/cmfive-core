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
        .ts('../../../../../../../modules/*/assets/ts/*', 'dist/', null, {
            base: function (file, ext, mm) { // mm => micromatch instance
                return 'dist/' + path.dirname(file).split(path.sep).reverse()[2] + '/';
            },
            mapping: {   // this take precedency over any other mapping // useless feature as laravel-mix doesn't support output in different formats. (until i find a workaround)
                ext: {
                    'scss': 'css'
                },
            }
        })
        .sass('../../../../../../../modules/*/assets/scss/*', 'dist/', {
            sassOptions: {
                includePaths: [__dirname + '/src/scss/']
            }
        },
        {
            mapping: {   // this take precedency over any other mapping // useless feature as laravel-mix doesn't support output in different formats. (until i find a workaround)
                ext: {
                    'scss': 'css'
                },
            }
        })
        .vue()
        .setPublicPath("dist")
        .setResourceRoot("/system/templates/base/dist")

    // mixGlob.sass('../../../../../../../modules/*/assets/scss/*', 'dist/', {
    //     sassOptions: {
    //         includePaths: [__dirname + '/src/scss/']
    //     }
    // },
    // {
    //     mapping: {   // this take precedency over any other mapping // useless feature as laravel-mix doesn't support output in different formats. (until i find a workaround)
    //         ext: {
    //             'scss': 'css'
    //         },
    //     }
    // })
    // .setPublicPath("dist")
    // .setResourceRoot("/system/templates/base/dist");

    // mixGlob
        
    //     .vue()
    //     .setPublicPath('dist')
    //     .setResourceRoot('/system/templates/base/dist/');

    // Dynamically compile module ts/scss assets
    // const BASE_PATH = "../../../../../../../modules/";
    // const parent_dir = fs.opendirSync(BASE_PATH);
    // let module_dir_ent;
    // while ((module_dir_ent = parent_dir.readSync()) !== null) {
    //     if (module_dir_ent.isDirectory && fs.existsSync(BASE_PATH + module_dir_ent.name + '/assets/') && fs.lstatSync(BASE_PATH + module_dir_ent.name + '/assets/').isDirectory()) {
    //         if (fs.existsSync(BASE_PATH + module_dir_ent.name + '/assets/ts') && fs.lstatSync(BASE_PATH + module_dir_ent.name + '/assets/ts').isDirectory()) {
    //             const ts_asset_dir = fs.opendirSync(BASE_PATH + module_dir_ent.name + '/assets/ts');
    //             let ts_asset_dir_ent;
    //             while ((ts_asset_dir_ent = ts_asset_dir.readSync()) !== null) {
    //                 if (ts_asset_dir_ent.name.split('.').pop() == 'ts' || ts_asset_dir_ent.name.split('.').pop() == 'vue') {
    //                     console.log('Compiling', ts_asset_dir_ent.name)
    //                     mix.ts(BASE_PATH + module_dir_ent.name + '/assets/ts/' + ts_asset_dir_ent.name, 'dist/' + module_dir_ent.name)
    //                         .vue()
    //                         .setPublicPath('dist')
    //                         .setResourceRoot('/system/templates/base/dist/');
    //                 }
    //             }
    //             ts_asset_dir.closeSync();
    //         }

    //         if (fs.existsSync(BASE_PATH + module_dir_ent.name + '/assets/scss') && fs.lstatSync(BASE_PATH + module_dir_ent.name + '/assets/scss').isDirectory()) {
    //             const scss_asset_dir = fs.opendirSync(BASE_PATH + module_dir_ent.name + '/assets/scss');
    //             let scss_asset_dir_ent;
    //             while ((scss_asset_dir_ent = scss_asset_dir.readSync()) !== null) {
    //                 console.log('Found sass file', scss_asset_dir_ent.name)
    //                 if (scss_asset_dir_ent.name.split('.').pop() == 'scss') {
    //                     mix.sass(BASE_PATH + module_dir_ent.name + '/assets/scss/' + scss_asset_dir_ent.name, 'dist/' + module_dir_ent.name, {
    //                         sassOptions: {
    //                             includePaths: [__dirname + '/src/scss/']
    //                         }
    //                     })
    //                     .setPublicPath('dist/')
    //                     .setResourceRoot('/system/templates/base/dist/');
    //                 }
    //             }
    //             scss_asset_dir.closeSync()
    //         }
    //     }
    // }
    // parent_dir.closeSync();
}

loadAssets();
