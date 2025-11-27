<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Job Applications Management</h1>
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
                        <v-select v-model="statusFilter" :items="statusOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadApplications"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="careerFilter" :items="careerOptions" label="Filter by Career"
                            prepend-inner-icon="mdi-briefcase" variant="outlined" density="compact" clearable
                            @update:model-value="loadApplications"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field v-model="search" label="Search applications" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadApplications"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Applications Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Job Applications</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="applications.length > 0">
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
                            <th class="sortable" @click="onSort('email')">
                                <div class="d-flex align-center">
                                    Email
                                    <v-icon :icon="getSortIcon('email')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Phone</th>
                            <th>Career</th>
                            <th class="sortable" @click="onSort('status')">
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('status')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('created_at')">
                                <div class="d-flex align-center">
                                    Applied Date
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
                                <v-skeleton-loader type="text" width="200"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="120"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="150"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="100" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="120"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32" class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Application Data -->
                        <template v-else>
                            <tr v-for="application in applications" :key="application.id">
                                <td>{{ application.name }}</td>
                                <td>{{ application.email }}</td>
                                <td>{{ application.phone || '-' }}</td>
                                <td>
                                    <v-chip size="small" variant="outlined" v-if="application.career">
                                        {{ application.career.title }}
                                    </v-chip>
                                    <span v-else class="text-grey">-</span>
                                </td>
                                <td>
                                    <v-select v-model="application.status" :items="statusOptions" variant="outlined"
                                        density="compact" hide-details @update:model-value="updateStatus(application)"
                                        :style="{ minWidth: '140px' }"></v-select>
                                </td>
                                <td>{{ formatDate(application.created_at) }}</td>
                                <td>
                                    <v-btn size="small" icon="mdi-eye" @click="viewApplication(application)" variant="text"
                                        color="info" title="View Application"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="deleteApplication(application.id)" variant="text"
                                        color="error" title="Delete Application"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="applications.length === 0">
                                <td colspan="7" class="text-center py-4">No applications found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="applications.length > 0 && pagination.total > 0">
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
                            @update:model-value="loadApplications">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Application Details Dialog -->
        <v-dialog v-model="showDetailsDialog" max-width="800" scrollable>
            <v-card v-if="selectedApplication">
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">Application Details</span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="showDetailsDialog = false"></v-btn>
                </v-card-title>
                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-label class="text-caption text-grey">Name</v-label>
                            <p class="text-body-1">{{ selectedApplication.name }}</p>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-label class="text-caption text-grey">Email</v-label>
                            <p class="text-body-1">
                                <a :href="`mailto:${selectedApplication.email}`">{{ selectedApplication.email }}</a>
                            </p>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-label class="text-caption text-grey">Phone</v-label>
                            <p class="text-body-1">{{ selectedApplication.phone || '-' }}</p>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-label class="text-caption text-grey">Career Position</v-label>
                            <p class="text-body-1">
                                <v-chip size="small" v-if="selectedApplication.career">
                                    {{ selectedApplication.career.title }}
                                </v-chip>
                                <span v-else>-</span>
                            </p>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-label class="text-caption text-grey">Status</v-label>
                            <p class="text-body-1">
                                <v-chip :color="getStatusColor(selectedApplication.status)" size="small">
                                    {{ selectedApplication.status }}
                                </v-chip>
                            </p>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-label class="text-caption text-grey">Applied Date</v-label>
                            <p class="text-body-1">{{ formatDateTime(selectedApplication.created_at) }}</p>
                        </v-col>
                        <v-col cols="12" v-if="selectedApplication.cover_letter">
                            <v-label class="text-caption text-grey">Cover Letter</v-label>
                            <div class="text-body-1 mt-2" v-html="selectedApplication.cover_letter"></div>
                        </v-col>
                        <v-col cols="12" v-if="selectedApplication.resume_url">
                            <v-label class="text-caption text-grey">Resume</v-label>
                            <div class="mt-2">
                                <v-btn color="primary" variant="outlined" :href="selectedApplication.resume_url" target="_blank"
                                    prepend-icon="mdi-download">
                                    Download Resume
                                </v-btn>
                            </div>
                        </v-col>
                        <v-col cols="12" v-if="selectedApplication.notes">
                            <v-label class="text-caption text-grey">Notes</v-label>
                            <p class="text-body-1">{{ selectedApplication.notes }}</p>
                        </v-col>
                        <v-col cols="12">
                            <v-label class="text-caption text-grey mb-2">Add/Edit Notes</v-label>
                            <v-textarea v-model="notesText" variant="outlined" rows="3"
                                placeholder="Add notes about this application..."></v-textarea>
                            <v-btn color="primary" @click="saveNotes" class="mt-2" :loading="savingNotes">
                                Save Notes
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
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
            applications: [],
            showDetailsDialog: false,
            selectedApplication: null,
            notesText: '',
            savingNotes: false,
            statusFilter: null,
            careerFilter: null,
            statusOptions: [
                { title: 'New', value: 'new' },
                { title: 'Reviewed', value: 'reviewed' },
                { title: 'Shortlisted', value: 'shortlisted' },
                { title: 'Rejected', value: 'rejected' },
                { title: 'Hired', value: 'hired' }
            ],
            careerOptions: []
        };
    },
    async mounted() {
        await this.loadApplications();
        await this.loadCareers();
        
        // Check if career_id is in query params
        if (this.$route.query.career_id) {
            this.careerFilter = parseInt(this.$route.query.career_id);
            await this.loadApplications();
        }
    },
    methods: {
        async loadApplications() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.statusFilter) {
                    params.status = this.statusFilter;
                }

                if (this.careerFilter) {
                    params.career_id = this.careerFilter;
                }

                const response = await this.$axios.get('/api/v1/job-applications', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.applications = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load job applications');
            } finally {
                this.loading = false;
            }
        },
        async loadCareers() {
            try {
                const response = await this.$axios.get('/api/v1/careers', {
                    params: { per_page: 1000 },
                    headers: this.getAuthHeaders()
                });
                
                const careers = response.data.data || [];
                this.careerOptions = careers.map(c => ({ 
                    title: c.title, 
                    value: c.id 
                }));
            } catch (error) {
                console.error('Failed to load careers:', error);
            }
        },
        async updateStatus(application) {
            try {
                await this.$axios.put(`/api/v1/job-applications/${application.id}`, {
                    status: application.status
                }, {
                    headers: this.getAuthHeaders()
                });
                this.showSuccess('Status updated successfully');
            } catch (error) {
                this.handleApiError(error, 'Failed to update status');
                // Reload to revert the change
                await this.loadApplications();
            }
        },
        viewApplication(application) {
            this.selectedApplication = { ...application };
            this.notesText = application.notes || '';
            this.showDetailsDialog = true;
        },
        async saveNotes() {
            if (!this.selectedApplication) return;
            
            this.savingNotes = true;
            try {
                await this.$axios.put(`/api/v1/job-applications/${this.selectedApplication.id}`, {
                    notes: this.notesText
                }, {
                    headers: this.getAuthHeaders()
                });
                this.showSuccess('Notes saved successfully');
                this.selectedApplication.notes = this.notesText;
                await this.loadApplications();
            } catch (error) {
                this.handleApiError(error, 'Failed to save notes');
            } finally {
                this.savingNotes = false;
            }
        },
        async deleteApplication(id) {
            if (confirm('Are you sure you want to delete this application?')) {
                try {
                    await this.$axios.delete(`/api/v1/job-applications/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Application deleted successfully');
                    await this.loadApplications();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting application');
                }
            }
        },
        getStatusColor(status) {
            const colors = {
                'new': 'info',
                'reviewed': 'primary',
                'shortlisted': 'warning',
                'rejected': 'error',
                'hired': 'success'
            };
            return colors[status] || 'grey';
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadApplications();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadApplications();
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

