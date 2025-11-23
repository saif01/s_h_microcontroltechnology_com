<template>
    <div class="product-detail-page bg-grey-lighten-5 min-vh-100 pb-16">
        <!-- Breadcrumbs -->
        <v-container class="py-4">
            <v-breadcrumbs :items="breadcrumbs" class="px-0">
                <template v-slot:divider>
                    <v-icon icon="mdi-chevron-right" size="small"></v-icon>
                </template>
                <template v-slot:title="{ item }">
                    <span :class="item.disabled ? 'text-medium-emphasis' : 'text-primary font-weight-bold'">
                        {{ item.title }}
                    </span>
                </template>
            </v-breadcrumbs>
        </v-container>

        <v-container>
            <v-row>
                <!-- Product Gallery -->
                <v-col cols="12" md="6">
                    <div class="gallery-container position-sticky top-20">
                        <v-card class="main-image-card rounded-xl overflow-hidden elevation-0 border-thin mb-4 bg-white d-flex align-center justify-center position-relative">
                            <v-chip
                                v-if="product.isNew"
                                color="amber-accent-4"
                                variant="flat"
                                class="position-absolute top-0 left-0 ma-4 z-index-2 font-weight-bold"
                            >
                                NEW
                            </v-chip>
                            <v-img
                                :src="activeImage"
                                max-height="500"
                                contain
                                class="product-main-img"
                            ></v-img>
                            <v-btn
                                icon="mdi-magnify-plus-outline"
                                variant="text"
                                color="grey-darken-2"
                                class="position-absolute bottom-0 right-0 ma-4 bg-white elevation-2"
                            ></v-btn>
                        </v-card>
                        
                        <div class="d-flex gap-4 overflow-x-auto py-2 hide-scrollbar">
                            <div 
                                v-for="(img, i) in product.images" 
                                :key="i"
                                class="thumbnail-card rounded-lg overflow-hidden cursor-pointer transition-all"
                                :class="{ 'active-thumb': activeImage === img }"
                                @click="activeImage = img"
                            >
                                <v-img :src="img" width="80" height="80" cover></v-img>
                            </div>
                        </div>
                    </div>
                </v-col>

                <!-- Product Info -->
                <v-col cols="12" md="6" class="pl-md-10">
                    <div class="product-info">
                        <div class="d-flex align-center gap-2 mb-4">
                            <v-chip color="primary" variant="tonal" size="small" class="font-weight-bold text-uppercase">
                                {{ product.category }}
                            </v-chip>
                            <div class="d-flex align-center text-caption text-medium-emphasis">
                                <v-icon icon="mdi-barcode" size="small" class="mr-1"></v-icon>
                                SKU: {{ product.sku }}
                            </div>
                        </div>

                        <h1 class="text-h3 font-weight-bold text-grey-darken-4 mb-2 lh-tight">
                            {{ product.title }}
                        </h1>

                        <div class="d-flex align-center mb-6">
                            <v-rating
                                :model-value="product.rating"
                                color="amber"
                                density="compact"
                                half-increments
                                readonly
                                size="small"
                            ></v-rating>
                            <span class="text-body-2 text-medium-emphasis ml-2">
                                ({{ product.reviewCount }} Reviews)
                            </span>
                        </div>

                        <div class="price-block mb-6 pa-4 bg-white rounded-lg border-thin d-inline-block">
                            <div class="d-flex align-end lh-1">
                                <span class="text-h3 font-weight-black text-primary">${{ product.price }}</span>
                                <span v-if="product.oldPrice" class="text-h6 text-medium-emphasis text-decoration-line-through ml-3 mb-1">
                                    ${{ product.oldPrice }}
                                </span>
                            </div>
                        </div>

                        <p class="text-body-1 text-grey-darken-1 mb-8 lh-relaxed">
                            {{ product.description }}
                        </p>

                        <!-- Key Features List -->
                        <div class="mb-8">
                            <div v-for="(feature, i) in product.keyFeatures" :key="i" class="d-flex align-center mb-2">
                                <v-icon icon="mdi-check-circle" color="success" size="small" class="mr-3"></v-icon>
                                <span class="text-body-2 font-weight-medium text-grey-darken-2">{{ feature }}</span>
                            </div>
                        </div>

                        <v-divider class="mb-8"></v-divider>

                        <!-- Actions -->
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex align-center justify-space-between mb-2">
                                <span class="text-subtitle-2 font-weight-bold text-grey-darken-3">Quantity</span>
                                <span class="text-caption font-weight-bold text-success d-flex align-center">
                                    <v-icon icon="mdi-circle-medium" color="success"></v-icon> In Stock
                                </span>
                            </div>
                            
                            <div class="d-flex gap-4 flex-wrap">
                                <v-text-field
                                    v-model="quantity"
                                    type="number"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details
                                    min="1"
                                    class="flex-grow-0"
                                    style="width: 100px;"
                                    bg-color="white"
                                ></v-text-field>
                                
                                <v-btn 
                                    color="primary" 
                                    size="large" 
                                    rounded="lg" 
                                    class="flex-grow-1 font-weight-bold elevation-4"
                                    prepend-icon="mdi-file-document-edit-outline"
                                >
                                    Add to Quote
                                </v-btn>
                                
                                <v-btn 
                                    variant="outlined" 
                                    color="grey-darken-1" 
                                    size="large" 
                                    rounded="lg" 
                                    icon="mdi-heart-outline"
                                ></v-btn>
                            </div>
                        </div>

                        <!-- Trust Badges -->
                        <div class="d-flex gap-6 mt-8 pt-6 border-top border-dashed">
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-shield-check-outline" color="primary" class="mr-2"></v-icon>
                                <div>
                                    <div class="text-caption font-weight-bold text-grey-darken-3">2 Year Warranty</div>
                                    <div class="text-caption text-medium-emphasis">Official Guarantee</div>
                                </div>
                            </div>
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-truck-fast-outline" color="primary" class="mr-2"></v-icon>
                                <div>
                                    <div class="text-caption font-weight-bold text-grey-darken-3">Fast Delivery</div>
                                    <div class="text-caption text-medium-emphasis">Nationwide Shipping</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <!-- Detailed Content Tabs -->
            <v-row class="mt-16">
                <v-col cols="12">
                    <v-card class="rounded-xl elevation-0 border-thin overflow-hidden">
                        <v-tabs v-model="tab" color="primary" align-tabs="start" bg-color="grey-lighten-4">
                            <v-tab value="overview" class="font-weight-bold text-capitalize">Overview</v-tab>
                            <v-tab value="specs" class="font-weight-bold text-capitalize">Technical Specs</v-tab>
                            <v-tab value="downloads" class="font-weight-bold text-capitalize">Downloads</v-tab>
                            <v-tab value="reviews" class="font-weight-bold text-capitalize">Reviews ({{ product.reviewCount }})</v-tab>
                        </v-tabs>
                        <v-divider></v-divider>
                        <v-card-text class="pa-8 bg-white">
                            <v-window v-model="tab">
                                <v-window-item value="overview">
                                    <div class="mw-800">
                                        <h3 class="text-h5 font-weight-bold mb-4 text-grey-darken-3">Product Overview</h3>
                                        <p class="text-body-1 text-grey-darken-1 lh-relaxed mb-6">
                                            {{ product.longDescription }}
                                        </p>
                                        <h4 class="text-h6 font-weight-bold mb-3 text-grey-darken-3">Why Choose This Model?</h4>
                                        <ul class="pl-4 text-body-1 text-grey-darken-1 lh-relaxed">
                                            <li class="mb-2">High efficiency double-conversion online topology</li>
                                            <li class="mb-2">Advanced battery management for longer lifespan</li>
                                            <li class="mb-2">Intuitive LCD interface for real-time monitoring</li>
                                            <li>Compact tower design fits easily in any workspace</li>
                                        </ul>
                                    </div>
                                </v-window-item>

                                <v-window-item value="specs">
                                    <v-table density="comfortable" class="specs-table">
                                        <tbody>
                                            <tr v-for="(value, key) in product.specs" :key="key">
                                                <td class="font-weight-bold text-grey-darken-2 bg-grey-lighten-5" width="250">{{ key }}</td>
                                                <td class="text-grey-darken-3">{{ value }}</td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-window-item>
                                
                                <v-window-item value="downloads">
                                    <v-list lines="two" class="bg-transparent">
                                        <v-list-item 
                                            v-for="doc in product.downloads" 
                                            :key="doc.title"
                                            rounded="lg"
                                            class="mb-2 border-thin bg-grey-lighten-5"
                                        >
                                            <template v-slot:prepend>
                                                <v-avatar color="primary-lighten-5" class="text-primary">
                                                    <v-icon :icon="doc.icon"></v-icon>
                                                </v-avatar>
                                            </template>
                                            <v-list-item-title class="font-weight-bold">{{ doc.title }}</v-list-item-title>
                                            <v-list-item-subtitle>{{ doc.size }} • {{ doc.type }}</v-list-item-subtitle>
                                            <template v-slot:append>
                                                <v-btn variant="text" color="primary" icon="mdi-download"></v-btn>
                                            </template>
                                        </v-list-item>
                                    </v-list>
                                </v-window-item>

                                <v-window-item value="reviews">
                                    <div class="d-flex flex-column align-center justify-center py-12 text-center">
                                        <v-icon icon="mdi-message-draw" size="48" color="grey-lighten-2" class="mb-4"></v-icon>
                                        <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No Reviews Yet</h3>
                                        <p class="text-body-2 text-medium-emphasis mb-6">Be the first to share your experience with this product.</p>
                                        <v-btn variant="outlined" color="primary">Write a Review</v-btn>
                                    </div>
                                </v-window-item>
                            </v-window>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Related Products -->
            <section class="mt-20">
                <div class="d-flex align-center justify-space-between mb-8">
                    <h2 class="text-h4 font-weight-bold text-grey-darken-3">You Might Also Like</h2>
                    <v-btn variant="text" color="primary" append-icon="mdi-arrow-right">View All</v-btn>
                </div>
                
                <v-row>
                    <v-col v-for="item in relatedProducts" :key="item.id" cols="12" sm="6" md="3">
                        <v-card class="h-100 rounded-lg border-thin elevation-0 bg-white group-hover-card" :to="`/products/${item.slug}`">
                            <div class="position-relative pa-4 bg-grey-lighten-5 d-flex align-center justify-center" style="height: 180px;">
                                <v-img :src="item.image" max-height="140" contain></v-img>
                            </div>
                            <div class="pa-4">
                                <div class="text-caption text-primary font-weight-bold mb-1">{{ item.category }}</div>
                                <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-3 mb-2 text-truncate">{{ item.title }}</h4>
                                <div class="font-weight-bold text-body-1">${{ item.price }}</div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </section>
        </v-container>
    </div>
