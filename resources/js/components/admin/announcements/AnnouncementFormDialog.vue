<template>
    <v-dialog v-model="dialog" max-width="1200" scrollable persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                <span class="text-h5 font-weight-bold">
                    {{ editingAnnouncement ? 'Edit Announcement' : 'Create New Announcement' }}
                </span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                    :disabled="saving || loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading announcement data...</p>
                    </div>
                </div>

                <!-- Form Content -->
                <v-form ref="formRef" v-else>
                    <v-tabs v-model="activeTab" bg-color="grey-lighten-4">
                        <v-tab value="basic">Basic Information</v-tab>
                        <v-tab value="content">Content</v-tab>
                        <v-tab value="settings">Settings & Schedule</v-tab>
                        <v-tab value="seo">SEO & Meta</v-tab>
                    </v-tabs>

                    <v-window v-model="activeTab">
                        <!-- Basic Information Tab -->
                        <v-window-item value="basic">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.title" label="Announcement Title *"
                                            variant="outlined" :rules="[v => !!v || 'Title is required']"
                                            hint="Enter the announcement title" @blur="generateSlug"
                                            persistent-hint></v-text-field>
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
                                        <v-select v-model="form.type" :items="typeOptions" label="Type *"
                                            variant="outlined" :rules="[v => !!v || 'Type is required']"
                                            hint="Announcement category" persistent-hint></v-select>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-select v-model="form.display_type" :items="displayTypeOptions"
                                            label="Display Type *" variant="outlined"
                                            :rules="[v => !!v || 'Display type is required']"
                                            hint="How the announcement will be displayed" persistent-hint></v-select>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.priority" label="Priority" type="number"
                                            variant="outlined" hint="Higher priority shows first (0-999)"
                                            persistent-hint min="0" max="999"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.short_description" label="Short Description"
                                            variant="outlined" rows="3" hint="Brief description shown in listings"
                                            persistent-hint></v-textarea>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Content Tab -->
                        <v-window-item value="content">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Full Content</v-label>
                                        <RichTextEditor v-model="form.content" :active="activeTab === 'content'"
                                            placeholder="Enter detailed announcement content..." />
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Media</h3>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-label class="mb-2">Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div v-if="imagePreview || form.image" class="mb-4">
                                                <v-img :src="imagePreview || resolveImageUrl(form.image)"
                                                    max-width="300" max-height="300" class="rounded elevation-2"
                                                    cover></v-img>
                                                <v-btn color="error" size="small" variant="text" class="mt-2"
                                                    @click="removeImage" prepend-icon="mdi-delete">
                                                    Remove Image
                                                </v-btn>
                                            </div>
                                            <v-file-input v-model="imageFile" accept="image/*" label="Select Image"
                                                variant="outlined" prepend-inner-icon="mdi-image" show-size clearable
                                                @update:model-value="handleImageSelect">
                                                <template v-slot:append>
                                                    <v-progress-circular v-if="uploadingImage" indeterminate
                                                        color="primary" size="24"></v-progress-circular>
                                                </template>
                                            </v-file-input>
                                            <v-alert v-if="imageFile && imageFile.size > 5242880" type="warning"
                                                variant="tonal" density="compact" class="mt-2">
                                                File size is larger than 5MB. Please choose a smaller image.
                                            </v-alert>
                                            <v-alert v-if="imageError" type="error" variant="tonal" density="compact"
                                                class="mt-2" closable @click:close="imageError = null">
                                                <div class="text-body-2">
                                                    <strong>Upload Error:</strong> {{ imageError }}
                                                </div>
                                            </v-alert>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-label class="mb-2">Video (URL or file path)</v-label>
                                        <v-text-field v-model="form.video" label="Video URL or Path" variant="outlined"
                                            hint="Enter video URL, YouTube link (youtube.com or youtu.be), or upload file path"
                                            persistent-hint
                                            placeholder="https://youtu.be/VIDEO_ID or https://www.youtube.com/watch?v=VIDEO_ID"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">External Link</h3>
                                    </v-col>
                                    <v-col cols="12" md="10">
                                        <v-text-field v-model="form.external_link" label="External Link URL"
                                            variant="outlined" hint="Optional link to external page" persistent-hint
                                            type="url"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="2">
                                        <v-switch v-model="form.open_in_new_tab" label="Open in new tab" color="primary"
                                            hint="Open link in new browser tab" persistent-hint></v-switch>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Settings & Schedule Tab -->
                        <v-window-item value="settings">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <h3 class="text-h6 font-weight-bold mb-4">Schedule</h3>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <DateTimePicker v-model="form.start_date" label="Start Date & Time"
                                            hint="When announcement becomes active" :persistent-hint="true"
                                            title="Select Start Date & Time">
                                        </DateTimePicker>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <DateTimePicker v-model="form.end_date" label="End Date & Time"
                                            hint="When announcement expires" :persistent-hint="true"
                                            :min-date="getStartDateForMin()" title="Select End Date & Time">
                                        </DateTimePicker>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Status & Settings</h3>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-switch v-model="form.is_active" label="Active" color="success"
                                            hint="Make this announcement active" persistent-hint></v-switch>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.language" label="Language" variant="outlined"
                                            hint="Language code (e.g., en, ar) - optional for multi-language"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- SEO & Meta Tab -->
                        <v-window-item value="seo">
                            <div class="pa-6">
                                <v-alert type="info" variant="tonal" class="mb-6" density="compact">
                                    <div class="text-body-2">
                                        <strong>SEO Best Practices:</strong> Fill in these fields to improve your
                                        announcement visibility in search engines and social media.
                                        If left empty, default values from the announcement title and description will
                                        be
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
                                                    title="Auto-generate from announcement title">
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
                                            <span v-else-if="form.meta_title.length > 0" class="ml-2 text-success">✓
                                                Good length</span>
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
                                            <span v-else-if="form.meta_description.length > 0"
                                                class="ml-2 text-success">✓ Good length</span>
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="form.meta_keywords" label="Meta Keywords"
                                            variant="outlined"
                                            hint="Comma-separated keywords for SEO (e.g., announcement, company news, event)"
                                            persistent-hint prepend-inner-icon="mdi-tag-multiple">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-auto-fix" size="small" variant="text"
                                                    @click="generateMetaKeywords"
                                                    :disabled="!form.title && !form.short_description"
                                                    title="Auto-generate from title and description">
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
                                            This image will be displayed when your announcement is shared on social
                                            media
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
                                                        No image selected. If not provided, the announcement image will
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

            <v-divider></v-divider>

            <v-card-actions class="pa-4">
                <v-spacer></v-spacer>
                <v-btn color="grey" variant="text" @click="closeDialog" :disabled="saving">
                    Cancel
                </v-btn>
                <v-btn color="primary" variant="elevated" @click="saveAnnouncement" :loading="saving"
                    :disabled="loading">
                    {{ editingAnnouncement ? 'Update' : 'Create' }} Announcement
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import RichTextEditor from '../../common/RichTextEditor.vue';
import DateTimePicker from '../../common/DateTimePicker.vue';
import { normalizeUploadPath, resolveUploadUrl } from '../../../utils/uploads';

