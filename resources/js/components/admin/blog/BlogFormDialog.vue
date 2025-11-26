<template>
    <v-dialog v-model="dialog" max-width="1400" scrollable persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                <span class="text-h5 font-weight-bold">
                    {{ editingPost ? 'Edit Blog Post' : 'Create New Blog Post' }}
                </span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                    :disabled="saving || loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading post data...</p>
                    </div>
                </div>

                <!-- Form Content -->
                <v-form ref="formRef" v-else>
                    <v-tabs v-model="activeTab" bg-color="grey-lighten-4">
                        <v-tab value="basic">Basic Information</v-tab>
                        <v-tab value="content">Content</v-tab>
                        <v-tab value="categories">Categories & Tags</v-tab>
                        <v-tab value="seo">SEO & Meta</v-tab>
                    </v-tabs>

                    <v-window v-model="activeTab">
                        <!-- Basic Information Tab -->
                        <v-window-item value="basic">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.title" label="Post Title *" variant="outlined"
                                            :rules="[v => !!v || 'Title is required']" hint="Enter the blog post title"
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
                                        <v-text-field v-model="form.published_at" label="Published Date" type="datetime-local"
                                            variant="outlined" hint="When to publish this post" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.excerpt" label="Excerpt" variant="outlined" rows="3"
                                            hint="Brief summary shown in blog listings" persistent-hint
                                            :counter="300"></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-switch v-model="form.published" label="Published" color="success"
                                            hint="Publish this post on the public website" persistent-hint></v-switch>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Content Tab -->
                        <v-window-item value="content">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Post Content *</v-label>
                                        <div class="rich-text-editor-wrapper">
                                            <div ref="editorContainer" class="rich-text-editor"></div>
                                        </div>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Featured Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="d-flex flex-column flex-md-row align-start">
                                                <!-- Image Preview -->
                                                <div v-if="imagePreview || form.featured_image" class="mr-md-4 mb-4 mb-md-0">
                                                    <v-img :src="imagePreview || resolveImageUrl(form.featured_image)"
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
                                                        label="Select Featured Image" variant="outlined" prepend-icon=""
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
                                                        No image selected. Upload an image to display with this post.
                                                    </div>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Categories & Tags Tab -->
                        <v-window-item value="categories">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 font-weight-bold mb-2">Categories</div>
                                        <v-select v-model="form.category_ids" :items="availableCategories"
                                            item-title="name" item-value="id" label="Select Categories" variant="outlined"
                                            multiple chips :loading="loadingCategories"
                                            hint="Select categories for this post (type='post')" persistent-hint></v-select>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 font-weight-bold mb-2">Tags</div>
                                        <v-combobox v-model="form.tag_names" :items="availableTags" label="Tags"
                                            variant="outlined" multiple chips closable-chips hint="Type to add new tags (type='post')"
                                            persistent-hint></v-combobox>
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
                                        post visibility in search engines and social media.
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
                                                    title="Auto-generate from post title">
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        <div class="text-caption" :class="getMetaTitleColor() + '--text'">
                                            {{ (form.meta_title || '').length }}/60 characters
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
                                                    @click="generateMetaDescription" :disabled="!form.excerpt"
                                                    title="Auto-generate from excerpt">
                                                </v-btn>
                                            </template>
                                        </v-textarea>
                                        <div class="text-caption" :class="getMetaDescriptionColor() + '--text'">
                                            {{ (form.meta_description || '').length }}/160 characters
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="form.meta_keywords" label="Meta Keywords"
                                            variant="outlined"
                                            hint="Comma-separated keywords for SEO" persistent-hint
                                            prepend-inner-icon="mdi-tag-multiple"></v-text-field>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Open Graph (Social Media)</h3>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-label class="mb-2">Open Graph Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="d-flex flex-column flex-md-row align-start">
                                                <!-- OG Image Preview -->
                                                <div v-if="ogImagePreview || form.og_image" class="mr-md-4 mb-4 mb-md-0">
                                                    <v-img :src="ogImagePreview || resolveImageUrl(form.og_image)"
                                                        max-width="300" max-height="300" class="rounded elevation-2"
                                                        cover></v-img>
                                                    <div class="mt-2">
                                                        <v-btn color="error" size="small" variant="text"
                                                            @click="removeOgImage" prepend-icon="mdi-delete">
                                                            Remove Image
                                                        </v-btn>
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
                <v-btn color="primary" variant="flat" @click="savePost" :loading="saving">
                    {{ editingPost ? 'Update' : 'Create' }} Post
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import Quill from 'quill';
import { normalizeUploadPath, resolveUploadUrl } from '../../../utils/uploads';
import 'quill/dist/quill.snow.css';

