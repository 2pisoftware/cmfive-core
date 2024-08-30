// vite.config.js
import { resolve } from 'path'
import { defineConfig } from 'vite'
import { glob } from 'glob';
import vue from '@vitejs/plugin-vue';

const _x = [
    resolve(__dirname, 'src/js/app.ts'),
    // resolve(__dirname, 'src/scss/app.scss'),
    ...glob.sync(resolve(__dirname, '../../', 'modules/**/assets/ts/*.ts')),
    // ...glob.sync(resolve(__dirname, '../../../../../../../', 'system/modules/**/assets/scss/*.scss')),
    ...glob.sync(resolve(__dirname, '../../../../../../../', 'modules/**/assets/ts/*.ts')),
    // ...glob.sync(resolve(__dirname, '../../../../../../../', 'modules/**/assets/scss/*.scss')),
];

let _fileMapObj = {};
_x.forEach((file) => {
    const name = file.split('/').pop().split('.').shift();
    const ext = file.split('.').pop();
    _fileMapObj[`${name}`] = file;
});

console.log("fileMap", _fileMapObj);

export default defineConfig({
    plugins: [vue()],
    build: {
        minify: false,
        target: 'es6',
        lib:
        {// Could also be a dictionary or array of multiple entry points
            entry: _x,
            name: 'cmfive',
            format: 'es',
        },
        rollupOptions: {
            // input: _fileMapObj,
            output: {
                // inlineDynamicImports: true,
                format: "es",
                // entryFileNames: '[name].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'style.css') return 'app.css';
                    return assetInfo.name;
                },
            },
            // plugins: [
            //     {
            //         name: 'wrap-in-iife',
            //         generateBundle(outputOptions, bundle) {
            //             Object.keys(bundle).forEach((fileName) => {
            //                 const file = bundle[fileName]
            //                 if (fileName.slice(-3) === '.js' && 'code' in file) {
            //                     file.code = `(() => {\n${file.code}})()`
            //                 }
            //             })
            //         }
            //     }
            // ]
        },
    },
    define: {
        'process.env': {}
    },
    resolve: {
        alias: {
            '~': resolve(__dirname, 'node_modules'),
            '@': resolve(__dirname, 'src'),
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    }
})