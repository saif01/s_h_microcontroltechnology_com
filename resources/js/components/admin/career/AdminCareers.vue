<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Careers Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create
                Career</v-btn>
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
                        <v-select v-model="publishedFilter" :items="publishedOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadCareers"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="departmentFilter" :items="departmentOptions" label="Filter by Department"
                            prepend-inner-icon="mdi-office-building" variant="outlined" density="compact" clearable
                            @update:model-value="loadCareers"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field v-model="search" label="Search careers" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadCareers"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Careers Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Careers</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="careers.length > 0">
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
                            <th class="sortable" @click="onSort('department')">
                                <div class="d-flex align-center">
                                    Department
                                    <v-icon :icon="getSortIcon('department')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('location')">
                                <div class="d-flex align-center">
                                    Location
                                    <v-icon :icon="getSortIcon('location')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Applications</th>
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
                                <v-skeleton-loader type="text" width="120"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="120"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="60" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="80" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32"
                                        class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"
                                        class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Career Data -->
                        <template v-else>
                            <tr v-for="career in careers" :key="career.id">
                                <td>{{ career.title }}</td>
                                <td>
                                    <v-chip size="small" variant="outlined" v-if="career.department">
                                        {{ career.department }}
                                    </v-chip>
                                    <span v-else class="text-grey">-</span>
                                </td>
                                <td>
                                    <v-chip size="small" variant="outlined" v-if="career.location">
                                        {{ career.location }}
                                    </v-chip>
                                    <span v-else class="text-grey">-</span>
                                </td>
                                <td>
                                    <v-chip size="small" color="primary" variant="flat">
                                        {{ career.applications_count || 0 }}
                                    </v-chip>
                                </td>
                                <td>
                                    <v-chip :color="career.published ? 'success' : 'error'" size="small">
                                        {{ career.published ? 'Published' : 'Draft' }}
                                    </v-chip>
                                </td>
                                <td>
                                    <v-btn size="small" icon="mdi-eye" @click="viewCareer(career)" variant="text"
                                        color="info" title="View Career"></v-btn>
                                    <v-btn size="small" icon="mdi-pencil" @click="editCareer(career)" variant="text"
                                        title="Edit Career"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="deleteCareer(career.id)"
                                        variant="text" color="error" title="Delete Career"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="careers.length === 0">
                                <td colspan="6" class="text-center py-4">No careers found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div
                    class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="careers.length > 0 && pagination.total > 0">
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
                        <v-pagination v-model="currentPage" :length="pagination.last_page" :total-visible="7"
                            density="comfortable" @update:model-value="loadCareers">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Career Form Dialog -->
        <CareerFormDialog v-model="showDialog" :editing-career="editingCareer" @saved="handleCareerSaved" />

        <!-- Career Details View Dialog -->
        <CareerDetailsDialog v-model="showDetailsDialog" :career-id="selectedCareerId" @edit="handleEditFromDetails" />
    </div>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import CareerFormDialog from './CareerFormDialog.vue';
import CareerDetailsDialog from './CareerDetailsDialog.vue';

export default {
    components: {
        CareerFormDialog,
        CareerDetailsDialog
    },
    mixins: [adminPaginationMixin],
    data() {
        return {
            careers: [],
            showDialog: false,
            editingCareer: null,
            showDetailsDialog: false,
            selectedCareerId: null,
            publishedFilter: null,
            departmentFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ],
            departmentOptions: []
        };
    },
    async mounted() {
        await this.loadCareers();
        await this.loadDepartments();
    },
    methods: {
        async loadCareers() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                if (this.departmentFilter) {
                    params.department = this.departmentFilter;
                }

                const response = await axios.get('/api/v1/careers', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.careers = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load careers');
            } finally {
                this.loading = false;
            }
        },
        async loadDepartments() {
            try {
                const response = await axios.get('/api/v1/careers', {
                    params: { per_page: 1000 },
                    headers: this.getAuthHeaders()
                });

                const allCareers = response.data.data || [];
                const departments = [...new Set(allCareers.map(c => c.department).filter(Boolean))];
                this.departmentOptions = departments.map(d => ({ title: d, value: d }));
            } catch (error) {
                console.error('Failed to load departments:', error);
            }
        },
        openDialog(career = null) {
            this.editingCareer = career;
            this.showDialog = true;
        },
        editCareer(career) {
            this.openDialog(career);
        },
        viewCareer(career) {
            this.selectedCareerId = career.id;
            this.showDetailsDialog = true;
        },
        handleEditFromDetails(career) {
            this.showDetailsDialog = false;
            this.$nextTick(() => {
                this.openDialog(career);
            });
        },
        handleCareerSaved() {
            this.loadCareers();
            this.loadDepartments();
        },
        async deleteCareer(id) {
            if (confirm('Are you sure you want to delete this career? This will also delete all associated job applications.')) {
                try {
                    await axios.delete(`/api/v1/careers/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Career deleted successfully');
                    await this.loadCareers();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting career');
                }
            }
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadCareers();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadCareers();
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

.page-title {
    font-weight: 600;
}

.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: rgba(0, 0, 0, 0.04);
}
</style>
