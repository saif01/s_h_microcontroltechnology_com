<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Why Choose Us Section Content</div>
        </v-col>
        <v-col cols="12" md="4">
            <v-text-field v-model="settings.why_choose_us_overline.value" label="Overline" variant="outlined"
                density="comfortable" color="primary" hint="e.g., WHY CHOOSE US" persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
            <v-text-field v-model="settings.why_choose_us_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Reliable Power, Guaranteed"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-divider class="my-2"></v-divider>
            <div class="text-subtitle-2 font-weight-bold mb-3">Section Image</div>
            <div class="text-caption text-medium-emphasis mb-3">
                <v-icon icon="mdi-information" size="small" class="mr-1"></v-icon>
                Recommended size: 1200x800px (3:2 ratio) or 1600x900px (16:9 ratio). Max file size: 5MB
            </div>

            <!-- Image Preview -->
            <div v-if="imageUrl" class="mb-3">
                <v-card variant="outlined" class="pa-2">
                    <div class="d-flex align-center">
                        <v-img :src="imageUrl" max-height="200" max-width="300" class="rounded mr-4" cover>
                            <template v-slot:placeholder>
                                <div class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                </div>
                            </template>
                        </v-img>
                        <div class="flex-grow-1">
                            <div class="text-body-2 font-weight-medium mb-1">Current Image</div>
                            <v-btn icon="mdi-open-in-new" variant="text" size="small" color="primary"
                                @click="window.open(imageUrl, '_blank')" class="mr-2">
                                <v-icon>mdi-open-in-new</v-icon>
                                <v-tooltip activator="parent">Open in new tab</v-tooltip>
                            </v-btn>
                        </div>
                    </div>
                </v-card>
            </div>

            <!-- File Upload -->
            <v-file-input v-model="imageFile" label="Upload Image" variant="outlined" density="comfortable"
                color="primary" accept="image/*" prepend-icon="mdi-image"
                hint="Upload a section image (JPG, PNG, GIF, WebP - Max 5MB, Recommended: 1200x800px)" persistent-hint
                show-size @update:model-value="handleImageChange" class="mb-3">
                <template v-slot:append-inner v-if="uploading">
                    <v-progress-circular indeterminate size="20" color="primary"></v-progress-circular>
                </template>
            </v-file-input>

            <!-- Or Enter URL -->
            <v-text-field v-model="settings.why_choose_us_image.value" label="Or Enter Image URL" variant="outlined"
                density="comfortable" color="primary" hint="Enter a direct URL to the image" persistent-hint
                prepend-inner-icon="mdi-link">
                <template v-slot:append-inner v-if="imageUrl && !imageFile">
                    <v-btn icon="mdi-open-in-new" variant="text" size="small"
                        @click="window.open(imageUrl, '_blank')"></v-btn>
                </template>
            </v-text-field>
        </v-col>
        <v-col cols="12">
            <v-divider class="my-4"></v-divider>
            <div class="d-flex justify-space-between align-center mb-4">
                <div class="text-subtitle-1 font-weight-bold">Features</div>
                <v-btn color="primary" prepend-icon="mdi-plus" @click="addWhyChooseUsFeature" size="small">
                    Add Feature
                </v-btn>
            </div>
            <div v-if="whyChooseUsFeatures.length === 0" class="text-center py-8 text-medium-emphasis">
                No features added. Click "Add Feature" to get started.
            </div>
            <v-card v-for="(feature, index) in whyChooseUsFeatures" :key="index" class="mb-4" variant="outlined">
                <v-card-text>
                    <div class="d-flex justify-space-between align-start mb-2">
                        <div class="text-subtitle-2 font-weight-bold">Feature {{ index + 1 }}</div>
                        <v-btn icon="mdi-delete" variant="text" color="error" size="small"
                            @click="removeWhyChooseUsFeature(index)"></v-btn>
                    </div>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="feature.title" label="Title" variant="outlined" density="comfortable"
                                color="primary" hint="e.g., Uninterrupted Operations" persistent-hint
                                @input="updateWhyChooseUsFeatures"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="feature.icon" label="Icon (Material Design)" variant="outlined"
                                density="comfortable" color="primary" hint="e.g., mdi-lightning-bolt-circle"
                                persistent-hint prepend-inner-icon="mdi-information" @input="updateWhyChooseUsFeatures">
                                <template v-slot:append-inner v-if="feature.icon">
                                    <v-icon :icon="feature.icon" size="small"></v-icon>
                                </template>
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="feature.desc" label="Description" variant="outlined"
                                density="comfortable" color="primary" persistent-hint rows="2" auto-grow
                                @input="updateWhyChooseUsFeatures"></v-textarea>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import axios from 'axios';

export default {
    name: 'WhyChooseUsSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            whyChooseUsFeatures: [],
            imageFile: null,
            uploading: false
        };
    },
    computed: {
        imageUrl() {
            return this.settings.why_choose_us_image?.value || '';
        }
    },
    mounted() {
        this.initializeWhyChooseUsFeatures();
        this.initializeImageSetting();
    },
    methods: {
        initializeImageSetting() {
            // Ensure why_choose_us_image setting exists
            if (!this.settings.why_choose_us_image) {
                this.settings.why_choose_us_image = {
                    value: '',
                    type: 'text',
                    group: 'home_page'
                };
            }
        },
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
        async handleImageChange(file) {
            // Handle file clearing
            if (!file || (Array.isArray(file) && file.length === 0)) {
                this.imageFile = null;
                return;
            }

            const fileToUpload = Array.isArray(file) ? file[0] : file;

            // Validate it's actually a File object
            if (!(fileToUpload instanceof File)) {
                console.warn('Invalid file object:', fileToUpload);
                this.imageFile = null;
                return;
            }

            // Validate file type
            if (!fileToUpload.type.startsWith('image/')) {
                this.showError('Please select a valid image file');
                this.imageFile = null;
                return;
            }

            // Validate file size (5MB = 5 * 1024 * 1024 bytes)
            const maxSize = 5 * 1024 * 1024;
            if (fileToUpload.size > maxSize) {
                this.showError('Image size must be less than 5MB');
                this.imageFile = null;
                return;
            }

            this.uploading = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'home_page');

                const token = localStorage.getItem('admin_token');
                if (!token) {
                    throw new Error('Authentication token not found. Please log in again.');
                }

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data && response.data.success) {
                    // Ensure the setting exists
                    if (!this.settings.why_choose_us_image) {
                        this.settings.why_choose_us_image = {
                            value: '',
                            type: 'text',
                            group: 'home_page'
                        };
                    }
                    this.settings.why_choose_us_image.value = response.data.url;
                    this.imageFile = null;
                    this.showSuccess('Image uploaded successfully');
                } else {
                    throw new Error(response.data?.message || 'Failed to upload image');
                }
            } catch (error) {
                console.error('Error uploading image:', error);
                let errorMessage = 'Failed to upload image';
                if (error.response) {
                    errorMessage = error.response.data?.message || error.response.statusText || errorMessage;
                } else if (error.message) {
                    errorMessage = error.message;
                }
                this.showError(errorMessage);
                this.imageFile = null;
            } finally {
                this.uploading = false;
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
