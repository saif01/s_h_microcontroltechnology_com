<template>
    <div class="products-page-modern">
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
        <section class="py-12 bg-grey-lighten-5 min-vh-100">
            <v-container>
                <!-- Filter Bar Component -->
                <FilterBar :categories="categories" :active-category="activeCategory" :search-query="searchQuery"
                    :sort-by="sortBy" :sort-options="sortOptions" :sort-by-label="sortByLabel"
                    :has-active-filters="hasActiveFilters" :active-filters-count="activeFiltersCount"
                    :comparison-count="comparisonProducts.length" :results-count="filteredProducts.length"
                    @update:active-category="setActiveCategory" @update:search-query="setSearchQuery"
                    @update:sort-by="setSortBy" @clear-filters="clearFilters" @open-comparison="openComparison" />

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-16">
                    <v-progress-circular indeterminate color="primary" size="64" />
                    <p class="text-body-1 text-medium-emphasis mt-4">Loading products...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="filteredProducts.length === 0" class="text-center py-16">
                    <v-icon icon="mdi-package-variant" size="64" color="grey-lighten-1" class="mb-4" />
                    <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No products found</h3>
                    <p class="text-body-2 text-medium-emphasis mb-6">
                        Try adjusting your filters or search terms.
                    </p>
                    <v-btn variant="outlined" color="primary" @click="clearFilters">
                        Clear Filters
                    </v-btn>
                </div>

                <!-- Products Grid -->
                <v-row v-else>
                    <v-col v-for="product in displayedProducts" :key="product.id" cols="12" sm="6" lg="4" xl="3">
                        <ProductCard :product="product" :comparison-disabled="!canAddMore && !isInComparison(product)"
                            @toggle-comparison="handleToggleComparison" />
                    </v-col>
                </v-row>

                <!-- Load More Section (if needed) -->
                <div v-if="displayedProducts.length > 0 && hasMoreProducts" class="text-center mt-12">
                    <v-btn variant="outlined" color="primary" size="large" rounded="pill" class="px-10 font-weight-bold"
                        @click="loadMore" :loading="loadingMore">
                        Load More Products
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- Comparison Dialog Component -->
        <ComparisonDialog v-model="showComparison" :products="comparisonProducts" :comparison-specs="comparisonSpecs"
            @remove-product="removeFromComparison" @clear-all="clearComparison" />
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
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

// Computed properties
const comparisonSpecs = computed(() => {
    return getComparisonSpecs();
});

// Display only the first N products from filtered results
const displayedProducts = computed(() => {
    return filteredProducts.value.slice(0, displayedCount.value);
});

// Check if there are more products to load
const hasMoreProducts = computed(() => {
    return filteredProducts.value.length > displayedCount.value;
});

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

// Reset pagination when filters change
watch([activeCategory, searchQuery, sortBy], () => {
    displayedCount.value = PRODUCTS_PER_PAGE;
});

// Lifecycle hooks
onMounted(async () => {
    await Promise.all([
        fetchCategories(),
        fetchProducts()
    ]);
});
</script>

<style scoped>
.products-page-modern {
    background: #f8fafc;
}

/* Hero section styles moved to app.css */
</style>
