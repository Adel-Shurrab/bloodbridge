import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/organization/theme.css'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
    },
});
