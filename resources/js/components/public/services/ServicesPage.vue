<template>
    <div class="services-page">
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
                            <v-icon icon="mdi-cogs" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">OUR EXPERTISE</span>
                        </div>
                        <h1
                            class="text-h4 text-lg-h2 font-weight-black text-white mb-6 lh-tight text-shadow-sm animate-slide-up">
                            Comprehensive Power Solutions
                        </h1>
                        <p
                            class="text-h6 text-white opacity-90 mw-700 mx-auto font-weight-light animate-slide-up-delay">
                            From installation to maintenance, we provide end-to-end technical support to keep your
                            operations running smoothly.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Services Grid -->
        <section class="py-20 bg-grey-lighten-5 position-relative">
            <div class="bg-pattern-dots"></div>
            <v-container class="position-relative z-index-1">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-12">
                    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                    <p class="mt-4 text-body-1">Loading services...</p>
                </div>

                <!-- Error State -->
                <v-alert v-else-if="error" type="warning" variant="tonal" class="mb-4">
                    {{ error }}
                </v-alert>

                <!-- Empty State -->
                <div v-else-if="services.length === 0" class="text-center py-12">
                    <v-icon icon="mdi-information-outline" size="64" color="grey-lighten-1" class="mb-4"></v-icon>
                    <h3 class="text-h5 mb-2">No services available</h3>
                    <p class="text-body-1 text-medium-emphasis">Check back soon for our latest service offerings.</p>
                </div>

                <!-- Services Grid -->
                <v-row v-else>
                    <v-col v-for="(service, i) in services" :key="service.id" cols="12" md="6">
                        <v-hover v-slot="{ isHovering, props }">
                            <v-card v-bind="props" :elevation="isHovering ? 12 : 2"
                                class="h-100 service-card-detailed rounded-xl transition-all overflow-hidden"
                                :to="`/services/${service.slug}`">
                                <div class="d-flex flex-column flex-md-row h-100">
                                    <!-- Service Image or Icon -->
                                    <div
                                        class="service-icon-side bg-primary-darken-1 d-flex align-center justify-center pa-6 position-relative overflow-hidden">
                                        <v-img v-if="service.image" :src="resolveImageUrl(service.image)" cover
                                            class="service-image position-absolute w-100 h-100">
                                            <template v-slot:placeholder>
                                                <div class="d-flex align-center justify-center fill-height">
                                                    <v-icon :icon="service.icon || getServiceIcon(i)" size="48"
                                                        color="amber-accent-4"></v-icon>
                                                </div>
                                            </template>
                                        </v-img>
                                        <v-icon v-else :icon="service.icon || getServiceIcon(i)" size="48"
                                            color="amber-accent-4"></v-icon>
                                    </div>
                                    <div class="pa-8 flex-grow-1 bg-white">
                                        <h3 class="text-h5 font-weight-bold mb-3 text-grey-darken-3">{{ service.title }}
                                        </h3>
                                        <p class="text-body-1 text-medium-emphasis mb-6 lh-relaxed">
                                            {{ service.short_description || defaultShortDescription }}
                                        </p>
                                        <div class="d-flex align-center">
                                            <span class="text-primary font-weight-bold link-hover-effect">
                                                Explore Service <v-icon icon="mdi-arrow-right" size="small"
                                                    class="ml-1"></v-icon>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </v-card>
                        </v-hover>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Our Process -->
        <section class="py-20 bg-surface">
            <v-container>
                <div class="text-center mb-16">
                    <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">HOW WE WORK</div>
                    <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">Our Process</h2>
                </div>

                <v-row class="position-relative process-timeline">
                    <!-- Connecting Line (Desktop) -->
                    <div class="d-none d-md-block process-line"></div>

                    <v-col v-for="(step, i) in process" :key="i" cols="12" md="4"
                        class="text-center position-relative z-index-2">
                        <div
                            class="process-step mb-6 mx-auto bg-white rounded-circle elevation-6 d-flex align-center justify-center text-h4 font-weight-black text-primary border-thin">
                            {{ i + 1 }}
                        </div>
                        <h4 class="text-h5 font-weight-bold mb-3">{{ step.title }}</h4>
                        <p class="text-body-1 text-medium-emphasis px-4">{{ step.desc }}</p>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA Section -->
        <section class="py-16 cta-section position-relative overflow-hidden">
            <div class="cta-pattern"></div>
            <v-container class="position-relative z-index-2 text-center">
                <h2 class="text-h3 font-weight-bold cta-heading mb-6">Need a Custom Solution?</h2>
                <p class="text-h6 text-white opacity-80 mb-8 font-weight-light">
                    We understand that every business is unique. Contact us for a tailored power strategy.
                </p>
                <v-btn size="x-large" color="amber-accent-4" variant="flat" rounded="pill"
                    class="px-10 font-weight-bold text-black hover-lift" :to="{ name: 'Contact' }">
                    Get a Free Consultation
                </v-btn>
            </v-container>
        </section>
    </div>
