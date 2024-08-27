// vite.config.js
import { resolve } from 'path'
import { defineConfig } from 'vite'
import { glob } from 'glob';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [vue()],
    // assetsInclude: ['node_modules/bootstrap-icons/icons/*.svg', 'node_modules/bootstrap-icons/font/*'],
    build: {
        minify: false,
        target: 'esnext',
        lib: 
        {// Could also be a dictionary or array of multiple entry points
            entry: [
                resolve(__dirname, 'src/js/app.ts'),
                resolve(__dirname, 'src/scss/app.scss'),
                ...glob.sync(resolve(__dirname, '../../../../../../../', 'system/modules/**/assets/ts/*.ts')),
                ...glob.sync(resolve(__dirname, '../../../../../../../', 'modules/**/assets/ts/*.ts')),
                ...glob.sync(resolve(__dirname, '../../../../../../../', 'modules/**/assets/scss/*.scss')),
            ],
            name: 'cmfive',
            format: 'es',
        },
        rollupOptions: {
            // external: ['vue'],
            output: {
                format: "es",
                entryFileNames: '[name].js',
                globals: {
                    vue: 'vue',
                },
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'style.css') return 'app.css';
                    return assetInfo.name;
                },
            }
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