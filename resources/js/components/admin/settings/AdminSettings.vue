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
                                    <div class="mb-6">
                                        <h2 class="text-h5 font-weight-bold mb-1">Home Page Settings</h2>
                                        <p class="text-body-2 text-medium-emphasis">Customize the hero section and
                                            content of your
                                            homepage</p>
                                    </div>

                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field v-model="settings.home_page.home_hero_title.value"
                                                label="Hero Title" variant="outlined" density="comfortable"
                                                color="primary" hint="Main headline displayed in the hero section"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea v-model="settings.home_page.home_hero_subtitle.value"
                                                label="Hero Subtitle" variant="outlined" density="comfortable"
                                                color="primary"
                                                hint="Subtitle or description displayed below the hero title"
                                                persistent-hint rows="3" auto-grow></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-divider class="my-4"></v-divider>
                                            <div class="text-subtitle-1 font-weight-bold mb-4">Statistics Section</div>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_1_value.value"
                                                label="Stat 1 - Value" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., 500+" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_1_label.value"
                                                label="Stat 1 - Label" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., Systems Installed"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_2_value.value"
                                                label="Stat 2 - Value" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., 99.9%" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_2_label.value"
                                                label="Stat 2 - Label" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., Power Uptime"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_3_value.value"
                                                label="Stat 3 - Value" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., 24/7" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_3_label.value"
                                                label="Stat 3 - Label" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., Support" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_4_value.value"
                                                label="Stat 4 - Value" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., 15+" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.home_page.stat_4_label.value"
                                                label="Stat 4 - Label" variant="outlined" density="comfortable"
                                                color="primary" hint="e.g., Years Experience"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- General Settings -->
                                <v-window-item value="general" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <div class="mb-6">
                                        <h2 class="text-h5 font-weight-bold mb-1">General Settings</h2>
                                        <p class="text-body-2 text-medium-emphasis">Basic information about your website
                                        </p>
                                    </div>

                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.general.site_name.value" label="Site Name"
                                                variant="outlined" density="comfortable" color="primary"
                                                hint="The name of your website" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.general.site_tagline.value"
                                                label="Site Tagline" variant="outlined" density="comfortable"
                                                color="primary" hint="A short tagline for your website"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.general.contact_email.value"
                                                label="Contact Email" type="email" variant="outlined"
                                                density="comfortable" color="primary" prepend-inner-icon="mdi-email"
                                                hint="Main contact email address" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.general.contact_phone.value"
                                                label="Contact Phone" variant="outlined" density="comfortable"
                                                color="primary" prepend-inner-icon="mdi-phone"
                                                hint="Main contact phone number" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea v-model="settings.general.address.value" label="Address"
                                                variant="outlined" density="comfortable" color="primary"
                                                prepend-inner-icon="mdi-map-marker" hint="Business address"
                                                persistent-hint rows="3" auto-grow></v-textarea>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Branding Settings -->
                                <v-window-item value="branding" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <div class="mb-6">
                                        <h2 class="text-h5 font-weight-bold mb-1">Branding</h2>
                                        <p class="text-body-2 text-medium-emphasis">Customize the look and feel of your
                                            site</p>
                                    </div>

                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.branding.logo.value" label="Logo URL"
                                                variant="outlined" density="comfortable" color="primary"
                                                prepend-inner-icon="mdi-image" hint="URL to your logo image"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.branding.favicon.value" label="Favicon URL"
                                                variant="outlined" density="comfortable" color="primary"
                                                prepend-inner-icon="mdi-image-size-select-actual"
                                                hint="URL to your favicon" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.branding.primary_color.value"
                                                label="Primary Color" type="color" variant="outlined"
                                                density="comfortable" style="height: 60px" hint="Primary brand color"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Social Media Settings -->
                                <v-window-item value="social" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <div class="mb-6">
                                        <h2 class="text-h5 font-weight-bold mb-1">Social Media Links</h2>
                                        <p class="text-body-2 text-medium-emphasis">Connect your social profiles</p>
                                    </div>

                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.social.facebook_url.value"
                                                label="Facebook URL" type="url" variant="outlined" density="comfortable"
                                                color="primary" hint="Your Facebook page URL" persistent-hint
                                                prepend-inner-icon="mdi-facebook"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.social.twitter_url.value"
                                                label="Twitter URL" type="url" variant="outlined" density="comfortable"
                                                color="primary" hint="Your Twitter profile URL" persistent-hint
                                                prepend-inner-icon="mdi-twitter"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.social.linkedin_url.value"
                                                label="LinkedIn URL" type="url" variant="outlined" density="comfortable"
                                                color="primary" hint="Your LinkedIn company page URL" persistent-hint
                                                prepend-inner-icon="mdi-linkedin"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.social.instagram_url.value"
                                                label="Instagram URL" type="url" variant="outlined"
                                                density="comfortable" color="primary" hint="Your Instagram profile URL"
                                                persistent-hint prepend-inner-icon="mdi-instagram"></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- SEO Settings -->
                                <v-window-item value="seo" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <div class="mb-6">
                                        <h2 class="text-h5 font-weight-bold mb-1">SEO Settings</h2>
                                        <p class="text-body-2 text-medium-emphasis">Optimize your site for search
                                            engines</p>
                                    </div>

                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field v-model="settings.seo.meta_title.value"
                                                label="Default Meta Title" variant="outlined" density="comfortable"
                                                color="primary" hint="Default meta title for pages"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea v-model="settings.seo.meta_description.value"
                                                label="Default Meta Description" variant="outlined"
                                                density="comfortable" color="primary"
                                                hint="Default meta description for pages" persistent-hint rows="3"
                                                auto-grow></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-text-field v-model="settings.seo.meta_keywords.value"
                                                label="Meta Keywords" variant="outlined" density="comfortable"
                                                color="primary" hint="Default meta keywords (comma-separated)"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Email Settings -->
                                <v-window-item value="email" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <div class="mb-6">
                                        <h2 class="text-h5 font-weight-bold mb-1">Email Settings</h2>
                                        <p class="text-body-2 text-medium-emphasis">Configure SMTP for email delivery
                                        </p>
                                    </div>

                                    <v-row>
                                        <v-col cols="12" md="8">
                                            <v-text-field v-model="settings.email.smtp_host.value" label="SMTP Host"
                                                variant="outlined" density="comfortable" color="primary"
                                                prepend-inner-icon="mdi-server" hint="SMTP server hostname"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field v-model="settings.email.smtp_port.value" label="SMTP Port"
                                                type="number" variant="outlined" density="comfortable" color="primary"
                                                hint="Port (e.g. 587, 465)" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.email.smtp_username.value"
                                                label="SMTP Username" variant="outlined" density="comfortable"
                                                color="primary" prepend-inner-icon="mdi-account"
                                                hint="SMTP authentication username" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.email.smtp_password.value"
                                                label="SMTP Password" type="password" variant="outlined"
                                                density="comfortable" color="primary" prepend-inner-icon="mdi-lock"
                                                hint="SMTP authentication password" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-select v-model="settings.email.smtp_encryption.value"
                                                :items="['none', 'tls', 'ssl']" label="SMTP Encryption"
                                                variant="outlined" density="comfortable" color="primary"
                                                prepend-inner-icon="mdi-shield-check" hint="Encryption method"
                                                persistent-hint></v-select>
                                        </v-col>
                                    </v-row>
                                </v-window-item>


                                <!-- Contact Page Settings -->
                                <v-window-item value="contact_page" transition="fade-transition"
                                    reverse-transition="fade-transition">
                                    <div class="mb-6">
                                        <h2 class="text-h5 font-weight-bold mb-1">Contact Page Settings</h2>
                                        <p class="text-body-2 text-medium-emphasis">Customize the content of your
                                            Contact Us page
                                        </p>
                                    </div>

                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.contact_page.contact_email.value"
                                                label="Contact Email" type="email" variant="outlined"
                                                density="comfortable" color="primary" prepend-inner-icon="mdi-email"
                                                hint="Email displayed on Contact Page" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.contact_page.contact_phone.value"
                                                label="Contact Phone" variant="outlined" density="comfortable"
                                                color="primary" prepend-inner-icon="mdi-phone"
                                                hint="Phone displayed on Contact Page" persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea v-model="settings.contact_page.contact_address.value"
                                                label="Physical Address" variant="outlined" density="comfortable"
                                                color="primary" prepend-inner-icon="mdi-map-marker"
                                                hint="Address displayed on Contact Page" persistent-hint rows="3"
                                                auto-grow></v-textarea>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="settings.contact_page.contact_hero_title.value"
                                                label="Hero Title" variant="outlined" density="comfortable"
                                                color="primary" hint="Title for the hero section"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-textarea v-model="settings.contact_page.contact_hero_subtitle.value"
                                                label="Hero Subtitle" variant="outlined" density="comfortable"
                                                color="primary" hint="Subtitle for the hero section" persistent-hint
                                                rows="2" auto-grow></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea v-model="settings.contact_page.contact_map_url.value"
                                                label="Google Maps Embed URL" variant="outlined" density="comfortable"
                                                color="primary" prepend-inner-icon="mdi-map"
                                                hint="Paste the 'src' URL from the Google Maps embed code"
                                                persistent-hint rows="3"></v-textarea>

                                            <div v-if="settings.contact_page.contact_map_url.value" class="mt-4">
                                                <div class="text-subtitle-2 mb-2">Map Preview:</div>
                                                <iframe :src="settings.contact_page.contact_map_url.value" width="100%"
                                                    height="300" style="border:0;" allowfullscreen="" loading="lazy"
                                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                                            </div>
                                        </v-col>
                                    </v-row>
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
import axios from 'axios';

export default {
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
                },
                general: {
                    site_name: { value: '', type: 'text', group: 'general' },
                    site_tagline: { value: '', type: 'text', group: 'general' },
                    contact_email: { value: '', type: 'email', group: 'general' },
                    contact_phone: { value: '', type: 'text', group: 'general' },
                    address: { value: '', type: 'textarea', group: 'general' },
                },
                contact_page: {
                    contact_email: { value: '', type: 'email', group: 'contact_page' },
                    contact_phone: { value: '', type: 'text', group: 'contact_page' },
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
                const response = await axios.get('/api/v1/settings', {
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

                await axios.post('/api/v1/settings', {
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
