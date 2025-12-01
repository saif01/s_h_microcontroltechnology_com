<template>
    <v-dialog :model-value="modelValue" max-width="1400" scrollable
        @update:model-value="$emit('update:modelValue', $event)">
        <v-card class="rounded-xl">
            <v-card-title class="d-flex align-center justify-space-between pa-6 bg-primary text-white">
                <div class="d-flex align-center">
                    <v-icon icon="mdi-scale-balance" class="mr-3" />
                    <span class="text-h5 font-weight-bold">Compare Products</span>
                </div>
                <v-btn icon="mdi-close" variant="text" color="white" @click="$emit('update:modelValue', false)" />
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
                                <th v-for="product in products" :key="product.id"
                                    class="comparison-product-header text-center">
                                    <div class="product-comparison-card">
                                        <v-btn icon="mdi-close" size="x-small" variant="text"
                                            class="position-absolute top-0 right-0"
                                            @click="$emit('remove-product', product)" />
                                        <v-img :src="getProductImage(product)" :alt="product.title" max-height="120"
                                            contain class="mb-3" />
                                        <h4 class="text-subtitle-1 font-weight-bold mb-2">
                                            {{ product.title }}
                                        </h4>
                                        <div class="text-h6 font-weight-black text-primary mb-2">
                                            {{ formatPrice(product) }}
                                        </div>
                                        <v-btn variant="outlined" color="primary" size="small"
                                            :to="`/products/${product.slug}`">
                                            View Details
                                        </v-btn>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Section: Basic Information -->
                            <tr class="section-header">
                                <td :colspan="products.length + 1"
                                    class="font-weight-bold text-h6 bg-primary text-white pa-3">
                                    Basic Information
                                </td>
                            </tr>

                            <!-- Category Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">Category</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <v-chip size="small" variant="tonal" color="primary">
                                        {{ getCategoryName(product) }}
                                    </v-chip>
                                </td>
                            </tr>

                            <!-- SKU Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">SKU</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <span class="font-weight-medium">{{ product.sku || 'N/A' }}</span>
                                </td>
                            </tr>

                            <!-- Model Row -->
                            <tr v-if="hasAnyModel">
                                <td class="font-weight-bold bg-grey-lighten-4">Model</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <span class="font-weight-medium">{{ getModel(product) }}</span>
                                </td>
                            </tr>

                            <!-- Price Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">Price</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <span class="text-h6 font-weight-black text-primary">{{ formatPrice(product)
                                    }}</span>
                                </td>
                            </tr>

                            <!-- Stock Row -->
                            <tr v-if="hasAnyStock">
                                <td class="font-weight-bold bg-grey-lighten-4">Stock Status</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <v-chip v-if="product.stock !== null && product.stock !== undefined"
                                        :color="product.stock > 0 ? 'success' : 'error'" size="small" variant="flat">
                                        {{ product.stock > 0 ? `In Stock (${product.stock})` : 'Out of Stock' }}
                                    </v-chip>
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- Featured Row -->
                            <tr v-if="hasAnyFeatured">
                                <td class="font-weight-bold bg-grey-lighten-4">Featured</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <v-chip v-if="product.featured" color="amber" size="small" variant="flat">
                                        <v-icon icon="mdi-star" size="x-small" class="mr-1" />
                                        Featured
                                    </v-chip>
                                    <span v-else class="text-medium-emphasis">No</span>
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
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- Section: Description -->
                            <tr v-if="hasAnyDescription" class="section-header">
                                <td :colspan="products.length + 1"
                                    class="font-weight-bold text-h6 bg-primary text-white pa-3">
                                    Description
                                </td>
                            </tr>

                            <!-- Short Description Row -->
                            <tr v-if="hasAnyDescription">
                                <td class="font-weight-bold bg-grey-lighten-4">Short Description</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <p v-if="product.short_description" class="text-body-2 text-left pa-2">
                                        {{ product.short_description }}
                                    </p>
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- Section: Key Features -->
                            <tr v-if="hasAnyKeyFeatures" class="section-header">
                                <td :colspan="products.length + 1"
                                    class="font-weight-bold text-h6 bg-primary text-white pa-3">
                                    Key Features
                                </td>
                            </tr>

                            <!-- Key Features Row -->
                            <tr v-if="hasAnyKeyFeatures">
                                <td class="font-weight-bold bg-grey-lighten-4">Features</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div v-if="getKeyFeatures(product).length"
                                        class="d-flex flex-column align-center gap-1 pa-2">
                                        <v-chip v-for="(feature, idx) in getKeyFeatures(product)" :key="idx"
                                            size="small" variant="tonal" color="primary" class="mb-1">
                                            {{ feature }}
                                        </v-chip>
                                    </div>
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- Section: Technical Specifications -->
                            <tr v-if="specRows.length > 0" class="section-header">
                                <td :colspan="products.length + 1"
                                    class="font-weight-bold text-h6 bg-primary text-white pa-3">
                                    Technical Specifications
                                </td>
                            </tr>

                            <!-- Specification Rows -->
                            <tr v-for="spec in specRows" :key="spec.key">
                                <td class="font-weight-bold bg-grey-lighten-4">{{ spec.label }}</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div class="pa-2">
                                        <span class="text-body-2">{{ getSpecValue(product, spec.key) }}</span>
                                    </div>
                                </td>
                            </tr>

                            <!-- Section: Additional Information -->
                            <tr class="section-header">
                                <td :colspan="products.length + 1"
                                    class="font-weight-bold text-h6 bg-primary text-white pa-3">
                                    Additional Information
                                </td>
                            </tr>

                            <!-- Tags Row -->
                            <tr v-if="hasAnyTags">
                                <td class="font-weight-bold bg-grey-lighten-4">Tags</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div v-if="getTags(product).length"
                                        class="d-flex flex-wrap justify-center gap-1 pa-2">
                                        <v-chip v-for="(tag, idx) in getTags(product)" :key="idx" size="x-small"
                                            variant="outlined" color="secondary">
                                            {{ tag }}
                                        </v-chip>
                                    </div>
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- Downloads Row -->
                            <tr v-if="hasAnyDownloads">
                                <td class="font-weight-bold bg-grey-lighten-4">Downloads</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div v-if="getDownloads(product).length"
                                        class="d-flex flex-column align-center gap-1 pa-2">
                                        <v-chip v-for="(download, idx) in getDownloads(product)" :key="idx" size="small"
                                            variant="outlined" color="info" class="mb-1">
                                            <v-icon icon="mdi-download" size="small" class="mr-1" />
                                            {{ download.name || `Download ${idx + 1}` }}
                                        </v-chip>
                                    </div>
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- FAQs Row -->
                            <tr v-if="hasAnyFAQs">
                                <td class="font-weight-bold bg-grey-lighten-4">FAQs</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div v-if="getFAQs(product).length"
                                        class="d-flex flex-column align-start gap-2 pa-2">
                                        <div v-for="(faq, idx) in getFAQs(product)" :key="idx" class="text-left w-100">
                                            <div class="font-weight-bold text-body-2 mb-1">
                                                Q{{ idx + 1 }}: {{ faq.question || 'Question' }}
                                            </div>
                                            <div class="text-body-2 text-medium-emphasis">
                                                A: {{ faq.answer || 'Answer' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- Warranty Info Row -->
                            <tr v-if="hasAnyWarranty">
                                <td class="font-weight-bold bg-grey-lighten-4">Warranty Information</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <div v-if="getWarrantyInfo(product)" class="text-left pa-2">
                                        <div v-for="(value, key) in getWarrantyInfo(product)" :key="key"
                                            class="mb-1 text-body-2">
                                            <span class="font-weight-medium">{{ toLabel(key) }}:</span>
                                            <span class="ml-1">{{ formatSpecValue(value) }}</span>
                                        </div>
                                    </div>
                                    <span v-else class="text-medium-emphasis">N/A</span>
                                </td>
                            </tr>

                            <!-- Recommended Use Row -->
                            <tr>
                                <td class="font-weight-bold bg-grey-lighten-4">Recommended Use</td>
                                <td v-for="product in products" :key="product.id" class="text-center">
                                    <span class="text-body-2">{{ getRecommendedUse(product) }}</span>
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
import { formatProductPrice } from '../../../utils/formatters';

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
    return '/assets/img/default.jpg';
};

const getCategoryName = (product) => {
    if (product.categories && product.categories.length > 0) {
        return product.categories[0].name;
    }
    return 'Uncategorized';
};

const formatPrice = (product) => {
    return formatProductPrice(product, 'Contact for Price');
};

const toLabel = (text = '') => {
    return text
        .toString()
        .replace(/_/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ')
        .trim();
};

const flattenSpecifications = (specs, parentKey = '', parentLabel = '') => {
    const rows = [];
    if (!specs || typeof specs !== 'object') return rows;

    Object.entries(specs).forEach(([key, value]) => {
        const keyPath = parentKey ? `${parentKey}.${key}` : key;
        const label = parentLabel ? `${parentLabel} > ${toLabel(key)}` : toLabel(key);

        if (value && typeof value === 'object' && !Array.isArray(value)) {
            rows.push(...flattenSpecifications(value, keyPath, label));
        } else {
            rows.push({ key: keyPath, label });
        }
    });

    return rows;
};

const specRows = computed(() => {
    const rowsMap = new Map();
    const excludedKeys = ['_key_features', '_faqs', '_warranty_info'];

    // Include any provided comparison specs first
    (props.comparisonSpecs || []).forEach(spec => {
        if (spec?.key && !excludedKeys.includes(spec.key)) {
            rowsMap.set(spec.key, spec.label || toLabel(spec.key));
        }
    });

    // Flatten all product specifications to capture nested details
    props.products.forEach(product => {
        if (product?.specifications) {
            flattenSpecifications(product.specifications).forEach(({ key, label }) => {
                // Exclude special fields that are handled separately
                if (!excludedKeys.some(excluded => key === excluded || key.startsWith(excluded + '.'))) {
                    if (!rowsMap.has(key)) {
                        rowsMap.set(key, label);
                    }
                }
            });
        }
    });

    return Array.from(rowsMap.entries()).map(([key, label]) => ({ key, label }));
});

const formatSpecValue = (value) => {
    if (value === null || value === undefined || value === '') return 'N/A';

    if (Array.isArray(value)) {
        if (value.length === 0) return 'N/A';
        const hasObjects = value.some(item => typeof item === 'object');
        if (hasObjects) {
            return value
                .map((item, idx) => {
                    if (item && typeof item === 'object') {
                        return Object.entries(item)
                            .map(([k, v]) => `${toLabel(k)}: ${formatSpecValue(v)}`)
                            .join('; ');
                    }
                    return `${idx + 1}. ${formatSpecValue(item)}`;
                })
                .join(' | ');
        }
        return value.join(', ');
    }

    if (typeof value === 'object') {
        return Object.entries(value)
            .map(([k, v]) => `${toLabel(k)}: ${formatSpecValue(v)}`)
            .join(' | ');
    }

    return value;
};

const getSpecValue = (product, keyPath) => {
    if (!product?.specifications) return 'N/A';
    const segments = keyPath.split('.');
    let value = product.specifications;

    for (const segment of segments) {
        if (value && Object.prototype.hasOwnProperty.call(value, segment)) {
            value = value[segment];
        } else {
            return 'N/A';
        }
    }

    return formatSpecValue(value);
};

const getKeyFeatures = (product) => {
    if (product.key_features && Array.isArray(product.key_features)) {
        return product.key_features;
    }
    // Also check in specifications
    if (product.specifications?._key_features && Array.isArray(product.specifications._key_features)) {
        return product.specifications._key_features;
    }
    return [];
};

const getRecommendedUse = (product) => {
    if (product.recommended_use) return product.recommended_use;

    const categoryName = getCategoryName(product).toLowerCase();
    if (categoryName.includes('ups')) return 'Data Centers, Servers';
    if (categoryName.includes('battery')) return 'Backup Power Systems';
    if (categoryName.includes('solar')) return 'Renewable Energy Systems';
    return 'General Purpose';
};

const getModel = (product) => {
    if (product.specifications?.Model) {
        return product.specifications.Model;
    }
    return 'N/A';
};

const getTags = (product) => {
    if (product.tags && Array.isArray(product.tags)) {
        return product.tags.map(tag => tag.name || tag);
    }
    return [];
};

const getDownloads = (product) => {
    if (product.downloads && Array.isArray(product.downloads)) {
        return product.downloads;
    }
    return [];
};

const getFAQs = (product) => {
    if (product.specifications?._faqs && Array.isArray(product.specifications._faqs)) {
        return product.specifications._faqs;
    }
    return [];
};

const getWarrantyInfo = (product) => {
    if (product.specifications?._warranty_info && typeof product.specifications._warranty_info === 'object') {
        return product.specifications._warranty_info;
    }
    return null;
};

// Computed properties for conditional rendering
const hasAnyRating = computed(() => {
    return props.products.some(p => p.rating);
});

const hasAnyModel = computed(() => {
    return props.products.some(p => p.specifications?.Model);
});

const hasAnyStock = computed(() => {
    return props.products.some(p => p.stock !== null && p.stock !== undefined);
});

const hasAnyFeatured = computed(() => {
    return props.products.some(p => p.featured);
});

const hasAnyDescription = computed(() => {
    return props.products.some(p => p.short_description || p.description);
});

const hasAnyKeyFeatures = computed(() => {
    return props.products.some(p => getKeyFeatures(p).length > 0);
});

const hasAnyTags = computed(() => {
    return props.products.some(p => getTags(p).length > 0);
});

const hasAnyDownloads = computed(() => {
    return props.products.some(p => getDownloads(p).length > 0);
});

const hasAnyFAQs = computed(() => {
    return props.products.some(p => getFAQs(p).length > 0);
});

const hasAnyWarranty = computed(() => {
    return props.products.some(p => getWarrantyInfo(p) !== null);
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

.section-header td {
    position: sticky;
    left: 0;
    z-index: 2;
}

tbody td {
    vertical-align: top;
    padding: 12px 16px !important;
}

tbody td:not(:first-child) {
    min-width: 250px;
    max-width: 350px;
}

.comparison-product-header {
    min-width: 280px;
}

/* Responsive Styles */
@media (max-width: 960px) {
    .comparison-table-content {
        min-width: 600px;
    }

    .comparison-header {
        width: 150px;
        padding: 12px !important;
        font-size: 0.875rem;
    }

    .comparison-product-header {
        padding: 12px !important;
        min-width: 200px;
    }

    .product-comparison-card {
        min-width: 200px;
        padding: 12px;
    }
}

@media (max-width: 600px) {
    .comparison-table-content {
        min-width: 500px;
    }

    .comparison-header {
        width: 120px;
        padding: 8px !important;
        font-size: 0.8125rem;
    }

    .comparison-product-header {
        padding: 8px !important;
        min-width: 150px;
    }

    .product-comparison-card {
        min-width: 150px;
        padding: 8px;
    }

    .product-comparison-card .v-img {
        height: 100px !important;
    }

    .product-comparison-card h4 {
        font-size: 0.875rem !important;
    }

    .product-comparison-card .text-body-2 {
        font-size: 0.75rem !important;
    }
}
</style>
