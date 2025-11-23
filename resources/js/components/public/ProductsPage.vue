<template>
    <div class="products-page-premium">
        <!-- Hero Section -->
        <section class="premium-hero position-relative d-flex align-center justify-center text-center overflow-hidden">
            <div class="hero-bg-dark"></div>
            <div class="hero-grid-overlay"></div>
            <div class="hero-glow-spotlight"></div>
            
            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="tech-badge d-inline-flex align-center px-4 py-1 mb-6">
                            <v-icon icon="mdi-shield-check" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-caption font-weight-bold text-amber-accent-4 tracking-widest text-uppercase">Industrial Grade</span>
                        </div>
                        <h1 class="text-h3 text-md-h1 font-weight-black text-white mb-6 tracking-tight">
                            ENGINEERED FOR <span class="text-transparent bg-clip-text bg-gradient-amber">POWER</span>
                        </h1>
                        <p class="text-h6 text-grey-lighten-1 opacity-80 mw-800 mx-auto font-weight-light mb-10">
                            High-performance UPS systems and power solutions designed for critical infrastructure.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Main Content -->
        <section class="py-12 bg-grey-lighten-5 min-vh-100 position-relative">
            <v-container>
                <!-- Horizontal Filter Bar -->
                <div class="filter-bar-glass mb-10 pa-4 rounded-xl d-flex flex-column flex-md-row align-center justify-space-between gap-4">
                    <!-- Categories -->
                    <div class="d-flex align-center overflow-x-auto w-100 w-md-auto pb-2 pb-md-0 hide-scrollbar">
                        <v-btn 
                            v-for="category in categories" 
                            :key="category.id"
                            variant="text"
                            :color="activeCategory === category.id ? 'primary' : 'grey-darken-1'"
                            class="font-weight-bold text-capitalize mr-2 rounded-lg"
                            :class="{ 'bg-white elevation-2': activeCategory === category.id }"
                            @click="activeCategory = category.id"
                        >
                            {{ category.name }}
                        </v-btn>
                    </div>

                    <!-- Sort & Search -->
                    <div class="d-flex align-center gap-4 w-100 w-md-auto">
                        <v-text-field
                            density="compact"
                            variant="outlined"
                            label="Search products..."
                            prepend-inner-icon="mdi-magnify"
                            hide-details
                            bg-color="white"
                            class="rounded-lg"
                            style="min-width: 200px;"
                        ></v-text-field>
                        <v-menu>
                            <template v-slot:activator="{ props }">
                                <v-btn v-bind="props" variant="outlined" color="grey-darken-2" class="bg-white" append-icon="mdi-chevron-down">
                                    Sort: {{ sortBy }}
                                </v-btn>
                            </template>
                            <v-list>
                                <v-list-item v-for="item in ['Newest', 'Price: Low to High', 'Price: High to Low']" :key="item" @click="sortBy = item">
                                    <v-list-item-title>{{ item }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </div>
                </div>

                <!-- Product Grid -->
                <v-row>
                    <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="6" lg="4" xl="3">
                        <v-hover v-slot="{ isHovering, props }">
                            <v-card
                                v-bind="props"
                                :elevation="isHovering ? 10 : 0"
                                class="product-card-tech h-100 bg-white rounded-lg overflow-hidden border-tech transition-all"
                                :to="`/products/${product.slug}`"
                            >
                                <!-- Image Area -->
                                <div class="img-container position-relative bg-grey-lighten-4 pa-8 d-flex align-center justify-center" style="height: 280px;">
                                    <v-chip
                                        v-if="product.isNew"
                                        color="amber-accent-4"
                                        variant="flat"
                                        size="x-small"
                                        class="position-absolute top-0 left-0 ma-4 font-weight-bold z-index-2"
                                    >
                                        NEW ARRIVAL
                                    </v-chip>
                                    
                                    <v-img
                                        :src="product.thumbnail || 'https://via.placeholder.com/400x300?text=Product'"
                                        max-height="200"
                                        contain
                                        class="product-img transition-transform"
                                        :class="{ 'scale-110': isHovering }"
                                    ></v-img>

                                    <!-- Quick Actions Overlay -->
                                    <div class="actions-overlay d-flex align-center justify-center gap-2">
                                        <v-btn icon="mdi-eye-outline" variant="flat" color="white" class="text-primary hover-scale"></v-btn>
                                        <v-btn icon="mdi-cart-outline" variant="flat" color="primary" class="hover-scale"></v-btn>
                                    </div>
                                </div>

                                <!-- Content Area -->
                                <div class="pa-5">
                                    <div class="d-flex justify-space-between align-start mb-2">
                                        <div class="text-caption font-weight-bold text-primary text-uppercase tracking-wide">{{ product.category }}</div>
                                        <div class="d-flex align-center">
                                            <v-icon icon="mdi-star" color="amber" size="x-small" class="mr-1"></v-icon>
                                            <span class="text-caption font-weight-bold">4.8</span>
                                        </div>
                                    </div>
                                    
                                    <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2 lh-tight text-truncate-2" style="min-height: 48px;">
                                        {{ product.title }}
                                    </h3>

                                    <!-- Tech Specs Grid -->
                                    <div class="specs-grid my-4 py-3 border-top border-bottom border-dashed">
                                        <div class="spec-item">
                                            <span class="spec-label">Capacity</span>
                                            <span class="spec-value">{{ product.specs.capacity }}</span>
                                        </div>
                                        <div class="spec-item">
                                            <span class="spec-label">Input</span>
                                            <span class="spec-value">{{ product.specs.input }}</span>
                                        </div>
                                        <div class="spec-item">
                                            <span class="spec-label">Type</span>
                                            <span class="spec-value">{{ product.specs.type }}</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-center justify-space-between mt-4">
                                        <div>
                                            <span class="text-caption text-medium-emphasis text-decoration-line-through mr-2" v-if="product.oldPrice">${{ product.oldPrice }}</span>
                                            <span class="text-h6 font-weight-black text-primary">${{ product.price }}</span>
                                        </div>
                                        <v-btn variant="text" color="primary" class="px-0 font-weight-bold text-capitalize">
                                            Details <v-icon icon="mdi-arrow-right" size="small" class="ml-1"></v-icon>
                                        </v-btn>
                                    </div>
                                </div>
                            </v-card>
                        </v-hover>
                    </v-col>
                </v-row>

                <div class="text-center mt-16">
                    <v-btn variant="outlined" color="primary" size="large" rounded="pill" class="px-10 font-weight-bold border-2">
                        View Full Catalog
                    </v-btn>
                </div>
            </v-container>
        </section>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ProductsPage',
    data() {
        return {
            products: [],
            activeCategory: 'all',
            sortBy: 'Newest',
            categories: [
                { id: 'all', name: 'All' },
                { id: 'ups', name: 'UPS Systems' },
                { id: 'batteries', name: 'Batteries' },
                { id: 'inverters', name: 'Inverters' },
                { id: 'solar', name: 'Solar' },
                { id: 'accessories', name: 'Accessories' }
            ]
        };
    },
    computed: {
        filteredProducts() {
            return this.products;
        }
    },
    async mounted() {
        await this.loadProducts();
    },
    methods: {
        async loadProducts() {
            try {
                const response = await axios.get('/api/public/products');
                this.products = response.data.map(p => this.enhanceProductData(p));
            } catch (error) {
                console.error('Error loading products:', error);
                // Fallback data with tech specs
                this.products = Array.from({ length: 8 }).map((_, i) => this.enhanceProductData({
                    id: i,
                    title: `Industrial Power Unit ${i + 100}`,
                    slug: `product-${i + 1}`,
                    category: i % 2 === 0 ? 'UPS Systems' : 'Batteries',
                    price: (Math.random() * 1000 + 200).toFixed(2)
                }));
            }
        },
        enhanceProductData(product) {
            return {
                ...product,
                isNew: Math.random() > 0.8,
                oldPrice: Math.random() > 0.5 ? (parseFloat(product.price) * 1.2).toFixed(2) : null,
                specs: {
                    capacity: '1000VA',
                    input: '230V AC',
                    type: 'Online Double'
                }
            };
        }
    }
};
</script>

