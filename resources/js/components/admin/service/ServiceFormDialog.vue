<template>
    <v-dialog v-model="dialog" max-width="1200" scrollable persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                <span class="text-h5 font-weight-bold">
                    {{ editingService ? 'Edit Service' : 'Create New Service' }}
                </span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                    :disabled="saving || loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading service data...</p>
                    </div>
                </div>

                <!-- Form Content -->
                <v-form ref="formRef" v-else>
                    <v-tabs v-model="activeTab" bg-color="grey-lighten-4">
                        <v-tab value="basic">Basic Information</v-tab>
                        <v-tab value="content">Content</v-tab>
                        <v-tab value="seo">SEO & Meta</v-tab>
                    </v-tabs>

                    <v-window v-model="activeTab">
                        <!-- Basic Information Tab -->
                        <v-window-item value="basic">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.title" label="Service Title *" variant="outlined"
                                            :rules="[v => !!v || 'Title is required']" hint="Enter the service title"
                                            @blur="generateSlug" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.slug" label="Slug *" variant="outlined"
                                            :rules="[v => !!v || 'Slug is required', v => !v || /^[a-z0-9-]+$/.test(v) || 'Slug can only contain lowercase letters, numbers, and hyphens']"
                                            hint="URL-friendly version (auto-generated from title)" persistent-hint>
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateSlug" :disabled="!form.title"></v-btn>
                                            </template>
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.order" label="Order" type="number"
                                            variant="outlined" hint="Display order (lower numbers first)"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.short_description" label="Short Description"
                                            variant="outlined" rows="3"
                                            hint="Brief description shown in service listings"
                                            persistent-hint></v-textarea>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.icon" label="Icon" variant="outlined"
                                            hint="Material Design Icon name (e.g., mdi-battery-charging-high)"
                                            persistent-hint prepend-inner-icon="mdi-information-outline">
                                            <template v-slot:append>
                                                <v-icon v-if="form.icon" :icon="form.icon" size="small"></v-icon>
                                            </template>
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.price_range" label="Price Range" variant="outlined"
                                            hint="e.g., $100 - $500" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-switch v-model="form.published" label="Published" color="success"
                                            hint="Publish this service on the public website"
                                            persistent-hint></v-switch>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Content Tab -->
                        <v-window-item value="content">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Full Description</v-label>
                                        <RichTextEditor v-model="form.description" :active="activeTab === 'content'"
                                            placeholder="Enter detailed description of the service..." />
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Service Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="d-flex flex-column flex-md-row align-start">
                                                <!-- Image Preview -->
                                                <div v-if="imagePreview || form.image" class="mr-md-4 mb-4 mb-md-0">
                                                    <v-img :src="imagePreview || resolveImageUrl(form.image)"
                                                        max-width="300" max-height="300" class="rounded elevation-2"
                                                        cover></v-img>
                                                    <div class="mt-2">
                                                        <v-btn color="error" size="small" variant="text"
                                                            @click="removeImage" prepend-icon="mdi-delete">
                                                            Remove Image
                                                        </v-btn>
                                                    </div>
                                                </div>

                                                <!-- Upload Section -->
                                                <div class="flex-grow-1">
                                                    <v-file-input v-model="imageFile" accept="image/*"
                                                        label="Select Service Image" variant="outlined" prepend-icon=""
                                                        prepend-inner-icon="mdi-image" show-size clearable
                                                        hint="Recommended size: 1200x800px or larger. Max file size: 5MB"
                                                        persistent-hint @update:model-value="handleImageSelect">
                                                        <template v-slot:append>
                                                            <v-progress-circular v-if="uploadingImage" indeterminate
                                                                color="primary" size="24">
                                                            </v-progress-circular>
                                                        </template>
                                                    </v-file-input>

                                                    <v-alert v-if="imageFile && imageFile.size > 5242880" type="warning"
                                                        variant="tonal" density="compact" class="mt-2">
                                                        File size is larger than 5MB. Please choose a smaller image.
                                                    </v-alert>

                                                    <v-alert v-if="imageError" type="error" variant="tonal"
                                                        density="compact" class="mt-2" closable
                                                        @click:close="imageError = null">
                                                        <div class="text-body-2">
                                                            <strong>Upload Error:</strong> {{ imageError }}
                                                        </div>
                                                    </v-alert>

                                                    <div v-if="!imagePreview && !imageFile"
                                                        class="text-caption text-medium-emphasis mt-2">
                                                        No image selected. Upload an image to display with this
                                                        service.
                                                    </div>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Features</v-label>
                                        <v-textarea v-model="featuresText" variant="outlined" rows="4"
                                            hint="Enter one feature per line" persistent-hint
                                            @blur="updateFeaturesFromText"></v-textarea>
                                        <div v-if="form.features && form.features.length > 0" class="mt-2">
                                            <v-chip v-for="(feature, i) in form.features" :key="i" class="ma-1" closable
                                                @click:close="removeFeature(i)">
                                                {{ feature }}
                                            </v-chip>
                                        </div>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Benefits</v-label>
                                        <v-textarea v-model="benefitsText" variant="outlined" rows="4"
                                            hint="Enter one benefit per line" persistent-hint
                                            @blur="updateBenefitsFromText"></v-textarea>
                                        <div v-if="form.benefits && form.benefits.length > 0" class="mt-2">
                                            <v-chip v-for="(benefit, i) in form.benefits" :key="i" class="ma-1" closable
                                                @click:close="removeBenefit(i)" color="success" variant="flat">
                                                {{ benefit }}
                                            </v-chip>
                                        </div>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- SEO Tab -->
                        <v-window-item value="seo">
                            <div class="pa-6">
                                <v-alert type="info" variant="tonal" class="mb-6" density="compact">
                                    <div class="text-body-2">
                                        <strong>SEO Best Practices:</strong> Fill in these fields to improve your
                                        service visibility in search
                                        engines and social media.
                                        If left empty, default values from the service title and description will be
                                        used.
                                    </div>
                                </v-alert>

                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.meta_title" label="Meta Title" variant="outlined"
                                            hint="SEO title for search engines (recommended: 50-60 characters)"
                                            persistent-hint :counter="60" :color="getMetaTitleColor()"
                                            prepend-inner-icon="mdi-format-title">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateMetaTitle" :disabled="!form.title"
                                                    title="Auto-generate from service title">
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        <!-- Suggestion for Meta Title -->
                                        <v-alert v-if="!form.meta_title && suggestedMetaTitle" type="info"
                                            variant="tonal" density="compact" class="mt-2" closable>
                                            <div class="d-flex align-center justify-space-between">
                                                <span class="text-body-2">
                                                    <strong>Suggested:</strong> {{ suggestedMetaTitle }}
                                                </span>
                                                <v-btn size="small" color="primary" variant="text"
                                                    @click="applySuggestedMetaTitle" class="ml-2">
                                                    Apply
                                                </v-btn>
                                            </div>
                                        </v-alert>
                                        <div class="text-caption" :class="getMetaTitleColor() + '--text'">
                                            {{ form.meta_title.length }}/60 characters
                                            <span v-if="form.meta_title.length < 50" class="ml-2">
                                                (Too short - recommended: 50-60)
                                            </span>
                                            <span v-else-if="form.meta_title.length > 60" class="ml-2">
                                                (Too long - may be truncated)
                                            </span>
                                            <span v-else class="ml-2 text-success">✓ Good length</span>
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-textarea v-model="form.meta_description" label="Meta Description"
                                            variant="outlined" rows="4"
                                            hint="SEO description for search results (recommended: 150-160 characters)"
                                            persistent-hint :counter="160" :color="getMetaDescriptionColor()"
                                            prepend-inner-icon="mdi-text">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateMetaDescription" :disabled="!form.short_description"
                                                    title="Auto-generate from short description">
                                                </v-btn>
                                            </template>
                                        </v-textarea>
                                        <!-- Suggestion for Meta Description -->
                                        <v-alert v-if="!form.meta_description && suggestedMetaDescription" type="info"
                                            variant="tonal" density="compact" class="mt-2" closable>
                                            <div class="d-flex align-center justify-space-between flex-wrap">
                                                <span class="text-body-2 mb-2">
                                                    <strong>Suggested:</strong> {{ suggestedMetaDescription }}
                                                </span>
                                                <v-btn size="small" color="primary" variant="text"
                                                    @click="applySuggestedMetaDescription">
                                                    Apply
                                                </v-btn>
                                            </div>
                                        </v-alert>
                                        <div class="text-caption" :class="getMetaDescriptionColor() + '--text'">
                                            {{ form.meta_description.length }}/160 characters
                                            <span v-if="form.meta_description.length < 120" class="ml-2">
                                                (Too short - recommended: 150-160)
                                            </span>
                                            <span v-else-if="form.meta_description.length > 160" class="ml-2">
                                                (Too long - may be truncated)
                                            </span>
                                            <span v-else class="ml-2 text-success">✓ Good length</span>
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="form.meta_keywords" label="Meta Keywords"
                                            variant="outlined"
                                            hint="Comma-separated keywords for SEO (e.g., power backup, UPS systems, battery)"
                                            persistent-hint prepend-inner-icon="mdi-tag-multiple">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-auto-fix" size="small" variant="text"
                                                    @click="generateMetaKeywords"
                                                    :disabled="!form.title && !form.short_description && form.features.length === 0"
                                                    title="Auto-generate from title, description, and features">
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        <!-- Suggestion for Meta Keywords -->
                                        <v-alert v-if="!form.meta_keywords && suggestedMetaKeywords" type="info"
                                            variant="tonal" density="compact" class="mt-2" closable>
                                            <div class="d-flex align-center justify-space-between flex-wrap">
                                                <span class="text-body-2 mb-2">
                                                    <strong>Suggested:</strong> {{ suggestedMetaKeywords }}
                                                </span>
                                                <v-btn size="small" color="primary" variant="text"
                                                    @click="applySuggestedMetaKeywords">
                                                    Apply
                                                </v-btn>
                                            </div>
                                        </v-alert>
                                        <div class="text-caption text-medium-emphasis">
                                            {{(form.meta_keywords || '').split(',').filter(k => k.trim()).length}}
                                            keywords entered
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Open Graph (Social Media)</h3>
                                        <p class="text-body-2 text-medium-emphasis mb-4">
                                            This image will be displayed when your service is shared on social media
                                            platforms like Facebook,
                                            Twitter, LinkedIn, etc.
                                        </p>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-label class="mb-2">Open Graph Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="d-flex flex-column flex-md-row align-start">
                                                <!-- OG Image Preview -->
                                                <div v-if="ogImagePreview || form.og_image"
                                                    class="mr-md-4 mb-4 mb-md-0">
                                                    <v-img :src="ogImagePreview || resolveImageUrl(form.og_image)"
                                                        max-width="300" max-height="300" class="rounded elevation-2"
                                                        cover></v-img>
                                                    <div class="mt-2">
                                                        <v-btn color="error" size="small" variant="text"
                                                            @click="removeOgImage" prepend-icon="mdi-delete">
                                                            Remove Image
                                                        </v-btn>
                                                    </div>
                                                    <div class="text-caption text-medium-emphasis mt-2">
                                                        Recommended: 1200x630px (1.91:1 ratio)
                                                    </div>
                                                </div>

                                                <!-- Upload Section -->
                                                <div class="flex-grow-1">
                                                    <v-file-input v-model="ogImageFile" accept="image/*"
                                                        label="Select Open Graph Image" variant="outlined"
                                                        prepend-icon="" prepend-inner-icon="mdi-image" show-size
                                                        clearable
                                                        hint="Recommended size: 1200x630px. Max file size: 5MB"
                                                        persistent-hint @update:model-value="handleOgImageSelect">
                                                        <template v-slot:append>
                                                            <v-progress-circular v-if="uploadingOgImage" indeterminate
                                                                color="primary" size="24">
                                                            </v-progress-circular>
                                                        </template>
                                                    </v-file-input>

                                                    <v-alert v-if="ogImageFile && ogImageFile.size > 5242880"
                                                        type="warning" variant="tonal" density="compact" class="mt-2">
                                                        File size is larger than 5MB. Please choose a smaller image.
                                                    </v-alert>

                                                    <v-alert v-if="ogImageError" type="error" variant="tonal"
                                                        density="compact" class="mt-2" closable
                                                        @click:close="ogImageError = null">
                                                        <div class="text-body-2">
                                                            <strong>Upload Error:</strong> {{ ogImageError }}
                                                        </div>
                                                    </v-alert>

                                                    <div v-if="!ogImagePreview && !ogImageFile"
                                                        class="text-caption text-medium-emphasis mt-2">
                                                        No image selected. If not provided, the service image will
                                                        be used.
                                                    </div>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>

                                    <!-- SEO Preview -->
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Search Engine Preview</h3>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="search-preview">
                                                <div class="search-url mb-1 text-caption" style="color: #006621;">
                                                    {{ getPreviewUrl() }}
                                                </div>
                                                <div class="search-title mb-1 text-h6"
                                                    :class="getMetaTitleColor() + '--text'"
                                                    style="color: #1a0dab; cursor: pointer;">
                                                    {{ getPreviewTitle() }}
                                                </div>
                                                <div class="search-description text-body-2"
                                                    :class="getMetaDescriptionColor() + '--text'"
                                                    style="color: #545454;">
                                                    {{ getPreviewDescription() }}
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>
                    </v-window>
                </v-form>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-4">
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="closeDialog" :disabled="saving">Cancel</v-btn>
                <v-btn color="primary" variant="flat" @click="saveService" :loading="saving">
                    {{ editingService ? 'Update' : 'Create' }} Service
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import RichTextEditor from '../../common/RichTextEditor.vue';
import { normalizeUploadPath, resolveUploadUrl } from '../../../utils/uploads';

