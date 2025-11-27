<template>
    <div class="settings-page">
        <div class="d-flex align-center justify-space-between mb-6">
            <div>
                <h1 class="text-h4 font-weight-bold">Settings</h1>
                <p class="text-body-1 text-medium-emphasis">Manage your application preferences and configurations</p>
            </div>
            <v-btn color="primary" size="large" prepend-icon="mdi-content-save" :loading="loading" @click="saveSettings"
                elevation="2">
                Save Changes
            </v-btn>
        </div>

        <v-row>
            <!-- Navigation Sidebar -->
            <v-col cols="12" md="3" lg="2">
                <v-card elevation="0" class="bg-transparent">
                    <v-list bg-color="transparent" color="primary" rounded="lg" class="pa-0">
                        <v-list-item value="general" @click="activeTab = 'general'" :active="activeTab === 'general'"
                            rounded="lg" class="mb-1">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-cog-outline"></v-icon>
                            </template>
                            <v-list-item-title class="font-weight-medium">General</v-list-item-title>
                        </v-list-item>

                        <v-list-item value="home_page" @click="activeTab = 'home_page'"
                            :active="activeTab === 'home_page'" rounded="lg" class="mb-1">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-home-outline"></v-icon>
                            </template>
                            <v-list-item-title class="font-weight-medium">Home Page</v-list-item-title>
                        </v-list-item>

                        <v-list-item value="contact_page" @click="activeTab = 'contact_page'"
                            :active="activeTab === 'contact_page'" rounded="lg" class="mb-1">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-card-account-phone-outline"></v-icon>
                            </template>
                            <v-list-item-title class="font-weight-medium">Contact Page</v-list-item-title>
                        </v-list-item>

                        <v-list-item value="branding" @click="activeTab = 'branding'" :active="activeTab === 'branding'"
                            rounded="lg" class="mb-1">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-palette-outline"></v-icon>
                            </template>
                            <v-list-item-title class="font-weight-medium">Branding</v-list-item-title>
                        </v-list-item>

                        <v-list-item value="social" @click="activeTab = 'social'" :active="activeTab === 'social'"
                            rounded="lg" class="mb-1">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-share-variant-outline"></v-icon>
                            </template>
                            <v-list-item-title class="font-weight-medium">Social Media</v-list-item-title>
                        </v-list-item>

                        <v-list-item value="seo" @click="activeTab = 'seo'" :active="activeTab === 'seo'" rounded="lg"
                            class="mb-1">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-magnify"></v-icon>
                            </template>
                            <v-list-item-title class="font-weight-medium">SEO</v-list-item-title>
                        </v-list-item>

                        <v-list-item value="email" @click="activeTab = 'email'" :active="activeTab === 'email'"
                            rounded="lg" class="mb-1">
                            <template v-slot:prepend>
                                <v-icon icon="mdi-email-outline"></v-icon>
                            </template>
                            <v-list-item-title class="font-weight-medium">Email</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-card>
            </v-col>

            <!-- Content Area -->
            <v-col cols="12" md="9" lg="10">
                <v-card elevation="1" rounded="lg" class="fill-height">
                    <v-card-text class="pa-6">
                        <v-form @submit.prevent="saveSettings">
                            <v-window v-model="activeTab">
                                <!-- Home Page Settings -->
                                <v-window-item value="home_page" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <HomePageSettings :settings="settings.home_page" />
                                </v-window-item>

                                <!-- General Settings -->
                                <v-window-item value="general" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <GeneralSettings :settings="settings.general" />
                                </v-window-item>

                                <!-- Branding Settings -->
                                <v-window-item value="branding" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <BrandingSettings :settings="settings.branding" />
                                </v-window-item>

                                <!-- Social Media Settings -->
                                <v-window-item value="social" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <SocialSettings :settings="settings.social" />
                                </v-window-item>

                                <!-- SEO Settings -->
                                <v-window-item value="seo" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <SEOSettings :settings="settings.seo" />
                                </v-window-item>

                                <!-- Email Settings -->
                                <v-window-item value="email" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <EmailSettings :settings="settings.email" />
                                </v-window-item>


                                <!-- Contact Page Settings -->
                                <v-window-item value="contact_page" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <ContactPageSettings :settings="settings.contact_page" />
                                </v-window-item>
                            </v-window>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import GeneralSettings from './sections/GeneralSettings.vue';
import HomePageSettings from './sections/HomePageSettings.vue';
import ContactPageSettings from './sections/ContactPageSettings.vue';
import BrandingSettings from './sections/BrandingSettings.vue';
import SocialSettings from './sections/SocialSettings.vue';
import SEOSettings from './sections/SEOSettings.vue';
import EmailSettings from './sections/EmailSettings.vue';

