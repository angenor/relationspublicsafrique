import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/css/app.css",
                "resources/css2/app.css",
                "resources/js/app.js",
                "resources/js2/app.js",
                "resources/css/media.css",
                "resources/js/media.js",
                "resources/css/projets.css",
                "resources/js/projets.js",
                "resources/css/events.css",
                "resources/js/events.js",
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },
});
