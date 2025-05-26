import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
    },
    build: {
        outDir: 'public/build',
        manifest: true,
        emptyOutDir: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build' // PENTING agar Laravel baca di public/build
        }),
    ],
    base: '/build/',
});
