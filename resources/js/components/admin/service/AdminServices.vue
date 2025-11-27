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
                        <!-- Skeleton Loaders -->
                        <tr v-if="loading" v-for="n in 5" :key="`skeleton-${n}`">
                            <td>
                                <v-skeleton-loader type="text" width="200"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="150" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="80" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32" class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32" class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Service Data -->
                        <template v-else>
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
                                    <v-btn size="small" icon="mdi-eye" @click="viewService(service)" variant="text"
                                        color="info" title="View Service"></v-btn>
                                    <v-btn size="small" icon="mdi-pencil" @click="editService(service)" variant="text"
                                        title="Edit Service"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="deleteService(service.id)" variant="text"
                                        color="error" title="Delete Service"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="services.length === 0">
                                <td colspan="4" class="text-center py-4">No services found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="services.length > 0 && pagination.total > 0">
                            Showing <strong>{{ ((currentPage - 1) * perPage) + 1 }}</strong> to 
                            <strong>{{ Math.min(currentPage * perPage, pagination.total) }}</strong> of 
                            <strong>{{ pagination.total.toLocaleString() }}</strong> records
                            <span v-if="pagination.last_page > 1" class="ml-2">
                                (Page {{ currentPage }} of {{ pagination.last_page }})
                            </span>
                        </span>
                        <span v-else>
                            No records found
                        </span>
                    </div>
                    <div v-if="pagination.last_page > 1" class="d-flex align-center gap-2">
                        <v-pagination 
                            v-model="currentPage" 
                            :length="pagination.last_page"
                            :total-visible="7"
                            density="comfortable"
                            @update:model-value="loadServices">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Service Form Dialog -->
        <ServiceFormDialog v-model="showDialog" :editing-service="editingService" @saved="handleServiceSaved" />

        <!-- Service Details View Dialog -->
        <ServiceDetailsDialog v-model="showDetailsDialog" :service-id="selectedServiceId"
            @edit="handleEditFromDetails" />
    </div>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import ServiceFormDialog from './ServiceFormDialog.vue';
import ServiceDetailsDialog from './ServiceDetailsDialog.vue';

export default {
    components: {
        ServiceFormDialog,
        ServiceDetailsDialog
    },
    mixins: [adminPaginationMixin],
    data() {
        return {
            services: [],
            showDialog: false,
            editingService: null,
            showDetailsDialog: false,
            selectedServiceId: null,
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

                const response = await this.$axios.get('/api/v1/services', {
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
        },
        editService(service) {
            this.openDialog(service);
        },
        viewService(service) {
            this.selectedServiceId = service.id;
            this.showDetailsDialog = true;
        },
        handleEditFromDetails(service) {
            this.showDetailsDialog = false;
            this.$nextTick(() => {
                this.openDialog(service);
            });
        },
        handleServiceSaved() {
            this.loadServices();
        },
        async deleteService(id) {
            if (confirm('Are you sure you want to delete this service?')) {
                try {
                    await this.$axios.delete(`/api/v1/services/${id}`, {
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
