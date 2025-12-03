<template>
    <div class="products-page-modern position-relative">
        <!-- Mobile Filter Toggle Button - Moved to root for better positioning context -->
        <v-btn v-if="!mobileFiltersOpen && isMobile" class="mobile-filter-toggle d-lg-none"
            :class="{ 'has-active-filters': hasActiveFilters }" color="primary" size="large" elevation="6"
            @click="mobileFiltersOpen = true">
            <v-icon icon="mdi-filter-variant" size="20" />
            <span class="filter-text ml-2">Filters</span>
            <v-badge v-if="hasActiveFilters" :content="activeFiltersCount" color="error" class="filter-badge"
                offset-x="-8" offset-y="-8" />
        </v-btn>

        <!-- Hero Section -->
        <section
            class="mb-10 page-hero position-relative d-flex align-center justify-center text-center overflow-hidden">
            <div class="hero-bg-gradient"></div>
            <div class="hero-pattern"></div>

            <!-- Animated gradient orbs -->
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>

            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-5 py-3 rounded-pill mb-8 animate-float">
                            <div class="pulse-dot mr-2"></div>
                            <v-icon icon="mdi-shield-check" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">PREMIUM
                                QUALITY</span>
                        </div>
                        <h1
                            class="text-h4 text-lg-h2 font-weight-black text-white mb-6 lh-tight text-shadow-sm animate-slide-up">
                            Explore Our <span class="text-amber-accent-3">Product Range</span>
                        </h1>
                        <p
                            class="text-h6 text-white opacity-90 mw-700 mx-auto font-weight-light animate-slide-up-delay">
                            Discover high-performance solutions designed for your business needs. Browse, compare, and
                            find the perfect product.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Main Content -->
        <section class="pb-12 bg-grey-lighten-5 min-vh-100">
            <v-container fluid>

                <v-row class="products-layout">
                    <!-- Desktop Sidebar - Filter Bar -->
                    <v-col lg="3" xl="2" class="filter-sidebar d-none d-lg-block">
                        <FilterBar :categories="categories" :active-category="activeCategory"
                            :search-query="searchQuery" :sort-by="sortBy" :sort-options="sortOptions"
                            :sort-by-label="sortByLabel" :has-active-filters="hasActiveFilters"
                            :active-filters-count="activeFiltersCount" :comparison-count="comparisonProducts.length"
                            :results-count="advancedFilteredProducts.length" :price-range="priceRange"
                            :availability="availability" :brands="availableBrands" :selected-brands="selectedBrands"
                            :min-rating="minRating" :features="availableFeatures" :selected-features="selectedFeatures"
                            :discount="discount" @update:active-category="setActiveCategory"
                            @update:search-query="setSearchQuery" @update:sort-by="setSortBy"
                            @update:price-range="setPriceRange" @update:availability="setAvailability"
                            @update:brands="setSelectedBrands" @update:min-rating="setMinRating"
                            @update:features="setSelectedFeatures" @update:discount="setDiscount"
                            @clear-filters="clearAllFilters" @open-comparison="openComparison" />
                    </v-col>

                    <!-- Mobile Dialog - Filter Bar -->
                    <v-dialog v-model="mobileFiltersOpen" fullscreen transition="dialog-bottom-transition"
                        class="mobile-filters-dialog d-lg-none" scrollable persistent>
                        <v-card class="mobile-filter-card">
                            <v-toolbar color="primary" class="mobile-filter-toolbar" elevation="2">
                                <v-btn icon="mdi-close" @click="mobileFiltersOpen = false"
                                    aria-label="Close filters" />
                                <v-toolbar-title class="font-weight-bold">
                                    <v-icon icon="mdi-filter-variant" size="20" class="mr-2" />
                                    Filters
                                </v-toolbar-title>
                                <v-spacer />
                                <v-chip v-if="hasActiveFilters" color="white" variant="flat" size="small"
                                    class="filter-count-chip">
                                    {{ activeFiltersCount }} active
                                </v-chip>
                            </v-toolbar>

                            <v-card-text class="mobile-dialog-content pa-4">
                                <div class="results-banner mb-4">
                                    <div class="results-icon-wrapper">
                                        <v-icon icon="mdi-package-variant" color="primary" size="24" />
                                    </div>
                                    <div class="results-info">
                                        <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">
                                            {{ advancedFilteredProducts.length }} Products
                                        </span>
                                        <span class="text-caption text-grey-darken-1">
                                            {{ hasActiveFilters ? 'Filtered results' : 'All available products' }}
                                        </span>
                                    </div>
                                </div>

                                <FilterBar :categories="categories" :active-category="activeCategory"
                                    :search-query="searchQuery" :sort-by="sortBy" :sort-options="sortOptions"
                                    :sort-by-label="sortByLabel" :has-active-filters="hasActiveFilters"
                                    :active-filters-count="activeFiltersCount"
                                    :comparison-count="comparisonProducts.length"
                                    :results-count="advancedFilteredProducts.length" :price-range="priceRange"
                                    :availability="availability" :brands="availableBrands"
                                    :selected-brands="selectedBrands" :min-rating="minRating"
                                    :features="availableFeatures" :selected-features="selectedFeatures"
                                    :discount="discount" :mobile="true" @update:active-category="setActiveCategory"
                                    @update:search-query="setSearchQuery" @update:sort-by="setSortBy"
                                    @update:price-range="setPriceRange" @update:availability="setAvailability"
                                    @update:brands="setSelectedBrands" @update:min-rating="setMinRating"
                                    @update:features="setSelectedFeatures" @update:discount="setDiscount"
                                    @clear-filters="clearAllFilters" @open-comparison="openComparison" />
                            </v-card-text>

                            <v-divider />

                            <v-card-actions class="mobile-dialog-footer pa-4">
                                <v-btn variant="outlined" color="grey-darken-1" size="large" class="flex-grow-1"
                                    @click="clearAllFilters">
                                    <v-icon icon="mdi-refresh" class="mr-2" />
                                    Reset
                                </v-btn>
                                <v-btn color="primary" size="large" class="flex-grow-1"
                                    @click="mobileFiltersOpen = false">
                                    <v-icon icon="mdi-check" class="mr-2" />
                                    Apply Filters
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>

                    <!-- Right Content - Products -->
                    <v-col cols="12" lg="9" xl="10" class="products-content">
                        <!-- Loading State -->
                        <div v-if="loading" class="text-center py-16">
                            <v-progress-circular indeterminate color="primary" size="64" />
                            <p class="text-body-1 text-medium-emphasis mt-4">Loading products...</p>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="advancedFilteredProducts.length === 0" class="text-center py-16">
                            <v-icon icon="mdi-package-variant" size="64" color="grey-lighten-1" class="mb-4" />
                            <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No products found</h3>
                            <p class="text-body-2 text-medium-emphasis mb-6">
                                Try adjusting your filters or search terms.
                            </p>
                            <v-btn variant="outlined" color="primary" @click="clearAllFilters">
                                Clear Filters
                            </v-btn>
                        </div>

                        <!-- Products Grid -->
                        <v-row v-else>
                            <v-col v-for="product in displayedProducts" :key="product.id" cols="12" sm="6" xl="4"
                                xxl="3">
                                <ProductCard :product="product"
                                    :comparison-disabled="!canAddMore && !isInComparison(product)"
                                    @toggle-comparison="handleToggleComparison" />
                            </v-col>
                        </v-row>

                        <!-- Load More Section (if needed) -->
                        <div v-if="displayedProducts.length > 0 && hasMoreProducts" class="text-center mt-12">
                            <v-btn variant="outlined" color="primary" size="large" rounded="pill"
                                class="px-10 font-weight-bold" @click="loadMore" :loading="loadingMore">
                                Load More Products
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Comparison Dialog Component -->
        <ComparisonDialog v-model="showComparison" :products="comparisonProducts" :comparison-specs="comparisonSpecs"
            @remove-product="removeFromComparison" @clear-all="clearComparison" />
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue';
import { useProducts } from '../../../composables/useProducts';
import { useCategories } from '../../../composables/useCategories';
import { useProductFilters } from '../../../composables/useProductFilters';
import { useProductComparison } from '../../../composables/useProductComparison';
import ProductCard from './ProductCard.vue';
import FilterBar from './FilterBar.vue';
import ComparisonDialog from './ComparisonDialog.vue';

