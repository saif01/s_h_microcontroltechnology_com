<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">Home Page Settings</h2>
            <p class="text-body-2 text-medium-emphasis">Customize the hero section and content of your homepage</p>
        </div>

        <!-- Home Page Tabs -->
        <v-tabs v-model="homePageTab" color="primary" class="mb-6">
            <v-tab value="hero">Hero Section</v-tab>
            <v-tab value="stats">Statistics</v-tab>
            <v-tab value="visibility">Section Visibility</v-tab>
            <v-tab value="trusted_by">Trusted By</v-tab>
            <v-tab value="services">Services</v-tab>
            <v-tab value="why_choose_us">Why Choose Us</v-tab>
            <v-tab value="testimonials">Testimonials</v-tab>
            <v-tab value="products">Products</v-tab>
            <v-tab value="cta">CTA Section</v-tab>
        </v-tabs>

        <v-window v-model="homePageTab">
            <!-- Hero Section Tab -->
            <v-window-item value="hero">
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-model="settings.home_hero_title.value"
                            label="Hero Title" variant="outlined" density="comfortable"
                            color="primary"
                            hint="Main headline displayed in the hero section"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea v-model="settings.home_hero_subtitle.value"
                            label="Hero Subtitle" variant="outlined" density="comfortable"
                            color="primary"
                            hint="Subtitle or description displayed below the hero title"
                            persistent-hint rows="3" auto-grow></v-textarea>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Statistics Section Tab -->
            <v-window-item value="stats">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">Statistics Section</div>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_1_value.value"
                            label="Stat 1 - Value" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., 500+"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_1_label.value"
                            label="Stat 1 - Label" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Systems Installed"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_2_value.value"
                            label="Stat 2 - Value" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., 99.9%"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_2_label.value"
                            label="Stat 2 - Label" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Power Uptime"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_3_value.value"
                            label="Stat 3 - Value" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., 24/7"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_3_label.value"
                            label="Stat 3 - Label" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Support"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_4_value.value"
                            label="Stat 4 - Value" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., 15+" persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="settings.stat_4_label.value"
                            label="Stat 4 - Label" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Years Experience"
                            persistent-hint></v-text-field>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Section Visibility Tab -->
            <v-window-item value="visibility">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">Section Visibility</div>
                        <p class="text-body-2 text-medium-emphasis mb-4">Enable or disable sections on the home page</p>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch v-model="settings.hero_section_enabled.value"
                            label="Hero Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">Hero Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch v-model="settings.stats_section_enabled.value"
                            label="Stats Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">Stats Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch
                            v-model="settings.trusted_by_section_enabled.value"
                            label="Trusted By Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">Trusted By Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch
                            v-model="settings.services_section_enabled.value"
                            label="Services Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">Services Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch
                            v-model="settings.why_choose_us_section_enabled.value"
                            label="Why Choose Us Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">Why Choose Us Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch
                            v-model="settings.testimonials_section_enabled.value"
                            label="Testimonials Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">Testimonials Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch
                            v-model="settings.featured_products_section_enabled.value"
                            label="Featured Products Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">Featured Products Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-switch v-model="settings.cta_section_enabled.value"
                            label="CTA Section" color="primary" hide-details
                            :true-value="'1'" :false-value="'0'">
                            <template v-slot:label>
                                <span class="text-body-1">CTA Section</span>
                            </template>
                        </v-switch>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Trusted By Section Tab -->
            <v-window-item value="trusted_by">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">Trusted By Section Content</div>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="settings.trusted_by_title.value"
                            label="Title" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., TRUSTED BY INDUSTRY LEADERS"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-divider class="my-4"></v-divider>
                        <div class="d-flex justify-space-between align-center mb-4">
                            <div class="text-subtitle-1 font-weight-bold">Client Logos</div>
                            <v-btn color="primary" prepend-icon="mdi-plus"
                                @click="addTrustedByClient" size="small">
                                Add Client Logo
                            </v-btn>
                        </div>
                        <div v-if="trustedByClients.length === 0"
                            class="text-center py-8 text-medium-emphasis">
                            No client logos added. Click "Add Client Logo" to get started.
                        </div>
                        <v-row>
                            <v-col v-for="(client, index) in trustedByClients" :key="index"
                                cols="12" md="6" lg="4">
                                <v-card variant="outlined" class="h-100">
                                    <v-card-text>
                                        <div
                                            class="d-flex justify-space-between align-start mb-3">
                                            <div class="text-subtitle-2 font-weight-bold">
                                                Client {{ index + 1 }}</div>
                                            <v-btn icon="mdi-delete" variant="text"
                                                color="error" size="small"
                                                @click="removeTrustedByClient(index)"></v-btn>
                                        </div>

                                        <!-- Logo Preview -->
                                        <div v-if="client.logo" class="mb-3 text-center">
                                            <v-img :src="client.logo" max-height="120"
                                                max-width="200" class="mx-auto mb-2"
                                                contain>
                                                <template v-slot:placeholder>
                                                    <div
                                                        class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                                        <v-progress-circular indeterminate
                                                            color="primary"></v-progress-circular>
                                                    </div>
                                                </template>
                                            </v-img>
                                        </div>

                                        <!-- File Upload -->
                                        <v-file-input v-model="client.file"
                                            label="Upload Logo" variant="outlined"
                                            density="comfortable" color="primary"
                                            accept="image/*" prepend-icon="mdi-image"
                                            hint="Upload a logo image (JPG, PNG, GIF, WebP - Max 5MB)"
                                            persistent-hint show-size
                                            @update:model-value="handleClientLogoChange(index)">
                                            <template v-slot:append-inner
                                                v-if="client.uploading">
                                                <v-progress-circular indeterminate size="20"
                                                    color="primary"></v-progress-circular>
                                            </template>
                                        </v-file-input>

                                        <!-- Or Enter URL -->
                                        <v-text-field v-model="client.logo"
                                            label="Or Enter Logo URL" variant="outlined"
                                            density="comfortable" color="primary"
                                            hint="Enter a direct URL to the logo image"
                                            persistent-hint prepend-inner-icon="mdi-link"
                                            @input="updateTrustedByClients">
                                            <template v-slot:append-inner
                                                v-if="client.logo && !client.file">
                                                <v-btn icon="mdi-open-in-new" variant="text"
                                                    size="small"
                                                    @click="window.open(client.logo, '_blank')"></v-btn>
                                            </template>
                                        </v-text-field>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Services Section Tab -->
            <v-window-item value="services">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">Services Section Content</div>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="settings.services_overline.value"
                            label="Overline" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., WHAT WE DO"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="8">
                        <v-text-field v-model="settings.services_title.value"
                            label="Title" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Power Support Solutions"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea v-model="settings.services_subtitle.value"
                            label="Subtitle" variant="outlined" density="comfortable"
                            color="primary" persistent-hint rows="2" auto-grow></v-textarea>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Why Choose Us Section Tab -->
            <v-window-item value="why_choose_us">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">Why Choose Us Section Content</div>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field
                            v-model="settings.why_choose_us_overline.value"
                            label="Overline" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., WHY CHOOSE US"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="8">
                        <v-text-field v-model="settings.why_choose_us_title.value"
                            label="Title" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Reliable Power, Guaranteed"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="settings.why_choose_us_image.value"
                            label="Image URL" variant="outlined" density="comfortable"
                            color="primary" hint="URL for the section image"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-divider class="my-4"></v-divider>
                        <div class="d-flex justify-space-between align-center mb-4">
                            <div class="text-subtitle-1 font-weight-bold">Features</div>
                            <v-btn color="primary" prepend-icon="mdi-plus"
                                @click="addWhyChooseUsFeature" size="small">
                                Add Feature
                            </v-btn>
                        </div>
                        <div v-if="whyChooseUsFeatures.length === 0"
                            class="text-center py-8 text-medium-emphasis">
                            No features added. Click "Add Feature" to get started.
                        </div>
                        <v-card v-for="(feature, index) in whyChooseUsFeatures" :key="index"
                            class="mb-4" variant="outlined">
                            <v-card-text>
                                <div class="d-flex justify-space-between align-start mb-2">
                                    <div class="text-subtitle-2 font-weight-bold">Feature {{ index + 1 }}</div>
                                    <v-btn icon="mdi-delete" variant="text" color="error"
                                        size="small"
                                        @click="removeWhyChooseUsFeature(index)"></v-btn>
                                </div>
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="feature.title" label="Title"
                                            variant="outlined" density="comfortable"
                                            color="primary"
                                            hint="e.g., Uninterrupted Operations"
                                            persistent-hint
                                            @input="updateWhyChooseUsFeatures"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="feature.icon"
                                            label="Icon (Material Design)"
                                            variant="outlined" density="comfortable"
                                            color="primary"
                                            hint="e.g., mdi-lightning-bolt-circle"
                                            persistent-hint
                                            prepend-inner-icon="mdi-information"
                                            @input="updateWhyChooseUsFeatures">
                                            <template v-slot:append-inner
                                                v-if="feature.icon">
                                                <v-icon :icon="feature.icon"
                                                    size="small"></v-icon>
                                            </template>
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="feature.desc"
                                            label="Description" variant="outlined"
                                            density="comfortable" color="primary"
                                            persistent-hint rows="2" auto-grow
                                            @input="updateWhyChooseUsFeatures"></v-textarea>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Testimonials Section Tab -->
            <v-window-item value="testimonials">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">Testimonials Section Content</div>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field
                            v-model="settings.testimonials_overline.value"
                            label="Overline" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., TESTIMONIALS"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="8">
                        <v-text-field v-model="settings.testimonials_title.value"
                            label="Title" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Client Success Stories"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea v-model="settings.testimonials_subtitle.value"
                            label="Subtitle" variant="outlined" density="comfortable"
                            color="primary" persistent-hint rows="2" auto-grow></v-textarea>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea v-model="settings.testimonials_data.value"
                            label="Testimonials (JSON)" variant="outlined"
                            density="comfortable" color="primary"
                            hint='Format: [{"text": "...", "name": "...", "role": "...", "avatar": "..."}]'
                            persistent-hint rows="8" auto-grow></v-textarea>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- Featured Products Section Tab -->
            <v-window-item value="products">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">Featured Products Section Content</div>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="settings.products_overline.value"
                            label="Overline" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., OUR PRODUCTS"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="8">
                        <v-text-field v-model="settings.products_title.value"
                            label="Title" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Featured Power Systems"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="settings.products_button_text.value"
                            label="Button Text" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., View All Products"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="settings.products_button_link.value"
                            label="Button Link" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., /products"
                            persistent-hint></v-text-field>
                    </v-col>
                </v-row>
            </v-window-item>

            <!-- CTA Section Tab -->
            <v-window-item value="cta">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-1 font-weight-bold mb-4">CTA Section Content</div>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="settings.cta_title.value"
                            label="Title" variant="outlined" density="comfortable"
                            color="primary" hint="e.g., Secure Your Power Today"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea v-model="settings.cta_subtitle.value"
                            label="Subtitle" variant="outlined" density="comfortable"
                            color="primary" persistent-hint rows="2" auto-grow></v-textarea>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="settings.cta_primary_button_text.value"
                            label="Primary Button Text" variant="outlined"
                            density="comfortable" color="primary" hint="e.g., Get Started"
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="settings.cta_primary_button_link.value"
                            label="Primary Button Link (JSON or URL)" variant="outlined"
                            density="comfortable" color="primary"
                            hint='e.g., {"name": "Contact"} or /contact'
                            persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="settings.cta_secondary_button_text.value"
                            label="Secondary Button Text" variant="outlined"
                            density="comfortable" color="primary"
                            hint="e.g., Contact Support" persistent-hint></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="settings.cta_secondary_button_link.value"
                            label="Secondary Button Link (JSON or URL)" variant="outlined"
                            density="comfortable" color="primary"
                            hint='e.g., {"name": "Contact"} or /contact'
                            persistent-hint></v-text-field>
                    </v-col>
                </v-row>
            </v-window-item>
        </v-window>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'HomePageSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            homePageTab: 'hero',
            whyChooseUsFeatures: [],
            trustedByClients: []
        };
    },
    mounted() {
        this.initializeWhyChooseUsFeatures();
        this.initializeTrustedByClients();
    },
    methods: {
        initializeWhyChooseUsFeatures() {
            const featuresJson = this.settings.why_choose_us_features.value;
            if (featuresJson) {
                try {
                    const parsed = typeof featuresJson === 'string' ? JSON.parse(featuresJson) : featuresJson;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.whyChooseUsFeatures = parsed;
                    } else {
                        this.whyChooseUsFeatures = this.getDefaultFeatures();
                    }
                } catch (e) {
                    console.warn('Error parsing features:', e);
                    this.whyChooseUsFeatures = this.getDefaultFeatures();
                }
            } else {
                this.whyChooseUsFeatures = this.getDefaultFeatures();
            }
        },
        getDefaultFeatures() {
            return [
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
            ];
        },
        addWhyChooseUsFeature() {
            this.whyChooseUsFeatures.push({
                title: '',
                desc: '',
                icon: 'mdi-star'
            });
            this.updateWhyChooseUsFeatures();
        },
        removeWhyChooseUsFeature(index) {
            this.whyChooseUsFeatures.splice(index, 1);
            this.updateWhyChooseUsFeatures();
        },
        updateWhyChooseUsFeatures() {
            this.settings.why_choose_us_features.value = JSON.stringify(this.whyChooseUsFeatures);
        },
        initializeTrustedByClients() {
            const clientsJson = this.settings.trusted_by_clients.value;
            if (clientsJson) {
                try {
                    const parsed = typeof clientsJson === 'string' ? JSON.parse(clientsJson) : clientsJson;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.trustedByClients = parsed.map(client => ({
                            logo: client.logo || '',
                            file: null,
                            uploading: false
                        }));
                    } else {
                        this.trustedByClients = [];
                    }
                } catch (e) {
                    console.warn('Error parsing trusted_by_clients:', e);
                    this.trustedByClients = [];
                }
            } else {
                this.trustedByClients = [];
            }
        },
        addTrustedByClient() {
            this.trustedByClients.push({
                logo: '',
                file: null,
                uploading: false
            });
        },
        removeTrustedByClient(index) {
            this.trustedByClients.splice(index, 1);
            this.updateTrustedByClients();
        },
        async handleClientLogoChange(index) {
            const client = this.trustedByClients[index];
            if (!client.file) {
                this.updateTrustedByClients();
                return;
            }

            const fileToUpload = Array.isArray(client.file) ? client.file[0] : client.file;
            if (!fileToUpload) {
                this.updateTrustedByClients();
                return;
            }

            client.uploading = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'clients');

                const token = localStorage.getItem('admin_token');
                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    client.logo = response.data.url;
                    client.file = null;
                    this.updateTrustedByClients();
                    this.showSuccess('Logo uploaded successfully');
                } else {
                    throw new Error(response.data.message || 'Failed to upload logo');
                }
            } catch (error) {
                console.error('Error uploading logo:', error);
                this.showError(error.response?.data?.message || error.message || 'Failed to upload logo');
                client.file = null;
            } finally {
                client.uploading = false;
            }
        },
        updateTrustedByClients() {
            const clientsData = this.trustedByClients.map(client => ({
                logo: client.logo || ''
            })).filter(client => client.logo);
            this.settings.trusted_by_clients.value = JSON.stringify(clientsData);
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

