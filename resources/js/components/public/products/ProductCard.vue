<template>
    <v-hover v-slot="{ isHovering, props: hoverProps }">
        <v-card v-bind="hoverProps" :elevation="isHovering ? 8 : 2"
            class="product-card h-100 bg-white rounded-xl overflow-hidden transition-all">
            <!-- Product Image -->
            <div class="product-image-container position-relative bg-grey-lighten-4"
                style="height: 240px; overflow: hidden;">
                <!-- Badges Container (Top Left) -->
                <div class="position-absolute top-0 left-0 ma-3 z-index-2 d-flex flex-column gap-2">
                    <v-chip v-if="product.featured" color="amber-accent-4" variant="flat" size="x-small"
                        class="font-weight-bold">
                        FEATURED
                    </v-chip>
                    <v-chip v-if="product.discount_percent > 0" color="error" variant="flat" size="x-small"
                        class="font-weight-bold">
                        -{{ Math.round(product.discount_percent) }}% OFF
                    </v-chip>
                </div>

                <!-- Availability Badge (Top Right) -->
                <v-chip v-if="product.availability && product.availability !== 'in_stock'"
                    :color="getAvailabilityColor(product.availability)" variant="flat" size="x-small"
                    class="position-absolute top-0 right-0 ma-3 font-weight-bold z-index-2">
                    {{ getAvailabilityLabel(product.availability) }}
                </v-chip>

                <v-img :src="productImage" :alt="product.title" height="100%" cover
                    class="product-img transition-transform" :class="{ 'scale-110': isHovering || isDefaultImage }" />

                <!-- Quick Actions Overlay -->
                <div class="actions-overlay d-flex align-center justify-center gap-2">
                    <v-tooltip text="View product details" location="top">
                        <template v-slot:activator="{ props: tooltipProps }">
                            <v-btn icon="mdi-eye-outline" variant="flat" color="white" size="small" class="hover-scale"
                                v-bind="tooltipProps" :to="`/products/${product.slug}`"
                                aria-label="View product details" />
                        </template>
                    </v-tooltip>
                    <v-tooltip text="Add to comparison" location="top">
                        <template v-slot:activator="{ props: tooltipProps }">
                            <v-btn icon="mdi-scale-balance" variant="flat" color="primary" size="small"
                                class="hover-scale" v-bind="tooltipProps" :disabled="comparisonDisabled"
                                @click.prevent="$emit('toggle-comparison', product)" aria-label="Add to comparison" />
                        </template>
                    </v-tooltip>
                    <v-tooltip text="Share product" location="top">
                        <template v-slot:activator="{ props: tooltipProps }">
                            <v-btn icon="mdi-share-variant" variant="flat" color="success" size="small"
                                class="hover-scale" v-bind="tooltipProps" @click.prevent="openShareMenu"
                                aria-label="Share product" />
                        </template>
                    </v-tooltip>
                </div>
            </div>

            <!-- Product Content -->
            <div class="pa-5">
                <div class="d-flex justify-space-between align-start mb-2">
                    <div class="text-caption font-weight-bold text-primary text-uppercase tracking-wide">
                        {{ categoryName }}
                    </div>
                    <div v-if="product.rating && product.rating > 0" class="d-flex align-center gap-1">
                        <v-icon icon="mdi-star" color="amber" size="16" />
                        <span class="text-body-2 font-weight-bold">{{ product.rating.toFixed(1) }}</span>
                        <span v-if="product.rating_count > 0" class="text-caption text-grey">
                            ({{ product.rating_count }})
                        </span>
                    </div>
                </div>

                <!-- Brand Name -->
                <div v-if="product.brand" class="text-caption text-medium-emphasis mb-1">
                    <v-icon icon="mdi-tag" size="12" class="mr-1" />
                    {{ product.brand }}
                </div>

                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2 lh-tight" style="min-height: 48px;">
                    {{ product.title }}
                </h3>

                <p class="text-body-2 text-medium-emphasis mb-3 line-clamp-2" style="min-height: 40px;">
                    {{ product.short_description }}
                </p>

                <!-- Features Chips -->
                <div v-if="product.features && product.features.length > 0" class="mb-3">
                    <div class="d-flex flex-wrap gap-1">
                        <v-chip v-for="feature in product.features.slice(0, 3)" :key="feature" size="x-small"
                            color="primary" variant="tonal" class="text-capitalize">
                            <v-icon :icon="getFeatureIcon(feature)" size="12" class="mr-1" />
                            {{ feature.replace('_', ' ') }}
                        </v-chip>
                    </div>
                </div>

                <!-- Quick Specs -->
                <div v-if="quickSpecs.length > 0"
                    class="specs-preview my-4 py-3 border-top border-bottom border-dashed">
                    <div class="d-flex flex-wrap gap-3">
                        <div v-for="(spec, idx) in quickSpecs" :key="idx" class="spec-badge">
                            <span class="spec-label">{{ spec.label }}</span>
                            <span class="spec-value">{{ spec.value }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-center justify-space-between mt-4">
                    <div v-if="hasPrice">
                        <!-- Show original price crossed out if there's a discount -->
                        <div v-if="product.discount_percent > 0 && product.price" class="d-flex flex-column">
                            <span class="text-caption text-medium-emphasis text-decoration-line-through">
                                Tk {{ formatNumber(product.price) }}
                            </span>
                            <span class="text-h6 font-weight-black text-error">
                                Tk {{ formatNumber(product.discounted_price || product.price) }}
                            </span>
                        </div>
                        <div v-else>
                            <span class="text-h6 font-weight-black text-primary">
                                {{ formattedPrice }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="text-h6 font-weight-bold text-primary">
                        {{ formattedPrice }}
                    </div>
                    <v-btn variant="text" color="primary" class="px-0 font-weight-bold text-capitalize"
                        :to="`/products/${product.slug}`">
                        View Details
                        <v-icon icon="mdi-arrow-right" size="small" class="ml-1" />
                    </v-btn>
                </div>
            </div>
        </v-card>

        <!-- Share Dialog Component -->
        <ShareDialog v-model="showShareMenu" :title="product.title" :url="productUrl" :share-text="shareText" />
    </v-hover>
</template>

<script setup>
import { computed, ref } from 'vue';
import ShareDialog from './ShareDialog.vue';
import { resolveUploadUrl } from '../../../utils/uploads';
import { formatNumber, formatProductPrice } from '../../../utils/formatters';

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    comparisonDisabled: {
        type: Boolean,
        default: false
    }
});

