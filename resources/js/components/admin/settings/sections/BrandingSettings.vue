<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">Branding</h2>
            <p class="text-body-2 text-medium-emphasis">Customize the look and feel of your site</p>
        </div>

        <v-row>
            <!-- Logo Upload Section -->
            <v-col cols="12" md="6">
                <v-card variant="outlined" class="pa-4">
                    <div class="text-subtitle-1 font-weight-bold mb-3">Logo</div>

                    <!-- Logo Preview -->
                    <div v-if="settings.logo.value" class="mb-4 text-center">
                        <v-img :src="resolveImageUrl(settings.logo.value)" alt="Logo Preview" max-height="120" max-width="300" contain
                            class="mx-auto mb-2" style="border: 1px solid rgba(0,0,0,0.1); border-radius: 4px;">
                        </v-img>
                        <v-btn size="small" variant="text" color="error" prepend-icon="mdi-delete"
                            @click="clearLogo">Remove Logo</v-btn>
                    </div>

                    <!-- File Upload -->
                    <v-file-input v-model="logoFile" label="Upload Logo" variant="outlined" density="comfortable"
                        color="primary" accept="image/*" prepend-icon="mdi-image"
                        hint="Upload a logo image (JPG, PNG, GIF, WebP - Max 5MB). Recommended size: 200x60px or 300x90px"
                        persistent-hint show-size @update:model-value="handleLogoUpload" class="mb-3">
                        <template v-slot:append-inner v-if="uploadingLogo">
                            <v-progress-circular indeterminate size="20" color="primary"></v-progress-circular>
                        </template>
                    </v-file-input>

                    <!-- Or Enter URL -->
                    <v-text-field v-model="settings.logo.value" label="Or Enter Logo URL" variant="outlined"
                        density="comfortable" color="primary" hint="Enter a direct URL to the logo image"
                        persistent-hint prepend-inner-icon="mdi-link">
                        <template v-slot:append-inner v-if="settings.logo.value && !logoFile">
                            <v-btn icon="mdi-open-in-new" variant="text" size="small"
                                @click="window.open(resolveImageUrl(settings.logo.value), '_blank')"></v-btn>
                        </template>
                    </v-text-field>
                </v-card>
            </v-col>

            <!-- Favicon Upload Section -->
            <v-col cols="12" md="6">
                <v-card variant="outlined" class="pa-4">
                    <div class="text-subtitle-1 font-weight-bold mb-3">Favicon</div>

                    <!-- Favicon Preview -->
                    <div v-if="settings.favicon.value" class="mb-4 text-center">
                        <v-img :src="resolveImageUrl(settings.favicon.value)" alt="Favicon Preview" max-height="64" max-width="64"
                            contain class="mx-auto mb-2" style="border: 1px solid rgba(0,0,0,0.1); border-radius: 4px;">
                        </v-img>
                        <v-btn size="small" variant="text" color="error" prepend-icon="mdi-delete"
                            @click="clearFavicon">Remove
                            Favicon</v-btn>
                    </div>

                    <!-- File Upload -->
                    <v-file-input v-model="faviconFile" label="Upload Favicon" variant="outlined" density="comfortable"
                        color="primary" accept="image/*" prepend-icon="mdi-image-size-select-actual"
                        hint="Upload a favicon image (JPG, PNG, GIF, WebP, ICO - Max 5MB). Recommended size: 32x32px or 64x64px (square format)"
                        persistent-hint show-size @update:model-value="handleFaviconUpload" class="mb-3">
                        <template v-slot:append-inner v-if="uploadingFavicon">
                            <v-progress-circular indeterminate size="20" color="primary"></v-progress-circular>
                        </template>
                    </v-file-input>

                    <!-- Or Enter URL -->
                    <v-text-field v-model="settings.favicon.value" label="Or Enter Favicon URL" variant="outlined"
                        density="comfortable" color="primary" hint="Enter a direct URL to the favicon image"
                        persistent-hint prepend-inner-icon="mdi-link">
                        <template v-slot:append-inner v-if="settings.favicon.value && !faviconFile">
                            <v-btn icon="mdi-open-in-new" variant="text" size="small"
                                @click="window.open(resolveImageUrl(settings.favicon.value), '_blank')"></v-btn>
                        </template>
                    </v-text-field>
                </v-card>
            </v-col>

            <!-- Primary Color -->
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.primary_color.value" label="Primary Color" type="color"
                    variant="outlined" density="comfortable" style="height: 60px" hint="Primary brand color"
                    persistent-hint></v-text-field>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import { normalizeUploadPath, resolveUploadUrl } from '../../../../utils/uploads';

export default {
    name: 'BrandingSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            logoFile: null,
            faviconFile: null,
            uploadingLogo: false,
            uploadingFavicon: false
        };
    },
    methods: {
        async handleLogoUpload() {
            if (!this.logoFile) {
                return;
            }

            const fileToUpload = Array.isArray(this.logoFile) ? this.logoFile[0] : this.logoFile;
            if (!fileToUpload) {
                return;
            }

            // Validate file type
            if (!fileToUpload.type.startsWith('image/')) {
                this.showError('Please select a valid image file');
                this.logoFile = null;
                return;
            }

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (fileToUpload.size > maxSize) {
                this.showError('File size must be less than 5MB');
                this.logoFile = null;
                return;
            }

            this.uploadingLogo = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'branding');

                const token = localStorage.getItem('admin_token');
                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = this.normalizeImageInput(response.data.path || response.data.url);
                    this.settings.logo.value = uploadedPath;
                    this.logoFile = null;
                    this.showSuccess('Logo uploaded successfully');
                } else {
                    throw new Error(response.data.message || 'Failed to upload logo');
                }
            } catch (error) {
                console.error('Error uploading logo:', error);
                this.showError(error.response?.data?.message || error.message || 'Failed to upload logo');
                this.logoFile = null;
            } finally {
                this.uploadingLogo = false;
            }
        },
        async handleFaviconUpload() {
            if (!this.faviconFile) {
                return;
            }

            const fileToUpload = Array.isArray(this.faviconFile) ? this.faviconFile[0] : this.faviconFile;
            if (!fileToUpload) {
                return;
            }

            // Validate file type
            if (!fileToUpload.type.startsWith('image/')) {
                this.showError('Please select a valid image file');
                this.faviconFile = null;
                return;
            }

            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (fileToUpload.size > maxSize) {
                this.showError('File size must be less than 5MB');
                this.faviconFile = null;
                return;
            }

            this.uploadingFavicon = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'branding');

                const token = localStorage.getItem('admin_token');
                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = this.normalizeImageInput(response.data.path || response.data.url);
                    this.settings.favicon.value = uploadedPath;
                    this.faviconFile = null;
                    this.showSuccess('Favicon uploaded successfully');
                } else {
                    throw new Error(response.data.message || 'Failed to upload favicon');
                }
            } catch (error) {
                console.error('Error uploading favicon:', error);
                this.showError(error.response?.data?.message || error.message || 'Failed to upload favicon');
                this.faviconFile = null;
            } finally {
                this.uploadingFavicon = false;
            }
        },
        clearLogo() {
            this.settings.logo.value = '';
            this.logoFile = null;
        },
        clearFavicon() {
            this.settings.favicon.value = '';
            this.faviconFile = null;
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
        },
        normalizeImageInput(imageValue) {
            return normalizeUploadPath(imageValue);
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        }
    }
};
</script>
