// vite.config.js
import { defineConfig } from "file:///C:/Users/user/Desktop/web/myapp/vue/node_modules/vite/dist/node/index.js";
import vue from "file:///C:/Users/user/Desktop/web/myapp/vue/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import { nodePolyfills } from "file:///C:/Users/user/Desktop/web/myapp/vue/node_modules/vite-plugin-node-polyfills/dist/index.js";
var vite_config_default = defineConfig({
  plugins: [
    vue(),
    nodePolyfills({
      protocol: true,
      // Enable specific Node.js polyfills as needed
      timers: true,
      stream: true,
      buffer: true
    })
  ],
  optimizeDeps: {
    include: ["agora-rtc-sdk-ng"]
    // Ensure Agora SDK is included for optimization
  },
  server: {
    host: true
    // Allow access from network
  },
  resolve: {
    alias: {
      "@": "/src"
      // Adjust based on your project structure
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFx1c2VyXFxcXERlc2t0b3BcXFxcd2ViXFxcXG15YXBwXFxcXHZ1ZVwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcdXNlclxcXFxEZXNrdG9wXFxcXHdlYlxcXFxteWFwcFxcXFx2dWVcXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL3VzZXIvRGVza3RvcC93ZWIvbXlhcHAvdnVlL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSc7XG5pbXBvcnQgeyBub2RlUG9seWZpbGxzIH0gZnJvbSAndml0ZS1wbHVnaW4tbm9kZS1wb2x5ZmlsbHMnO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICBwbHVnaW5zOiBbXG4gICAgdnVlKCksXG4gICAgbm9kZVBvbHlmaWxscyh7XG4gICAgICBwcm90b2NvbDogdHJ1ZSwgLy8gRW5hYmxlIHNwZWNpZmljIE5vZGUuanMgcG9seWZpbGxzIGFzIG5lZWRlZFxuICAgICAgdGltZXJzOiB0cnVlLFxuICAgICAgc3RyZWFtOiB0cnVlLFxuICAgICAgYnVmZmVyOiB0cnVlLFxuICAgIH0pLFxuICBdLFxuICBvcHRpbWl6ZURlcHM6IHtcbiAgICBpbmNsdWRlOiBbJ2Fnb3JhLXJ0Yy1zZGstbmcnXSwgLy8gRW5zdXJlIEFnb3JhIFNESyBpcyBpbmNsdWRlZCBmb3Igb3B0aW1pemF0aW9uXG4gIH0sXG4gIHNlcnZlcjoge1xuICAgIGhvc3Q6IHRydWUsIC8vIEFsbG93IGFjY2VzcyBmcm9tIG5ldHdvcmtcbiAgfSxcbiAgcmVzb2x2ZToge1xuICAgIGFsaWFzOiB7XG4gICAgICAnQCc6ICcvc3JjJywgLy8gQWRqdXN0IGJhc2VkIG9uIHlvdXIgcHJvamVjdCBzdHJ1Y3R1cmVcbiAgICB9LFxuICB9LFxufSk7Il0sCiAgIm1hcHBpbmdzIjogIjtBQUF5UyxTQUFTLG9CQUFvQjtBQUN0VSxPQUFPLFNBQVM7QUFDaEIsU0FBUyxxQkFBcUI7QUFFOUIsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDMUIsU0FBUztBQUFBLElBQ1AsSUFBSTtBQUFBLElBQ0osY0FBYztBQUFBLE1BQ1osVUFBVTtBQUFBO0FBQUEsTUFDVixRQUFRO0FBQUEsTUFDUixRQUFRO0FBQUEsTUFDUixRQUFRO0FBQUEsSUFDVixDQUFDO0FBQUEsRUFDSDtBQUFBLEVBQ0EsY0FBYztBQUFBLElBQ1osU0FBUyxDQUFDLGtCQUFrQjtBQUFBO0FBQUEsRUFDOUI7QUFBQSxFQUNBLFFBQVE7QUFBQSxJQUNOLE1BQU07QUFBQTtBQUFBLEVBQ1I7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNQLE9BQU87QUFBQSxNQUNMLEtBQUs7QUFBQTtBQUFBLElBQ1A7QUFBQSxFQUNGO0FBQ0YsQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