// Pagination constants
const PRODUCTS_PER_PAGE = 12;

// Initialize composables
const { products, loading, fetchProducts } = useProducts();
const { categories, fetchCategories } = useCategories();
const {
    activeCategory,
    searchQuery,
    sortBy,
    sortOptions,
    filteredProducts,
    sortByLabel,
    hasActiveFilters,
    activeFiltersCount,
    setActiveCategory,
    setSortBy,
    setSearchQuery,
    clearFilters
} = useProductFilters(products);

const {
    comparisonProducts,
    showComparison,
    canAddMore,
    isInComparison,
    toggleComparison,
    removeFromComparison,
    clearComparison,
    openComparison,
    getComparisonSpecs
} = useProductComparison(3);

// Pagination state
const displayedCount = ref(PRODUCTS_PER_PAGE);
const loadingMore = ref(false);

// Mobile filters state
const mobileFiltersOpen = ref(false);

// Mobile screen detection
const isMobile = ref(false);

// Advanced filter states
const priceRange = ref([0, 10000]);
const availability = ref([]);
const selectedBrands = ref([]);
const minRating = ref(0);
const selectedFeatures = ref([]);
const discount = ref(null);

// Available filter options
const availableBrands = computed(() => {
    // Extract unique brands from products
    const brandsSet = new Set();
    products.value.forEach(product => {
        if (product.brand) {
            brandsSet.add(product.brand);
        }
    });
    return Array.from(brandsSet).sort();
});

