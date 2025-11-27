<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Tags Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create Tag</v-btn>
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
                            @update:model-value="loadTags"></v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="search" label="Search tags" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadTags"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Tags Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Tags</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="tags.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                            pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
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
                            <th class="sortable" @click="onSort('type')">
                                <div class="d-flex align-center">
                                    Type
                                    <v-icon :icon="getSortIcon('type')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Description</th>
                            <th class="sortable" @click="onSort('created_at')">
                                <div class="d-flex align-center">
                                    Created
                                    <v-icon :icon="getSortIcon('created_at')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Skeleton Loaders -->
                        <tr v-if="loading" v-for="n in 5" :key="`skeleton-${n}`">
                            <td>
                                <v-skeleton-loader type="text" width="150"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="120" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="80" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="200"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="100"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32" class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Tag Data -->
                        <template v-else>
                            <tr v-for="tag in tags" :key="tag.id">
                                <td>
                                    <div class="font-weight-medium">{{ tag.name }}</div>
                                </td>
                                <td>
                                    <v-chip size="small" variant="outlined">{{ tag.slug }}</v-chip>
                                </td>
                                <td>
                                    <v-chip size="small" color="primary" variant="tonal">{{ tag.type }}</v-chip>
                                </td>
                                <td>
                                    <span class="text-body-2" v-if="tag.description">{{ tag.description }}</span>
                                    <span v-else class="text-caption text-grey">No description</span>
                                </td>
                                <td>
                                    <span class="text-caption">{{ tag.created_at ? new
                                        Date(tag.created_at).toLocaleDateString() : '-' }}</span>
                                </td>
                                <td>
                                    <v-btn size="small" icon="mdi-pencil" @click="editTag(tag)" variant="text"
                                        color="primary"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="deleteTag(tag.id)" variant="text"
                                        color="error"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="tags.length === 0">
                                <td colspan="6" class="text-center py-4">No tags found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <div class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="tags.length > 0 && pagination.total > 0">
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
                        <v-pagination 
                            v-model="currentPage" 
                            :length="pagination.last_page"
                            :total-visible="7"
                            density="comfortable"
                            @update:model-value="loadTags">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Tag Form Dialog -->
        <v-dialog v-model="showDialog" max-width="700" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">
                        {{ editingTag ? 'Edit Tag' : 'Create New Tag' }}
                    </span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"></v-btn>
                </v-card-title>

                <v-card-text class="pa-6">
                    <v-form ref="formRef">
                        <v-row>
                            <v-col cols="12">
                                <v-text-field v-model="form.name" label="Tag Name *" variant="outlined"
                                    :rules="[v => !!v || 'Name is required']" hint="Enter the tag name"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="form.slug" label="Slug" variant="outlined"
                                    hint="URL-friendly version (auto-generated if empty)"
                                    :rules="[v => !v || /^[a-z0-9-]+$/.test(v) || 'Slug can only contain lowercase letters, numbers, and hyphens']">
                                    <template v-slot:append>
                                        <v-btn icon="mdi-refresh" size="small" variant="text"
                                            @click="generateSlug"></v-btn>
                                    </template>
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select v-model="form.type" :items="typeOptions" label="Type *" variant="outlined"
                                    :rules="[v => !!v || 'Type is required']"
                                    hint="Select the type of content this tag applies to"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="form.description" label="Description" variant="outlined" rows="3"
                                    hint="Brief description of the tag (optional)"></v-textarea>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="closeDialog">Cancel</v-btn>
                    <v-btn color="primary" variant="flat" @click="saveTag" :loading="saving">
                        {{ editingTag ? 'Update' : 'Create' }} Tag
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            tags: [],
            showDialog: false,
            editingTag: null,
            saving: false,
            typeFilter: null,
            typeOptions: [
                { title: 'Product', value: 'product' },
                { title: 'Service', value: 'service' },
                { title: 'Post', value: 'post' },
                { title: 'General', value: 'general' }
            ],
            form: {
                name: '',
                slug: '',
                type: 'product',
                description: ''
            }
        };
    },
    async mounted() {
        await this.loadTags();
    },
    methods: {
        async loadTags() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }
                if (this.typeFilter) {
                    params.type = this.typeFilter;
                }

                const response = await this.$axios.get('/api/v1/tags', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.tags = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load tags');
            } finally {
                this.loading = false;
            }
        },
        async openDialog(tag = null) {
            this.editingTag = tag;
            this.showDialog = true;

            if (tag) {
                try {
                    await this.loadTagForEdit(tag);
                } catch (error) {
                    console.error('Error loading tag for edit:', error);
                    // Error message already shown by handleApiError in loadTagForEdit
                }
            } else {
                this.resetForm();
            }
        },
        async loadTagForEdit(tag) {
            try {
                const response = await this.$axios.get(`/api/v1/tags/${tag.id}`, {
                    headers: this.getAuthHeaders()
                });

                // Handle different response structures
                const data = response.data?.data || response.data;

                if (!data) {
                    throw new Error('No tag data received from server');
                }

                this.form = {
                    name: data.name || '',
                    slug: data.slug || '',
                    type: data.type || 'product',
                    description: data.description || ''
                };
            } catch (error) {
                this.handleApiError(error, 'Failed to load tag');
                throw error; // Re-throw to ensure openDialog catches it
            }
        },
        resetForm() {
            this.form = {
                name: '',
                slug: '',
                type: this.typeFilter || 'product',
                description: ''
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
        async saveTag() {
            // Validate form
            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            try {
                this.saving = true;

                // Prepare form data - slug is optional, will be auto-generated if empty
                const formData = {
                    name: this.form.name,
                    type: this.form.type,
                    description: this.form.description || null
                };

                // Only include slug if it's provided
                if (this.form.slug) {
                    formData.slug = this.form.slug;
                }

                if (this.editingTag) {
                    await this.$axios.put(`/api/v1/tags/${this.editingTag.id}`, formData, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Tag updated successfully');
                } else {
                    await this.$axios.post('/api/v1/tags', formData, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Tag created successfully');
                }

                this.closeDialog();
                await this.loadTags();
            } catch (error) {
                this.handleApiError(error, 'Failed to save tag');
            } finally {
                this.saving = false;
            }
        },
        editTag(tag) {
            this.openDialog(tag);
        },
        async deleteTag(id) {
            if (confirm('Are you sure you want to delete this tag? This action cannot be undone.')) {
                try {
                    await this.$axios.delete(`/api/v1/tags/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Tag deleted successfully');
                    await this.loadTags();
                } catch (error) {
                    const errorMessage = error.response?.data?.error ||
                        error.response?.data?.message ||
                        'Error deleting tag';
                    this.showError(errorMessage);
                }
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.editingTag = null;
            this.resetForm();
            if (this.$refs.formRef) {
                this.$refs.formRef.resetValidation();
            }
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadTags();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadTags();
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
