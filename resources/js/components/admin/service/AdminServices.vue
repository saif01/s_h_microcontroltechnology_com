<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Services Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create
                Service</v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="publishedFilter" :items="publishedOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadServices"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search services" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadServices"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Services Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Services</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="services.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                            pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th class="sortable" @click="onSort('title')">
                                <div class="d-flex align-center">
                                    Title
                                    <v-icon :icon="getSortIcon('title')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('slug')">
                                <div class="d-flex align-center">
                                    Slug
                                    <v-icon :icon="getSortIcon('slug')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('published')">
                                <div class="d-flex align-center">
                                    Published
                                    <v-icon :icon="getSortIcon('published')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="service in services" :key="service.id">
                            <td>{{ service.title }}</td>
                            <td>
                                <v-chip size="small" variant="outlined">{{ service.slug }}</v-chip>
                            </td>
                            <td>
                                <v-chip :color="service.published ? 'success' : 'error'" size="small">
                                    {{ service.published ? 'Published' : 'Draft' }}
                                </v-chip>
                            </td>
                            <td>
                                <v-btn size="small" icon="mdi-pencil" @click="editService(service)"
                                    variant="text"></v-btn>
                                <v-btn size="small" icon="mdi-delete" @click="deleteService(service.id)" variant="text"
                                    color="error"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="services.length === 0">
                            <td colspan="4" class="text-center py-4">No services found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex justify-space-between align-center mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="services.length > 0">
                            Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                                pagination.total) }} of {{ pagination.total }} records
                        </span>
                        <span v-else>
                            No records found
                        </span>
                    </div>
                    <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                        @update:model-value="loadServices"></v-pagination>
                </div>
            </v-card-text>
        </v-card>

        <!-- Service Form Dialog -->
        <v-dialog v-model="showDialog" max-width="1200" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">
                        {{ editingService ? 'Edit Service' : 'Create New Service' }}
                    </span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                        :disabled="saving"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0">
                    <!-- Loading State -->
                    <div v-if="loadingService" class="d-flex align-center justify-center pa-12">
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
                                            <v-text-field v-model="form.title" label="Service Title *"
                                                variant="outlined" :rules="[v => !!v || 'Title is required']"
                                                hint="Enter the service title" @blur="generateSlug"
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
                                            <v-text-field v-model="form.price_range" label="Price Range"
                                                variant="outlined" hint="e.g., $100 - $500"
                                                persistent-hint></v-text-field>
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
                                            <v-textarea v-model="form.description" label="Full Description"
                                                variant="outlined" rows="8"
                                                hint="Detailed description of the service (supports HTML)"
                                                persistent-hint></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-label class="mb-2">Service Image</v-label>
                                            <div class="d-flex align-center mb-4">
                                                <v-img v-if="imagePreview" :src="imagePreview" max-width="200"
                                                    max-height="200" class="mr-4 rounded" cover></v-img>
                                                <div>
                                                    <v-file-input v-model="imageFile" accept="image/*"
                                                        label="Select Image" variant="outlined" density="compact"
                                                        hide-details="auto" prepend-icon=""
                                                        prepend-inner-icon="mdi-image"
                                                        @update:model-value="handleImageSelect">
                                                    </v-file-input>
                                                    <v-btn v-if="imagePreview && !imageFile" color="error" size="small"
                                                        variant="text" @click="removeImage" class="mt-2">
                                                        Remove Image
                                                    </v-btn>
                                                    <v-progress-circular v-if="uploadingImage" indeterminate
                                                        color="primary" size="24" class="ml-2"></v-progress-circular>
                                                </div>
                                            </div>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-label class="mb-2">Features</v-label>
                                            <v-textarea v-model="featuresText" variant="outlined" rows="4"
                                                hint="Enter one feature per line" persistent-hint
                                                @blur="updateFeaturesFromText"></v-textarea>
                                            <div v-if="form.features && form.features.length > 0" class="mt-2">
                                                <v-chip v-for="(feature, i) in form.features" :key="i" class="ma-1"
                                                    closable @click:close="removeFeature(i)">
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
                                                <v-chip v-for="(benefit, i) in form.benefits" :key="i" class="ma-1"
                                                    closable @click:close="removeBenefit(i)" color="success"
                                                    variant="flat">
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
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field v-model="form.meta_title" label="Meta Title"
                                                variant="outlined" hint="SEO title (recommended: 50-60 characters)"
                                                persistent-hint counter="60"></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea v-model="form.meta_description" label="Meta Description"
                                                variant="outlined" rows="3"
                                                hint="SEO description (recommended: 150-160 characters)" persistent-hint
                                                counter="160"></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-text-field v-model="form.meta_keywords" label="Meta Keywords"
                                                variant="outlined" hint="Comma-separated keywords for SEO"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-label class="mb-2">Open Graph Image</v-label>
                                            <div class="d-flex align-center mb-4">
                                                <v-img v-if="ogImagePreview" :src="ogImagePreview" max-width="200"
                                                    max-height="200" class="mr-4 rounded" cover></v-img>
                                                <div>
                                                    <v-file-input v-model="ogImageFile" accept="image/*"
                                                        label="Select OG Image" variant="outlined" density="compact"
                                                        hide-details="auto" prepend-icon=""
                                                        prepend-inner-icon="mdi-image"
                                                        @update:model-value="handleOgImageSelect">
                                                    </v-file-input>
                                                    <v-btn v-if="ogImagePreview && !ogImageFile" color="error"
                                                        size="small" variant="text" @click="removeOgImage" class="mt-2">
                                                        Remove Image
                                                    </v-btn>
                                                    <v-progress-circular v-if="uploadingOgImage" indeterminate
                                                        color="primary" size="24" class="ml-2"></v-progress-circular>
                                                </div>
                                            </div>
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
    </div>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            services: [],
            showDialog: false,
            editingService: null,
            loadingService: false,
            activeTab: 'basic',
            publishedFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ],
            imageFile: null,
            imagePreview: null,
            uploadingImage: false,
            ogImageFile: null,
            ogImagePreview: null,
            uploadingOgImage: false,
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
    async mounted() {
        await this.loadServices();
    },
    methods: {
        async loadServices() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                const response = await axios.get('/api/v1/services', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.services = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load services');
            } finally {
                this.loading = false;
            }
        },
        openDialog(service = null) {
            this.editingService = service;
            this.showDialog = true;
            this.activeTab = 'basic';

            if (service) {
                this.loadServiceForEdit(service);
            } else {
                this.resetForm();
            }
        },
        closeDialog() {
            if (!this.saving && !this.loadingService) {
                this.showDialog = false;
                this.editingService = null;
                this.resetForm();
            }
        },
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
            this.ogImageFile = null;
            this.ogImagePreview = null;
            this.featuresText = '';
            this.benefitsText = '';

            if (this.$refs.formRef) {
                this.$refs.formRef.resetValidation();
            }
        },
        async loadServiceForEdit(service) {
            this.loadingService = true;
            try {
                const response = await axios.get(`/api/v1/services/${service.id}`, {
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

                // Set previews
                this.imagePreview = data.image || null;
                this.ogImagePreview = data.og_image || null;

                // Set text for features and benefits
                this.featuresText = Array.isArray(data.features) ? data.features.join('\n') : '';
                this.benefitsText = Array.isArray(data.benefits) ? data.benefits.join('\n') : '';
            } catch (error) {
                this.handleApiError(error, 'Failed to load service data');
                this.closeDialog();
            } finally {
                this.loadingService = false;
            }
        },
        editService(service) {
            this.openDialog(service);
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
            if (file && file.length > 0) {
                const selectedFile = Array.isArray(file) ? file[0] : file;
                this.imageFile = selectedFile;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(selectedFile);
            }
        },
        removeImage() {
            this.imagePreview = null;
            this.form.image = '';
        },
        handleOgImageSelect(file) {
            if (file && file.length > 0) {
                const selectedFile = Array.isArray(file) ? file[0] : file;
                this.ogImageFile = selectedFile;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.ogImagePreview = e.target.result;
                };
                reader.readAsDataURL(selectedFile);
            }
        },
        removeOgImage() {
            this.ogImagePreview = null;
            this.form.og_image = '';
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

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    return response.data.url;
                } else {
                    throw new Error(response.data.message || 'Failed to upload image');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to upload image';
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

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    return response.data.url;
                } else {
                    throw new Error(response.data.message || 'Failed to upload OG image');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to upload OG image';
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
                        const imageUrl = await this.uploadImage();
                        if (imageUrl) {
                            this.form.image = imageUrl;
                        }
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload image');
                        return;
                    }
                }

                if (this.ogImageFile) {
                    try {
                        const ogImageUrl = await this.uploadOgImage();
                        if (ogImageUrl) {
                            this.form.og_image = ogImageUrl;
                        }
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload OG image');
                        return;
                    }
                }

                // Prepare payload
                const payload = {
                    title: this.form.title,
                    slug: this.form.slug,
                    short_description: this.form.short_description || null,
                    description: this.form.description || null,
                    icon: this.form.icon || null,
                    image: this.form.image || null,
                    price_range: this.form.price_range || null,
                    features: this.form.features.length > 0 ? this.form.features : null,
                    benefits: this.form.benefits.length > 0 ? this.form.benefits : null,
                    meta_title: this.form.meta_title || null,
                    meta_description: this.form.meta_description || null,
                    meta_keywords: this.form.meta_keywords || null,
                    og_image: this.form.og_image || null,
                    published: this.form.published || false,
                    order: this.form.order || 0
                };

                // Save service
                if (this.editingService) {
                    // Update existing service
                    await axios.put(`/api/v1/services/${this.editingService.id}`, payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service updated successfully');
                } else {
                    // Create new service
                    await axios.post('/api/v1/services', payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service created successfully');
                }

                // Reload services and close dialog
                await this.loadServices();
                this.closeDialog();
            } catch (error) {
                this.handleApiError(error, this.editingService ? 'Failed to update service' : 'Failed to create service');
            } finally {
                this.saving = false;
            }
        },
        async deleteService(id) {
            if (confirm('Are you sure you want to delete this service?')) {
                try {
                    await axios.delete(`/api/v1/services/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service deleted successfully');
                    await this.loadServices();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting service');
                }
            }
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadServices();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadServices();
        }
    }
};
</script>
