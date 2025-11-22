import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// Plugin to make font URLs in CSS relative
const relativeFontUrlsPlugin = () => {
    return {
        name: 'relative-font-urls',
        generateBundle(options, bundle) {
            Object.keys(bundle).forEach((fileName) => {
                const chunk = bundle[fileName];
                if (chunk.type === 'asset' && chunk.fileName.endsWith('.css')) {
                    // Replace absolute URLs with relative paths for fonts
                    // Handles URLs with or without quotes, and with query parameters
                    // Match: url(https://domain/build/assets/filename.ext?params)
                    // Use a simpler pattern that matches absolute URLs and replaces them
                    // Fixed regex: removed closing parenthesis from character class to avoid parsing issues
                    chunk.source = chunk.source.replace(
                        /url\((['"]?)(https?:\/\/[^'"]+\/build\/assets\/([^'"?]+\.(woff2?|eot|ttf|otf)))([^'"]*)\1\)/gi,
                        (match, quote, fullUrl, filename) => {
                            // Extract just the filename without query parameters
                            const cleanFilename = filename.split('?')[0];
                            const quoteChar = quote || '';
                            return `url(${quoteChar}./${cleanFilename}${quoteChar})`;
                        }
                    );
                }
            });
        },
    };
};

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    const normalizeBase = (value) => {
        if (!value || value === '/') {
            return '/';
        }

        // For shared hosting, prevent absolute URLs in ASSET_URL
        // This ensures font URLs in CSS are relative
        if (/^https?:\/\//i.test(value)) {
            console.warn('Warning: ASSET_URL should be relative for shared hosting (e.g., /s_h) not an absolute URL');
        }

        if (/^https?:\/\//i.test(value)) {
            return value.endsWith('/') ? value : `${value}/`;
        }

        const trimmed = value.replace(/^\/+|\/+$/g, '');
        return `/${trimmed}/`;
    };

    const withBuild = (value) => {
        const normalized = normalizeBase(value);
        return normalized.endsWith('build/') ? normalized : `${normalized}build/`;
    };

    // For production builds, use relative base to avoid CORS issues
    const base = mode === 'production' && !env.ASSET_URL
        ? '/build/'
        : withBuild(env.ASSET_URL || '/');

    return {
        base,
        plugins: [
            relativeFontUrlsPlugin(),
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
                // compilerOptions: {
                //     compatConfig: {
                //         MODE: 2
                //     },
                //     isCustomElement: tag => tag === 'lottie-player'
                // },
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
                    // Disable deprecation warnings
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
                    assetFileNames: (assetInfo) => {
                        // Keep font files with relative paths
                        if (assetInfo.name && /\.(woff|woff2|eot|ttf|otf)$/.test(assetInfo.name)) {
                            return 'assets/[name]-[hash][extname]';
                        }
                        return 'assets/[name]-[hash][extname]';
                    },
                },
            },
            // Ensure relative URLs in CSS for fonts
            assetsInlineLimit: 0,
        },
    };
});
