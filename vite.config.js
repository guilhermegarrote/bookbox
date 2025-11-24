import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fg from 'fast-glob';
import path from 'path';

const jsApi = fg.sync('resources/js/api/**/*.js');
const jsComponents = fg.sync('resources/js/components/**/*.js');
const jsPages = fg.sync('resources/js/pages/**/*.js');

const cssEmails = 'resources/css/emails/style.css';

export default defineConfig({
    resolve: {
        alias: {
            '@css': path.resolve(__dirname, 'resources/css'),
            '@js': path.resolve(__dirname, 'resources/js'),
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...jsApi,
                ...jsComponents,
                ...jsPages,
                cssEmails,
            ],
            refresh: true,
        }),
    ],
});
