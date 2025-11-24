<template>
    <div class="product-detail-page bg-grey-lighten-5 min-vh-100 pb-16">
        <!-- Hero Section -->
        <section class="product-hero position-relative d-flex align-center overflow-hidden">
            <div class="hero-background"></div>
            <div class="hero-overlay"></div>
            
            <v-container class="position-relative z-index-2">
                <v-row align="center">
                    <v-col cols="12" md="8">
                        <v-breadcrumbs :items="breadcrumbs" class="px-0 mb-4">
                            <template v-slot:divider>
                                <v-icon icon="mdi-chevron-right" size="small" color="white"></v-icon>
                            </template>
                            <template v-slot:title="{ item }">
                                <span :class="item.disabled ? 'text-grey-lighten-2' : 'text-white'">
                                    {{ item.title }}
                                </span>
                            </template>
                        </v-breadcrumbs>
                        
                        <div class="d-flex align-center gap-3 mb-4">
                            <v-chip 
                                v-if="product.featured"
                                color="amber-accent-4"
                                variant="flat"
                                size="small"
                                class="font-weight-bold"
                            >
                                FEATURED
                            </v-chip>
                            <v-chip 
                                color="white"
                                variant="flat"
                                size="small"
                                class="font-weight-bold text-primary"
                            >
                                {{ getCategoryName(product) }}
                            </v-chip>
                        </div>
                        
                        <h1 class="text-h3 text-md-h1 font-weight-black text-white mb-4">
                            {{ product.title }}
                        </h1>
                        
                        <p class="text-h6 text-grey-lighten-1 opacity-90 mb-6">
                            {{ product.short_description || product.description }}
                        </p>
                        
                        <div class="d-flex align-center gap-6 flex-wrap">
                            <div v-if="product.rating" class="d-flex align-center">
                                <v-rating
                                    :model-value="product.rating"
                                    color="amber"
                                    density="compact"
                                    half-increments
                                    readonly
                                    size="small"
                                ></v-rating>
                                <span class="text-body-2 text-white ml-2">
                                    ({{ product.reviewCount || 0 }} Reviews)
                                </span>
                            </div>
                            <div class="d-flex align-center text-white">
                                <v-icon icon="mdi-barcode" size="small" class="mr-2"></v-icon>
                                <span class="text-body-2">SKU: {{ product.sku || 'N/A' }}</span>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="4" class="text-center">
                        <div class="hero-price-card pa-6 rounded-xl bg-white elevation-8">
                            <div class="text-caption text-medium-emphasis mb-2">Starting From</div>
                            <div class="text-h3 font-weight-black text-primary mb-4">
                                {{ formatPrice(product) }}
                            </div>
                            <v-btn 
                                color="primary" 
                                size="large" 
                                rounded="lg" 
                                class="w-100 font-weight-bold elevation-2"
                                prepend-icon="mdi-file-document-edit-outline"
                            >
                                Request Quote
                            </v-btn>
                            <v-btn 
                                variant="text" 
                                color="primary" 
                                class="w-100 mt-2"
                                prepend-icon="mdi-download"
                            >
                                Download Datasheet
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <v-container class="mt-8">
            <v-row>
                <!-- Product Gallery -->
                <v-col cols="12" md="6">
                    <div class="gallery-container position-sticky top-20">
                        <v-card class="main-image-card rounded-xl overflow-hidden elevation-2 mb-4 bg-white d-flex align-center justify-center position-relative">
                            <v-chip
                                v-if="product.featured"
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
                                @click="showImageZoom = true"
                            ></v-btn>
                        </v-card>
                        
                        <div class="d-flex gap-3 overflow-x-auto py-2 hide-scrollbar">
                            <div 
                                v-for="(img, i) in productImages" 
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
                <v-col cols="12" md="6" class="pl-md-6">
                    <div class="product-info">
                        <!-- Price Block -->
                        <div class="price-block mb-6 pa-5 bg-white rounded-xl border-thin elevation-1">
                            <div class="d-flex align-end lh-1 mb-2">
                                <span class="text-h3 font-weight-black text-primary">{{ formatPrice(product) }}</span>
                                <span v-if="product.oldPrice" class="text-h6 text-medium-emphasis text-decoration-line-through ml-3 mb-1">
                                    ${{ product.oldPrice }}
                                </span>
                            </div>
                            <div class="d-flex align-center gap-4 flex-wrap mt-4">
                                <v-btn 
                                    color="primary" 
                                    size="large" 
                                    rounded="lg" 
                                    class="flex-grow-1 font-weight-bold elevation-2"
                                    prepend-icon="mdi-file-document-edit-outline"
                                >
                                    Request Quote
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

                        <!-- Key Features -->
                        <v-card class="mb-6 rounded-xl elevation-1">
                            <v-card-title class="bg-primary text-white">
                                <v-icon icon="mdi-star-circle" class="mr-2"></v-icon>
                                Key Features
                            </v-card-title>
                            <v-card-text class="pa-4">
                                <div v-if="keyFeatures.length > 0">
                                    <div v-for="(feature, i) in keyFeatures" :key="i" class="d-flex align-center mb-3">
                                        <v-icon icon="mdi-check-circle" color="success" size="small" class="mr-3"></v-icon>
                                        <span class="text-body-1 font-weight-medium text-grey-darken-2">{{ feature }}</span>
                                    </div>
                                </div>
                                <div v-else class="text-body-2 text-medium-emphasis">
                                    No features listed.
                                </div>
                            </v-card-text>
                        </v-card>

                        <!-- Quick Specs -->
                        <v-card class="mb-6 rounded-xl elevation-1">
                            <v-card-title class="bg-grey-lighten-4">
                                <v-icon icon="mdi-information" class="mr-2"></v-icon>
                                Quick Specifications
                            </v-card-title>
                            <v-card-text class="pa-0">
                                <v-list lines="two" density="compact">
                                    <v-list-item 
                                        v-for="(value, key) in quickSpecs" 
                                        :key="key"
                                        class="border-bottom"
                                    >
                                        <v-list-item-title class="text-caption text-medium-emphasis">
                                            {{ formatSpecLabel(key) }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle class="font-weight-bold">
                                            {{ value }}
                                        </v-list-item-subtitle>
                                    </v-list-item>
                                </v-list>
                            </v-card-text>
                        </v-card>

                        <!-- Trust Badges -->
                        <div class="trust-badges d-flex gap-4 flex-wrap">
                            <div class="trust-badge d-flex align-center pa-3 bg-white rounded-lg border-thin flex-grow-1">
                                <v-icon icon="mdi-shield-check-outline" color="primary" size="large" class="mr-3"></v-icon>
                                <div>
                                    <div class="text-caption font-weight-bold text-grey-darken-3">Warranty</div>
                                    <div class="text-caption text-medium-emphasis">{{ warrantyInfo.period }}</div>
                                </div>
                            </div>
                            <div class="trust-badge d-flex align-center pa-3 bg-white rounded-lg border-thin flex-grow-1">
                                <v-icon icon="mdi-truck-fast-outline" color="primary" size="large" class="mr-3"></v-icon>
                                <div>
                                    <div class="text-caption font-weight-bold text-grey-darken-3">Delivery</div>
                                    <div class="text-caption text-medium-emphasis">Fast Shipping</div>
                                </div>
                            </div>
                            <div class="trust-badge d-flex align-center pa-3 bg-white rounded-lg border-thin flex-grow-1">
                                <v-icon icon="mdi-headset" color="primary" size="large" class="mr-3"></v-icon>
                                <div>
                                    <div class="text-caption font-weight-bold text-grey-darken-3">Support</div>
                                    <div class="text-caption text-medium-emphasis">24/7 Available</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <!-- Detailed Content Tabs -->
            <v-row class="mt-8">
                <v-col cols="12">
                    <v-card class="rounded-xl elevation-2 overflow-hidden">
                        <v-tabs v-model="tab" color="primary" align-tabs="start" bg-color="grey-lighten-4">
                            <v-tab value="overview" class="font-weight-bold text-capitalize">
                                <v-icon icon="mdi-information-outline" class="mr-2"></v-icon>
                                Overview
                            </v-tab>
                            <v-tab value="specs" class="font-weight-bold text-capitalize">
                                <v-icon icon="mdi-cog-outline" class="mr-2"></v-icon>
                                Technical Specs
                            </v-tab>
                            <v-tab value="features" class="font-weight-bold text-capitalize">
                                <v-icon icon="mdi-star-outline" class="mr-2"></v-icon>
                                Features
                            </v-tab>
                            <v-tab value="downloads" class="font-weight-bold text-capitalize">
                                <v-icon icon="mdi-download-outline" class="mr-2"></v-icon>
                                Downloads
                            </v-tab>
                            <v-tab value="faq" class="font-weight-bold text-capitalize">
                                <v-icon icon="mdi-help-circle-outline" class="mr-2"></v-icon>
                                FAQs
                            </v-tab>
                            <v-tab value="warranty" class="font-weight-bold text-capitalize">
                                <v-icon icon="mdi-shield-check-outline" class="mr-2"></v-icon>
                                Warranty & Service
                            </v-tab>
                        </v-tabs>
                        <v-divider></v-divider>
                        <v-card-text class="pa-8 bg-white">
                            <v-window v-model="tab">
                                <!-- Overview Tab -->
                                <v-window-item value="overview">
                                    <div class="mw-900">
                                        <h3 class="text-h5 font-weight-bold mb-4 text-grey-darken-3">Product Overview</h3>
                                        <div class="text-body-1 text-grey-darken-1 lh-relaxed mb-6" v-html="formattedDescription"></div>
                                        
                                        <h4 class="text-h6 font-weight-bold mb-4 text-grey-darken-3">Why Choose This Product?</h4>
                                        <v-row>
                                            <v-col cols="12" md="6" v-for="(benefit, i) in productBenefits" :key="i">
                                                <div class="d-flex align-start mb-3">
                                                    <v-icon icon="mdi-check-circle" color="success" class="mr-3 mt-1"></v-icon>
                                                    <div>
                                                        <div class="font-weight-bold text-grey-darken-2 mb-1">{{ benefit.title }}</div>
                                                        <div class="text-body-2 text-medium-emphasis">{{ benefit.description }}</div>
                                                    </div>
                                                </div>
                                            </v-col>
                                        </v-row>
                                    </div>
                                </v-window-item>

                                <!-- Technical Specs Tab -->
                                <v-window-item value="specs">
                                    <div>
                                        <h3 class="text-h5 font-weight-bold mb-6 text-grey-darken-3">Technical Specifications</h3>
                                        <v-table density="comfortable" class="specs-table">
                                            <tbody>
                                                <tr v-for="(value, key) in allSpecifications" :key="key">
                                                    <td class="font-weight-bold text-grey-darken-2 bg-grey-lighten-5" width="300">
                                                        {{ formatSpecLabel(key) }}
                                                    </td>
                                                    <td class="text-grey-darken-3">{{ value }}</td>
                                                </tr>
                                            </tbody>
                                        </v-table>
                                    </div>
                                </v-window-item>

                                <!-- Features Tab -->
                                <v-window-item value="features">
                                    <div>
                                        <h3 class="text-h5 font-weight-bold mb-6 text-grey-darken-3">Product Features</h3>
                                        <v-row>
                                            <v-col 
                                                cols="12" 
                                                md="6" 
                                                v-for="(feature, i) in detailedFeatures" 
                                                :key="i"
                                            >
                                                <v-card class="feature-card pa-4 rounded-lg border-thin mb-4">
                                                    <div class="d-flex align-start">
                                                        <v-icon :icon="feature.icon || 'mdi-check-circle'" color="primary" class="mr-3 mt-1"></v-icon>
                                                        <div>
                                                            <div class="font-weight-bold text-grey-darken-2 mb-2">{{ feature.title }}</div>
                                                            <div class="text-body-2 text-medium-emphasis">{{ feature.description }}</div>
                                                        </div>
                                                    </div>
                                                </v-card>
                                            </v-col>
                                        </v-row>
                                    </div>
                                </v-window-item>
                                
                                <!-- Downloads Tab -->
                                <v-window-item value="downloads">
                                    <div>
                                        <h3 class="text-h5 font-weight-bold mb-6 text-grey-darken-3">Product Downloads</h3>
                                        <v-list lines="two" class="bg-transparent">
                                            <v-list-item 
                                                v-for="(doc, idx) in downloadableFiles" 
                                                :key="idx"
                                                rounded="lg"
                                                class="mb-3 border-thin bg-grey-lighten-5"
                                            >
                                                <template v-slot:prepend>
                                                    <v-avatar color="primary-lighten-5" size="48" class="text-primary">
                                                        <v-icon :icon="getFileIcon(doc.type)" size="large"></v-icon>
                                                    </v-avatar>
                                                </template>
                                                <v-list-item-title class="font-weight-bold">{{ doc.title }}</v-list-item-title>
                                                <v-list-item-subtitle>
                                                    {{ doc.size || 'N/A' }} • {{ doc.type || 'Document' }}
                                                </v-list-item-subtitle>
                                                <template v-slot:append>
                                                    <v-btn 
                                                        variant="text" 
                                                        color="primary" 
                                                        icon="mdi-download"
                                                        @click="downloadFile(doc)"
                                                    ></v-btn>
                                                </template>
                                            </v-list-item>
                                        </v-list>
                                    </div>
                                </v-window-item>

                                <!-- FAQs Tab -->
                                <v-window-item value="faq">
                                    <div>
                                        <h3 class="text-h5 font-weight-bold mb-6 text-grey-darken-3">Frequently Asked Questions</h3>
                                        <v-expansion-panels variant="accordion" class="faq-panels">
                                            <v-expansion-panel 
                                                v-for="(faq, i) in productFAQs" 
                                                :key="i"
                                                class="mb-2"
                                            >
                                                <v-expansion-panel-title class="font-weight-bold">
                                                    {{ faq.question }}
                                                </v-expansion-panel-title>
                                                <v-expansion-panel-text class="text-body-1 text-medium-emphasis">
                                                    {{ faq.answer }}
                                                </v-expansion-panel-text>
                                            </v-expansion-panel>
                                        </v-expansion-panels>
                                    </div>
                                </v-window-item>

                                <!-- Warranty & Service Tab -->
                                <v-window-item value="warranty">
                                    <div>
                                        <h3 class="text-h5 font-weight-bold mb-6 text-grey-darken-3">Warranty & Service Information</h3>
                                        
                                        <v-card class="mb-6 rounded-lg border-thin">
                                            <v-card-title class="bg-primary text-white">
                                                <v-icon icon="mdi-shield-check" class="mr-2"></v-icon>
                                                Warranty Coverage
                                            </v-card-title>
                                            <v-card-text class="pa-6">
                                                <div class="mb-4">
                                                    <div class="text-h6 font-weight-bold mb-2">Warranty Period</div>
                                                    <div class="text-body-1">{{ warrantyInfo.period }}</div>
                                                </div>
                                                <div class="mb-4">
                                                    <div class="text-h6 font-weight-bold mb-2">What's Covered</div>
                                                    <ul class="pl-4">
                                                        <li v-for="(item, i) in warrantyInfo.coverage" :key="i" class="mb-2">
                                                            {{ item }}
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div>
                                                    <div class="text-h6 font-weight-bold mb-2">Terms & Conditions</div>
                                                    <div class="text-body-2 text-medium-emphasis">{{ warrantyInfo.terms }}</div>
                                                </div>
                                            </v-card-text>
                                        </v-card>

                                        <v-card class="rounded-lg border-thin">
                                            <v-card-title class="bg-grey-lighten-4">
                                                <v-icon icon="mdi-tools" class="mr-2"></v-icon>
                                                Service & Support
                                            </v-card-title>
                                            <v-card-text class="pa-6">
                                                <v-row>
                                                    <v-col cols="12" md="6" v-for="(service, i) in serviceInfo" :key="i">
                                                        <div class="d-flex align-start mb-4">
                                                            <v-icon :icon="service.icon" color="primary" size="large" class="mr-3"></v-icon>
                                                            <div>
                                                                <div class="font-weight-bold text-grey-darken-2 mb-1">{{ service.title }}</div>
                                                                <div class="text-body-2 text-medium-emphasis">{{ service.description }}</div>
                                                            </div>
                                                        </div>
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                        </v-card>
                                    </div>
                                </v-window-item>
                            </v-window>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Related Products -->
            <section class="mt-12">
                <div class="d-flex align-center justify-space-between mb-8">
                    <h2 class="text-h4 font-weight-bold text-grey-darken-3">Related Products</h2>
                    <v-btn variant="text" color="primary" append-icon="mdi-arrow-right" :to="'/products'">
                        View All Products
                    </v-btn>
                </div>
                
                <v-row>
                    <v-col v-for="item in relatedProducts" :key="item.id" cols="12" sm="6" md="3">
                        <v-card 
                            class="h-100 rounded-lg border-thin elevation-0 bg-white group-hover-card" 
                            :to="`/products/${item.slug}`"
                        >
                            <div class="position-relative pa-4 bg-grey-lighten-5 d-flex align-center justify-center" style="height: 180px;">
                                <v-img :src="getProductImage(item)" max-height="140" contain></v-img>
                            </div>
                            <div class="pa-4">
                                <div class="text-caption text-primary font-weight-bold mb-1">{{ getCategoryName(item) }}</div>
                                <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-3 mb-2 text-truncate">{{ item.title }}</h4>
                                <div class="font-weight-bold text-body-1">{{ formatPrice(item) }}</div>
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </section>
        </v-container>

        <!-- Image Zoom Dialog -->
        <v-dialog v-model="showImageZoom" max-width="1200">
            <v-card class="rounded-xl">
                <v-card-actions class="pa-2">
                    <v-spacer></v-spacer>
                    <v-btn icon="mdi-close" variant="text" @click="showImageZoom = false"></v-btn>
                </v-card-actions>
                <v-card-text class="pa-0">
                    <v-img :src="activeImage" max-height="80vh" contain></v-img>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ProductDetailPage',
    data() {
        return {
            tab: 'overview',
            activeImage: '',
            showImageZoom: false,
            product: {
                title: 'Loading...',
                price: '0.00',
                images: [],
                specifications: {},
                downloads: [],
                key_features: []
            },
            relatedProducts: [],
            breadcrumbs: [
                { title: 'Home', disabled: false, href: '/' },
                { title: 'Products', disabled: false, href: '/products' },
                { title: 'Loading...', disabled: true, href: '#' },
            ],
            warrantyInfo: {
                period: '2 Years',
                coverage: [
                    'Manufacturing defects',
                    'Component failures',
                    'Normal wear and tear',
                    'Technical support'
                ],
                terms: 'Warranty is valid from the date of purchase. Original purchase receipt required. Warranty does not cover damage caused by misuse, accidents, or unauthorized modifications.'
            },
            serviceInfo: [
                {
                    icon: 'mdi-headset',
                    title: '24/7 Technical Support',
                    description: 'Round-the-clock assistance for all your technical queries and troubleshooting needs.'
                },
                {
                    icon: 'mdi-tools',
                    title: 'On-Site Service',
                    description: 'Professional technicians available for on-site installation and maintenance.'
                },
                {
                    icon: 'mdi-truck-fast',
                    title: 'Fast Replacement',
                    description: 'Quick replacement service for defective units under warranty.'
                },
                {
                    icon: 'mdi-school',
                    title: 'Training & Documentation',
                    description: 'Comprehensive training materials and documentation for optimal product usage.'
                }
            ]
        };
    },
    computed: {
        productImages() {
            if (this.product.images && Array.isArray(this.product.images) && this.product.images.length > 0) {
                return this.product.images;
            }
            if (this.product.thumbnail) {
                return [this.product.thumbnail];
            }
            return ['https://via.placeholder.com/600x600?text=Product'];
        },
        keyFeatures() {
            if (this.product.key_features && Array.isArray(this.product.key_features)) {
                return this.product.key_features;
            }
            // Extract from description or use defaults
            return [
                'High Performance',
                'Reliable Design',
                'Energy Efficient',
                'Easy Installation'
            ];
        },
        quickSpecs() {
            const specs = {};
            if (this.product.specifications) {
                const quickKeys = ['capacity', 'input', 'output', 'type', 'voltage', 'power'];
                quickKeys.forEach(key => {
                    if (this.product.specifications[key]) {
                        specs[key] = this.product.specifications[key];
                    }
                });
            }
            return Object.keys(specs).length > 0 ? specs : {
                'Type': 'Industrial Grade',
                'Category': this.getCategoryName(this.product),
                'Warranty': '2 Years'
            };
        },
        allSpecifications() {
            return this.product.specifications || {};
        },
        formattedDescription() {
            if (this.product.description) {
                return this.product.description.replace(/\n/g, '<br>');
            }
            return this.product.short_description || 'No description available.';
        },
        productBenefits() {
            return [
                {
                    title: 'Superior Performance',
                    description: 'Engineered for maximum efficiency and reliability in demanding environments.'
                },
                {
                    title: 'Advanced Technology',
                    description: 'Incorporates the latest innovations for optimal performance and longevity.'
                },
                {
                    title: 'Easy Integration',
                    description: 'Designed to seamlessly integrate with your existing infrastructure.'
                },
                {
                    title: 'Comprehensive Support',
                    description: 'Backed by expert technical support and comprehensive documentation.'
                }
            ];
        },
        detailedFeatures() {
            const features = [];
            if (this.product.key_features && Array.isArray(this.product.key_features)) {
                this.product.key_features.forEach((feature, idx) => {
                    features.push({
                        title: feature,
                        description: `This product includes ${feature.toLowerCase()} for enhanced performance and reliability.`,
                        icon: 'mdi-check-circle'
                    });
                });
            }
            if (features.length === 0) {
                return [
                    { title: 'High Performance', description: 'Optimized for maximum output and efficiency.', icon: 'mdi-speedometer' },
                    { title: 'Reliable Design', description: 'Built to last with quality components.', icon: 'mdi-shield-check' },
                    { title: 'Energy Efficient', description: 'Designed to minimize power consumption.', icon: 'mdi-lightning-bolt' },
                    { title: 'Easy Installation', description: 'Simple setup process with clear instructions.', icon: 'mdi-tools' }
                ];
            }
            return features;
        },
        downloadableFiles() {
            if (this.product.downloads && Array.isArray(this.product.downloads) && this.product.downloads.length > 0) {
                return this.product.downloads.map(doc => ({
                    title: doc.title || doc.name || 'Document',
                    type: doc.type || 'PDF',
                    size: doc.size || 'N/A',
                    url: doc.url || doc.path || '#'
                }));
            }
            // Default downloads
            return [
                { title: 'Product Datasheet', type: 'PDF', size: '1.2 MB', url: '#' },
                { title: 'User Manual', type: 'PDF', size: '2.4 MB', url: '#' },
                { title: 'Installation Guide', type: 'PDF', size: '800 KB', url: '#' }
            ];
        },
        productFAQs() {
            // Default FAQs - can be extended from product data
            return [
                {
                    question: 'What is the warranty period for this product?',
                    answer: 'This product comes with a 2-year warranty covering manufacturing defects and component failures. Please refer to the warranty section for complete details.'
                },
                {
                    question: 'How do I install this product?',
                    answer: 'Installation instructions are included in the user manual. For complex installations, we recommend contacting our technical support team or scheduling an on-site service.'
                },
                {
                    question: 'What are the power requirements?',
                    answer: 'Power requirements vary by model. Please refer to the technical specifications table for detailed electrical requirements.'
                },
                {
                    question: 'Is technical support available?',
                    answer: 'Yes, we provide 24/7 technical support via phone, email, and live chat. Our support team is ready to assist with any questions or issues.'
                },
                {
                    question: 'Can I get a custom quote?',
                    answer: 'Absolutely! Click the "Request Quote" button to submit your requirements, and our sales team will provide a customized quote within 24 hours.'
                }
            ];
        }
    },
    mounted() {
        this.loadProduct();
    },
    watch: {
        '$route.params.slug': 'loadProduct'
    },
    methods: {
        async loadProduct() {
            const slug = this.$route.params.slug;
            try {
                const response = await axios.get(`/api/openapi/products/${slug}`);
                this.product = response.data || {};
                this.activeImage = this.productImages[0];
                this.breadcrumbs[2].title = this.product.title;
                await this.loadRelatedProducts();
            } catch (error) {
                console.error('Error loading product:', error);
                // Fallback mock data
                const title = slug ? slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Product Title';
                this.product = {
                    title: title,
                    sku: 'MCT-UPS-2000X',
                    price: '499.00',
                    oldPrice: '599.00',
                    rating: 4.5,
                    reviewCount: 24,
                    featured: true,
                    short_description: 'Professional-grade Uninterruptible Power Supply with double-conversion technology.',
                    description: `The ${title} is engineered for the most demanding IT environments. It features true online double-conversion topology, which provides the highest level of power protection by isolating your equipment from raw utility power.`,
                    images: [
                        'https://via.placeholder.com/600x600?text=Main+Product',
                        'https://via.placeholder.com/600x600?text=Side+View',
                        'https://via.placeholder.com/600x600?text=Back+Panel'
                    ],
                    specifications: {
                        'Capacity': '2000VA / 1800W',
                        'Input Voltage': '110-300 VAC',
                        'Output Voltage': '208/220/230/240 VAC',
                        'Frequency Range': '40Hz ~ 70Hz',
                        'Battery Type': '12V / 9Ah',
                        'Recharge Time': '4 hours to 90%',
                        'Noise Level': 'Less than 50dBA',
                        'Dimensions': '190 x 318 x 421 mm',
                        'Weight': '15.5 kg'
                    },
                    key_features: [
                        'True Online Double-Conversion',
                        'Output Power Factor 0.9',
                        'User-Friendly LCD Display',
                        'ECO Mode for Energy Saving'
                    ],
                    categories: [{ id: 1, name: 'UPS Systems' }]
                };
                this.activeImage = this.productImages[0];
                this.breadcrumbs[2].title = title;
            }
        },
        async loadRelatedProducts() {
            try {
                const response = await axios.get('/api/openapi/products');
                const allProducts = response.data || [];
                // Filter out current product and get related ones
                this.relatedProducts = allProducts
                    .filter(p => p.id !== this.product.id && p.slug !== this.product.slug)
                    .slice(0, 4);
            } catch (error) {
                console.error('Error loading related products:', error);
                this.relatedProducts = [];
            }
        },
        formatPrice(product) {
            if (product.price_range) return product.price_range;
            if (product.price) return `$${parseFloat(product.price).toFixed(2)}`;
            return 'Contact for Price';
        },
        formatSpecLabel(key) {
            return key
                .replace(/_/g, ' ')
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        },
        getCategoryName(product) {
            if (product.categories && product.categories.length > 0) {
                return product.categories[0].name;
            }
            return 'Uncategorized';
        },
        getProductImage(product) {
            if (product.thumbnail) return product.thumbnail;
            if (product.images && product.images.length > 0) return product.images[0];
            return 'https://via.placeholder.com/300x300?text=Product';
        },
        getFileIcon(type) {
            const iconMap = {
                'PDF': 'mdi-file-pdf-box',
                'ZIP': 'mdi-folder-zip',
                'DOC': 'mdi-file-word',
                'DOCX': 'mdi-file-word',
                'XLS': 'mdi-file-excel',
                'XLSX': 'mdi-file-excel'
            };
            return iconMap[type?.toUpperCase()] || 'mdi-file-document-outline';
        },
        downloadFile(doc) {
            if (doc.url && doc.url !== '#') {
                window.open(doc.url, '_blank');
            } else {
                // Handle download logic
                console.log('Download:', doc);
            }
        }
    }
};
</script>