defineEmits(['toggle-comparison']);

// Share menu state
const showShareMenu = ref(false);

// Helper function to resolve image URLs using the standard utility
const resolveImageUrl = (url) => {
    if (!url || typeof url !== 'string') return null;
    return resolveUploadUrl(url);
};

// Helper function to extract first image from images field
const getFirstImage = (images) => {
    if (!images) return null;

    // If it's already an array
    if (Array.isArray(images)) {
        return images.length > 0 ? images[0] : null;
    }

    // If it's a string, try to parse as JSON
    if (typeof images === 'string') {
        try {
            const parsed = JSON.parse(images);
            if (Array.isArray(parsed) && parsed.length > 0) {
                return parsed[0];
            }
        } catch (e) {
            // If parsing fails, treat the string itself as the image URL
            return images;
        }
    }

    return null;
};

// Computed properties
const productImage = computed(() => {
    let imageUrl = null;

    // Try thumbnail first
    if (props.product?.thumbnail) {
        imageUrl = resolveImageUrl(props.product.thumbnail);
    }

    // If no thumbnail, try images array
    if (!imageUrl && props.product?.images) {
        const firstImage = getFirstImage(props.product.images);
        if (firstImage) {
            imageUrl = resolveImageUrl(firstImage);
        }
    }

    return imageUrl || '/assets/img/default.jpg';
});

