<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Services Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="showDialog = true">Create Service</v-btn>
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
            <v-card-title>Services</v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Published</th>
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

                <!-- Pagination -->
                <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                    @update:model-value="loadServices" class="mt-4"></v-pagination>
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
            services: [],
            showDialog: false,
            publishedFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ]
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
        editService(service) {
            // TODO: Implement edit functionality
            alert('Edit functionality coming soon');
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
        }
    }
};
</script>
