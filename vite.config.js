import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],

    server: {
        host: '0.0.0.0', // Agar bisa diakses dari luar container
        watch: {
            usePolling: true, // Gunakan polling untuk file changes di Docker
        },
        hmr: {
            host: 'localhost', // Pastikan sesuai dengan host Anda
        },
    },
});
