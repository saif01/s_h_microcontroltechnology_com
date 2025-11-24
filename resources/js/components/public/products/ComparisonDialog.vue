<template>
    <v-dialog :model-value="modelValue" max-width="1400" scrollable @update:model-value="$emit('update:modelValue', $event)">
        <v-card class="rounded-xl">
            <v-card-title class="d-flex align-center justify-space-between pa-6 bg-primary text-white">
                <div class="d-flex align-center">
                    <v-icon icon="mdi-scale-balance" class="mr-3" />
                    <span class="text-h5 font-weight-bold">Compare Products</span>
                </div>
                <v-btn
                    icon="mdi-close"
                    variant="text"
                    color="white"
                    @click="$emit('update:modelValue', false)"
                />
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Empty State -->
                <div v-if="products.length === 0" class="text-center py-16">
                    <v-icon icon="mdi-scale-balance" size="64" color="grey-lighten-1" class="mb-4" />
                    <p class="text-body-1 text-medium-emphasis">No products selected for comparison.</p>
                    <p class="text-caption text-medium-emphasis mt-2">Select up to 3 products to compare.</p>
                </div>

                <!-- Comparison Table -->
                <div v-else class="comparison-table">
                    <v-table density="comfortable" class="comparison-table-content">
                        <thead>
                            <tr>
                                <th class="comparison-header">Feature</th>
                                <th
                                    v-for="product in products"
                                    :key="product.id"
                                    class="comparison-product-header text-center"
                                >
                                    <div class="product-comparison-card">
                                        <v-btn
                                            icon="mdi-close"
                                            size="x-small"
                                            variant="text"
                                            class="position-absolute top-0 right-0"
                                            @click="$emit('remove-product', product)"
                                        />
                                        <v-img
                                            :src="getProductImage(product)"
                                            :alt="product.title"
                                            max-height="120"
                                            contain
                                            class="mb-3"
                                        />
                                        <h4 class="text-subtitle-1 font-weight-bold mb-2">
                                            {{ product.title }}
                                        </h4>
                                        <div class="text-h6 font-weight-black text-primary mb-2">
                                            {{ formatPrice(product) }}
                                        </div>
                                        <v-btn
                                            variant="outlined"
                                            color="primary"
                                            size="small"
                                            :to="`/products/${product.slug}`"
                                        >
                                            View Details
                                        </v-btn>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Category Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">Category</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    {{ getCategoryName(product) }}
                                </td>
                            </tr>

                            <!-- SKU Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">SKU</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    {{ product.sku || 'N/A' }}
                                </td>
                            </tr>

                            <!-- Price Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">Price</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    {{ formatPrice(product) }}
                                </td>
                            </tr>

                            <!-- Rating Row -->
                            <tr v-if="hasAnyRating">
                                <td class="font-weight-bold bg-grey-lighten-4">Rating</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div v-if="product.rating" class="d-flex align-center justify-center">
                                        <v-icon icon="mdi-star" color="amber" size="small" class="mr-1" />
                                        <span class="font-weight-bold">{{ product.rating }}</span>
                                    </div>
                                    <span v-else>N/A</span>
                                </td>
                            </tr>

                            <!-- Specification Rows -->
                            <tr v-for="spec in comparisonSpecs" :key="spec.key">
                                <td class="font-weight-bold bg-grey-lighten-4">{{ spec.label }}</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    {{ getSpecValue(product, spec.key) }}
                                </td>
                            </tr>

                            <!-- Key Features Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">Key Features</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div class="d-flex flex-column align-center gap-1">
                                        <v-chip
                                            v-for="(feature, idx) in getKeyFeatures(product).slice(0, 3)"
                                            :key="idx"
                                            size="x-small"
                                            variant="tonal"
                                            color="primary"
                                            class="mb-1"
                                        >
                                            {{ feature }}
                                        </v-chip>
                                    </div>
                                </td>
                            </tr>

                            <!-- Recommended Use Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">Recommended Use</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    {{ getRecommendedUse(product) }}
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </div>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-5">
                <v-spacer />
                <v-btn variant="text" @click="$emit('clear-all')">
                    Clear All
                </v-btn>
                <v-btn color="primary" variant="flat" @click="$emit('update:modelValue', false)">
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    products: {
        type: Array,
        default: () => []
    },
    comparisonSpecs: {
        type: Array,
        default: () => []
    }
});

defineEmits(['update:modelValue', 'remove-product', 'clear-all']);

// Helper functions
const getProductImage = (product) => {
    if (product.thumbnail) return product.thumbnail;
    if (product.images && product.images.length > 0) return product.images[0];
    return 'https://via.placeholder.com/400x300?text=Product';
};

const getCategoryName = (product) => {
    if (product.categories && product.categories.length > 0) {
        return product.categories[0].name;
    }
    return 'Uncategorized';
};

const formatPrice = (product) => {
    if (product.price_range) return product.price_range;
    if (product.price) return `$${parseFloat(product.price).toFixed(2)}`;
    return 'Contact for Price';
};

const getSpecValue = (product, key) => {
    if (product.specifications && product.specifications[key]) {
        return product.specifications[key];
    }
    return 'N/A';
};

const getKeyFeatures = (product) => {
    if (product.key_features && Array.isArray(product.key_features)) {
        return product.key_features;
    }
    return ['High Performance', 'Reliable Design', 'Energy Efficient'];
};

const getRecommendedUse = (product) => {
    if (product.recommended_use) return product.recommended_use;
    
    const categoryName = getCategoryName(product).toLowerCase();
    if (categoryName.includes('ups')) return 'Data Centers, Servers';
    if (categoryName.includes('battery')) return 'Backup Power Systems';
    if (categoryName.includes('solar')) return 'Renewable Energy Systems';
    return 'General Purpose';
};

const hasAnyRating = computed(() => {
    return props.products.some(p => p.rating);
});
</script>

<style scoped>
.comparison-table {
    overflow-x: auto;
}

.comparison-table-content {
    min-width: 800px;
}

.comparison-header {
    background: #f1f5f9 !important;
    font-weight: 700;
    padding: 16px !important;
    width: 200px;
    position: sticky;
    left: 0;
    z-index: 1;
}

.comparison-product-header {
    background: #f8fafc !important;
    padding: 16px !important;
    vertical-align: top;
}

.product-comparison-card {
    position: relative;
    padding: 16px;
    min-width: 250px;
}

tbody td:first-child {
    position: sticky;
    left: 0;
    z-index: 1;
    background: #f1f5f9;
}

tbody tr:nth-child(even) td:first-child {
    background: #f8fafc;
}
</style>

