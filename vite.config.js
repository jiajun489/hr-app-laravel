import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0', // agar bisa diakses dari container Railway
        port: 5173,      // port default Vite (boleh diubah)
    },
    build: {
        outDir: 'public/build', // hasil build production ke folder public
        manifest: true,         // penting agar laravel-vite-plugin membaca file build
        emptyOutDir: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    base: '/build/', // agar semua path CSS/JS benar saat diakses melalui domain
});
