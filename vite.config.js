import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  // Configuración del servidor de desarrollo
  server: {
    host: true,        // --host 0.0.0.0
    watch: {
      // Ignora estas rutas para no saturar inotify
      ignored: [
        '**/vendor/**',
        '**/node_modules/**',
      ],
    },
  },

  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
});
