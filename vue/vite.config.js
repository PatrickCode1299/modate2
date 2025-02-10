import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { nodePolyfills } from 'vite-plugin-node-polyfills';

export default defineConfig({
  plugins: [
    vue(),
    nodePolyfills({
      protocol: true, // Enable specific Node.js polyfills as needed
      timers: true,
      stream: true,
      buffer: true,
    }),
  ],
  optimizeDeps: {
    include: ['agora-rtc-sdk-ng'], // Ensure Agora SDK is included for optimization
  },
  server: {
    host: true, // Allow access from network
  },
  resolve: {
    alias: {
      '@': '/src', // Adjust based on your project structure
    },
  },
});