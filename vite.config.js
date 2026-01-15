import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import sass from 'sass-embedded';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/site.js',
                'resources/sass/site.scss',
                'resources/js/admin.js',
                'resources/sass/admin.scss',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                implementation: sass,
                quietDeps: true, // Suppresses warnings from dependencies
            },
        },
    },
});
