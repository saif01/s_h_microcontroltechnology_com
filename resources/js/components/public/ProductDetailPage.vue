<template>
    <div class="product-detail-page">
        <v-container class="py-12">
            <v-row>
                <!-- Product Image -->
                <v-col cols="12" md="6">
                    <v-card class="rounded-xl overflow-hidden elevation-0 border-thin bg-grey-lighten-5 d-flex align-center justify-center" height="500">
                        <v-img 
                            src="https://via.placeholder.com/600x600?text=Product+Image" 
                            max-height="400"
                            contain
                        ></v-img>
                    </v-card>
                </v-col>

                <!-- Product Info -->
                <v-col cols="12" md="6" class="pl-md-10">
                    <div class="mb-2">
                        <v-chip color="primary" size="small" class="font-weight-bold text-uppercase mb-4">UPS Systems</v-chip>
                    </div>
                    <h1 class="text-h3 font-weight-bold text-grey-darken-3 mb-4">{{ product.title }}</h1>
                    <div class="text-h4 font-weight-black text-primary mb-6">$299.00</div>
                    
                    <p class="text-body-1 text-medium-emphasis mb-8 lh-relaxed">
                        {{ product.description }}
                    </p>

                    <div class="d-flex align-center gap-4 mb-8">
                        <v-text-field
                            v-model="quantity"
                            type="number"
                            variant="outlined"
                            density="compact"
                            hide-details
                            style="max-width: 100px;"
                            min="1"
                        ></v-text-field>
                        <v-btn color="primary" size="large" rounded="pill" class="flex-grow-1 font-weight-bold elevation-4">
                            Add to Quote
                        </v-btn>
                    </div>

                    <v-divider class="mb-8"></v-divider>

                    <div class="d-flex gap-8">
                        <div class="d-flex align-center">
                            <v-icon icon="mdi-shield-check" color="success" class="mr-2"></v-icon>
                            <span class="text-body-2 font-weight-bold">2 Year Warranty</span>
                        </div>
                        <div class="d-flex align-center">
                            <v-icon icon="mdi-truck-fast" color="primary" class="mr-2"></v-icon>
                            <span class="text-body-2 font-weight-bold">Fast Shipping</span>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <!-- Tabs: Description, Specs, Reviews -->
            <v-row class="mt-16">
                <v-col cols="12">
                    <v-card class="rounded-xl elevation-2">
                        <v-tabs v-model="tab" color="primary" align-tabs="start">
                            <v-tab value="desc">Description</v-tab>
                            <v-tab value="specs">Specifications</v-tab>
                            <v-tab value="reviews">Reviews</v-tab>
                        </v-tabs>
                        <v-card-text class="pa-8">
                            <v-window v-model="tab">
                                <v-window-item value="desc">
                                    <p class="text-body-1 text-grey-darken-2 lh-relaxed">
                                        This high-performance UPS system provides reliable power backup for your critical devices. 
                                        Featuring advanced battery management and surge protection, it ensures your equipment stays safe 
                                        and operational during power fluctuations and outages.
                                    </p>
                                </v-window-item>
                                <v-window-item value="specs">
                                    <v-table density="comfortable">
                                        <tbody>
                                            <tr>
                                                <td class="font-weight-bold text-grey-darken-2" width="200">Capacity</td>
                                                <td>1000VA / 600W</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold text-grey-darken-2">Input Voltage</td>
                                                <td>230V AC</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold text-grey-darken-2">Battery Type</td>
                                                <td>Sealed Lead-Acid</td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold text-grey-darken-2">Recharge Time</td>
                                                <td>6-8 Hours</td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-window-item>
                                <v-window-item value="reviews">
                                    <div class="text-center py-8 text-medium-emphasis">
                                        No reviews yet. Be the first to review this product!
                                    </div>
                                </v-window-item>
                            </v-window>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
export default {
    name: 'ProductDetailPage',
    data() {
        return {
            tab: 'desc',
            quantity: 1,
            product: {
                title: 'Loading...',
                description: 'Please wait while we load the product details.'
            }
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
            // Mock data loading based on slug
            const slug = this.$route.params.slug;
            const title = slug.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            
            this.product = {
                title: title,
                description: `Experience superior performance with the ${title}. Designed for efficiency and durability, this product is the perfect solution for your power needs.`
            };
        }
    }
};
</script>

<style scoped>
.border-thin { border: 1px solid rgba(0,0,0,0.08) !important; }
.lh-relaxed { line-height: 1.8; }
.gap-4 { gap: 16px; }
.gap-8 { gap: 32px; }
</style>
