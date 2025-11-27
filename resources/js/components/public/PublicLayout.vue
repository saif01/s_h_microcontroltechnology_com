<template>
    <v-app>
        <!-- Topbar -->
        <AppBar :is-scrolled="isScrolled" :site-name="siteName" :site-tagline="siteTagline" :menu-items="menuItems"
            :logo="brandingLogo" @toggle-drawer="drawer = !drawer" />

        <!-- Mobile Navigation Drawer -->
        <MobileDrawer v-model="drawer" :site-name="siteName" :menu-items="menuItems" :logo="brandingLogo" />

        <!-- Main Content -->
        <v-main class="bg-grey-lighten-5">
            <router-view />
        </v-main>

        <!-- Footer -->
        <Footer :site-name="siteName" :footer-description="footerDescription" :menu-items="menuItems"
            :settings="settings" :logo="brandingLogo" :newsletter-description="newsletterDescription" />

        <!-- Floating WhatsApp Button -->
        <WhatsAppFloat />

        <!-- Go to Top Button -->
        <GoToTopButton />
    </v-app>
</template>

<script>
import AppBar from './layout/AppBar.vue';
import MobileDrawer from './layout/MobileDrawer.vue';
import Footer from './layout/Footer.vue';
import WhatsAppFloat from './layout/WhatsAppFloat.vue';
import GoToTopButton from './layout/GoToTopButton.vue';

export default {
    name: 'PublicLayout',
    components: {
        AppBar,
        MobileDrawer,
        Footer,
        WhatsAppFloat,
        GoToTopButton
    },
    data() {
        return {
            drawer: false,
            isScrolled: false,
            siteName: 'Micro Control Technology',
            siteTagline: 'TECHNOLOGY',
            footerDescription: 'Leading provider of technical power support solutions. We ensure your business stays powered with reliable UPS systems, backup generators, and professional maintenance.',
            newsletterDescription: 'Subscribe to get the latest power tips and updates.',
            brandingLogo: null,
            settings: {
                // General settings
                site_name: '',
                site_tagline: '',
                contact_email: '',
                contact_phone: '',
                address: '',
                // Social media
                facebook_url: '',
                twitter_url: '',
                linkedin_url: '',
                instagram_url: '',
                whatsapp_number: '',
            },
            menuItems: [
                { id: 1, label: 'Home', url: '/' },
                { id: 2, label: 'Services', url: '/services' },
                { id: 3, label: 'Products', url: '/products' },
                { id: 4, label: 'Blog', url: '/blog' },
                { id: 5, label: 'Careers', url: '/careers' },
                { id: 6, label: 'About Us', url: '/about' },
                { id: 7, label: 'Contact', url: '/contact' },
            ]
        };
    },
    async mounted() {
        await this.loadSettings();
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        async loadSettings() {
            try {
                const response = await this.$axios.get('/api/openapi/settings');
                const data = response.data;

                // Update settings object
                Object.keys(this.settings).forEach(key => {
                    if (data[key] !== undefined && data[key] !== null) {
                        this.settings[key] = data[key];
                    }
                });

                // Update site name
                if (data.site_name) {
                    this.siteName = data.site_name;
                }

                // Update site tagline
                if (data.site_tagline) {
                    this.siteTagline = data.site_tagline;
                }

                // Update footer description if available (could be from contact_page or general)
                if (data.footer_description) {
                    this.footerDescription = data.footer_description;
                } else if (data.about_description) {
                    this.footerDescription = data.about_description;
                }

                // Update newsletter description if available
                if (data.newsletter_description) {
                    this.newsletterDescription = data.newsletter_description;
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }

            // Load branding settings separately
            await this.loadBrandingSettings();
        },
        async loadBrandingSettings() {
            try {
                const response = await this.$axios.get('/api/openapi/settings?group=branding');
                if (response.data && response.data.logo) {
                    this.brandingLogo = response.data.logo;
                }
            } catch (error) {
                console.error('Error loading branding settings:', error);
                // Don't show error to user, just use default
            }
        },
        handleScroll() {
            this.isScrolled = window.scrollY > 20;
        }
    }
};
</script>

<style scoped>
/* Main layout styles - minimal since styles are in component files */
</style>
