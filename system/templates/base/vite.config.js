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
        minify: "terser",
        target: 'es6',
        lib:
        {
            entry: _x,
            name: 'cmfive',
            format: 'es',
        },
        rollupOptions: {
            output: {
                format: "es",
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'style.css') return 'app.css';
                    return assetInfo.name;
                },
            },
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