const isDefaultImage = computed(() => {
    return productImage.value === '/assets/img/default.jpg';
});

const categoryName = computed(() => {
    if (props.product.categories && props.product.categories.length > 0) {
        return props.product.categories[0].name;
    }
    return 'Uncategorized';
});

const hasPrice = computed(() => {
    return !!(props.product.price !== null && props.product.price !== undefined && props.product.price !== '') || !!props.product.price_range;
});

const formattedPrice = computed(() => {
    return formatProductPrice(props.product, 'Contact for Price');
});

const quickSpecs = computed(() => {
    const specs = [];
    if (props.product.specifications) {
        if (props.product.specifications.capacity) {
            specs.push({ label: 'Capacity', value: props.product.specifications.capacity });
        }
        if (props.product.specifications.input) {
            specs.push({ label: 'Input', value: props.product.specifications.input });
        }
        if (props.product.specifications.type) {
            specs.push({ label: 'Type', value: props.product.specifications.type });
        }
    }
    return specs.slice(0, 3);
});

// Share functionality
const productUrl = computed(() => {
    const baseUrl = window.location.origin;
    return `${baseUrl}/products/${props.product.slug}`;
});

const shareText = computed(() => {
    const title = props.product.title || 'Product';
    const description = props.product.short_description || '';
    const price = formattedPrice.value;
    return `Check out ${title}${description ? ` - ${description}` : ''}${price ? ` - ${price}` : ''}`;
});

const openShareMenu = () => {
    // Check if Web Share API is available (mobile devices)
    if (navigator.share) {
        navigator.share({
            title: props.product.title,
            text: shareText.value,
            url: productUrl.value
        }).catch((error) => {
            // User cancelled or error occurred, show share menu instead
            if (error.name !== 'AbortError') {
                showShareMenu.value = true;
            }
        });
    } else {
        showShareMenu.value = true;
    }
};

// Helper function to get feature icon
const getFeatureIcon = (feature) => {
    const iconMap = {
        'wireless': 'mdi-wifi',
        'waterproof': 'mdi-water',
        'bluetooth': 'mdi-bluetooth',
        'rechargeable': 'mdi-battery-charging',
        'warranty': 'mdi-shield-check',
        'eco_friendly': 'mdi-leaf'
    };
    return iconMap[feature] || 'mdi-check-circle';
};

// Helper function to get availability color
const getAvailabilityColor = (availability) => {
    const colorMap = {
        'out_of_stock': 'error',
        'pre_order': 'warning',
        'coming_soon': 'info'
    };
    return colorMap[availability] || 'success';
};

// Helper function to get availability label
const getAvailabilityLabel = (availability) => {
    const labelMap = {
        'in_stock': 'In Stock',
        'out_of_stock': 'Out of Stock',
        'pre_order': 'Pre-Order',
        'coming_soon': 'Coming Soon'
    };
    return labelMap[availability] || availability;
};
</script>

<style scoped>
.product-card {
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.product-card:hover {
    border-color: rgb(var(--v-theme-primary));
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.product-image-container {
    overflow: hidden;
}

.actions-overlay {
    position: absolute;
    bottom: 20px;
    left: 0;
    width: 100%;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.product-card:hover .actions-overlay {
    opacity: 1;
    transform: translateY(0);
}

.hover-scale {
    transition: transform 0.2s ease;
}

.hover-scale:hover {
    transform: scale(1.1);
}

.scale-110 {
    transform: scale(1.1);
}

.specs-preview {
    border-color: #e2e8f0 !important;
}

.spec-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    min-width: 60px;
}

.spec-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    color: #94a3b8;
    font-weight: 600;
    margin-bottom: 2px;
}

.spec-value {
    font-size: 0.75rem;
    font-weight: 700;
    color: #334155;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Utility classes moved to app.css */

@media (max-width: 600px) {
    .product-image-container {
        height: 200px !important;
    }
}
</style>
