import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Font Plus Jakarta Sans dimuat dari Google Fonts di layout (app.blade.php), jadi tidak lewat Vite.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