</template>

<script>
import { resolveUploadUrl } from '../../../utils/uploads';

export default {
    name: 'ServicesPage',
    data() {
        return {
            services: [],
            loading: true,
            error: null,
            process: [
                {
                    title: 'Consultation',
                    desc: 'We assess your power needs and current infrastructure to recommend the best solution.'
                },
                {
                    title: 'Installation',
                    desc: 'Our certified technicians handle the installation with precision and adherence to safety standards.'
                },
                {
                    title: 'Support',
                    desc: 'We provide ongoing maintenance and 24/7 support to ensure your systems never fail.'
                }
            ],
            defaultShortDescription: 'From installation to maintenance, we provide end-to-end technical support to keep your operations running smoothly.',
            defaultTitle: 'Comprehensive Power Solutions',
            defaultDescription: 'From installation to maintenance, we provide end-to-end technical support to keep your operations running smoothly.',
            defaultButtonText: 'Explore Service',
            defaultButtonLink: '/services',
            defaultOverline: 'OUR EXPERTISE',
        };
    },
    async mounted() {
        await this.loadServices();
        this.updateMetaTags();
    },
    beforeUnmount() {
        this.cleanupMetaTags();
    },
    methods: {
        // Update SEO meta tags for services page
        updateMetaTags() {
            const appName = import.meta.env.VITE_APP_NAME || 'Micro Control Technology';
            const baseUrl = window.location.origin;

            // Page Title
            document.title = `Services - ${appName}`;

            // Meta Description
            const description = 'Explore our comprehensive power solutions including UPS systems, backup generators, and professional maintenance services.';
            this.updateMetaTag('name', 'description', description);

            // Meta Keywords
            const keywords = 'power solutions, UPS systems, backup generators, power maintenance, electrical services, Micro Control Technology';
            this.updateMetaTag('name', 'keywords', keywords);

            // Open Graph Tags
            this.updateMetaTag('property', 'og:title', `Services - ${appName}`);
            this.updateMetaTag('property', 'og:description', description);
            this.updateMetaTag('property', 'og:type', 'website');
            this.updateMetaTag('property', 'og:url', `${baseUrl}${this.$route.fullPath}`);

            // Twitter Card Tags
            this.updateMetaTag('name', 'twitter:card', 'summary');
            this.updateMetaTag('name', 'twitter:title', `Services - ${appName}`);
            this.updateMetaTag('name', 'twitter:description', description);
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

            // Remove page-specific meta tags
            const ogTags = ['og:title', 'og:description', 'og:image', 'og:url'];
            const twitterTags = ['twitter:title', 'twitter:description', 'twitter:image'];

            [...ogTags, ...twitterTags].forEach(tag => {
                const meta = document.querySelector(`meta[property="${tag}"], meta[name="${tag}"]`);
                if (meta) meta.remove();
            });
        },

        async loadServices() {
            this.loading = true;
            this.error = null;

            try {
                const response = await this.$axios.get('/api/openapi/services');
                this.services = response.data || [];
            } catch (error) {
                console.error('Error loading services:', error);
                this.error = 'Failed to load services. Please try again later.';
                this.services = [];
            } finally {
                this.loading = false;
            }
        },
        getServiceIcon(index) {
            const icons = ['mdi-battery-charging-high', 'mdi-factory', 'mdi-home-lightning', 'mdi-battery-sync', 'mdi-lightning-bolt', 'mdi-tools'];
            return icons[index % icons.length];
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        }
    }
};
</script>

<style scoped>
/* Hero and common component styles moved to app.css */

/* Common styles (service-card-detailed, service-icon-side, link-hover-effect, process-step, process-line, cta-pattern) moved to app.css */

/* Service Image Styles */
.service-icon-side .service-image {
    opacity: 0.9;
    transition: opacity 0.3s ease;
}

.service-icon-side:hover .service-image {
    opacity: 1;
}

/* CTA Section Styles - Moved to app.css */

/* Responsive Styles */
@media (max-width: 960px) {
    .service-icon-side {
        width: 100%;
        min-height: 200px;
    }

    .process-line {
        display: none;
    }

    .process-step {
        margin-bottom: 16px !important;
    }
}

@media (max-width: 600px) {
    .service-card-detailed {
        margin-bottom: 16px;
    }

    .service-icon-side {
        min-height: 100px;
    }

    .service-icon-side .v-icon {
        font-size: 36px !important;
    }

    .process-step {
        width: 60px;
        height: 60px;
        font-size: 1.5rem !important;
    }

    .cta-pattern {
        opacity: 0.2;
    }
}
</style>
