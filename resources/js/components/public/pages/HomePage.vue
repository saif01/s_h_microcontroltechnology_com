<template>
    <div class="home-page">
        <HeroSection :hero-title="heroTitle" :hero-subtitle="heroSubtitle" />
        <StatsSection :stats="stats" />
        <TrustedBySection :clients="clients" />
        <ServicesSection :services="services" />
        <WhyChooseUsSection :features="features" />
        <TestimonialsSection :testimonials="testimonials" />
        <FeaturedProductsSection :products="products" />
        <CTASection />
    </div>
</template>

<script>
import axios from 'axios';
import HeroSection from './home_page_sections/HeroSection.vue';
import StatsSection from './home_page_sections/StatsSection.vue';
import TrustedBySection from './home_page_sections/TrustedBySection.vue';
import ServicesSection from './home_page_sections/ServicesSection.vue';
import WhyChooseUsSection from './home_page_sections/WhyChooseUsSection.vue';
import TestimonialsSection from './home_page_sections/TestimonialsSection.vue';
import FeaturedProductsSection from './home_page_sections/FeaturedProductsSection.vue';
import CTASection from './home_page_sections/CTASection.vue';

export default {
    name: 'HomePage',
    components: {
        HeroSection,
        StatsSection,
        TrustedBySection,
        ServicesSection,
        WhyChooseUsSection,
        TestimonialsSection,
        FeaturedProductsSection,
        CTASection
    },
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
    }
};
</script>

<style scoped>
/* Home page specific styles if needed */
</style>