export default {
    name: 'BlogFormDialog',
    mixins: [adminPaginationMixin],
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        editingPost: {
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
            quillEditor: null,
            availableCategories: [],
            availableTags: [],
            loadingCategories: false,
            form: {
                title: '',
                slug: '',
                excerpt: '',
                content: '',
                featured_image: '',
                published_at: '',
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: '',
                published: false,
                category_ids: [],
                tag_names: []
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
        }
    },
    watch: {
        modelValue(newVal) {
            if (newVal) {
                this.activeTab = 'basic';
                this.loadCategories();
                this.loadTags();
                if (this.editingPost) {
                    this.loadPostForEdit();
                } else {
                    this.resetForm();
                }
            }
        },
        editingPost: {
            handler(newVal) {
                if (newVal && this.dialog) {
                    this.loadPostForEdit();
                } else if (!newVal && this.dialog) {
                    this.resetForm();
                }
            },
            immediate: false
        },
        activeTab(newTab) {
            if (newTab === 'content' && this.dialog && !this.loading) {
                setTimeout(() => {
                    this.initEditor();
                }, 300);
            } else if (newTab !== 'content' && this.quillEditor) {
                this.destroyEditor();
            }
        }
    },
    methods: {
        resetForm() {
            if (this.quillEditor) {
                this.destroyEditor();
            }

            this.form = {
                title: '',
                slug: '',
                excerpt: '',
                content: '',
                featured_image: '',
                published_at: '',
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: '',
                published: false,
                category_ids: [],
                tag_names: []
            };
            this.imageFile = null;
            this.imagePreview = null;
            this.imageError = null;
            this.ogImageFile = null;
            this.ogImagePreview = null;
            this.ogImageError = null;

            if (this.$refs.formRef) {
                this.$refs.formRef.resetValidation();
            }
        },
        async loadPostForEdit() {
            if (!this.editingPost) return;

            this.loading = true;
            try {
                const response = await axios.get(`/api/v1/blog-posts/${this.editingPost.id}`, {
                    headers: this.getAuthHeaders()
                });
                const data = response.data;

                // Format published_at for datetime-local input
                let publishedAt = '';
                if (data.published_at) {
                    const date = new Date(data.published_at);
                    publishedAt = date.toISOString().slice(0, 16);
                }

                this.form = {
                    title: data.title || '',
                    slug: data.slug || '',
                    excerpt: data.excerpt || '',
                    content: data.content || '',
                    featured_image: data.featured_image || '',
                    published_at: publishedAt,
                    meta_title: data.meta_title || '',
                    meta_description: data.meta_description || '',
                    meta_keywords: data.meta_keywords || '',
                    og_image: data.og_image || '',
                    published: data.published || false,
                    category_ids: (data.categories || []).map(c => c.id),
                    tag_names: (data.tags || []).map(t => t.name || t)
                };

                this.imagePreview = this.resolveImageUrl(data.featured_image || '');
                this.ogImagePreview = this.resolveImageUrl(data.og_image || '');
            } catch (error) {
                this.handleApiError(error, 'Failed to load post data');
                this.closeDialog();
            } finally {
                this.loading = false;
            }
        },
        async loadCategories() {
            try {
                this.loadingCategories = true;
                const response = await axios.get('/api/v1/categories?type=post', {
                    headers: this.getAuthHeaders()
                }).catch(() => null);

                if (!response) {
                    response = await axios.get('/api/v1/categories', {
                        headers: this.getAuthHeaders()
                    }).catch(() => null);
                }

                if (response && response.data) {
                    const categories = response.data?.data || response.data || [];
                    this.availableCategories = Array.isArray(categories)
                        ? categories.filter(c => !c.type || c.type === 'post')
                        : [];
                } else {
                    this.availableCategories = [];
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                this.availableCategories = [];
            } finally {
                this.loadingCategories = false;
            }
        },
        async loadTags() {
            try {
                const response = await axios.get('/api/v1/tags?type=post', {
                    headers: this.getAuthHeaders()
                }).catch(() => null);

                if (!response) {
                    response = await axios.get('/api/v1/tags', {
                        headers: this.getAuthHeaders()
                    }).catch(() => null);
                }

                if (response && response.data) {
                    const tags = response.data?.data || response.data || [];
                    const filteredTags = Array.isArray(tags)
                        ? tags.filter(t => !t.type || t.type === 'post')
                        : [];
                    this.availableTags = filteredTags.map(t => t.name || t);
                } else {
                    this.availableTags = [];
                }
            } catch (error) {
                console.error('Error loading tags:', error);
                this.availableTags = [];
            }
        },
        closeDialog() {
            if (!this.saving && !this.loading) {
                this.destroyEditor();
                this.dialog = false;
                this.resetForm();
            }
        },
        initEditor() {
            if (this.quillEditor) {
                this.destroyEditor();
            }

            this.$nextTick(() => {
                if (this.$refs.editorContainer && this.activeTab === 'content') {
                    try {
                        this.$refs.editorContainer.innerHTML = '';

                        this.quillEditor = new Quill(this.$refs.editorContainer, {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                    [{ 'align': [] }],
                                    ['link', 'image'],
                                    ['blockquote', 'code-block'],
                                    ['clean']
                                ]
                            },
                            placeholder: 'Enter blog post content...'
                        });

                        if (this.form.content) {
                            this.quillEditor.root.innerHTML = this.form.content;
                        }

                        this.quillEditor.on('text-change', () => {
                            this.form.content = this.quillEditor.root.innerHTML;
                        });
                    } catch (error) {
                        console.error('Error initializing Quill editor:', error);
                        this.showError('Failed to initialize text editor');
                    }
                }
            });
        },
        destroyEditor() {
            if (this.quillEditor) {
                try {
                    if (this.quillEditor.root && this.quillEditor.root.innerHTML) {
                        const content = this.quillEditor.root.innerHTML.trim();
                        if (content && content !== '<p><br></p>') {
                            this.form.content = content;
                        }
                    }
                } catch (error) {
                    console.error('Error saving editor content:', error);
                }

                try {
                    this.quillEditor = null;
                    if (this.$refs.editorContainer) {
                        this.$refs.editorContainer.innerHTML = '';
                    }
                } catch (error) {
                    console.error('Error destroying editor:', error);
                    this.quillEditor = null;
                }
            } else if (this.$refs.editorContainer) {
                this.$refs.editorContainer.innerHTML = '';
            }
        },
        generateSlug() {
            if (this.form.title && !this.editingPost) {
                this.form.slug = this.form.title
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        },
        handleImageSelect(file) {
            this.imageError = null;

            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.imageError = 'Image file size must be less than 5MB';
                    this.imageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.imageError = 'Please select a valid image file';
                    this.imageFile = null;
                    return;
                }

                this.imageFile = selectedFile;

                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    this.imagePreview = null;
                }
            } else {
                this.imageFile = null;
                if (!this.form.featured_image) {
                    this.imagePreview = null;
                } else {
                    this.imagePreview = this.resolveImageUrl(this.form.featured_image);
                }
            }
        },
        removeImage() {
            if (confirm('Are you sure you want to remove this image?')) {
                this.imagePreview = null;
                this.imageFile = null;
                this.form.featured_image = '';
                this.imageError = null;
            }
        },
        handleOgImageSelect(file) {
            this.ogImageError = null;

            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.ogImageError = 'OG image file size must be less than 5MB';
                    this.ogImageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.ogImageError = 'Please select a valid image file';
                    this.ogImageFile = null;
                    return;
                }

                this.ogImageFile = selectedFile;

                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.ogImagePreview = e.target.result;
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
                    this.ogImagePreview = this.resolveImageUrl(this.form.og_image);
                }
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
        generateMetaTitle() {
            if (this.form.title) {
                this.form.meta_title = this.form.title.length > 60
                    ? this.form.title.substring(0, 57) + '...'
                    : this.form.title;
            }
        },
        generateMetaDescription() {
            if (this.form.excerpt) {
                let description = this.form.excerpt;
                if (description.length > 160) {
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
        async uploadImage() {
            if (!this.imageFile) return null;

            const fileToUpload = Array.isArray(this.imageFile) ? this.imageFile[0] : this.imageFile;
            if (!fileToUpload) return null;

            this.uploadingImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'blog');
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = this.normalizeImageInput(response.data.path || response.data.url);
                    this.form.featured_image = uploadedPath;
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
        async uploadOgImage() {
            if (!this.ogImageFile) return null;

            const fileToUpload = Array.isArray(this.ogImageFile) ? this.ogImageFile[0] : this.ogImageFile;
            if (!fileToUpload) return null;

            this.uploadingOgImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'blog/og');
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await axios.post('/api/v1/upload/image', formData, {
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
        async savePost() {
            if (this.quillEditor) {
                this.form.content = this.quillEditor.root.innerHTML;
            }

            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            if (!this.form.content || this.form.content.trim() === '' || this.form.content === '<p><br></p>') {
                this.showError('Post content is required');
                return;
            }

            this.saving = true;

            try {
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

                const payload = {
                    title: this.form.title,
                    slug: this.form.slug,
                    excerpt: this.form.excerpt || null,
                    content: this.form.content,
                    featured_image: this.normalizeImageInput(this.form.featured_image) || null,
                    published_at: this.form.published_at || null,
                    meta_title: this.form.meta_title || null,
                    meta_description: this.form.meta_description || null,
                    meta_keywords: this.form.meta_keywords || null,
                    og_image: this.normalizeImageInput(this.form.og_image) || null,
                    published: this.form.published || false,
                    category_ids: this.form.category_ids || [],
                    tag_names: this.form.tag_names || []
                };

                if (this.editingPost) {
                    await axios.put(`/api/v1/blog-posts/${this.editingPost.id}`, payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Blog post updated successfully');
                } else {
                    await axios.post('/api/v1/blog-posts', payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Blog post created successfully');
                }

                this.$emit('saved');
                this.closeDialog();
            } catch (error) {
                this.handleApiError(error, this.editingPost ? 'Failed to update post' : 'Failed to create post');
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
.rich-text-editor-wrapper {
    border: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 4px;
    overflow: hidden;
}

.rich-text-editor {
    min-height: 400px;
}
</style>