export default {
    name: 'AnnouncementFormDialog',
    components: {
        RichTextEditor,
        DateTimePicker
    },
    mixins: [adminPaginationMixin],
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        editingAnnouncement: {
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
            typeOptions: [
                { title: 'Company News', value: 'company_news' },
                { title: 'Offers / Promotions', value: 'offers_promotions' },
                { title: 'Events', value: 'events' },
                { title: 'Holidays', value: 'holidays' },
                { title: 'Urgent Alerts', value: 'urgent_alerts' }
            ],
            displayTypeOptions: [
                { title: 'Slider Banner', value: 'slider_banner' },
                { title: 'Popup', value: 'popup' },
                { title: 'Sidebar Ticker', value: 'sidebar_ticker' },
                { title: 'Page Section', value: 'page_section' }
            ],
            form: {
                title: '',
                slug: '',
                content: '',
                short_description: '',
                type: 'company_news',
                display_type: 'slider_banner',
                image: '',
                video: '',
                external_link: '',
                open_in_new_tab: false,
                start_date: '',
                end_date: '',
                is_active: true,
                priority: 0,
                language: '',
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: ''
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
                if (this.editingAnnouncement) {
                    this.loadAnnouncementData();
                } else {
                    this.resetForm();
                }
            }
        }
    },
    methods: {
        normalizeImageInput(imageValue) {
            return normalizeUploadPath(imageValue);
        },
        resolveImageUrl(value) {
            return resolveUploadUrl(value);
        },
        resetForm() {
            this.form = {
                title: '',
                slug: '',
                content: '',
                short_description: '',
                type: 'company_news',
                display_type: 'slider_banner',
                image: '',
                video: '',
                external_link: '',
                open_in_new_tab: false,
                start_date: '',
                end_date: '',
                is_active: true,
                priority: 0,
                language: '',
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: ''
            };
            this.imageFile = null;
            this.imagePreview = null;
            this.ogImageFile = null;
            this.ogImagePreview = null;
            this.activeTab = 'basic';
        },
        async loadAnnouncementData() {
            if (!this.editingAnnouncement) return;

            try {
                this.loading = true;
                const response = await this.$axios.get(`/api/v1/announcements/${this.editingAnnouncement.id}`, {
                    headers: this.getAuthHeaders()
                });

                const announcement = response.data;

                // Normalize image paths
                const normalizedImage = this.normalizeImageInput(announcement.image || '');
                const normalizedOgImage = this.normalizeImageInput(announcement.og_image || '');

                this.form = {
                    title: announcement.title || '',
                    slug: announcement.slug || '',
                    content: announcement.content || '',
                    short_description: announcement.short_description || '',
                    type: announcement.type || 'company_news',
                    display_type: announcement.display_type || 'slider_banner',
                    image: normalizedImage,
                    video: announcement.video || '',
                    external_link: announcement.external_link || '',
                    open_in_new_tab: announcement.open_in_new_tab || false,
                    start_date: announcement.start_date || '',
                    end_date: announcement.end_date || '',
                    is_active: announcement.is_active !== undefined ? announcement.is_active : true,
                    priority: announcement.priority || 0,
                    language: announcement.language || '',
                    meta_title: announcement.meta_title || '',
                    meta_description: announcement.meta_description || '',
                    meta_keywords: announcement.meta_keywords || '',
                    og_image: normalizedOgImage
                };

                // Set preview images
                this.imagePreview = announcement.image ? this.resolveImageUrl(announcement.image || normalizedImage) : null;
                this.ogImagePreview = announcement.og_image ? this.resolveImageUrl(announcement.og_image || normalizedOgImage) : null;
            } catch (error) {
                this.handleApiError(error, 'Failed to load announcement data');
                this.closeDialog();
            } finally {
                this.loading = false;
            }
        },
        formatDateTimeForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '';
            return date.toISOString();
        },
        getStartDateForMin() {
            if (this.form.start_date) {
                const date = new Date(this.form.start_date);
                if (!isNaN(date.getTime())) {
                    return date.toISOString().split('T')[0];
                }
            }
            return null;
        },
        generateSlug() {
            if (this.form.title && !this.editingAnnouncement) {
                this.form.slug = this.form.title.toLowerCase()
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
        async uploadImage() {
            if (!this.imageFile) return null;

            const fileToUpload = Array.isArray(this.imageFile) ? this.imageFile[0] : this.imageFile;
            if (!fileToUpload) return null;

            this.uploadingImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'announcements');
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
                    this.imageError = null;
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload image');
                }
            } catch (error) {
                let errorMessage = 'Failed to upload image';
                if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                }
                this.imageError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingImage = false;
            }
        },
        removeImage() {
            if (confirm('Are you sure you want to remove this image?')) {
                this.imagePreview = null;
                this.imageFile = null;
                this.form.image = '';
                this.imageError = null;
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
        async uploadOgImage() {
            if (!this.ogImageFile) return null;

            const fileToUpload = Array.isArray(this.ogImageFile) ? this.ogImageFile[0] : this.ogImageFile;
            if (!fileToUpload) return null;

            this.uploadingOgImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'announcements');
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
                    this.ogImageError = null;
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload OG image');
                }
            } catch (error) {
                let errorMessage = 'Failed to upload OG image';
                if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                }
                this.ogImageError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingOgImage = false;
            }
        },
        removeOgImage() {
            if (confirm('Are you sure you want to remove the Open Graph image?')) {
                this.ogImagePreview = null;
                this.ogImageFile = null;
                this.form.og_image = '';
                this.ogImageError = null;
            }
        },
        async saveAnnouncement() {
            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            this.saving = true;

            try {
                // Upload images if files are selected
                if (this.imageFile) {
                    try {
                        await this.uploadImage();
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload image');
                        this.saving = false;
                        return;
                    }
                }

                if (this.ogImageFile) {
                    try {
                        await this.uploadOgImage();
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload OG image');
                        this.saving = false;
                        return;
                    }
                }

                const payload = { ...this.form };

                // Normalize image paths before sending
                if (payload.image) {
                    payload.image = this.normalizeImageInput(payload.image);
                }
                if (payload.og_image) {
                    payload.og_image = this.normalizeImageInput(payload.og_image);
                }

                // Convert datetime-local format to ISO string for API
                if (payload.start_date) {
                    payload.start_date = new Date(payload.start_date).toISOString();
                }
                if (payload.end_date) {
                    payload.end_date = new Date(payload.end_date).toISOString();
                }

                let response;
                if (this.editingAnnouncement) {
                    response = await this.$axios.put(`/api/v1/announcements/${this.editingAnnouncement.id}`, payload, {
                        headers: this.getAuthHeaders()
                    });
                } else {
                    response = await this.$axios.post('/api/v1/announcements', payload, {
                        headers: this.getAuthHeaders()
                    });
                }

                this.showSuccess(this.editingAnnouncement ? 'Announcement updated successfully' : 'Announcement created successfully');
                this.$emit('saved', response.data);
                this.closeDialog();
            } catch (error) {
                this.handleApiError(error, this.editingAnnouncement ? 'Failed to update announcement' : 'Failed to create announcement');
            } finally {
                this.saving = false;
            }
        },
        closeDialog() {
            if (!this.saving && !this.loading) {
                this.resetForm();
                this.dialog = false;
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

            // Add announcement type as keyword
            if (this.form.type) {
                const typeLabels = {
                    'company_news': 'company news',
                    'offers_promotions': 'offers promotions',
                    'events': 'events',
                    'holidays': 'holidays',
                    'urgent_alerts': 'urgent alerts'
                };
                const typeKeyword = typeLabels[this.form.type] || this.form.type;
                keywords.push(typeKeyword);
            }

            // Remove duplicates and common words
            const commonWords = ['the', 'and', 'for', 'with', 'from', 'this', 'that', 'your', 'our', 'announcement', 'announcements', 'about', 'when', 'where'];
            const uniqueKeywords = [...new Set(keywords)]
                .filter(word => !commonWords.includes(word))
                .slice(0, 10);

            return uniqueKeywords.length > 0 ? uniqueKeywords.join(', ') : null;
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
            return this.form.meta_title || this.form.title || 'Announcement Title';
        },
        getPreviewDescription() {
            return this.form.meta_description || this.form.short_description || 'Announcement description will appear here in search results...';
        },
        getPreviewUrl() {
            const baseUrl = window.location.origin || 'https://example.com';
            const slug = this.form.slug || 'announcement-slug';
            return `${baseUrl}/announcements/${slug}`;
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