<style scoped>
.products-page-premium {
    background: #f8fafc;
}

/* Hero Styles */
.premium-hero {
    height: 400px;
    background: #0f172a;
}

.hero-bg-dark {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 50% 50%, #1e293b 0%, #0f172a 100%);
}

.hero-grid-overlay {
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

.hero-glow-spotlight {
    position: absolute;
    top: -50%;
    left: 20%;
    width: 60%;
    height: 200%;
    background: radial-gradient(ellipse at center, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
    transform: rotate(-15deg);
    pointer-events: none;
}

.tech-badge {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 4px;
}

.bg-gradient-amber {
    background: linear-gradient(to right, #fbbf24, #d97706);
}

.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Filter Bar */
.filter-bar-glass {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 90px;
    z-index: 10;
}

/* Tech Card Styles */
.product-card-tech {
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-card-tech:hover {
    border-color: #3b82f6;
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}

.img-container {
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

.product-card-tech:hover .actions-overlay {
    opacity: 1;
    transform: translateY(0);
}

.scale-110 { transform: scale(1.08); }

.specs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.spec-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.spec-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    color: #94a3b8;
    font-weight: 600;
    margin-bottom: 2px;
}

.spec-value {
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
}

.border-dashed { border-style: dashed !important; border-color: #e2e8f0 !important; }

.mw-800 { max-width: 800px; }
.lh-tight { line-height: 1.2; }
.tracking-tight { letter-spacing: -0.025em; }
.tracking-widest { letter-spacing: 0.15em; }
.gap-2 { gap: 8px; }
.gap-4 { gap: 16px; }

.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
