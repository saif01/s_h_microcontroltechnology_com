<template>
    <div class="service-detail-page">
        <!-- Hero Section -->
        <section v-if="!loading && !error"
            class="page-hero position-relative d-flex align-center justify-center text-center">
            <div class="hero-bg-gradient"></div>
            <div class="hero-pattern"></div>
            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-4 py-2 rounded-pill mb-6">
                            <v-icon icon="mdi-cogs" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">SERVICE
                                DETAILS</span>
                        </div>
                        <h1 class="text-h4 text-lg-h3 font-weight-black text-white mb-6 lh-tight text-shadow-sm">{{
                            service.title }}</h1>
                        <p class="text-h6 text-white opacity-80 mw-800 mx-auto font-weight-light">
                            {{ service.short_description }}
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Loading State -->
        <section v-if="loading" class="py-20 bg-surface">
            <v-container>
                <div class="text-center">
                    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                    <p class="mt-4 text-body-1">Loading service details...</p>
                </div>
            </v-container>
        </section>

        <!-- Error State -->
        <section v-else-if="error" class="py-20 bg-surface">
            <v-container>
                <v-alert type="error" variant="tonal" class="mb-4">
                    {{ error }}
                </v-alert>
                <v-btn color="primary" :to="{ name: 'Services' }">Back to Services</v-btn>
            </v-container>
        </section>

        <!-- Content Section -->
        <section v-else class="py-20 bg-surface">
            <v-container>
                <v-row>
                    <v-col cols="12" md="8">
                        <div class="text-body-1 text-grey-darken-3 lh-relaxed mb-8">
                            <!-- Service Image -->
                            <div v-if="service.image" class="mb-8">
                                <v-img :src="resolveImageUrl(service.image)" max-width="100%" height="400" cover
                                    class="rounded-xl elevation-4"></v-img>
                            </div>
                            
                            <!-- Service Description -->
                            <div v-if="service.description" class="mb-6" v-html="service.description"></div>
                            <p v-else class="mb-6">
                                At Micro Control Technology, our <strong>{{ service.title }}</strong> service is
                                designed to ensure your operations never miss a beat. We understand the critical nature
                                of reliable power, which is why we offer comprehensive solutions tailored to your
                                specific needs.
                            </p>

                            <!-- Key Benefits -->
                            <div v-if="service.benefits && service.benefits.length > 0">
                                <h3 class="text-h5 font-weight-bold mb-4">Key Benefits</h3>
                                <v-list density="comfortable" class="bg-transparent mb-6">
                                    <v-list-item v-for="(benefit, i) in service.benefits" :key="i" class="px-0">
                                        <template v-slot:prepend>
                                            <v-icon color="primary" icon="mdi-check-circle" class="mr-3"></v-icon>
                                        </template>
                                        <v-list-item-title class="text-body-1">{{ benefit }}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </div>

                            <!-- Features -->
                            <div v-if="service.features && service.features.length > 0">
                                <h3 class="text-h5 font-weight-bold mb-4">Features</h3>
                                <v-list density="comfortable" class="bg-transparent mb-6">
                                    <v-list-item v-for="(feature, i) in service.features" :key="i" class="px-0">
                                        <template v-slot:prepend>
                                            <v-icon color="primary" icon="mdi-star" class="mr-3"></v-icon>
                                        </template>
                                        <v-list-item-title class="text-body-1">{{ feature }}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </div>

                            <!-- Price Range -->
                            <div v-if="service.price_range" class="mb-6">
                                <h3 class="text-h5 font-weight-bold mb-2">Pricing</h3>
                                <p class="text-body-1">{{ service.price_range }}</p>
                            </div>

                            <p>
                                Contact us today to learn more about how our {{ service.title }} can benefit your
                                business or home.
                            </p>
                        </div>
                        <v-btn color="primary" size="large" rounded="pill" :to="{ name: 'Contact' }"
                            class="font-weight-bold px-8">
                            Get a Quote for this Service
                        </v-btn>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-card class="rounded-xl pa-6 elevation-4 bg-grey-lighten-5">
                            <h4 class="text-h6 font-weight-bold mb-4">Other Services</h4>
                            <div v-if="loadingOtherServices" class="text-center py-4">
                                <v-progress-circular indeterminate color="primary" size="32"></v-progress-circular>
                            </div>
                            <v-list v-else-if="otherServices.length > 0" class="bg-transparent">
                                <v-list-item v-for="item in otherServices" :key="item.id" :to="`/services/${item.slug}`"
                                    rounded="lg" active-color="primary" class="mb-2">
                                    <template v-slot:prepend>
                                        <v-icon icon="mdi-chevron-right" size="small"></v-icon>
                                    </template>
                                    <v-list-item-title class="font-weight-medium">{{ item.title }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                            <p v-else class="text-body-2 text-medium-emphasis text-center py-4">No other services
                                available</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import { resolveUploadUrl } from '../../../utils/uploads';

export default {
    name: 'ServiceDetailPage',
    data() {
        return {
            service: {
                title: 'Loading...',
                short_description: 'Please wait while we load the service details.'
            },
            otherServices: [],
            loading: true,
            loadingOtherServices: false,
            error: null
        };
    },
    mounted() {
        this.loadService();
        this.loadOtherServices();
    },
    watch: {
        '$route.params.slug': function () {
            this.loadService();
            this.loadOtherServices();
        }
    },
    beforeUnmount() {
        this.cleanupMetaTags();
    },
    methods: {
        // Update SEO meta tags
        updateMetaTags() {
            const appName = import.meta.env.VITE_APP_NAME || 'Micro Control Technology';
            const baseUrl = window.location.origin;
            
            // Page Title
            const title = this.service.meta_title || this.service.title || 'Service';
            document.title = `${title} - ${appName}`;
            
            // Meta Description
            const description = this.service.meta_description || this.service.short_description || '';
            this.updateMetaTag('name', 'description', description);
            
            // Meta Keywords
            if (this.service.meta_keywords) {
                this.updateMetaTag('name', 'keywords', this.service.meta_keywords);
            }
            
            // Open Graph Tags
            this.updateMetaTag('property', 'og:title', title);
            this.updateMetaTag('property', 'og:description', description);
            this.updateMetaTag('property', 'og:type', 'website');
            this.updateMetaTag('property', 'og:url', `${baseUrl}${this.$route.fullPath}`);
            
            // OG Image
            const ogImage = this.service.og_image || this.service.image || '';
            if (ogImage) {
                // Ensure absolute URL
                const imageUrl = ogImage.startsWith('http') ? ogImage : `${baseUrl}${ogImage}`;
                this.updateMetaTag('property', 'og:image', imageUrl);
                this.updateMetaTag('property', 'og:image:secure_url', imageUrl);
            }
            
            // Twitter Card Tags
            this.updateMetaTag('name', 'twitter:card', 'summary_large_image');
            this.updateMetaTag('name', 'twitter:title', title);
            this.updateMetaTag('name', 'twitter:description', description);
            if (ogImage) {
                const imageUrl = ogImage.startsWith('http') ? ogImage : `${baseUrl}${ogImage}`;
                this.updateMetaTag('name', 'twitter:image', imageUrl);
            }
        },
        
        // Helper to update or create meta tag
        updateMetaTag(attr, value, content) {
            if (!content) return;
            
            const metaTag = document.querySelector(`meta[${attr}="${value}"]`);
            if (metaTag) {
                metaTag.setAttribute('content', content);
            } else {
                const tag = document.createElement('meta');
                tag.setAttribute(attr, value);
                tag.setAttribute('content', content);
                document.head.appendChild(tag);
            }
        },
        
        // Clean up meta tags when component is destroyed
        cleanupMetaTags() {
            // Reset to default title
            const appName = import.meta.env.VITE_APP_NAME || 'Micro Control Technology';
            document.title = `${appName}`;
            
            // Remove service-specific meta tags (keep base ones)
            const ogTags = ['og:title', 'og:description', 'og:image', 'og:image:secure_url', 'og:url'];
            const twitterTags = ['twitter:title', 'twitter:description', 'twitter:image'];
            
            [...ogTags, ...twitterTags].forEach(tag => {
                const meta = document.querySelector(`meta[property="${tag}"], meta[name="${tag}"]`);
                if (meta) meta.remove();
            });
        },
        
        async loadService() {
            this.loading = true;
            this.error = null;

            try {
                const slug = this.$route.params.slug;
                const response = await axios.get(`/api/openapi/services/${slug}`);
                this.service = response.data;

                // Update SEO meta tags after service loads
                this.$nextTick(() => {
                    this.updateMetaTags();
                });
            } catch (error) {
                console.error('Error loading service:', error);

                if (error.response && error.response.status === 404) {
                    this.error = 'Service not found. The service you are looking for does not exist or is not published.';
                } else {
                    this.error = 'Failed to load service details. Please try again later.';
                }

                // Reset service to default state
                this.service = {
                    title: 'Service Not Found',
                    short_description: 'The requested service could not be found.'
                };
            } finally {
                this.loading = false;
            }
        },
        async loadOtherServices() {
            this.loadingOtherServices = true;

            try {
                const response = await axios.get('/api/openapi/services');
                const allServices = response.data || [];
                const currentSlug = this.$route.params.slug;

                // Filter out current service and limit to 5
                this.otherServices = allServices
                    .filter(s => s.slug !== currentSlug)
                    .slice(0, 5);
            } catch (error) {
                console.error('Error loading other services:', error);
                this.otherServices = [];
            } finally {
                this.loadingOtherServices = false;
            }
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        }
    }
};
</script>

<style scoped>
.page-hero {
    height: 300px;
}

/* Hero, glass-pill, and utility classes moved to app.css */

/* Responsive Styles */
@media (max-width: 960px) {
    .page-hero {
        height: 250px;
        padding: 40px 0;
    }
}

@media (max-width: 600px) {
    .page-hero {
        height: 200px;
        padding: 30px 0;
    }
}
</style>
