import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/admin.js',
                'resources/sass/admin.scss', // Admin entry point
                'resources/js/site.js',
                'resources/sass/site.scss', // Site entry point
                'resources/js/gallery.js', // Gallery page
                'resources/js/admin-dates.js', // Admin dates page
                'resources/js/admin-dates-datatables.js', // DataTables for dates
                'resources/js/admin-djs-datatables.js', // DataTables for DJs
            ],
            refresh: true,
        }),
    ],
});
