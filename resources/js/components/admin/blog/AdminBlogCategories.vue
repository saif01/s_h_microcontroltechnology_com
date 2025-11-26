<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Blog Categories Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create
                Category</v-btn>
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
                            @update:model-value="loadCategories"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search categories" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadCategories"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Categories Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Blog Categories</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="categories.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                            pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th class="sortable" @click="onSort('name')">
                                <div class="d-flex align-center">
                                    Name
                                    <v-icon :icon="getSortIcon('name')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('slug')">
                                <div class="d-flex align-center">
                                    Slug
                                    <v-icon :icon="getSortIcon('slug')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Parent</th>
                            <th class="sortable" @click="onSort('order')">
                                <div class="d-flex align-center">
                                    Order
                                    <v-icon :icon="getSortIcon('order')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('published')">
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('published')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Skeleton Loaders -->
                        <tr v-if="loading" v-for="n in 5" :key="`skeleton-${n}`">
                            <td>
                                <v-skeleton-loader type="avatar" width="40" height="40"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="150" class="mb-1"></v-skeleton-loader>
                                <v-skeleton-loader type="text" width="100" height="16"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="120" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="100"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="50"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="80" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32"
                                        class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Category Data -->
                        <template v-else>
                            <tr v-for="category in categories" :key="category.id">
                                <td>
                                    <v-avatar size="40" v-if="category.image">
                                        <v-img :src="resolveImageUrl(category.image)" cover></v-img>
                                    </v-avatar>
                                    <v-avatar size="40" v-else color="grey-lighten-2">
                                        <v-icon icon="mdi-folder"></v-icon>
                                    </v-avatar>
                                </td>
                                <td>
                                    <div class="font-weight-medium">{{ category.name }}</div>
                                    <div v-if="category.children && category.children.length > 0"
                                        class="text-caption text-grey">
                                        {{ category.children.length }} sub-category(ies)
                                    </div>
                                </td>
                                <td>
                                    <v-chip size="small" variant="outlined">{{ category.slug }}</v-chip>
                                </td>
                                <td>
                                    <span v-if="category.parent" class="text-body-2">{{ category.parent.name }}</span>
                                    <span v-else class="text-caption text-grey">Root</span>
                                </td>
                                <td>{{ category.order || 0 }}</td>
                                <td>
                                    <v-chip :color="category.published ? 'success' : 'error'" size="small">
                                        {{ category.published ? 'Published' : 'Draft' }}
                                    </v-chip>
                                </td>
                                <td>
                                    <v-btn size="small" icon="mdi-pencil" @click="editCategory(category)" variant="text"
                                        color="primary"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="deleteCategory(category)"
                                        variant="text" color="error"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="categories.length === 0">
                                <td colspan="7" class="text-center py-4">No categories found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <div
                    class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="categories.length > 0 && pagination.total > 0">
                            Showing <strong>{{ ((currentPage - 1) * perPage) + 1 }}</strong> to
                            <strong>{{ Math.min(currentPage * perPage, pagination.total) }}</strong> of
                            <strong>{{ pagination.total.toLocaleString() }}</strong> records
                            <span v-if="pagination.last_page > 1" class="ml-2">
                                (Page {{ currentPage }} of {{ pagination.last_page }})
                            </span>
                        </span>
                        <span v-else>No records found</span>
                    </div>
                    <div v-if="pagination.last_page > 1" class="d-flex align-center gap-2">
                        <v-pagination v-model="currentPage" :length="pagination.last_page" :total-visible="7"
                            density="comfortable" @update:model-value="loadCategories">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Category Form Dialog -->
        <v-dialog v-model="showDialog" max-width="800" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">
                        {{ editingCategory ? 'Edit Blog Category' : 'Create New Blog Category' }}
                    </span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"></v-btn>
                </v-card-title>

                <v-card-text class="pa-6">
                    <v-form ref="formRef">
                        <v-row>
                            <v-col cols="12">
                                <v-text-field v-model="form.name" label="Category Name *" variant="outlined"
                                    :rules="[v => !!v || 'Name is required']"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="form.slug" label="Slug *" variant="outlined"
                                    hint="URL-friendly version of name" :rules="[v => !!v || 'Slug is required']">
                                    <template v-slot:append>
                                        <v-btn icon="mdi-refresh" size="small" variant="text"
                                            @click="generateSlug"></v-btn>
                                    </template>
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="form.type" label="Type" variant="outlined" disabled
                                    hint="Blog categories are always type 'post'"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="form.description" label="Description" variant="outlined" rows="3"
                                    hint="Brief description of the category"></v-textarea>
                            </v-col>
                            <v-col cols="12">
                                <v-label class="mb-2">Category Image</v-label>
                                <v-card variant="outlined" class="pa-4">
                                    <div class="d-flex flex-column flex-md-row gap-4">
                                        <!-- Preview Section -->
                                        <div v-if="imagePreview || form.image" class="flex-shrink-0">
                                            <v-img :src="imagePreview || resolveImageUrl(form.image)" max-width="200"
                                                max-height="200" contain class="rounded border"
                                                style="min-width: 150px; min-height: 150px;">
                                            </v-img>
                                            <div class="mt-2">
                                                <v-btn color="error" size="small" variant="text" @click="removeImage"
                                                    prepend-icon="mdi-delete">
                                                    Remove Image
                                                </v-btn>
                                            </div>
                                        </div>

                                        <!-- Upload Section -->
                                        <div class="flex-grow-1">
                                            <v-file-input v-model="imageFile" accept="image/*"
                                                label="Select Category Image" variant="outlined" prepend-icon=""
                                                prepend-inner-icon="mdi-image" show-size clearable
                                                hint="Recommended size: 400x400px or larger. Max file size: 5MB"
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

                                            <div v-if="!imagePreview && !form.image && !imageFile"
                                                class="text-caption text-medium-emphasis mt-2">
                                                No image selected. Upload an image to display with this category.
                                            </div>

                                            <!-- Alternative: Direct URL input -->
                                            <v-text-field v-model="form.image" label="Or enter image URL directly"
                                                variant="outlined" density="compact" prepend-inner-icon="mdi-link"
                                                hint="Enter a direct image URL" class="mt-2" clearable>
                                            </v-text-field>
                                        </div>
                                    </div>
                                </v-card>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select v-model="form.parent_id" :items="parentCategoryOptions" item-title="name"
                                    item-value="id" label="Parent Category" variant="outlined" clearable
                                    hint="Select parent category for hierarchical structure">
                                    <template v-slot:item="{ props, item }">
                                        <v-list-item v-bind="props" :title="item.raw.name">
                                            <template v-slot:prepend>
                                                <v-icon icon="mdi-folder" class="mr-2"></v-icon>
                                            </template>
                                        </v-list-item>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model.number="form.order" label="Display Order" variant="outlined"
                                    type="number" hint="Lower numbers appear first"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-switch v-model="form.published" label="Published" color="success"></v-switch>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="closeDialog">Cancel</v-btn>
                    <v-btn color="primary" variant="flat" @click="saveCategory" :loading="saving">
                        {{ editingCategory ? 'Update' : 'Create' }} Category
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import { normalizeUploadPath, resolveUploadUrl } from '../../../utils/uploads';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            categories: [],
            showDialog: false,
            editingCategory: null,
            saving: false,
            publishedFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ],
            form: {
                name: '',
                slug: '',
                type: 'post', // Always 'post' for blog categories
                description: '',
                image: '',
                parent_id: null,
                order: 0,
                published: true
            },
            allCategories: [],
            originalSlug: null,
            originalId: null,
            imageFile: null,
            imagePreview: null,
            uploadingImage: false
        };
    },
    computed: {
        parentCategoryOptions() {
            // Filter out the current category being edited and its children to prevent circular references
            // Only show 'post' type categories
            return this.allCategories
                .filter(cat => {
                    if (this.editingCategory && cat.id === this.editingCategory.id) return false;
                    // Only show post type categories
                    return cat.type === 'post';
                })
                .map(cat => ({
                    id: cat.id,
                    name: cat.name,
                    type: cat.type
                }));
        }
    },
    watch: {
        'form.image'(newVal) {
            // Update preview when image URL is directly entered (if no file is selected)
            if (newVal && !this.imageFile) {
                this.imagePreview = this.resolveImageUrl(newVal);
            } else if (!newVal && !this.imageFile) {
                this.imagePreview = null;
            }
        }
    },
    async mounted() {
        await Promise.all([
            this.loadCategories(),
            this.loadAllCategories()
        ]);
    },
    methods: {
        async loadCategories() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();
                params.type = 'post'; // Always filter by 'post' type

                if (this.search) {
                    params.search = this.search;
                }
                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                const response = await axios.get('/api/v1/blog-categories', {
                    params,
                    headers: this.getAuthHeaders()
                });

                const categories = response.data.data || [];
                this.categories = categories.map(cat => ({
                    ...cat,
                    image: this.resolveImageUrl(cat.image)
                }));
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load blog categories');
            } finally {
                this.loading = false;
            }
        },
        async loadAllCategories() {
            try {
                // Load all blog categories for parent selection (without pagination)
                const response = await axios.get('/api/v1/blog-categories', {
                    params: { per_page: 1000 },
                    headers: this.getAuthHeaders()
                }).catch(() => ({ data: { data: [] } }));
                this.allCategories = response.data?.data || [];
            } catch (error) {
                console.error('Error loading all categories:', error);
                this.allCategories = [];
            }
        },
        openDialog(category = null) {
            this.editingCategory = category;
            if (category) {
                this.loadCategoryForEdit(category);
            } else {
                this.resetForm();
            }
            this.showDialog = true;
        },
        async loadCategoryForEdit(category) {
            try {
                this.loading = true;
                // Use ID first, fallback to slug (controller supports both)
                const identifier = category.id || category.slug;
                const response = await axios.get(`/api/v1/blog-categories/${identifier}`, {
                    headers: this.getAuthHeaders()
                });
                const data = response.data;

                const normalizedImage = this.normalizeImageInput(data.image || '');

                this.form = {
                    name: data.name || '',
                    slug: data.slug || '',
                    type: 'post', // Always 'post' for blog categories
                    description: data.description || '',
                    image: normalizedImage,
                    parent_id: data.parent_id || null,
                    order: data.order || 0,
                    published: data.published !== false
                };
                // Store original slug and ID for update request
                this.originalSlug = data.slug;
                this.originalId = data.id;
                // Reset image file and preview when loading existing category
                this.imageFile = null;
                this.imagePreview = this.resolveImageUrl(data.image || normalizedImage);
            } catch (error) {
                this.handleApiError(error, 'Failed to load category');
                this.closeDialog();
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.form = {
                name: '',
                slug: '',
                type: 'post', // Always 'post' for blog categories
                description: '',
                image: '',
                parent_id: null,
                order: 0,
                published: true
            };
            this.imageFile = null;
            this.imagePreview = null;
        },
        generateSlug() {
            if (this.form.name) {
                this.form.slug = this.form.name
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
            }
        },
        handleImageSelect(file) {
            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.showError('Image file size must be less than 5MB');
                    this.imageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.showError('Please select a valid image file');
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
                }
            }
        },
        removeImage() {
            if (confirm('Are you sure you want to remove this image?')) {
                this.imagePreview = null;
                this.imageFile = null;
                this.form.image = '';
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
                formData.append('folder', 'blog/categories');
                if (this.form.name) {
                    formData.append('prefix', this.form.name);
                }

                const response = await axios.post('/api/v1/upload/image', formData, {
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
                    return uploadedPath;
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
        async saveCategory() {
            // Validate form
            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            try {
                this.saving = true;

                // Upload image if new file is selected
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

                const payload = {
                    ...this.form,
                    type: 'post', // Ensure type is always 'post'
                    image: this.normalizeImageInput(this.form.image)
                };

                if (this.editingCategory) {
                    // Use ID first, fallback to slug (controller supports both)
                    const identifier = this.originalId || this.editingCategory.id || this.originalSlug || this.editingCategory.slug;
                    await axios.put(`/api/v1/blog-categories/${identifier}`, payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Blog category updated successfully');
                } else {
                    await axios.post('/api/v1/blog-categories', payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Blog category created successfully');
                }

                this.closeDialog();
                await Promise.all([
                    this.loadCategories(),
                    this.loadAllCategories()
                ]);
            } catch (error) {
                this.handleApiError(error, 'Failed to save blog category');
            } finally {
                this.saving = false;
            }
        },
        editCategory(category) {
            this.openDialog(category);
        },
        async deleteCategory(category) {
            if (confirm('Are you sure you want to delete this blog category? This action cannot be undone.')) {
                try {
                    // Use ID first, fallback to slug (controller supports both)
                    const identifier = category.id || category.slug;
                    await axios.delete(`/api/v1/blog-categories/${identifier}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Blog category deleted successfully');
                    await Promise.all([
                        this.loadCategories(),
                        this.loadAllCategories()
                    ]);
                } catch (error) {
                    const errorMessage = error.response?.data?.error || 'Error deleting blog category';
                    this.showError(errorMessage);
                }
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.editingCategory = null;
            this.originalSlug = null;
            this.originalId = null;
            this.imageFile = null;
            this.imagePreview = null;
            this.resetForm();
            if (this.$refs.formRef) {
                this.$refs.formRef.resetValidation();
            }
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadCategories();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadCategories();
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
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: rgba(0, 0, 0, 0.04);
}
</style>