const availableFeatures = computed(() => {
    // Define common product features
    return [
        { value: 'wireless', label: 'Wireless', icon: 'mdi-wifi' },
        { value: 'waterproof', label: 'Waterproof', icon: 'mdi-water' },
        { value: 'bluetooth', label: 'Bluetooth', icon: 'mdi-bluetooth' },
        { value: 'rechargeable', label: 'Rechargeable', icon: 'mdi-battery-charging' },
        { value: 'warranty', label: 'Warranty', icon: 'mdi-shield-check' },
        { value: 'eco_friendly', label: 'Eco-Friendly', icon: 'mdi-leaf' }
    ];
});

// Computed properties
const comparisonSpecs = computed(() => {
    return getComparisonSpecs();
});

// Apply advanced filters to filteredProducts
const advancedFilteredProducts = computed(() => {
    let filtered = [...filteredProducts.value];

    // Price range filter
    if (priceRange.value[0] > 0 || priceRange.value[1] < 10000) {
        filtered = filtered.filter(product => {
            const price = parseFloat(product.price) || 0;
            return price >= priceRange.value[0] && price <= priceRange.value[1];
        });
    }

    // Availability filter
    if (availability.value.length > 0) {
        filtered = filtered.filter(product => {
            return availability.value.includes(product.availability);
        });
    }

    // Brand filter
    if (selectedBrands.value.length > 0) {
        filtered = filtered.filter(product => {
            return selectedBrands.value.includes(product.brand);
        });
    }

    // Rating filter
    if (minRating.value > 0) {
        filtered = filtered.filter(product => {
            const rating = parseFloat(product.rating) || 0;
            return rating >= minRating.value;
        });
    }

    // Features filter
    if (selectedFeatures.value.length > 0) {
        filtered = filtered.filter(product => {
            if (!product.features || !Array.isArray(product.features)) return false;
            return selectedFeatures.value.some(feature => product.features.includes(feature));
        });
    }

    // Discount filter
    if (discount.value) {
        filtered = filtered.filter(product => {
            const discountPercent = parseFloat(product.discount_percent) || 0;
            if (discount.value === 'any') return discountPercent > 0;
            return discountPercent >= parseFloat(discount.value);
        });
    }

    return filtered;
});

// Display only the first N products from filtered results
const displayedProducts = computed(() => {
    return advancedFilteredProducts.value.slice(0, displayedCount.value);
});

// Check if there are more products to load
const hasMoreProducts = computed(() => {
    return advancedFilteredProducts.value.length > displayedCount.value;
});

// Advanced filter setter methods
const setPriceRange = (range) => {
    priceRange.value = range;
};

const setAvailability = (availabilityValue) => {
    availability.value = availabilityValue;
};

const setSelectedBrands = (brands) => {
    selectedBrands.value = brands;
};

const setMinRating = (rating) => {
    minRating.value = rating;
};

const setSelectedFeatures = (features) => {
    selectedFeatures.value = features;
};

const setDiscount = (discountValue) => {
    discount.value = discountValue;
};

// Methods
const handleToggleComparison = (product) => {
    const success = toggleComparison(product);
    if (!success && !isInComparison(product)) {
        // Show error message - product limit reached
        console.warn('Cannot add more than 3 products to comparison');
        // You can integrate with a toast notification system here
    }
};