<style scoped>
.product-detail-page {
    background: #f8fafc;
}

/* Hero Section */
.product-hero {
    min-height: 400px;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 60px 0;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 50% 50%, #1e293b 0%, #0f172a 100%);
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, transparent 100%);
}

.hero-price-card {
    border: 2px solid rgba(var(--v-theme-primary), 0.2);
}

/* Gallery */
.gallery-container {
    position: sticky;
    top: 100px;
}

.main-image-card {
    height: 500px;
    background: radial-gradient(circle at center, #ffffff 0%, #f8fafc 100%);
}

.thumbnail-card {
    border: 2px solid transparent;
    opacity: 0.7;
    cursor: pointer;
}

.thumbnail-card:hover {
    opacity: 1;
}

.thumbnail-card.active-thumb {
    border-color: rgb(var(--v-theme-primary));
    opacity: 1;
}

/* Specs Table */
.specs-table tr:not(:last-child) td {
    border-bottom: 1px solid rgba(0,0,0,0.08);
}

.specs-table td {
    padding: 16px !important;
}

/* Features */
.feature-card {
    transition: all 0.2s ease;
}

.feature-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

/* FAQs */
.faq-panels {
    background: transparent;
}

/* Trust Badges */
.trust-badge {
    transition: all 0.2s ease;
}

.trust-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

/* Related Products */
.group-hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.group-hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

/* Utilities */
.border-thin {
    border: 1px solid rgba(0,0,0,0.08) !important;
}

.lh-relaxed {
    line-height: 1.7;
}

.mw-900 {
    max-width: 900px;
}

.gap-3 {
    gap: 12px;
}

.gap-4 {
    gap: 16px;
}

.gap-6 {
    gap: 24px;
}

.z-index-2 {
    z-index: 2;
}

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Responsive */
@media (max-width: 960px) {
    .product-hero {
        min-height: 300px;
        padding: 40px 0;
    }
    
    .gallery-container {
        position: static;
    }
    
    .main-image-card {
        height: 400px;
    }
}

@media (max-width: 600px) {
    .product-hero {
        min-height: 250px;
        padding: 30px 0;
    }
    
    .main-image-card {
        height: 300px;
    }
}
</style>