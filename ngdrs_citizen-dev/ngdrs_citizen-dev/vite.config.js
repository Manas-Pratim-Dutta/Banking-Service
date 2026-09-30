import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.ts',
                'resources/js/pages/appointment/main.ts',
                'resources/js/pages/edeed/main.ts',
                'resources/js/pages/identifier/main.ts',
                'resources/js/pages/party/main.ts',
                'resources/js/pages/property/main.ts',
                'resources/js/pages/generalinfo/main.ts',
                'resources/js/pages/witness/main.ts',
                'resources/js/pages/fee-calculation/main.ts',
                'resources/js/pages/upload/main.ts',
                'resources/js/welcome.ts',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
