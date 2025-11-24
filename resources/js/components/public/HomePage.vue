<template>
    <div class="home-page">
        <!-- Hero Section -->
        <section class="hero-section position-relative overflow-hidden">
            <div class="animated-gradient-bg"></div>
            <div class="hero-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
            <v-container class="h-100 position-relative z-index-2">
                <v-row class="h-100 align-center">
                    <v-col cols="12" md="7" class="text-white">
                        <v-fade-transition appear>
                            <div>
                                <div class="glass-pill d-inline-flex align-center px-4 py-2 rounded-pill mb-6">
                                    <v-icon icon="mdi-flash" color="amber-accent-3" size="small" class="mr-2"></v-icon>
                                    <span class="text-subtitle-2 font-weight-bold tracking-wide">POWERING YOUR
                                        SUCCESS</span>
                                </div>
                                <h1 class="text-h4 text-lg-h3 font-weight-black mb-6 lh-tight text-shadow-sm">
                                    {{ heroTitle }}
                                </h1>
                                <p class="text-h6 text-lg-h5 font-weight-light mb-8 opacity-90 mw-600 lh-relaxed">
                                    {{ heroSubtitle }}
                                </p>
                                <div class="d-flex gap-4 flex-wrap">
                                    <v-btn size="x-large" color="amber-accent-4" variant="flat" rounded="pill"
                                        class="px-8 text-black font-weight-bold elevation-4 hover-scale"
                                        :to="{ name: 'Contact' }">
                                        Get a Quote
                                        <v-icon end icon="mdi-arrow-right" class="ml-2"></v-icon>
                                    </v-btn>
                                    <v-btn size="x-large" variant="outlined" color="white" rounded="pill"
                                        class="px-8 font-weight-bold border-2 hover-fill" to="/services">
                                        Our Solutions
                                    </v-btn>
                                </div>
                            </div>
                        </v-fade-transition>
                    </v-col>

                    <v-col cols="12" md="5" class="d-none d-md-flex justify-center align-center">
                        <div class="hero-visual-3d">
                            <div class="glass-card card-main">
                                <v-icon icon="mdi-battery-charging-high" size="48" color="amber-accent-3"
                                    class="mb-4"></v-icon>
                                <div class="text-h5 font-weight-bold text-white mb-2">Always On</div>
                                <div class="text-body-2 text-white opacity-80">Uninterrupted power supply for critical
                                    operations.</div>
                            </div>
                            <div class="glass-card card-float-1">
                                <v-icon icon="mdi-shield-check" size="32" color="success"></v-icon>
                                <div class="text-subtitle-2 font-weight-bold text-white mt-2">Protected</div>
                            </div>
                            <div class="glass-card card-float-2">
                                <v-icon icon="mdi-lightning-bolt" size="32" color="amber-accent-3"></v-icon>
                                <div class="text-subtitle-2 font-weight-bold text-white mt-2">Efficient</div>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-container>

            <!-- Wave Divider -->
            <div class="wave-divider">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                    preserveAspectRatio="none">
                    <path
                        d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                        class="shape-fill"></path>
                </svg>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-8 py-md-12 bg-surface position-relative z-index-2 mt-n12">
            <v-container>
                <v-card elevation="10" rounded="xl" class="stats-card overflow-hidden">
                    <v-row no-gutters>
                        <v-col v-for="(stat, index) in stats" :key="index" cols="6" md="3">
                            <div
                                class="py-8 px-4 text-center stat-item h-100 d-flex flex-column justify-center align-center">
                                <div
                                    class="text-h3 text-md-h2 font-weight-black text-transparent bg-clip-text gradient-text mb-2">
                                    {{ stat.value }}
                                </div>
                                <div
                                    class="text-subtitle-1 text-uppercase font-weight-bold text-medium-emphasis tracking-wide">
                                    {{ stat.label }}
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                </v-card>
            </v-container>
        </section>

        <!-- Trusted By Section (Logo Marquee) -->
        <section class="py-12 overflow-hidden bg-grey-lighten-5">
            <v-container fluid class="px-0">
                <div class="text-center mb-8">
                    <div class="text-overline text-medium-emphasis tracking-widest">TRUSTED BY INDUSTRY LEADERS</div>
                </div>
                <div class="marquee-container">
                    <div class="marquee-track">
                        <div v-for="(client, i) in [...clients, ...clients]" :key="i" class="marquee-item mx-8">
                            <v-img :src="client.logo" width="120" height="60"
                                class="client-logo grayscale hover-color transition-all"></v-img>
                        </div>
                    </div>
                </div>
            </v-container>
        </section>

        <!-- Services Section -->
        <section class="py-20 bg-grey-lighten-5 position-relative">
            <div class="bg-pattern-dots"></div>
            <v-container class="position-relative z-index-1">
                <div class="text-center mb-16">
                    <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">WHAT WE DO</div>
                    <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">Power Support Solutions</h2>
                    <div class="divider-center mx-auto mb-6"></div>
                    <p class="text-h6 text-medium-emphasis mx-auto mw-800 font-weight-regular">
                        We ensure uninterrupted operations for businesses and households with high-quality power
                        products and services.
                    </p>
                </div>

                <v-row v-if="services.length > 0">
                    <v-col v-for="(service, i) in services" :key="service.id" cols="12" md="4">
                        <v-hover v-slot="{ isHovering, props }">
                            <v-card v-bind="props" :elevation="isHovering ? 16 : 2"
                                class="h-100 service-card-modern rounded-xl transition-all"
                                :to="`/services/${service.slug}`">
                                <div class="card-gradient-border"></div>
                                <v-card-item class="pt-8 px-6 pb-6">
                                    <div
                                        class="service-icon-box mb-6 rounded-lg elevation-3 d-inline-flex pa-4 bg-white text-primary">
                                        <v-icon :icon="service.icon || getServiceIcon(i)" size="32"></v-icon>
                                    </div>
                                    <v-card-title class="text-h5 font-weight-bold mb-3 text-grey-darken-3">
                                        {{ service.title }}
                                    </v-card-title>
                                    <v-card-subtitle class="text-body-1 text-wrap mb-4 opacity-70 lh-relaxed">
                                        {{ service.short_description }}
                                    </v-card-subtitle>
                                </v-card-item>
                                <v-card-actions class="px-6 pb-8">
                                    <span class="text-primary font-weight-bold link-hover-effect">
                                        Learn More <v-icon icon="mdi-arrow-right" size="small" class="ml-1"></v-icon>
                                    </span>
                                </v-card-actions>
                            </v-card>
                        </v-hover>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Why Choose Us Section -->
        <section class="py-20 bg-surface position-relative overflow-hidden">
            <!-- Decorative blobs -->
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>

            <v-container class="position-relative z-index-1">
                <v-row align="center">
                    <v-col cols="12" md="6" class="pr-md-12">
                        <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">WHY CHOOSE US
                        </div>
                        <h2 class="text-h3 font-weight-bold mb-8 text-grey-darken-3">Reliable Power, Guaranteed</h2>

                        <div class="feature-list">
                            <div v-for="(feature, i) in features" :key="i" class="feature-item d-flex align-start mb-8">
                                <div class="feature-icon mr-6 rounded-circle bg-primary-lighten-5 pa-3 text-primary">
                                    <v-icon :icon="feature.icon" size="24"></v-icon>
                                </div>
                                <div>
                                    <h4 class="text-h6 font-weight-bold mb-2">{{ feature.title }}</h4>
                                    <p class="text-body-1 text-medium-emphasis">{{ feature.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="6">
                        <div class="image-composition position-relative">
                            <div class="composition-bg rounded-xl bg-grey-lighten-3"></div>
                            <v-img
                                src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                                class="rounded-xl elevation-10 position-relative z-index-2 composition-img" cover
                                height="500"></v-img>
                            <div class="composition-card glass-card-dark pa-6 rounded-lg elevation-10">
                                <div class="d-flex align-center mb-2">
                                    <v-icon icon="mdi-check-decagram" color="success" class="mr-2"></v-icon>
                                    <span class="text-h6 font-weight-bold text-white">100% Verified</span>
                                </div>
                                <div class="text-caption text-white opacity-80">Quality Assurance</div>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Testimonials Section (Dark Premium) -->
        <section class="py-24 bg-dark-gradient position-relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="testimonial-bg-shape shape-t1"></div>
            <div class="testimonial-bg-shape shape-t2"></div>
            <div class="huge-quote-mark">"</div>

            <v-container class="position-relative z-index-1">
                <div class="text-center mb-16">
                    <div class="glass-pill-sm d-inline-flex align-center px-4 py-1 rounded-pill mb-4">
                        <span class="text-caption font-weight-bold text-white tracking-widest">TESTIMONIALS</span>
                    </div>
                    <h2 class="text-h3 font-weight-bold mb-6 text-white">Client Success Stories</h2>
                    <p class="text-h6 text-white opacity-70 mx-auto mw-600 font-weight-light">
                        See how we've helped businesses and households stay powered up and protected.
                    </p>
                </div>

                <v-carousel cycle height="auto" hide-delimiters show-arrows="hover" interval="8000"
                    class="testimonial-carousel pb-12">
                    <template v-slot:prev="{ props }">
                        <v-btn variant="outlined" color="white" icon="mdi-chevron-left" @click="props.onClick"
                            class="carousel-nav-btn"></v-btn>
                    </template>
                    <template v-slot:next="{ props }">
                        <v-btn variant="outlined" color="white" icon="mdi-chevron-right" @click="props.onClick"
                            class="carousel-nav-btn"></v-btn>
                    </template>

                    <v-carousel-item v-for="(testimonial, i) in testimonials" :key="i">
                        <v-row justify="center">
                            <v-col cols="12" md="9" lg="8">
                                <v-card
                                    class="testimonial-glass-card text-center pa-10 pa-md-16 rounded-xl elevation-0 position-relative">
                                    <div class="d-flex flex-column align-center position-relative z-index-2">
                                        <div class="avatar-glow mb-8">
                                            <v-avatar size="90" class="elevation-4 border-2-white">
                                                <v-img :src="testimonial.avatar" cover></v-img>
                                            </v-avatar>
                                        </div>

                                        <v-icon icon="mdi-format-quote-open" size="48" color="white"
                                            class="mb-6 opacity-50"></v-icon>

                                        <p
                                            class="text-h5 text-md-h4 font-weight-light font-italic mb-8 text-white lh-relaxed testimonial-text">
                                            "{{ testimonial.text }}"
                                        </p>

                                        <div class="text-center">
                                            <div class="text-h6 font-weight-bold text-white mb-1">{{ testimonial.name }}
                                            </div>
                                            <div
                                                class="text-subtitle-2 text-primary-lighten-2 font-weight-bold text-uppercase tracking-wide">
                                                {{ testimonial.role }}</div>
                                        </div>
                                    </div>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-carousel-item>
                </v-carousel>
            </v-container>
        </section>

        <!-- Featured Products Section -->
        <section class="py-20 bg-surface">
            <v-container>
                <div class="d-flex flex-column flex-sm-row justify-space-between align-end mb-12">
                    <div>
                        <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">OUR PRODUCTS</div>
                        <h2 class="text-h3 font-weight-bold text-grey-darken-3">Featured Power Systems</h2>
                    </div>
                    <v-btn variant="outlined" color="primary" to="/products"
                        class="mt-4 mt-sm-0 rounded-pill px-6 font-weight-bold">
                        View All Products
                    </v-btn>
                </div>

                <v-row v-if="products.length > 0">
                    <v-col v-for="product in products" :key="product.id" cols="12" md="4">
                        <v-hover v-slot="{ isHovering, props }">
                            <v-card v-bind="props" :elevation="isHovering ? 12 : 0"
                                class="h-100 product-card-modern bg-white rounded-xl overflow-hidden border-thin"
                                :to="`/products/${product.slug}`">
                                <div class="img-wrapper overflow-hidden position-relative" style="height: 260px;">
                                    <v-img
                                        :src="product.thumbnail || 'https://via.placeholder.com/400x300?text=Product'"
                                        height="100%" cover class="product-img transition-transform"
                                        :class="{ 'scale-110': isHovering }">
                                        <template v-slot:placeholder>
                                            <div
                                                class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                                <v-progress-circular indeterminate
                                                    color="primary"></v-progress-circular>
                                            </div>
                                        </template>
                                    </v-img>
                                    <div class="overlay-actions d-flex align-center justify-center">
                                        <v-btn icon="mdi-eye" color="white" variant="flat" class="mr-2"></v-btn>
                                        <v-btn icon="mdi-cart" color="primary" variant="flat"></v-btn>
                                    </div>
                                </div>
                                <v-card-item class="pt-6 px-6">
                                    <div class="text-caption text-primary font-weight-bold mb-1 text-uppercase">Category
                                    </div>
                                    <v-card-title class="text-h6 font-weight-bold mb-2 text-grey-darken-3 px-0">
                                        {{ product.title }}
                                    </v-card-title>
                                    <v-card-text class="text-body-2 text-medium-emphasis px-0 text-truncate-2">
                                        {{ product.short_description }}
                                    </v-card-text>
                                </v-card-item>
                                <v-card-actions class="px-6 pb-6 pt-0">
                                    <div class="font-weight-bold text-h6 text-primary">$99.00</div>
                                    <v-spacer></v-spacer>
                                    <span class="text-caption text-medium-emphasis">Details &rarr;</span>
                                </v-card-actions>
                            </v-card>
                        </v-hover>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA Section -->
        <section class="py-24 position-relative overflow-hidden cta-section">
            <div class="cta-gradient-bg"></div>
            <div class="cta-particles"></div>
            <v-container class="position-relative z-index-1">
                <v-row justify="center" class="text-center">
                    <v-col cols="12" md="10" lg="8">
                        <h2 class="text-h3 text-md-h2 font-weight-black text-white mb-6 lh-tight">
                            Secure Your Power Today
                        </h2>
                        <p class="text-h6 text-white opacity-90 mb-10 font-weight-light mw-800 mx-auto">
                            Don't let power outages disrupt your life or business. Contact us for reliable backup
                            solutions.
                        </p>
                        <div class="d-flex flex-column flex-sm-row justify-center gap-4">
                            <v-btn size="x-large" color="amber-accent-4" variant="flat"
                                class="text-black font-weight-bold px-10 rounded-pill elevation-6 hover-lift"
                                :to="{ name: 'Contact' }">
                                Get Started
                            </v-btn>
                            <v-btn size="x-large" variant="outlined" color="white"
                                class="font-weight-bold px-10 rounded-pill border-2" :to="{ name: 'Contact' }">
                                Contact Support
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'HomePage',
    data() {
        return {
            heroTitle: 'Uninterrupted Power for Your Business & Home',
            heroSubtitle: 'Reliable technical power support solutions, including UPS systems, industrial backup, batteries, and professional maintenance services.',
            services: [],
            products: [],
            posts: [],
            stats: [
                { value: '500+', label: 'Systems Installed' },
                { value: '99.9%', label: 'Power Uptime' },
                { value: '24/7', label: 'Support' },
                { value: '15+', label: 'Years Experience' }
            ],
            homePageSettings: {},
            features: [
                {
                    title: 'Uninterrupted Operations',
                    desc: 'We ensure your business never stops with our reliable backup power solutions and UPS systems.',
                    icon: 'mdi-lightning-bolt-circle'
                },
                {
                    title: 'High-Quality Products',
                    desc: 'We supply top-tier batteries, inverters, and power management systems built for long-term performance.',
                    icon: 'mdi-shield-star'
                },
                {
                    title: 'Responsive Support',
                    desc: 'Our professional maintenance team is available 24/7 to handle installation and repairs.',
                    icon: 'mdi-headset'
                }
            ],
            clients: [
                { logo: 'https://via.placeholder.com/120x60?text=Client+1' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+2' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+3' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+4' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+5' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+6' },
            ],
            testimonials: [
                {
                    text: "Their UPS installation saved our manufacturing line during a critical power outage. Highly recommended for industrial needs.",
                    name: "Robert Fox",
                    role: "Plant Manager, InduTech",
                    avatar: "https://i.pravatar.cc/150?img=12"
                },
                {
                    text: "Excellent home backup solution. The installation was professional, and the system works seamlessly.",
                    name: "Sarah Jenkins",
                    role: "Homeowner",
                    avatar: "https://i.pravatar.cc/150?img=5"
                },
                {
                    text: "We rely on them for all our battery maintenance. Responsive, knowledgeable, and efficient team.",
                    name: "David Miller",
                    role: "IT Director, NetCorp",
                    avatar: "https://i.pravatar.cc/150?img=3"
                }
            ]
        };
    },
    async mounted() {
        await this.loadHomeData();
    },
    methods: {
        async loadHomeData() {
            try {
                const response = await axios.get('/api/openapi/home');
                const data = response.data;

                // Store home page settings if available
                if (data.homePageSettings) {
                    this.homePageSettings = data.homePageSettings;
                }

                // Handle homePage data
                if (data.homePage) {
                    // Use settings if available, otherwise use page data
                    this.heroTitle = this.homePageSettings.home_hero_title || data.homePage.title || this.heroTitle;
                    // Use content or short description from homePage, or keep default
                    const subtitle = this.homePageSettings.home_hero_subtitle || data.homePage.content;
                    if (subtitle) {
                        // If content is HTML, extract text or use as is
                        this.heroSubtitle = subtitle.replace(/<[^>]*>/g, '').substring(0, 200) || this.heroSubtitle;
                    }
                } else if (this.homePageSettings.home_hero_title) {
                    // Use settings if no page exists
                    this.heroTitle = this.homePageSettings.home_hero_title;
                    this.heroSubtitle = this.homePageSettings.home_hero_subtitle || this.heroSubtitle;
                }

                // Update stats from settings if available
                if (this.homePageSettings.stat_1_value && this.homePageSettings.stat_1_label) {
                    this.stats[0] = { value: this.homePageSettings.stat_1_value, label: this.homePageSettings.stat_1_label };
                }
                if (this.homePageSettings.stat_2_value && this.homePageSettings.stat_2_label) {
                    this.stats[1] = { value: this.homePageSettings.stat_2_value, label: this.homePageSettings.stat_2_label };
                }
                if (this.homePageSettings.stat_3_value && this.homePageSettings.stat_3_label) {
                    this.stats[2] = { value: this.homePageSettings.stat_3_value, label: this.homePageSettings.stat_3_label };
                }
                if (this.homePageSettings.stat_4_value && this.homePageSettings.stat_4_label) {
                    this.stats[3] = { value: this.homePageSettings.stat_4_value, label: this.homePageSettings.stat_4_label };
                }

                // Handle services - map API response to component format
                if (data.services && Array.isArray(data.services) && data.services.length > 0) {
                    this.services = data.services.map(service => ({
                        id: service.id,
                        title: service.title || '',
                        slug: service.slug || '',
                        short_description: service.short_description || service.description || '',
                        icon: service.icon || null,
                        image: service.image || null,
                    }));
                } else {
                    // Fallback services if API returns empty or services module not enabled
                    this.services = [
                        { id: 1, title: 'UPS Systems', slug: 'ups-systems', short_description: 'Reliable Uninterruptible Power Supply systems for critical equipment.' },
                        { id: 2, title: 'Industrial Backup', slug: 'industrial-backup', short_description: 'Heavy-duty power backup solutions for industrial applications.' },
                        { id: 3, title: 'Home Power Solutions', slug: 'home-power', short_description: 'Keep your home running smoothly during outages.' },
                        { id: 4, title: 'Battery Maintenance', slug: 'battery-maintenance', short_description: 'Professional testing, replacement, and disposal services.' },
                        { id: 5, title: 'Power Management', slug: 'power-management', short_description: 'Smart energy monitoring and efficiency optimization.' },
                        { id: 6, title: 'Installation Services', slug: 'installation', short_description: 'Expert installation of all power systems and wiring.' }
                    ];
                }

                // Handle products - map API response to component format
                if (data.products && Array.isArray(data.products) && data.products.length > 0) {
                    this.products = data.products.map(product => ({
                        id: product.id,
                        title: product.title || '',
                        slug: product.slug || '',
                        short_description: product.short_description || product.description || '',
                        thumbnail: product.thumbnail || (product.images && product.images[0]) || null,
                        price: product.price || product.price_range || null,
                        show_price: product.show_price !== false,
                    }));
                }

                // Handle blog posts if blog module is enabled (optional)
                if (data.posts && Array.isArray(data.posts) && data.posts.length > 0) {
                    // Store posts if needed for future use
                    this.posts = data.posts;
                }

            } catch (error) {
                console.error('Error loading home data:', error);
                // Fallback for demo purposes if API fails
                this.services = [
                    { id: 1, title: 'UPS Systems', slug: 'ups-systems', short_description: 'Reliable Uninterruptible Power Supply systems for critical equipment.' },
                    { id: 2, title: 'Industrial Backup', slug: 'industrial-backup', short_description: 'Heavy-duty power backup solutions for industrial applications.' },
                    { id: 3, title: 'Home Power Solutions', slug: 'home-power', short_description: 'Keep your home running smoothly during outages.' },
                    { id: 4, title: 'Battery Maintenance', slug: 'battery-maintenance', short_description: 'Professional testing, replacement, and disposal services.' },
                    { id: 5, title: 'Power Management', slug: 'power-management', short_description: 'Smart energy monitoring and efficiency optimization.' },
                    { id: 6, title: 'Installation Services', slug: 'installation', short_description: 'Expert installation of all power systems and wiring.' }
                ];
            }
        },
        getServiceIcon(index) {
            const icons = ['mdi-battery-charging-high', 'mdi-factory', 'mdi-home-lightning', 'mdi-battery-sync', 'mdi-lightning-bolt', 'mdi-tools'];
            return icons[index % icons.length];
        }
    }
};
</script>

<style scoped>
/* General Utilities */
.z-index-1 {
    z-index: 1;
}

.z-index-2 {
    z-index: 2;
}

.lh-tight {
    line-height: 1.1;
}

.lh-relaxed {
    line-height: 1.6;
}

.mw-600 {
    max-width: 600px;
}

.mw-800 {
    max-width: 800px;
}

.tracking-wide {
    letter-spacing: 0.05em;
}

.tracking-widest {
    letter-spacing: 0.15em;
}

.gap-4 {
    gap: 1rem;
}

.text-shadow-sm {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.border-thin {
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
}

.border-2 {
    border-width: 2px !important;
}

.border-2-white {
    border: 2px solid white !important;
}

/* Hero Section */
.hero-section {
    min-height: 800px;
    display: flex;
    align-items: center;
    position: relative;
}

.animated-gradient-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(-45deg, #1e3a8a, #2563eb, #d97706, #1e3a8a);
    /* Blue to Amber */
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

@keyframes gradientBG {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.hero-shapes .shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.6;
    animation: floatShape 20s infinite alternate;
}

.shape-1 {
    width: 400px;
    height: 400px;
    background: #3b82f6;
    top: -100px;
    left: -100px;
}

.shape-2 {
    width: 300px;
    height: 300px;
    background: #f59e0b;
    /* Amber */
    bottom: 10%;
    right: 10%;
    animation-delay: -5s;
}

.shape-3 {
    width: 200px;
    height: 200px;
    background: #60a5fa;
    top: 20%;
    right: 30%;
    animation-delay: -10s;
}

@keyframes floatShape {
    0% {
        transform: translate(0, 0) rotate(0deg);
    }

    100% {
        transform: translate(50px, 50px) rotate(20deg);
    }
}

.glass-pill {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-pill-sm {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.hero-visual-3d {
    position: relative;
    width: 400px;
    height: 400px;
    perspective: 1000px;
}

.glass-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 24px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    padding: 24px;
    transition: transform 0.3s ease;
}

.card-main {
    width: 280px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
}

.card-float-1 {
    top: 20%;
    right: 0;
    width: 140px;
    animation: float 6s ease-in-out infinite;
    z-index: 1;
}

.card-float-2 {
    bottom: 20%;
    left: 0;
    width: 140px;
    animation: float 6s ease-in-out infinite 2s;
    z-index: 3;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

.wave-divider {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
    transform: rotate(180deg);
}

.wave-divider svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 100px;
}

.wave-divider .shape-fill {
    fill: rgb(var(--v-theme-surface));
}

/* Stats Section */
.stats-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
}

.stat-item:not(:last-child) {
    border-right: 1px solid rgba(0, 0, 0, 0.05);
}

.gradient-text {
    background: linear-gradient(45deg, #2563eb, #d97706);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

/* Marquee */
.marquee-container {
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
}

.marquee-track {
    display: inline-flex;
    animation: scroll 30s linear infinite;
}

.marquee-item {
    flex-shrink: 0;
}

.client-logo {
    opacity: 0.5;
    transition: all 0.3s ease;
}

.client-logo.grayscale {
    filter: grayscale(100%);
}

.client-logo:hover {
    opacity: 1;
    filter: grayscale(0%);
    transform: scale(1.1);
}

@keyframes scroll {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-50%);
    }
}

/* Services Section */
.bg-pattern-dots {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.5;
}

.divider-center {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #d97706);
    border-radius: 2px;
}

.service-card-modern {
    background: white;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.card-gradient-border {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #f59e0b);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.service-card-modern:hover .card-gradient-border {
    opacity: 1;
}

.service-card-modern:hover {
    transform: translateY(-8px);
}

.link-hover-effect {
    display: inline-flex;
    align-items: center;
    transition: transform 0.2s ease;
}

.service-card-modern:hover .link-hover-effect {
    transform: translateX(4px);
}

/* Why Choose Us */
.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    z-index: 0;
}

.blob-1 {
    width: 500px;
    height: 500px;
    background: #e0f2fe;
    top: -100px;
    right: -100px;
}

.blob-2 {
    width: 400px;
    height: 400px;
    background: #fef3c7;
    bottom: -50px;
    left: -100px;
}

.composition-bg {
    position: absolute;
    top: 20px;
    right: -20px;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.glass-card-dark {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(12px);
    position: absolute;
    bottom: 40px;
    left: -30px;
    z-index: 3;
    min-width: 200px;
}

/* Testimonials Dark Theme */
.bg-dark-gradient {
    background: linear-gradient(135deg, #0f172a 0%, #172554 100%);
}

.testimonial-glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.avatar-glow {
    position: relative;
}

.avatar-glow::after {
    content: '';
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    border-radius: 50%;
    background: linear-gradient(45deg, #3b82f6, #f59e0b);
    z-index: -1;
    opacity: 0.7;
}

.testimonial-text {
    font-family: 'Georgia', serif;
}

.huge-quote-mark {
    position: absolute;
    top: -50px;
    right: 10%;
    font-size: 400px;
    line-height: 1;
    font-family: 'Georgia', serif;
    color: rgba(255, 255, 255, 0.03);
    pointer-events: none;
    z-index: 0;
}

.testimonial-bg-shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.3;
}

.shape-t1 {
    width: 400px;
    height: 400px;
    background: #3b82f6;
    top: -100px;
    left: -100px;
}

.shape-t2 {
    width: 300px;
    height: 300px;
    background: #f59e0b;
    bottom: -50px;
    right: -50px;
}

.carousel-nav-btn {
    opacity: 0.5;
    transition: all 0.3s ease;
}

.carousel-nav-btn:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.1);
}

/* Products */
.product-card-modern .overlay-actions {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card-modern:hover .overlay-actions {
    opacity: 1;
}

.scale-110 {
    transform: scale(1.1);
}

/* CTA */
.cta-section {
    background: #111827;
}

.cta-gradient-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at top right, #2563eb, transparent 40%),
        radial-gradient(circle at bottom left, #d97706, transparent 40%);
    opacity: 0.6;
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

/* Responsive */
@media (max-width: 960px) {
    .hero-section {
        min-height: auto;
        padding-top: 60px;
        padding-bottom: 60px;
    }

    .stat-item:not(:last-child) {
        border-right: none;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .composition-bg {
        display: none;
    }

    .glass-card-dark {
        left: 20px;
        bottom: 20px;
    }

    .huge-quote-mark {
        font-size: 200px;
        top: 0;
    }
}
</style>