</template>

<script>
export default {
    name: 'ProductDetailPage',
    data() {
        return {
            tab: 'overview',
            quantity: 1,
            activeImage: '',
            breadcrumbs: [
                { title: 'Home', disabled: false, href: '/' },
                { title: 'Products', disabled: false, href: '/products' },
                { title: 'UPS Systems', disabled: false, href: '/products?category=ups' },
                { title: 'Loading...', disabled: true, href: '#' },
            ],
            product: {
                title: 'Loading...',
                price: '0.00',
                images: [],
                specs: {},
                downloads: [],
                keyFeatures: []
            },
            relatedProducts: []
        };
    },
    mounted() {
        this.loadProduct();
    },
    watch: {
        '$route.params.slug': 'loadProduct'
    },
    methods: {
        loadProduct() {
            // Mock Data Loading
            const slug = this.$route.params.slug;
            const title = slug ? slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Product Title';
            
            // Simulate API response
            this.product = {
                title: title,
                sku: 'MCT-UPS-2000X',
                price: '499.00',
                oldPrice: '599.00',
                rating: 4.5,
                reviewCount: 24,
                category: 'UPS Systems',
                isNew: true,
                description: 'Professional-grade Uninterruptible Power Supply with double-conversion technology. Ensures clean, reliable power for your critical servers and networking equipment.',
                longDescription: `The ${title} is engineered for the most demanding IT environments. It features true online double-conversion topology, which provides the highest level of power protection by isolating your equipment from raw utility power. With a zero transfer time to battery, it ensures seamless operation during power outages.`,
                keyFeatures: [
                    'True Online Double-Conversion',
                    'Output Power Factor 0.9',
                    'User-Friendly LCD Display',
                    'ECO Mode for Energy Saving'
                ],
                images: [
                    'https://via.placeholder.com/600x600?text=Main+Product',
                    'https://via.placeholder.com/600x600?text=Side+View',
                    'https://via.placeholder.com/600x600?text=Back+Panel',
                    'https://via.placeholder.com/600x600?text=In+Use'
                ],
                specs: {
                    'Capacity': '2000VA / 1800W',
                    'Input Voltage': '110-300 VAC',
                    'Output Voltage': '208/220/230/240 VAC',
                    'Frequency Range': '40Hz ~ 70Hz',
                    'Battery Type': '12V / 9Ah',
                    'Recharge Time': '4 hours to 90%',
                    'Noise Level': 'Less than 50dBA',
                    'Dimensions': '190 x 318 x 421 mm'
                },
                downloads: [
                    { title: 'User Manual', type: 'PDF', size: '2.4 MB', icon: 'mdi-file-document-outline' },
                    { title: 'Datasheet', type: 'PDF', size: '1.1 MB', icon: 'mdi-file-chart-outline' },
                    { title: 'Software Driver', type: 'ZIP', size: '15 MB', icon: 'mdi-folder-zip-outline' }
                ]
            };

            this.activeImage = this.product.images[0];
            this.breadcrumbs[3].title = title;

            // Mock Related Products
            this.relatedProducts = Array.from({ length: 4 }).map((_, i) => ({
                id: i,
                title: `Power Backup Unit ${i + 1}`,
                slug: `product-${i + 1}`,
                price: (Math.random() * 300 + 100).toFixed(2),
                category: 'UPS Systems',
                image: 'https://via.placeholder.com/300x300?text=Related'
            }));
        }
    }
};
</script>

<style scoped>
.border-thin { border: 1px solid rgba(0,0,0,0.08) !important; }
.border-dashed { border-style: dashed !important; border-color: #e2e8f0 !important; }
.lh-tight { line-height: 1.2; }
.lh-relaxed { line-height: 1.7; }
.gap-2 { gap: 8px; }
.gap-4 { gap: 16px; }
.gap-6 { gap: 24px; }
.top-20 { top: 100px; } /* Sticky offset */

.main-image-card {
    height: 500px;
    background: radial-gradient(circle at center, #ffffff 0%, #f8fafc 100%);
}

.thumbnail-card {
    border: 2px solid transparent;
    opacity: 0.7;
}

.thumbnail-card:hover {
    opacity: 1;
}

.thumbnail-card.active-thumb {
    border-color: rgb(var(--v-theme-primary));
    opacity: 1;
}

.specs-table tr:not(:last-child) td {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.specs-table td {
    padding: 16px !important;
}

.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.group-hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.group-hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}
</style>
