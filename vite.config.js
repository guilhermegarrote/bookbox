import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fg from 'fast-glob';

const jsApi = fg.sync('resources/js/api/**/*.js');
const jsComponents = fg.sync('resources/js/components/**/*.js');
const jsPages = fg.sync('resources/js/pages/**/*.js');

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...jsApi,
                ...jsComponents,
                ...jsPages,
            ],
            refresh: true,
        }),
    ],
});