export default {
    components: {
        GeneralSettings,
        HomePageSettings,
        ContactPageSettings,
        BrandingSettings,
        SocialSettings,
        SEOSettings,
        EmailSettings
    },
    data() {
        return {
            activeTab: 'general',
            settings: {
                home_page: {
                    home_hero_title: { value: 'Uninterrupted Power for Your Business & Home', type: 'text', group: 'home_page' },
                    home_hero_subtitle: { value: 'Reliable technical power support solutions, including UPS systems, industrial backup, batteries, and professional maintenance services.', type: 'textarea', group: 'home_page' },
                    stat_1_value: { value: '500+', type: 'text', group: 'home_page' },
                    stat_1_label: { value: 'Systems Installed', type: 'text', group: 'home_page' },
                    stat_2_value: { value: '99.9%', type: 'text', group: 'home_page' },
                    stat_2_label: { value: 'Power Uptime', type: 'text', group: 'home_page' },
                    stat_3_value: { value: '24/7', type: 'text', group: 'home_page' },
                    stat_3_label: { value: 'Support', type: 'text', group: 'home_page' },
                    stat_4_value: { value: '15+', type: 'text', group: 'home_page' },
                    stat_4_label: { value: 'Years Experience', type: 'text', group: 'home_page' },
                    // Section visibility settings
                    hero_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    stats_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    trusted_by_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    services_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    why_choose_us_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    testimonials_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    featured_products_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    cta_section_enabled: { value: '1', type: 'boolean', group: 'home_page' },
                    // Dynamic section content
                    trusted_by_title: { value: 'TRUSTED BY INDUSTRY LEADERS', type: 'text', group: 'home_page' },
                    trusted_by_clients: { value: '', type: 'textarea', group: 'home_page' },
                    services_overline: { value: 'WHAT WE DO', type: 'text', group: 'home_page' },
                    services_title: { value: 'Power Support Solutions', type: 'text', group: 'home_page' },
                    services_subtitle: { value: 'We ensure uninterrupted operations for businesses and households with high-quality power products and services.', type: 'textarea', group: 'home_page' },
                    services_data: { value: '', type: 'textarea', group: 'home_page' },
                    why_choose_us_overline: { value: 'WHY CHOOSE US', type: 'text', group: 'home_page' },
                    why_choose_us_title: { value: 'Reliable Power, Guaranteed', type: 'text', group: 'home_page' },
                    why_choose_us_image: { value: 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', type: 'text', group: 'home_page' },
                    why_choose_us_features: { value: '', type: 'textarea', group: 'home_page' },
                    testimonials_overline: { value: 'TESTIMONIALS', type: 'text', group: 'home_page' },
                    testimonials_title: { value: 'Client Success Stories', type: 'text', group: 'home_page' },
                    testimonials_subtitle: { value: "See how we've helped businesses and households stay powered up and protected.", type: 'textarea', group: 'home_page' },
                    testimonials_data: { value: '', type: 'textarea', group: 'home_page' },
                    products_overline: { value: 'OUR PRODUCTS', type: 'text', group: 'home_page' },
                    products_title: { value: 'Featured Power Systems', type: 'text', group: 'home_page' },
                    products_button_text: { value: 'View All Products', type: 'text', group: 'home_page' },
                    products_button_link: { value: '/products', type: 'text', group: 'home_page' },
                    cta_title: { value: 'Secure Your Power Today', type: 'text', group: 'home_page' },
                    cta_subtitle: { value: "Don't let power outages disrupt your life or business. Contact us for reliable backup solutions.", type: 'textarea', group: 'home_page' },
                    cta_primary_button_text: { value: 'Get Started', type: 'text', group: 'home_page' },
                    cta_primary_button_link: { value: '{"name": "Contact"}', type: 'text', group: 'home_page' },
                    cta_secondary_button_text: { value: 'Contact Support', type: 'text', group: 'home_page' },
                    cta_secondary_button_link: { value: '{"name": "Contact"}', type: 'text', group: 'home_page' },
                },
                general: {
                    site_name: { value: '', type: 'text', group: 'general' },
                    site_tagline: { value: '', type: 'text', group: 'general' },
                    contact_email: { value: '', type: 'email', group: 'general' },
                    contact_phone: { value: '', type: 'text', group: 'general' },
                    whatsapp_number: { value: '', type: 'text', group: 'general' },
                    address: { value: '', type: 'textarea', group: 'general' },
                    newsletter_description: { value: 'Subscribe to get the latest power tips and updates.', type: 'textarea', group: 'general' },
                },
                contact_page: {
                    contact_email: { value: '', type: 'email', group: 'contact_page' },
                    contact_phone: { value: '', type: 'text', group: 'contact_page' },
                    contact_phone_secondary: { value: '', type: 'text', group: 'contact_page' },
                    contact_address: { value: '', type: 'textarea', group: 'contact_page' },
                    contact_hero_title: { value: 'Contact Us', type: 'text', group: 'contact_page' },
                    contact_hero_subtitle: { value: 'We are here to help.', type: 'textarea', group: 'contact_page' },
                    contact_map_url: { value: '', type: 'textarea', group: 'contact_page' },
                },
                social: {
                    facebook_url: { value: '', type: 'url', group: 'social' },
                    twitter_url: { value: '', type: 'url', group: 'social' },
                    linkedin_url: { value: '', type: 'url', group: 'social' },
                    instagram_url: { value: '', type: 'url', group: 'social' },
                },
                seo: {
                    meta_title: { value: '', type: 'text', group: 'seo' },
                    meta_description: { value: '', type: 'textarea', group: 'seo' },
                    meta_keywords: { value: '', type: 'text', group: 'seo' },
                },
                email: {
                    smtp_host: { value: '', type: 'text', group: 'email' },
                    smtp_port: { value: '', type: 'text', group: 'email' },
                    smtp_username: { value: '', type: 'text', group: 'email' },
                    smtp_password: { value: '', type: 'password', group: 'email' },
                    smtp_encryption: { value: 'tls', type: 'text', group: 'email' },
                },
                branding: {
                    logo: { value: '', type: 'image', group: 'branding' },
                    favicon: { value: '', type: 'image', group: 'branding' },
                    primary_color: { value: '#1976D2', type: 'color', group: 'branding' },
                },
            },
            loading: false
        };
    },
    async mounted() {
        await this.loadSettings();
    },
    methods: {
        async loadSettings() {
            try {
                const token = localStorage.getItem('admin_token');
                const response = await this.$axios.get('/api/v1/settings', {
                    headers: { Authorization: `Bearer ${token}` }
                });

                // Merge loaded settings with defaults
                const loadedSettings = response.data;

                // Update existing settings with loaded values
                Object.keys(loadedSettings).forEach(group => {
                    if (this.settings[group]) {
                        Object.keys(loadedSettings[group]).forEach(key => {
                            if (this.settings[group][key]) {
                                // Update value from loaded settings
                                this.settings[group][key].value = loadedSettings[group][key].value || '';
                                // Update type and group if they exist
                                if (loadedSettings[group][key].type) {
                                    this.settings[group][key].type = loadedSettings[group][key].type;
                                }
                                if (loadedSettings[group][key].group) {
                                    this.settings[group][key].group = loadedSettings[group][key].group;
                                }
                            } else {
                                // Add new setting that wasn't in defaults
                                this.settings[group][key] = {
                                    value: loadedSettings[group][key].value || '',
                                    type: loadedSettings[group][key].type || 'text',
                                    group: loadedSettings[group][key].group || group,
                                };
                            }
                        });
                    } else {
                        // Add new group that wasn't in defaults
                        this.settings[group] = {};
                        Object.keys(loadedSettings[group]).forEach(key => {
                            this.settings[group][key] = {
                                value: loadedSettings[group][key].value || '',
                                type: loadedSettings[group][key].type || 'text',
                                group: loadedSettings[group][key].group || group,
                            };
                        });
                    }
                });
            } catch (error) {
                console.error('Error loading settings:', error);
                this.showError('Failed to load settings');
            }
        },
        async saveSettings() {
            this.loading = true;
            try {
                const token = localStorage.getItem('admin_token');

                // Flatten settings for API
                const settingsToSave = {};
                Object.keys(this.settings).forEach(group => {
                    Object.keys(this.settings[group]).forEach(key => {
                        settingsToSave[key] = this.settings[group][key];
                    });
                });

                await this.$axios.post('/api/v1/settings', {
                    settings: settingsToSave
                }, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showSuccess('Settings saved successfully');
            } catch (error) {
                console.error('Error saving settings:', error);
                let message = 'Error saving settings';
                if (error.response && error.response.data && error.response.data.message) {
                    message = error.response.data.message;
                } else if (error.message) {
                    message = error.message;
                }
                this.showError(message);
            } finally {
                this.loading = false;
            }
        },
        showSuccess(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: message
                });
            } else {
                alert(message);
            }
        },
        showError(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'error',
                    title: message
                });
            } else {
                alert(message);
            }
        }
    }
};
</script>

<style scoped>
.settings-page {
    max-width: 1600px;
    margin: 0 auto;
}
</style>
