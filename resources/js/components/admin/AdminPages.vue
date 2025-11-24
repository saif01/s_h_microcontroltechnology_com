<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Pages Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="showDialog = true" class="add-button">Create Page</v-btn>
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
                            @update:model-value="loadPages"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search pages" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadPages"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Pages Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Pages</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="pages.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage, pagination.total) }} of {{ pagination.total }}
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
                            <th>Type</th>
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
                        <tr v-for="page in pages" :key="page.id">
                            <td>{{ page.title }}</td>
                            <td>
                                <v-chip size="small" variant="outlined">{{ page.slug }}</v-chip>
                            </td>
                            <td>{{ page.page_type || '-' }}</td>
                            <td>
                                <v-chip :color="page.published ? 'success' : 'error'" size="small">
                                    {{ page.published ? 'Published' : 'Draft' }}
                                </v-chip>
                            </td>
                            <td>
                                <v-btn size="small" icon="mdi-pencil" @click="editPage(page)" variant="text"></v-btn>
                                <v-btn size="small" icon="mdi-delete" @click="deletePage(page.id)" variant="text"
                                    color="error"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="pages.length === 0">
                            <td colspan="5" class="text-center py-4">No pages found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex justify-space-between align-center mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="pages.length > 0">
                            Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage, pagination.total) }} of {{ pagination.total }} records
                        </span>
                        <span v-else>
                            No records found
                        </span>
                    </div>
                    <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                        @update:model-value="loadPages"></v-pagination>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            pages: [],
            showDialog: false,
            publishedFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ]
        };
    },
    async mounted() {
        await this.loadPages();
    },
    methods: {
        async loadPages() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                const response = await axios.get('/api/v1/pages', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.pages = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load pages');
            } finally {
                this.loading = false;
            }
        },
        onSort(field) {
            this.handleSort(field);
            this.loadPages();
        },
        editPage(page) {
            // TODO: Implement edit functionality
            alert('Edit functionality coming soon');
        },
        async deletePage(id) {
            if (confirm('Are you sure you want to delete this page?')) {
                try {
                    await axios.delete(`/api/v1/pages/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Page deleted successfully');
                    await this.loadPages();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting page');
                }
            }
        }
    }
};
</script>
