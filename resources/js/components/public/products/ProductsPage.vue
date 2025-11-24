<template>
    <div class="products-page-modern">
        <!-- Hero Section -->
        <section class="hero-section position-relative d-flex align-center justify-center text-center overflow-hidden">
            <div class="hero-background" />
            <div class="hero-pattern" />

            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div class="hero-content">
                        <div class="hero-badge d-inline-flex align-center px-4 py-2 mb-6 rounded-pill">
                            <v-icon icon="mdi-shield-check" color="white" size="small" class="mr-2" />
                            <span class="text-caption font-weight-bold text-white text-uppercase">Premium Quality</span>
                        </div>
                        <h1 class="text-h4 text-lg-h3 font-weight-black text-white mb-6 lh-tight text-shadow-sm">
                            Explore Our <span class="text-amber-accent-3">Product Range</span>
                        </h1>
                        <p class="text-h6 text-grey-lighten-1 opacity-95 mw-800 mx-auto font-weight-light mb-10">
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
                    <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="6" lg="4" xl="3">
                        <ProductCard :product="product" :comparison-disabled="!canAddMore && !isInComparison(product)"
                            @toggle-comparison="handleToggleComparison" />
                    </v-col>
                </v-row>

                <!-- Load More Section (if needed) -->
                <div v-if="filteredProducts.length > 0 && hasMore" class="text-center mt-12">
                    <v-btn variant="outlined" color="primary" size="large" rounded="pill" class="px-10 font-weight-bold"
                        @click="loadMore">
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
import { ref, onMounted, computed } from 'vue';
import { useProducts } from '../../../composables/useProducts';
import { useCategories } from '../../../composables/useCategories';
import { useProductFilters } from '../../../composables/useProductFilters';
import { useProductComparison } from '../../../composables/useProductComparison';
import ProductCard from './ProductCard.vue';
import FilterBar from './FilterBar.vue';
import ComparisonDialog from './ComparisonDialog.vue';

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

// Additional state
const hasMore = ref(false);

// Computed properties
const comparisonSpecs = computed(() => {
    return getComparisonSpecs();
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
    // Implement pagination if needed
    hasMore.value = false;
};

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

/* Hero Section */
.hero-section {
    min-height: 350px;
    height: 350px;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 60px 0;
    z-index: 1;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 50% 50%, #1e293b 0%, #0f172a 100%);
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    opacity: 0.5;
}

.hero-badge {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    white-space: nowrap;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.hero-content {
    position: relative;
    z-index: 10;
}

.hero-content h1,
.hero-content p {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Utilities */
.mw-800 {
    max-width: 800px;
}

.lh-tight {
    line-height: 1.2;
}

.z-index-2 {
    z-index: 2;
}

/* Responsive */
@media (max-width: 960px) {
    .hero-section {
        height: 280px;
    }
}

@media (max-width: 600px) {
    .hero-section {
        height: 240px;
    }
}
</style>