const loadMore = () => {
    loadingMore.value = true;
    // Simulate a small delay for better UX (optional)
    setTimeout(() => {
        displayedCount.value += PRODUCTS_PER_PAGE;
        loadingMore.value = false;
    }, 200);
};

// Enhanced clear filters function
const clearAllFilters = () => {
    clearFilters(); // Clear basic filters
    priceRange.value = [0, 10000];
    availability.value = [];
    selectedBrands.value = [];
    minRating.value = 0;
    selectedFeatures.value = [];
    discount.value = null;
};

// Reset pagination when filters change
watch([activeCategory, searchQuery, sortBy, priceRange, availability, selectedBrands, minRating, selectedFeatures, discount], () => {
    displayedCount.value = PRODUCTS_PER_PAGE;
}, { deep: true });


// Handle window resize
const handleResize = () => {
    isMobile.value = window.innerWidth < 1280;
};

// Lifecycle hooks
onMounted(async () => {
    // Set initial mobile state immediately
    handleResize();
    // Add resize listener
    window.addEventListener('resize', handleResize);

    await Promise.all([
        fetchCategories(),
        fetchProducts()
    ]);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
.products-page-modern {
    background: #f8fafc;
}

/* Mobile Filter Toggle Button */
.mobile-filter-toggle {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    z-index: 2000 !important;
    border-radius: 28px !important;
    font-weight: 700;
    letter-spacing: 0.5px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
    padding: 12px 24px !important;
    min-height: 56px !important;
    white-space: nowrap;
    display: inline-flex !important;
}

.mobile-filter-toggle .filter-text {
    font-size: 0.95rem;
    font-weight: 700;
    margin-left: 8px;
}

.mobile-filter-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2) !important;
}

.mobile-filter-toggle:active {
    transform: translateY(0);
}

.mobile-filter-toggle.has-active-filters {
    animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {

    0%,
    100% {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    50% {
        box-shadow: 0 8px 32px rgba(var(--v-theme-primary), 0.4);
    }
}

.mobile-filter-toggle .filter-badge :deep(.v-badge__badge) {
    font-size: 0.7rem;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    animation: pulse 2s infinite;
    font-weight: 700;
}

@keyframes pulse {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }
}

/* Mobile Filters Dialog */
.mobile-filters-dialog {
    z-index: 2000;
}

.mobile-filters-dialog :deep(.v-overlay__scrim) {
    backdrop-filter: blur(4px);
    background: rgba(0, 0, 0, 0.6) !important;
}

/* Ensure button stays visible when dialog is open */
.mobile-filters-dialog:not([model-value="false"])~.mobile-filter-toggle {
    z-index: 2500 !important;
    transform: translateY(-4px) !important;
}

.mobile-filter-card {
    background: linear-gradient(180deg, #f9fafb 0%, #ffffff 100%) !important;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.mobile-filter-toolbar {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-filter-toolbar :deep(.v-toolbar__content) {
    padding: 8px 12px;
}

.filter-count-chip {
    color: rgb(var(--v-theme-primary)) !important;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.results-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.1), rgba(var(--v-theme-primary), 0.05));
    border-radius: 16px;
    border: 1.5px solid rgba(var(--v-theme-primary), 0.2);
    box-shadow: 0 2px 8px rgba(var(--v-theme-primary), 0.1);
}

.results-icon-wrapper {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.results-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.mobile-dialog-content {
    overflow-y: auto;
    flex: 1 1 auto;
    background: #f9fafb;
    height: 0;
}

.mobile-dialog-content::-webkit-scrollbar {
    width: 6px;
}

.mobile-dialog-content::-webkit-scrollbar-track {
    background: transparent;
}

.mobile-dialog-content::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.15);
    border-radius: 10px;
}

.mobile-dialog-content::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.25);
}

.mobile-dialog-footer {
    background: white;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.06);
    gap: 12px;
    flex: 0 0 auto;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
}

.mobile-dialog-footer .v-btn {
    font-weight: 700;
    letter-spacing: 0.5px;
    border-radius: 12px;
    min-height: 48px;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.mobile-dialog-footer .v-btn:first-child {
    flex: 1;
    border: 2px solid rgba(0, 0, 0, 0.12);
}

.mobile-dialog-footer .v-btn:last-child {
    flex: 2;
    box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.3);
}

/* Products Layout */
.products-layout {
    align-items: flex-start;
}

