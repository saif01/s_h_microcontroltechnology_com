<template>
    <section class="py-20 bg-surface">
        <v-container>
            <div class="d-flex flex-column flex-sm-row justify-space-between align-end mb-12">
                <div>
                    <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">{{ overline ||
                        defaultOverline }}</div>
                    <h2 class="text-h3 font-weight-bold text-grey-darken-3">{{ title || defaultTitle }}</h2>
                </div>
                <v-btn variant="outlined" color="primary" :to="buttonLink || defaultButtonLink"
                    class="mt-4 mt-sm-0 rounded-pill px-6 font-weight-bold">
                    {{ buttonText || defaultButtonText }}
                </v-btn>
            </div>

            <v-row v-if="limitedProducts.length > 0">
                <v-col v-for="product in limitedProducts" :key="product.id" cols="12" md="4">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 12 : 0"
                            class="h-100 product-card-modern bg-white rounded-xl overflow-hidden border-thin"
                            :to="`/products/${product.slug}`">
                            <div class="img-wrapper overflow-hidden position-relative" style="height: 260px;">
                                <v-img :src="getProductImage(product)"
                                    height="100%" cover class="product-img transition-transform"
                                    :class="{ 'scale-110': isHovering }">
                                    <template v-slot:placeholder>
                                        <div class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                            <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                        </div>
                                    </template>
                                </v-img>
                                <div class="overlay-actions d-flex align-center justify-center">
                                    <v-btn icon="mdi-eye" color="white" variant="flat" class="mr-2"></v-btn>
                                    <v-btn icon="mdi-cart" color="primary" variant="flat"></v-btn>
                                </div>
                            </div>
                            <v-card-item class="pt-6 px-6">
                                <div class="text-caption text-primary font-weight-bold mb-1 text-uppercase">Category
                                </div>
                                <v-card-title class="text-h6 font-weight-bold mb-2 text-grey-darken-3 px-0">
                                    {{ product.title }}
                                </v-card-title>
                                <v-card-text class="text-body-2 text-medium-emphasis px-0 text-truncate-2">
                                    {{ product.short_description }}
                                </v-card-text>
                            </v-card-item>
                            <v-card-actions class="px-6 pb-6 pt-0">
                                <div class="font-weight-bold text-h6 text-primary">$99.00</div>
                                <v-spacer></v-spacer>
                                <span class="text-caption text-medium-emphasis">Details &rarr;</span>
                            </v-card-actions>
                        </v-card>
                    </v-hover>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script>
import { resolveUploadUrl } from '../../../../utils/uploads';

export default {
    name: 'FeaturedProductsSection',
    props: {
        overline: {
            type: String,
            default: 'OUR PRODUCTS'
        },
        title: {
            type: String,
            default: 'Featured Power Systems'
        },
        buttonText: {
            type: String,
            default: 'View All Products'
        },
        buttonLink: {
            type: String,
            default: '/products'
        },
        products: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            defaultOverline: 'OUR PRODUCTS',
            defaultTitle: 'Featured Power Systems',
            defaultButtonText: 'View All Products',
            defaultButtonLink: '/products'
        };
    },
    computed: {
        limitedProducts() {
            return this.products.slice(0, 3);
        }
    },
    methods: {
        getProductImage(product) {
            if (product.thumbnail) return resolveUploadUrl(product.thumbnail);
            if (product.images && product.images.length > 0) return resolveUploadUrl(product.images[0]);
            return 'https://via.placeholder.com/400x300?text=Product';
        }
    }
};
</script>

<style scoped>
.product-card-modern .overlay-actions {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card-modern:hover .overlay-actions {
    opacity: 1;
}

.scale-110 {
    transform: scale(1.1);
}

@media (max-width: 600px) {
    .product-card-modern {
        margin-bottom: 16px;
    }
}
</style>
