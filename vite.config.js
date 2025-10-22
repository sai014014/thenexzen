import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/css/booking-flow.css',
                'resources/css/bookings.css',
                'resources/css/business.css',
                'resources/css/components.css',
                'resources/css/super-admin-bugs.css',
                'resources/css/super-admin-businesses.css',
                'resources/css/super-admin-modern.css',
                'resources/css/super-admin.css',
                'resources/css/welcome.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