.filter-sidebar {
    position: sticky;
    top: 80px;
    align-self: flex-start;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    padding-right: 16px;
}

.filter-sidebar::-webkit-scrollbar {
    width: 6px;
}

.filter-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.filter-sidebar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}

.filter-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

.products-content {
    padding-left: 24px;
}

/* Hero section styles moved to app.css */

/* Responsive Styles */
/* Desktop - hide mobile elements */
@media (min-width: 1280px) {
    .products-content {
        width: 100%;
    }
}

/* Mobile - ensure full width content */
@media (max-width: 1279px) {
    .products-content {
        width: 100%;
        flex: 0 0 100%;
        max-width: 100%;
        padding-left: 0 !important;
    }

    .filter-sidebar {
        display: none !important;
    }

    /* Mobile filter button styles - Vue controls visibility */
    .mobile-filter-toggle {
        display: inline-flex !important;
    }

    .mobile-filter-toggle .filter-text {
        display: inline !important;
    }

    .mobile-filter-toggle .v-icon {
        display: inline-flex !important;
    }

    .mobile-dialog-content :deep(.filter-shell) {
        margin-bottom: 0;
    }

    .mobile-dialog-content :deep(.filter-card) {
        border-radius: 12px;
        box-shadow: none !important;
        border: none;
        background: white !important;
    }
}



@media (max-width: 960px) {
    .mobile-filter-toggle {
        bottom: 20px !important;
        right: 20px !important;
        font-size: 0.9rem;
        padding: 12px 20px !important;
        display: inline-flex !important;
        z-index: 2000 !important;
    }

    .products-layout {
        margin: 0 !important;
    }

    .mobile-filter-toolbar :deep(.v-toolbar-title) {
        font-size: 1.1rem;
    }

    .results-banner {
        padding: 10px 14px;
    }

    .mobile-dialog-content {
        padding: 12px !important;
    }

    .mobile-dialog-footer {
        padding: 12px !important;
    }

    .page-hero {
        min-height: 400px !important;
        padding: 48px 16px !important;
    }

    .page-hero h1 {
        font-size: 2rem !important;
        line-height: 1.2 !important;
        margin-bottom: 16px !important;
    }

    .page-hero p {
        font-size: 1.1rem !important;
        line-height: 1.5 !important;
    }

    .glass-pill {
        padding: 10px 20px !important;
        margin-bottom: 24px !important;
    }

    .glass-pill span {
        font-size: 0.75rem !important;
    }

    .gradient-orb {
        width: 200px !important;
        height: 200px !important;
    }
}

@media (max-width: 600px) {
    .mobile-filter-toggle {
        bottom: 16px !important;
        right: 16px !important;
        padding: 10px 20px !important;
        min-height: 52px !important;
        display: inline-flex !important;
        z-index: 2000 !important;
    }

    .mobile-filter-toggle .filter-text {
        font-size: 0.9rem;
        display: inline !important;
    }

    .mobile-filter-toolbar :deep(.v-toolbar-title) {
        font-size: 1rem;
    }

    .filter-count-chip {
        font-size: 0.7rem !important;
    }

    .results-banner {
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    .mobile-dialog-content {
        padding: 8px !important;
    }

    .mobile-dialog-footer {
        padding: 8px !important;
        gap: 8px;
    }

    .mobile-dialog-footer .v-btn {
        min-height: 44px;
        font-size: 0.9rem;
    }

    .page-hero {
        min-height: 350px !important;
        padding: 32px 12px !important;
    }

    .page-hero h1 {
        font-size: 1.75rem !important;
        line-height: 1.3 !important;
        margin-bottom: 12px !important;
    }

    .page-hero p {
        font-size: 1rem !important;
        line-height: 1.4 !important;
        padding: 0 8px !important;
    }

    .glass-pill {
        padding: 8px 16px !important;
        margin-bottom: 20px !important;
    }

    .glass-pill .v-icon {
        font-size: 16px !important;
    }

    .glass-pill span {
        font-size: 0.7rem !important;
    }

    .gradient-orb {
        width: 150px !important;
        height: 150px !important;
    }

    .orb-1 {
        top: -75px !important;
        left: -75px !important;
    }

    .orb-2 {
        bottom: -75px !important;
        right: -75px !important;
    }

    .orb-3 {
        top: 50% !important;
        right: -75px !important;
    }
}
</style>