export default {
    name: 'ServiceFormDialog',
    components: {
        RichTextEditor
    },
    mixins: [adminPaginationMixin],
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        editingService: {
            type: Object,
            default: null
        }
    },
    emits: ['update:modelValue', 'saved'],
    data() {
        return {
            activeTab: 'basic',
            loading: false,
            saving: false,
            imageFile: null,
            imagePreview: null,
            uploadingImage: false,
            imageError: null,
            ogImageFile: null,
            ogImagePreview: null,
            uploadingOgImage: false,
            ogImageError: null,
            featuresText: '',
            benefitsText: '',
            form: {
                title: '',
                slug: '',
                short_description: '',
                description: '',
                icon: '',
                image: '',
                price_range: '',
                features: [],
                benefits: [],
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: '',
                published: false,
                order: 0
            }
        };
    },
    computed: {
        dialog: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        suggestedMetaTitle() {
            if (this.form.meta_title || !this.form.title) return null;
            return this.form.title.length > 60
                ? this.form.title.substring(0, 57) + '...'
                : this.form.title;
        },
        suggestedMetaDescription() {
            if (this.form.meta_description || !this.form.short_description) return null;
            let suggestion = this.form.short_description;
            if (suggestion.length > 160) {
                // Try to cut at a word boundary
                suggestion = suggestion.substring(0, 157);
                const lastSpace = suggestion.lastIndexOf(' ');
                if (lastSpace > 120) {
                    suggestion = suggestion.substring(0, lastSpace) + '...';
                } else {
                    suggestion += '...';
                }
            }
            return suggestion;
        },
        suggestedMetaKeywords() {
            if (this.form.meta_keywords) return null;
            return this.generateSuggestedKeywords();
        }
    },
    watch: {
        modelValue(newVal) {
            if (newVal) {
                this.activeTab = 'basic';
                if (this.editingService) {
                    this.loadServiceForEdit();
                } else {
                    this.resetForm();
                }
            }
        },
        editingService: {
            handler(newVal) {
                if (newVal && this.dialog) {
                    this.loadServiceForEdit();
                } else if (!newVal && this.dialog) {
                    this.resetForm();
                }
            },
            immediate: false
        }
    },
    methods: {
        resetForm() {
            this.form = {
                title: '',
                slug: '',
                short_description: '',
                description: '',
                icon: '',
                image: '',
                price_range: '',
                features: [],
                benefits: [],
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: '',
                published: false,
                order: 0
            };
            this.imageFile = null;
            this.imagePreview = null;
            this.imageError = null;
            this.ogImageFile = null;
            this.ogImagePreview = null;
            this.ogImageError = null;
            this.featuresText = '';
            this.benefitsText = '';

            if (this.$refs.formRef) {
                this.$refs.formRef.resetValidation();
            }
        },
        async loadServiceForEdit() {
            if (!this.editingService) return;

            this.loading = true;
            try {
                const response = await this.$axios.get(`/api/v1/services/${this.editingService.id}`, {
                    headers: this.getAuthHeaders()
                });
                const data = response.data;

                this.form = {
                    title: data.title || '',
                    slug: data.slug || '',
                    short_description: data.short_description || '',
                    description: data.description || '',
                    icon: data.icon || '',
                    image: data.image || '',
                    price_range: data.price_range || '',
                    features: Array.isArray(data.features) ? [...data.features] : [],
                    benefits: Array.isArray(data.benefits) ? [...data.benefits] : [],
                    meta_title: data.meta_title || '',
                    meta_description: data.meta_description || '',
                    meta_keywords: data.meta_keywords || '',
                    og_image: data.og_image || '',
                    published: data.published || false,
                    order: data.order || 0
                };

                // Normalize and resolve image URLs
                const normalizedImage = this.normalizeImageInput(data.image || '');
                const normalizedOgImage = this.normalizeImageInput(data.og_image || '');
                this.form.image = normalizedImage;
                this.form.og_image = normalizedOgImage;
                this.imagePreview = this.resolveImageUrl(data.image || normalizedImage);
                this.ogImagePreview = this.resolveImageUrl(data.og_image || normalizedOgImage);
                this.featuresText = Array.isArray(data.features) ? data.features.join('\n') : '';
                this.benefitsText = Array.isArray(data.benefits) ? data.benefits.join('\n') : '';
            } catch (error) {
                this.handleApiError(error, 'Failed to load service data');
                this.closeDialog();
            } finally {
                this.loading = false;
            }
        },
        closeDialog() {
            if (!this.saving && !this.loading) {
                this.dialog = false;
                this.resetForm();
            }
        },
        generateSlug() {
            if (this.form.title && !this.editingService) {
                this.form.slug = this.form.title
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        },
        handleImageSelect(file) {
            // Clear previous error
            this.imageError = null;

            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.imageError = 'Image file size must be less than 5MB';
                    this.imageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.imageError = 'Please select a valid image file (jpeg, jpg, png, gif, webp)';
                    this.imageFile = null;
                    return;
                }

                this.imageFile = selectedFile;

                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.onerror = () => {
                        this.showError('Failed to read image file');
                        this.imageFile = null;
                        this.imagePreview = null;
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    this.imagePreview = null;
                }
            } else {
                this.imageFile = null;
                if (!this.form.image) {
                    this.imagePreview = null;
                } else {
                    // Update preview when image URL is directly entered (if no file is selected)
                    this.imagePreview = this.resolveImageUrl(this.form.image);
                }
            }
        },
        removeImage() {
            if (confirm('Are you sure you want to remove this image?')) {
                this.imagePreview = null;
                this.imageFile = null;
                this.form.image = '';
                this.imageError = null; // Clear error
            }
        },
        handleOgImageSelect(file) {
            // Clear previous error
            this.ogImageError = null;

            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.ogImageError = 'OG image file size must be less than 5MB';
                    this.ogImageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.ogImageError = 'Please select a valid image file (jpeg, jpg, png, gif, webp)';
                    this.ogImageFile = null;
                    return;
                }

                this.ogImageFile = selectedFile;

                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.ogImagePreview = e.target.result;
                    };
                    reader.onerror = () => {
                        this.showError('Failed to read image file');
                        this.ogImageFile = null;
                        this.ogImagePreview = null;
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    this.ogImagePreview = null;
                }
            } else {
                this.ogImageFile = null;
                if (!this.form.og_image) {
                    this.ogImagePreview = null;
                } else {
                    // Update preview when OG image URL is directly entered (if no file is selected)
                    this.ogImagePreview = this.resolveImageUrl(this.form.og_image);
                }
            }
        },
        removeOgImage() {
            if (confirm('Are you sure you want to remove the Open Graph image?')) {
                this.ogImagePreview = null;
                this.ogImageFile = null;
                this.form.og_image = '';
                this.ogImageError = null; // Clear error
            }
        },
        generateMetaTitle() {
            if (this.form.title) {
                this.form.meta_title = this.form.title.length > 60
                    ? this.form.title.substring(0, 57) + '...'
                    : this.form.title;
            }
        },
        generateMetaDescription() {
            if (this.form.short_description) {
                let description = this.form.short_description;
                if (description.length > 160) {
                    // Try to cut at a word boundary
                    description = description.substring(0, 157);
                    const lastSpace = description.lastIndexOf(' ');
                    if (lastSpace > 120) {
                        description = description.substring(0, lastSpace) + '...';
                    } else {
                        description += '...';
                    }
                }
                this.form.meta_description = description;
            }
        },
        generateMetaKeywords() {
            const keywords = this.generateSuggestedKeywords();
            if (keywords) {
                this.form.meta_keywords = keywords;
            }
        },
        generateSuggestedKeywords() {
            const keywords = [];

            // Extract keywords from title
            if (this.form.title) {
                const titleWords = this.form.title
                    .toLowerCase()
                    .replace(/[^\w\s]/g, ' ')
                    .split(/\s+/)
                    .filter(word => word.length > 3)
                    .slice(0, 5);
                keywords.push(...titleWords);
            }

            // Extract keywords from short description
            if (this.form.short_description) {
                const descWords = this.form.short_description
                    .toLowerCase()
                    .replace(/[^\w\s]/g, ' ')
                    .split(/\s+/)
                    .filter(word => word.length > 4)
                    .slice(0, 5);
                keywords.push(...descWords);
            }

            // Add features as keywords
            if (this.form.features && this.form.features.length > 0) {
                this.form.features.forEach(feature => {
                    const featureWords = feature
                        .toLowerCase()
                        .replace(/[^\w\s]/g, ' ')
                        .split(/\s+/)
                        .filter(word => word.length > 3)
                        .slice(0, 2);
                    keywords.push(...featureWords);
                });
            }

            // Remove duplicates and common words
            const commonWords = ['the', 'and', 'for', 'with', 'from', 'this', 'that', 'your', 'our', 'service', 'services'];
            const uniqueKeywords = [...new Set(keywords)]
                .filter(word => !commonWords.includes(word))
                .slice(0, 10);

            return uniqueKeywords.length > 0 ? uniqueKeywords.join(', ') : null;
        },
        suggestMetaFields() {
            // This method is called when basic info changes to suggest meta fields
            // It's called asynchronously via watchers
        },
        applySuggestedMetaTitle() {
            if (this.suggestedMetaTitle) {
                this.form.meta_title = this.suggestedMetaTitle;
            }
        },
        applySuggestedMetaDescription() {
            if (this.suggestedMetaDescription) {
                this.form.meta_description = this.suggestedMetaDescription;
            }
        },
        applySuggestedMetaKeywords() {
            if (this.suggestedMetaKeywords) {
                this.form.meta_keywords = this.suggestedMetaKeywords;
            }
        },
        getMetaTitleColor() {
            const length = this.form.meta_title ? this.form.meta_title.length : 0;
            if (length === 0) return 'primary';
            if (length < 50) return 'warning';
            if (length > 60) return 'error';
            return 'success';
        },
        getMetaDescriptionColor() {
            const length = this.form.meta_description ? this.form.meta_description.length : 0;
            if (length === 0) return 'primary';
            if (length < 120) return 'warning';
            if (length > 160) return 'error';
            return 'success';
        },
        getPreviewTitle() {
            return this.form.meta_title || this.form.title || 'Service Title';
        },
        getPreviewDescription() {
            return this.form.meta_description || this.form.short_description || 'Service description will appear here in search results...';
        },
        getPreviewUrl() {
            const baseUrl = window.location.origin || 'https://example.com';
            const slug = this.form.slug || 'service-slug';
            return `${baseUrl}/services/${slug}`;
        },
        updateFeaturesFromText() {
            if (this.featuresText) {
                this.form.features = this.featuresText
                    .split('\n')
                    .map(f => f.trim())
                    .filter(f => f.length > 0);
            } else {
                this.form.features = [];
            }
        },
        updateBenefitsFromText() {
            if (this.benefitsText) {
                this.form.benefits = this.benefitsText
                    .split('\n')
                    .map(b => b.trim())
                    .filter(b => b.length > 0);
            } else {
                this.form.benefits = [];
            }
        },
        removeFeature(index) {
            this.form.features.splice(index, 1);
            this.featuresText = this.form.features.join('\n');
        },
        removeBenefit(index) {
            this.form.benefits.splice(index, 1);
            this.benefitsText = this.form.benefits.join('\n');
        },
        async uploadImage() {
            if (!this.imageFile) return null;

            const fileToUpload = Array.isArray(this.imageFile) ? this.imageFile[0] : this.imageFile;
            if (!fileToUpload) return null;

            this.uploadingImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'services');
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = this.normalizeImageInput(response.data.path || response.data.url);
                    this.form.image = uploadedPath;
                    this.imagePreview = this.resolveImageUrl(response.data.url || uploadedPath);
                    this.imageFile = null;
                    this.imageError = null; // Clear error on success
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload image');
                }
            } catch (error) {
                console.error('Error uploading image:', error);
                console.error('Error response:', error.response?.data);

                // Extract validation errors
                let errorMessage = 'Failed to upload image';
                if (error.response?.status === 422) {
                    // Validation error
                    const errors = error.response.data.errors;
                    if (errors && errors.image) {
                        errorMessage = Array.isArray(errors.image) ? errors.image.join(', ') : errors.image;
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                } else if (error.message) {
                    errorMessage = error.message;
                }

                this.imageError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingImage = false;
            }
        },
        async uploadOgImage() {
            if (!this.ogImageFile) return null;

            const fileToUpload = Array.isArray(this.ogImageFile) ? this.ogImageFile[0] : this.ogImageFile;
            if (!fileToUpload) return null;

            this.uploadingOgImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'services/og');
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = this.normalizeImageInput(response.data.path || response.data.url);
                    this.form.og_image = uploadedPath;
                    this.ogImagePreview = this.resolveImageUrl(response.data.url || uploadedPath);
                    this.ogImageFile = null;
                    this.ogImageError = null; // Clear error on success
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload OG image');
                }
            } catch (error) {
                console.error('Error uploading OG image:', error);
                console.error('Error response:', error.response?.data);

                // Extract validation errors
                let errorMessage = 'Failed to upload OG image';
                if (error.response?.status === 422) {
                    // Validation error
                    const errors = error.response.data.errors;
                    if (errors && errors.image) {
                        errorMessage = Array.isArray(errors.image) ? errors.image.join(', ') : errors.image;
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                } else if (error.message) {
                    errorMessage = error.message;
                }

                this.ogImageError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingOgImage = false;
            }
        },
        async saveService() {
            // Validate form
            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            // Update arrays from text
            this.updateFeaturesFromText();
            this.updateBenefitsFromText();

            this.saving = true;

            try {
                // Upload images if new files are selected
                if (this.imageFile) {
                    try {
                        await this.uploadImage();
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload image');
                        return;
                    }
                }

                if (this.ogImageFile) {
                    try {
                        await this.uploadOgImage();
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload OG image');
                        return;
                    }
                }

                // Prepare payload with normalized image paths
                const payload = {
                    title: this.form.title,
                    slug: this.form.slug,
                    short_description: this.form.short_description || null,
                    description: this.form.description || null,
                    icon: this.form.icon || null,
                    image: this.normalizeImageInput(this.form.image) || null,
                    price_range: this.form.price_range || null,
                    features: this.form.features.length > 0 ? this.form.features : null,
                    benefits: this.form.benefits.length > 0 ? this.form.benefits : null,
                    meta_title: this.form.meta_title || null,
                    meta_description: this.form.meta_description || null,
                    meta_keywords: this.form.meta_keywords || null,
                    og_image: this.normalizeImageInput(this.form.og_image) || null,
                    published: this.form.published || false,
                    order: this.form.order || 0
                };

                // Save service
                if (this.editingService) {
                    await this.$axios.put(`/api/v1/services/${this.editingService.id}`, payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service updated successfully');
                } else {
                    await this.$axios.post('/api/v1/services', payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service created successfully');
                }

                this.$emit('saved');
                this.closeDialog();
            } catch (error) {
                this.handleApiError(error, this.editingService ? 'Failed to update service' : 'Failed to create service');
            } finally {
                this.saving = false;
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

<style scoped>
/* SEO Preview Styles */
.search-preview {
    max-width: 600px;
    padding: 16px;
    font-family: arial, sans-serif;
}

.search-url {
    font-size: 14px;
    line-height: 1.3;
    word-wrap: break-word;
}

.search-title {
    font-size: 20px;
    line-height: 1.3;
    font-weight: 400;
    cursor: pointer;
}

.search-title:hover {
    text-decoration: underline;
}

.search-description {
    line-height: 1.58;
    word-wrap: break-word;
}
</style>
