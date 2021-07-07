const mix = require("laravel-mix");
const fs = require("fs");
// const path = require("path");

// async function loadAssets() {
    mix
        .ts("src/js/app.ts", "")
        .vue()
        .sass("src/scss/app.scss", "")
        .setPublicPath("dist")
        .setResourceRoot("/system/templates/base/dist");

    // Dynamically compile module ts/scss assets
    const BASE_PATH = "../../../../../../../modules/";
    const parent_dir = fs.opendirSync(BASE_PATH);
    let module_dir_ent;
    while ((module_dir_ent = parent_dir.readSync()) !== null) {
        if (module_dir_ent.isDirectory && fs.existsSync(BASE_PATH + module_dir_ent.name + '/assets/') && fs.lstatSync(BASE_PATH + module_dir_ent.name + '/assets/').isDirectory()) {
            if (fs.existsSync(BASE_PATH + module_dir_ent.name + '/assets/ts') && fs.lstatSync(BASE_PATH + module_dir_ent.name + '/assets/ts').isDirectory()) {
                const ts_asset_dir = fs.opendirSync(BASE_PATH + module_dir_ent.name + '/assets/ts');
                let ts_asset_dir_ent;
                while ((ts_asset_dir_ent = ts_asset_dir.readSync()) !== null) {
                    if (ts_asset_dir_ent.name.split('.').pop() == 'ts') {
                        console.log('Compiling', ts_asset_dir_ent.name)
                        mix.ts(BASE_PATH + module_dir_ent.name + '/assets/ts/' + ts_asset_dir_ent.name, '').vue()
                            .setPublicPath('dist')
                            .setResourceRoot('/system/templates/base/dist/' + module_dir_ent.name + '/');
                    }
                }
                ts_asset_dir.closeSync();
            }

            if (fs.existsSync(BASE_PATH + module_dir_ent.name + '/assets/scss') && fs.lstatSync(BASE_PATH + module_dir_ent.name + '/assets/scss').isDirectory()) {
                const scss_asset_dir = fs.opendirSync(BASE_PATH + module_dir_ent.name + '/assets/ts');
                let scss_asset_dir_ent;
                while ((scss_asset_dir_ent = scss_asset_dir.readSync()) !== null) {
                    if (scss_asset_dir_ent.name.split('.').pop() == 'scss') {
                        console.log('Compiling', scss_asset_dir_ent.name)
                        mix.sass(BASE_PATH + module_dir_ent.name + '/assets/scss/' + scss_asset_dir_ent.name, '', {
                            sassOptions: {
                                includePaths: ['/system/templates/base/src/scss/']
                            }
                        })
                        .setPublicPath('dist')
                        .setResourceRoot('/system/templates/base/dist/' + module_dir_ent.name + '/');
                    }
                }
                scss_asset_dir.closeSync()
            }
        }
    }
    parent_dir.closeSync();
// }

// loadAssets();
// mix
//     .ts('../../../../../../../modules/**/assets/ts/*', 'dist/').vue()
//     .sass('../../../../../../../modules/bridge/assets/scss/app.scss', '', {
//         sassOptions: {
//             includePaths: ['/system/templates/base/src/scss']
//         }
//     })
//     .setPublicPath('dist')
//     .setResourceRoot('/system/templates/base/dist');
