<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Product Categories Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create Category</v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="3">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="typeFilter" :items="typeOptions" label="Filter by Type"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadCategories"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="publishedFilter" :items="publishedOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadCategories"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
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
                <span>Categories</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="categories.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage, pagination.total) }} of {{ pagination.total }}
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
                            <th>Type</th>
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
                        <tr v-for="category in categories" :key="category.id">
                            <td>
                                <v-avatar size="40" v-if="category.image">
                                    <v-img :src="category.image" cover></v-img>
                                </v-avatar>
                                <v-avatar size="40" v-else color="grey-lighten-2">
                                    <v-icon icon="mdi-folder"></v-icon>
                                </v-avatar>
                            </td>
                            <td>
                                <div class="font-weight-medium">{{ category.name }}</div>
                                <div v-if="category.children && category.children.length > 0" class="text-caption text-grey">
                                    {{ category.children.length }} sub-category(ies)
                                </div>
                            </td>
                            <td>
                                <v-chip size="small" variant="outlined">{{ category.slug }}</v-chip>
                            </td>
                            <td>
                                <v-chip size="small" color="primary" variant="tonal">{{ category.type }}</v-chip>
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
                                <v-btn size="small" icon="mdi-pencil" @click="editCategory(category)"
                                    variant="text" color="primary"></v-btn>
                                <v-btn size="small" icon="mdi-delete" @click="deleteCategory(category.id)" variant="text"
                                    color="error"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="categories.length === 0">
                            <td colspan="8" class="text-center py-4">No categories found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <div class="d-flex justify-space-between align-center mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="categories.length > 0">
                            Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage, pagination.total) }} of {{ pagination.total }} records
                        </span>
                        <span v-else>No records found</span>
                    </div>
                    <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                        @update:model-value="loadCategories"></v-pagination>
                </div>
            </v-card-text>
        </v-card>

        <!-- Category Form Dialog -->
        <v-dialog v-model="showDialog" max-width="800" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">
                        {{ editingCategory ? 'Edit Category' : 'Create New Category' }}
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
                                    hint="URL-friendly version of name"
                                    :rules="[v => !!v || 'Slug is required']">
                                    <template v-slot:append>
                                        <v-btn icon="mdi-refresh" size="small" variant="text"
                                            @click="generateSlug"></v-btn>
                                    </template>
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select v-model="form.type" :items="typeOptions" label="Type *" variant="outlined"
                                    :rules="[v => !!v || 'Type is required']"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="form.description" label="Description" variant="outlined" rows="3"
                                    hint="Brief description of the category"></v-textarea>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="form.image" label="Image URL" variant="outlined"
                                    prepend-inner-icon="mdi-image" hint="Category image URL"></v-text-field>
                                <div v-if="form.image" class="mt-2">
                                    <v-img :src="form.image" max-height="150" contain class="rounded"></v-img>
                                </div>
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
import adminPaginationMixin from '../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            categories: [],
            showDialog: false,
            editingCategory: null,
            saving: false,
            typeFilter: 'product',
            publishedFilter: null,
            typeOptions: [
                { title: 'Product', value: 'product' },
                { title: 'Service', value: 'service' },
                { title: 'Post', value: 'post' },
                { title: 'General', value: 'general' }
            ],
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ],
            form: {
                name: '',
                slug: '',
                type: 'product',
                description: '',
                image: '',
                parent_id: null,
                order: 0,
                published: true
            },
            allCategories: []
        };
    },
    computed: {
        parentCategoryOptions() {
            // Filter out the current category being edited and its children to prevent circular references
            return this.allCategories
                .filter(cat => {
                    if (this.editingCategory && cat.id === this.editingCategory.id) return false;
                    // Filter by same type
                    return cat.type === this.form.type;
                })
                .map(cat => ({
                    id: cat.id,
                    name: cat.name,
                    type: cat.type
                }));
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

                if (this.search) {
                    params.search = this.search;
                }
                if (this.typeFilter) {
                    params.type = this.typeFilter;
                }
                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                const response = await axios.get('/api/v1/categories', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.categories = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load categories');
            } finally {
                this.loading = false;
            }
        },
        async loadAllCategories() {
            try {
                // Load all categories for parent selection (without pagination)
                const response = await axios.get('/api/v1/categories', {
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
                const response = await axios.get(`/api/v1/categories/${category.id}`, {
                    headers: this.getAuthHeaders()
                });
                const data = response.data;

                this.form = {
                    name: data.name || '',
                    slug: data.slug || '',
                    type: data.type || 'product',
                    description: data.description || '',
                    image: data.image || '',
                    parent_id: data.parent_id || null,
                    order: data.order || 0,
                    published: data.published !== false
                };
            } catch (error) {
                this.handleApiError(error, 'Failed to load category');
            }
        },
        resetForm() {
            this.form = {
                name: '',
                slug: '',
                type: this.typeFilter || 'product',
                description: '',
                image: '',
                parent_id: null,
                order: 0,
                published: true
            };
        },
        generateSlug() {
            if (this.form.name) {
                this.form.slug = this.form.name
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
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

                if (this.editingCategory) {
                    await axios.put(`/api/v1/categories/${this.editingCategory.id}`, this.form, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Category updated successfully');
                } else {
                    await axios.post('/api/v1/categories', this.form, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Category created successfully');
                }

                this.closeDialog();
                await Promise.all([
                    this.loadCategories(),
                    this.loadAllCategories()
                ]);
            } catch (error) {
                this.handleApiError(error, 'Failed to save category');
            } finally {
                this.saving = false;
            }
        },
        editCategory(category) {
            this.openDialog(category);
        },
        async deleteCategory(id) {
            if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                try {
                    await axios.delete(`/api/v1/categories/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Category deleted successfully');
                    await Promise.all([
                        this.loadCategories(),
                        this.loadAllCategories()
                    ]);
                } catch (error) {
                    const errorMessage = error.response?.data?.error || 'Error deleting category';
                    this.showError(errorMessage);
                }
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.editingCategory = null;
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
