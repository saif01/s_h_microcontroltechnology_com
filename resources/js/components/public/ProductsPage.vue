<template>
    <div class="products-page-modern">
        <!-- Hero Section -->
        <section class="hero-section position-relative d-flex align-center justify-center text-center overflow-hidden">
            <div class="hero-background"></div>
            <div class="hero-pattern"></div>

            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div class="hero-content">
                        <div class="hero-badge d-inline-flex align-center px-4 py-2 mb-6 rounded-pill">
                            <v-icon icon="mdi-shield-check" color="white" size="small" class="mr-2"></v-icon>
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
                <!-- Filter & Search Bar -->
                <div class="filter-section mb-8">
                    <v-card class="filter-card pa-4 rounded-xl elevation-2"
                        :class="{ 'sticky-filter': isFilterSticky }">
                        <div class="d-flex flex-column flex-md-row align-center justify-space-between gap-4">
                            <!-- Category Filter -->
                            <div
                                class="category-filter d-flex align-center overflow-x-auto w-100 w-md-auto pb-2 pb-md-0 hide-scrollbar">
                                <v-btn v-for="category in categories" :key="category.id" variant="text"
                                    :color="activeCategory === category.id ? 'primary' : 'grey-darken-1'"
                                    class="font-weight-bold text-capitalize mr-2 rounded-lg px-4" :class="{
                                        'bg-primary text-white elevation-2': activeCategory === category.id,
                                        'text-grey-darken-1': activeCategory !== category.id
                                    }" @click="setActiveCategory(category.id)">
                                    <v-icon v-if="category.icon" :icon="category.icon" size="small"
                                        class="mr-2"></v-icon>
                                    {{ category.name }}
                                </v-btn>
                            </div>

                            <!-- Search & Sort -->
                            <div class="d-flex align-center gap-3 w-100 w-md-auto flex-wrap">
                                <v-text-field v-model="searchQuery" density="compact" variant="outlined"
                                    label="Search products..." prepend-inner-icon="mdi-magnify" hide-details
                                    bg-color="white" class="rounded-lg flex-grow-1"
                                    style="min-width: 200px; max-width: 300px;" clearable
                                    @update:model-value="handleSearch"></v-text-field>

                                <v-menu>
                                    <template v-slot:activator="{ props }">
                                        <v-btn v-bind="props" variant="outlined" color="grey-darken-2"
                                            class="bg-white text-grey-darken-2" append-icon="mdi-chevron-down">
                                            Sort: {{ sortByLabel }}
                                        </v-btn>
                                    </template>
                                    <v-list>
                                        <v-list-item v-for="item in sortOptions" :key="item.value"
                                            @click="sortBy = item.value">
                                            <v-list-item-title class="text-grey-darken-2">{{ item.label
                                            }}</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>

                                <!-- Comparison Button -->
                                <v-btn v-if="comparisonProducts.length > 0" color="primary" variant="flat"
                                    prepend-icon="mdi-scale-balance" class="text-white font-weight-bold"
                                    @click="showComparison = true">
                                    Compare ({{ comparisonProducts.length }})
                                </v-btn>
                            </div>
                        </div>
                    </v-card>
                </div>

                <!-- Products Grid -->
                <div v-if="loading" class="text-center py-16">
                    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                    <p class="text-body-1 text-medium-emphasis mt-4">Loading products...</p>
                </div>

                <div v-else-if="filteredProducts.length === 0" class="text-center py-16">
                    <v-icon icon="mdi-package-variant" size="64" color="grey-lighten-1" class="mb-4"></v-icon>
                    <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No products found</h3>
                    <p class="text-body-2 text-medium-emphasis mb-6">Try adjusting your filters or search terms.</p>
                    <v-btn variant="outlined" color="primary" @click="clearFilters">Clear Filters</v-btn>
                </div>

                <v-row v-else>
                    <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="6" lg="4" xl="3">
                        <v-hover v-slot="{ isHovering, props }">
                            <v-card v-bind="props" :elevation="isHovering ? 8 : 2"
                                class="product-card h-100 bg-white rounded-xl overflow-hidden transition-all">
                                <!-- Product Image -->
                                <div class="product-image-container position-relative bg-grey-lighten-4 pa-6 d-flex align-center justify-center"
                                    style="height: 240px;">
                                    <v-chip v-if="product.featured" color="amber-accent-4" variant="flat" size="x-small"
                                        class="position-absolute top-0 left-0 ma-3 font-weight-bold z-index-2">
                                        FEATURED
                                    </v-chip>

                                    <v-img :src="getProductImage(product)" max-height="180" contain
                                        class="product-img transition-transform"
                                        :class="{ 'scale-110': isHovering }"></v-img>

                                    <!-- Quick Actions Overlay -->
                                    <div class="actions-overlay d-flex align-center justify-center gap-2">
                                        <v-btn icon="mdi-eye-outline" variant="flat" color="white" size="small"
                                            class="hover-scale" :to="`/products/${product.slug}`"></v-btn>
                                        <v-btn icon="mdi-scale-balance" variant="flat" color="primary" size="small"
                                            class="hover-scale" @click="toggleComparison(product)"
                                            :disabled="comparisonProducts.length >= 3 && !isInComparison(product)"></v-btn>
                                    </div>
                                </div>

                                <!-- Product Content -->
                                <div class="pa-5">
                                    <div class="d-flex justify-space-between align-start mb-2">
                                        <div
                                            class="text-caption font-weight-bold text-primary text-uppercase tracking-wide">
                                            {{ getCategoryName(product) }}
                                        </div>
                                        <div v-if="product.rating" class="d-flex align-center">
                                            <v-icon icon="mdi-star" color="amber" size="x-small" class="mr-1"></v-icon>
                                            <span class="text-caption font-weight-bold">{{ product.rating }}</span>
                                        </div>
                                    </div>

                                    <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2 lh-tight"
                                        style="min-height: 48px;">
                                        {{ product.title }}
                                    </h3>

                                    <p class="text-body-2 text-medium-emphasis mb-4 line-clamp-2"
                                        style="min-height: 40px;">
                                        {{ product.short_description }}
                                    </p>

                                    <!-- Quick Specs -->
                                    <div v-if="getQuickSpecs(product).length > 0"
                                        class="specs-preview my-4 py-3 border-top border-bottom border-dashed">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div v-for="(spec, idx) in getQuickSpecs(product)" :key="idx"
                                                class="spec-badge">
                                                <span class="spec-label">{{ spec.label }}</span>
                                                <span class="spec-value">{{ spec.value }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-center justify-space-between mt-4">
                                        <div>
                                            <span v-if="product.oldPrice"
                                                class="text-caption text-medium-emphasis text-decoration-line-through mr-2">
                                                ${{ product.oldPrice }}
                                            </span>
                                            <span class="text-h6 font-weight-black text-primary">
                                                {{ formatPrice(product) }}
                                            </span>
                                        </div>
                                        <v-btn variant="text" color="primary"
                                            class="px-0 font-weight-bold text-capitalize"
                                            :to="`/products/${product.slug}`">
                                            View Details
                                            <v-icon icon="mdi-arrow-right" size="small" class="ml-1"></v-icon>
                                        </v-btn>
                                    </div>
                                </div>
                            </v-card>
                        </v-hover>
                    </v-col>
                </v-row>

                <!-- Load More / Pagination -->
                <div v-if="filteredProducts.length > 0 && hasMore" class="text-center mt-12">
                    <v-btn variant="outlined" color="primary" size="large" rounded="pill" class="px-10 font-weight-bold"
                        @click="loadMore">
                        Load More Products
                    </v-btn>
                </div>
            </v-container>
        </section>

        <!-- Product Comparison Dialog -->
        <v-dialog v-model="showComparison" max-width="1400" scrollable>
            <v-card class="rounded-xl">
                <v-card-title class="d-flex align-center justify-space-between pa-6 bg-primary text-white">
                    <div class="d-flex align-center">
                        <v-icon icon="mdi-scale-balance" class="mr-3"></v-icon>
                        <span class="text-h5 font-weight-bold">Compare Products</span>
                    </div>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="showComparison = false"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0">
                    <div v-if="comparisonProducts.length === 0" class="text-center py-16">
                        <v-icon icon="mdi-scale-balance" size="64" color="grey-lighten-1" class="mb-4"></v-icon>
                        <p class="text-body-1 text-medium-emphasis">No products selected for comparison.</p>
                        <p class="text-caption text-medium-emphasis mt-2">Select up to 3 products to compare.</p>
                    </div>

                    <div v-else class="comparison-table">
                        <v-table density="comfortable" class="comparison-table-content">
                            <thead>
                                <tr>
                                    <th class="comparison-header">Feature</th>
                                    <th v-for="product in comparisonProducts" :key="product.id"
                                        class="comparison-product-header text-center">
                                        <div class="product-comparison-card">
                                            <v-btn icon="mdi-close" size="x-small" variant="text"
                                                class="position-absolute top-0 right-0"
                                                @click="removeFromComparison(product)"></v-btn>
                                            <v-img :src="getProductImage(product)" max-height="120" contain
                                                class="mb-3"></v-img>
                                            <h4 class="text-subtitle-1 font-weight-bold mb-2">{{ product.title }}</h4>
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
                                <tr>
                                    <td class="font-weight-bold bg-grey-lighten-4">Category</td>
                                    <td v-for="product in comparisonProducts" :key="product.id" class="text-center">
                                        {{ getCategoryName(product) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold bg-grey-lighten-4">SKU</td>
                                    <td v-for="product in comparisonProducts" :key="product.id" class="text-center">
                                        {{ product.sku || 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold bg-grey-lighten-4">Price</td>
                                    <td v-for="product in comparisonProducts" :key="product.id" class="text-center">
                                        {{ formatPrice(product) }}
                                    </td>
                                </tr>
                                <tr v-for="spec in getComparisonSpecs()" :key="spec.key">
                                    <td class="font-weight-bold bg-grey-lighten-4">{{ spec.label }}</td>
                                    <td v-for="product in comparisonProducts" :key="product.id" class="text-center">
                                        {{ getSpecValue(product, spec.key) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold bg-grey-lighten-4">Key Features</td>
                                    <td v-for="product in comparisonProducts" :key="product.id" class="text-center">
                                        <div class="d-flex flex-column align-center gap-1">
                                            <v-chip v-for="(feature, idx) in getKeyFeatures(product).slice(0, 3)"
                                                :key="idx" size="x-small" variant="tonal" color="primary" class="mb-1">
                                                {{ feature }}
                                            </v-chip>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold bg-grey-lighten-4">Recommended Use</td>
                                    <td v-for="product in comparisonProducts" :key="product.id" class="text-center">
                                        {{ getRecommendedUse(product) }}
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </div>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-5">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="clearComparison">Clear All</v-btn>
                    <v-btn color="primary" variant="flat" @click="showComparison = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ProductsPage',
    data() {
        return {
            products: [],
            categories: [],
            activeCategory: 'all',
            searchQuery: '',
            sortBy: 'newest',
            loading: false,
            hasMore: false,
            isFilterSticky: false,
            comparisonProducts: [],
            showComparison: false,
            sortOptions: [
                { label: 'Newest First', value: 'newest' },
                { label: 'Price: Low to High', value: 'price_asc' },
                { label: 'Price: High to Low', value: 'price_desc' },
                { label: 'Name: A to Z', value: 'name_asc' },
                { label: 'Name: Z to A', value: 'name_desc' },
                { label: 'Featured First', value: 'featured' }
            ]
        };
    },
    computed: {
        filteredProducts() {
            let filtered = [...this.products];

            // Category filter
            if (this.activeCategory !== 'all') {
                filtered = filtered.filter(p => {
                    const categoryIds = p.categories?.map(c => c.id) || [];
                    return categoryIds.includes(this.activeCategory);
                });
            }

            // Search filter
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(p =>
                    p.title?.toLowerCase().includes(query) ||
                    p.short_description?.toLowerCase().includes(query) ||
                    p.sku?.toLowerCase().includes(query) ||
                    p.description?.toLowerCase().includes(query)
                );
            }

            // Sort
            filtered.sort((a, b) => {
                switch (this.sortBy) {
                    case 'price_asc':
                        return (parseFloat(a.price) || 0) - (parseFloat(b.price) || 0);
                    case 'price_desc':
                        return (parseFloat(b.price) || 0) - (parseFloat(a.price) || 0);
                    case 'name_asc':
                        return (a.title || '').localeCompare(b.title || '');
                    case 'name_desc':
                        return (b.title || '').localeCompare(a.title || '');
                    case 'featured':
                        if (a.featured && !b.featured) return -1;
                        if (!a.featured && b.featured) return 1;
                        return 0;
                    case 'newest':
                    default:
                        return new Date(b.created_at || 0) - new Date(a.created_at || 0);
                }
            });

            return filtered;
        },
        sortByLabel() {
            const option = this.sortOptions.find(o => o.value === this.sortBy);
            return option ? option.label : 'Newest First';
        }
    },
    async mounted() {
        await this.loadCategories();
        await this.loadProducts();
        this.setupStickyFilter();
    },
    methods: {
        async loadCategories() {
            try {
                // Try to load categories from API, fallback to default
                const response = await axios.get('/api/openapi/categories?type=product').catch(() => null);
                if (response && response.data) {
                    this.categories = [
                        { id: 'all', name: 'All Products', icon: 'mdi-view-grid' },
                        ...response.data.map(c => ({
                            id: c.id,
                            name: c.name,
                            icon: this.getCategoryIcon(c.slug || c.name)
                        }))
                    ];
                } else {
                    // Default categories
                    this.categories = [
                        { id: 'all', name: 'All Products', icon: 'mdi-view-grid' },
                        { id: 'ups', name: 'UPS Systems', icon: 'mdi-power' },
                        { id: 'batteries', name: 'Batteries', icon: 'mdi-battery' },
                        { id: 'inverters', name: 'Inverters', icon: 'mdi-flash' },
                        { id: 'solar', name: 'Solar', icon: 'mdi-solar-power' },
                        { id: 'accessories', name: 'Accessories', icon: 'mdi-package-variant' }
                    ];
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                this.categories = [
                    { id: 'all', name: 'All Products', icon: 'mdi-view-grid' },
                    { id: 'ups', name: 'UPS Systems', icon: 'mdi-power' },
                    { id: 'batteries', name: 'Batteries', icon: 'mdi-battery' },
                    { id: 'inverters', name: 'Inverters', icon: 'mdi-flash' },
                    { id: 'solar', name: 'Solar', icon: 'mdi-solar-power' },
                    { id: 'accessories', name: 'Accessories', icon: 'mdi-package-variant' }
                ];
            }
        },
        async loadProducts() {
            this.loading = true;
            try {
                const response = await axios.get('/api/openapi/products');
                this.products = response.data || [];
            } catch (error) {
                console.error('Error loading products:', error);
                // Fallback mock data
                this.products = Array.from({ length: 12 }).map((_, i) => ({
                    id: i + 1,
                    title: `Industrial Power Unit ${i + 100}`,
                    slug: `product-${i + 1}`,
                    sku: `MCT-${String(i + 1).padStart(4, '0')}`,
                    short_description: 'High-performance power solution designed for reliability and efficiency.',
                    price: (Math.random() * 1000 + 200).toFixed(2),
                    featured: i % 3 === 0,
                    categories: [{ id: i % 2 === 0 ? 'ups' : 'batteries', name: i % 2 === 0 ? 'UPS Systems' : 'Batteries' }],
                    specifications: {
                        capacity: '1000VA',
                        input: '230V AC',
                        type: 'Online Double'
                    }
                }));
            } finally {
                this.loading = false;
            }
        },
        setActiveCategory(categoryId) {
            this.activeCategory = categoryId;
        },
        handleSearch() {
            // Search is handled by computed property
        },
        clearFilters() {
            this.activeCategory = 'all';
            this.searchQuery = '';
            this.sortBy = 'newest';
        },
        loadMore() {
            // Implement pagination if needed
            this.hasMore = false;
        },
        getProductImage(product) {
            if (product.thumbnail) return product.thumbnail;
            if (product.images && product.images.length > 0) return product.images[0];
            return 'https://via.placeholder.com/400x300?text=Product';
        },
        getCategoryName(product) {
            if (product.categories && product.categories.length > 0) {
                return product.categories[0].name;
            }
            return 'Uncategorized';
        },
        getCategoryIcon(slug) {
            const iconMap = {
                'ups': 'mdi-power',
                'batteries': 'mdi-battery',
                'inverters': 'mdi-flash',
                'solar': 'mdi-solar-power',
                'accessories': 'mdi-package-variant'
            };
            return iconMap[slug?.toLowerCase()] || 'mdi-package-variant';
        },
        formatPrice(product) {
            if (product.price_range) return product.price_range;
            if (product.price) return `$${parseFloat(product.price).toFixed(2)}`;
            return 'Contact for Price';
        },
        getQuickSpecs(product) {
            const specs = [];
            if (product.specifications) {
                if (product.specifications.capacity) {
                    specs.push({ label: 'Capacity', value: product.specifications.capacity });
                }
                if (product.specifications.input) {
                    specs.push({ label: 'Input', value: product.specifications.input });
                }
                if (product.specifications.type) {
                    specs.push({ label: 'Type', value: product.specifications.type });
                }
            }
            return specs.slice(0, 3);
        },
        toggleComparison(product) {
            const index = this.comparisonProducts.findIndex(p => p.id === product.id);
            if (index > -1) {
                this.comparisonProducts.splice(index, 1);
            } else {
                if (this.comparisonProducts.length >= 3) {
                    this.$toast?.error('You can compare up to 3 products at a time');
                    return;
                }
                this.comparisonProducts.push(product);
            }
        },
        isInComparison(product) {
            return this.comparisonProducts.some(p => p.id === product.id);
        },
        removeFromComparison(product) {
            this.toggleComparison(product);
        },
        clearComparison() {
            this.comparisonProducts = [];
        },
        getComparisonSpecs() {
            const allSpecs = new Set();
            this.comparisonProducts.forEach(product => {
                if (product.specifications) {
                    Object.keys(product.specifications).forEach(key => allSpecs.add(key));
                }
            });
            return Array.from(allSpecs).map(key => ({
                key,
                label: key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ')
            }));
        },
        getSpecValue(product, key) {
            if (product.specifications && product.specifications[key]) {
                return product.specifications[key];
            }
            return 'N/A';
        },
        getKeyFeatures(product) {
            if (product.key_features && Array.isArray(product.key_features)) {
                return product.key_features;
            }
            // Extract from description or use defaults
            return [
                'High Performance',
                'Reliable Design',
                'Energy Efficient'
            ];
        },
        getRecommendedUse(product) {
            if (product.recommended_use) return product.recommended_use;
            // Default based on category
            const category = this.getCategoryName(product).toLowerCase();
            if (category.includes('ups')) return 'Data Centers, Servers';
            if (category.includes('battery')) return 'Backup Power Systems';
            if (category.includes('solar')) return 'Renewable Energy Systems';
            return 'General Purpose';
        },
        setupStickyFilter() {
            window.addEventListener('scroll', () => {
                this.isFilterSticky = window.scrollY > 200;
            });
        }
    }
};
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

/* Filter Section */
.filter-card {
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(12px);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.filter-card * {
    color: inherit;
}

.filter-card .v-btn {
    color: inherit !important;
}

.filter-card .v-text-field {
    color: inherit !important;
}

.filter-card.sticky-filter {
    position: sticky;
    top: 80px;
    z-index: 100;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

/* Product Cards */
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

/* Specs */
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

/* Comparison Table */
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

/* Utilities */
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.mw-800 {
    max-width: 800px;
}

.lh-tight {
    line-height: 1.2;
}

.tracking-wide {
    letter-spacing: 0.05em;
}

.gap-2 {
    gap: 8px;
}

.gap-3 {
    gap: 12px;
}

.gap-4 {
    gap: 16px;
}

.z-index-2 {
    z-index: 2;
}

/* Responsive */
@media (max-width: 960px) {
    .hero-section {
        height: 280px;
    }

    .filter-card.sticky-filter {
        top: 70px;
    }
}

@media (max-width: 600px) {
    .hero-section {
        height: 240px;
    }

    .product-image-container {
        height: 200px !important;
    }
}
</style>