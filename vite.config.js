import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        // Use the ASSET_URL if defined, otherwise default to /public/build/
        // This handles cases where the domain points to the project root instead of the public folder
        base: env.ASSET_URL || '/public/build/',

        server: {
            // Enable CORS for development
            cors: true,
            // Ensure the dev server is accessible
            host: '0.0.0.0',
            hmr: {
                host: 'localhost',
            },
        },

        plugins: [
            laravel({
                input: [
                    'resources/sass/app.scss',
                    'resources/js/app.js',
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
                vue: 'vue/dist/vue.esm-bundler.js',
            },
        },

        css: {
            preprocessorOptions: {
                scss: {
                    quietDeps: true,
                    sassOptions: {
                        quiet: true,
                    },
                },
            },
        },

        build: {
            cssCodeSplit: true,
            rollupOptions: {
                output: {
                    // Organize assets neatly
                    assetFileNames: (assetInfo) => {
                        if (assetInfo.name && /\.(woff|woff2|eot|ttf|otf)$/.test(assetInfo.name)) {
                            return 'assets/fonts/[name]-[hash][extname]';
                        }
                        if (assetInfo.name && /\.(png|jpe?g|gif|svg|webp)$/.test(assetInfo.name)) {
                            return 'assets/images/[name]-[hash][extname]';
                        }
                        return 'assets/[name]-[hash][extname]';
                    },
                },
            },
        },
    };
});
