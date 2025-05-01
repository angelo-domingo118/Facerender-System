import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/fabric-test.js',
                'resources/js/editor-canvas.js',
                'resources/js/livewire-sortable.js'
            ],
            refresh: true,
        }),
    ],
});
