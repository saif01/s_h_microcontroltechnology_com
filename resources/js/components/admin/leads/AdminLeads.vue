<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Leads Management</h1>
            <v-btn color="primary" prepend-icon="mdi-download" @click="exportLeads">Export to CSV</v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="2">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-select v-model="statusFilter" :items="statusOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadLeads"></v-select>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-select v-model="typeFilter" :items="typeOptions" label="Filter by Type"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadLeads"></v-select>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-select v-model="readFilter" :items="readOptions" label="Filter by Read Status"
                            prepend-inner-icon="mdi-email" variant="outlined" density="compact" clearable
                            @update:model-value="loadLeads"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search leads" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadLeads"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Leads Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Leads</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="leads.length > 0">
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
                            <th class="sortable" @click="onSort('phone')">
                                <div class="d-flex align-center">
                                    Phone
                                    <v-icon :icon="getSortIcon('phone')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('type')">
                                <div class="d-flex align-center">
                                    Type
                                    <v-icon :icon="getSortIcon('type')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('status')">
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('status')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('created_at')">
                                <div class="d-flex align-center">
                                    Date
                                    <v-icon :icon="getSortIcon('created_at')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Read</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Skeleton Loaders -->
                        <tr v-if="loading" v-for="n in 5" :key="`skeleton-${n}`">
                            <td>
                                <div class="d-flex align-center">
                                    <v-skeleton-loader type="avatar" width="16" height="16" class="mr-2"></v-skeleton-loader>
                                    <v-skeleton-loader type="text" width="120"></v-skeleton-loader>
                                </div>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="180"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="100"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="80" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="90" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="120"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="70" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32" class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Lead Data -->
                        <template v-else>
                            <tr v-for="lead in leads" :key="lead.id" :class="{ 'unread-row': !lead.is_read }">
                                <td>
                                    <div class="d-flex align-center">
                                        <v-icon v-if="!lead.is_read" icon="mdi-circle" size="small" color="primary"
                                            class="mr-2"></v-icon>
                                        <span v-else class="mr-2" style="width: 16px;"></span>
                                        {{ lead.name }}
                                    </div>
                                </td>
                                <td>{{ lead.email }}</td>
                                <td>{{ lead.phone || '-' }}</td>
                                <td>
                                    <v-chip size="small" variant="outlined">{{ lead.type }}</v-chip>
                                </td>
                                <td>
                                    <v-chip :color="getStatusColor(lead.status)" size="small">
                                        {{ lead.status }}
                                    </v-chip>
                                </td>
                                <td>{{ formatDate(lead.created_at) }}</td>
                                <td>
                                    <v-chip v-if="lead.is_read" size="small" color="success" variant="flat">
                                        <v-icon icon="mdi-check" size="small" class="mr-1"></v-icon>
                                        Read
                                    </v-chip>
                                    <v-chip v-else size="small" color="error" variant="flat">
                                        <v-icon icon="mdi-email" size="small" class="mr-1"></v-icon>
                                        Unread
                                    </v-chip>
                                </td>
                                <td>
                                    <v-btn size="small" icon="mdi-eye" @click="viewLead(lead)" variant="text"
                                        class="mr-1"></v-btn>
                                    <v-btn v-if="!lead.is_read" size="small" icon="mdi-email-mark-as-unread"
                                        @click="markAsRead(lead)" variant="text" color="primary"
                                        title="Mark as read"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="leads.length === 0">
                                <td colspan="8" class="text-center py-4">No leads found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="leads.length > 0 && pagination.total > 0">
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
                            @update:model-value="loadLeads">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- View Lead Dialog -->
        <v-dialog v-model="showDialog" max-width="800" scrollable>
            <v-card v-if="selectedLead">
                <v-card-title class="d-flex justify-space-between align-center bg-primary text-white">
                    <div class="d-flex align-center">
                        <v-icon icon="mdi-email" class="mr-2"></v-icon>
                        <span>Lead Details</span>
                    </div>
                    <v-btn icon="mdi-close" variant="text" @click="closeDialog" color="white"></v-btn>
                </v-card-title>

                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Name</div>
                                <div class="text-body-1 font-weight-medium">{{ selectedLead.name }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Email</div>
                                <div class="text-body-1">
                                    <a :href="`mailto:${selectedLead.email}`" class="text-primary text-decoration-none">
                                        {{ selectedLead.email }}
                                    </a>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Phone</div>
                                <div class="text-body-1">
                                    <a v-if="selectedLead.phone" :href="`tel:${selectedLead.phone}`"
                                        class="text-primary text-decoration-none">
                                        {{ selectedLead.phone }}
                                    </a>
                                    <span v-else class="text-grey">N/A</span>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Type</div>
                                <div class="text-body-1">
                                    <v-chip size="small" variant="outlined">{{ selectedLead.type }}</v-chip>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Status</div>
                                <div class="text-body-1">
                                    <v-chip :color="getStatusColor(selectedLead.status)" size="small">
                                        {{ selectedLead.status }}
                                    </v-chip>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Read Status</div>
                                <div class="text-body-1">
                                    <v-chip v-if="selectedLead.is_read" size="small" color="success" variant="flat">
                                        <v-icon icon="mdi-check" size="small" class="mr-1"></v-icon>
                                        Read
                                    </v-chip>
                                    <v-chip v-else size="small" color="error" variant="flat">
                                        <v-icon icon="mdi-email" size="small" class="mr-1"></v-icon>
                                        Unread
                                    </v-chip>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" v-if="selectedLead.subject">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Subject</div>
                                <div class="text-body-1 font-weight-medium">{{ selectedLead.subject }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Message</div>
                                <div class="text-body-1 pa-4 bg-grey-lighten-4 rounded"
                                    style="white-space: pre-wrap; min-height: 100px;">
                                    {{ selectedLead.message || 'No message provided' }}
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Created At</div>
                                <div class="text-body-2">{{ formatDate(selectedLead.created_at) }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6" v-if="selectedLead.read_at">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Read At</div>
                                <div class="text-body-2">{{ formatDate(selectedLead.read_at) }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" v-if="selectedLead.assigned_user">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Assigned To</div>
                                <div class="text-body-1">{{ selectedLead.assigned_user.name }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" v-if="selectedLead.notes">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Notes</div>
                                <div class="text-body-1 pa-4 bg-grey-lighten-4 rounded" style="white-space: pre-wrap;">
                                    {{ selectedLead.notes }}
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-5">
                    <v-spacer></v-spacer>
                    <v-btn v-if="!selectedLead.is_read" color="primary" prepend-icon="mdi-email-mark-as-unread"
                        @click="markAsReadFromDialog">
                        Mark as Read
                    </v-btn>
                    <v-btn color="grey" variant="text" @click="closeDialog">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
    inject: ['refreshUnreadCount'],
    data() {
        return {
            leads: [],
            statusFilter: null,
            typeFilter: null,
            readFilter: null,
            showDialog: false,
            selectedLead: null,
            statusOptions: [
                { title: 'New', value: 'new' },
                { title: 'Contacted', value: 'contacted' },
                { title: 'Qualified', value: 'qualified' },
                { title: 'Converted', value: 'converted' },
                { title: 'Lost', value: 'lost' }
            ],
            typeOptions: [
                { title: 'Contact', value: 'contact' },
                { title: 'Quote', value: 'quote' },
                { title: 'Support', value: 'support' }
            ],
            readOptions: [
                { title: 'Unread', value: 'unread' },
                { title: 'Read', value: 'read' }
            ]
        };
    },
    async mounted() {
        await this.loadLeads();
    },
    methods: {
        async loadLeads() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.statusFilter) {
                    params.status = this.statusFilter;
                }

                if (this.typeFilter) {
                    params.type = this.typeFilter;
                }

                if (this.readFilter === 'unread') {
                    params.is_read = false;
                } else if (this.readFilter === 'read') {
                    params.is_read = true;
                }

                const response = await this.$axios.get('/api/v1/leads', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.leads = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load leads');
            } finally {
                this.loading = false;
            }
        },
        async viewLead(lead) {
            try {
                // Fetch full lead details and mark as read
                const response = await this.$axios.get(`/api/v1/leads/${lead.id}`, {
                    headers: this.getAuthHeaders()
                });

                this.selectedLead = response.data;
                this.showDialog = true;

                // Reload leads to update read status
                await this.loadLeads();

                // Refresh unread count using inject
                if (this.refreshUnreadCount) {
                    this.refreshUnreadCount();
                }
            } catch (error) {
                this.handleApiError(error, 'Failed to load lead details');
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.selectedLead = null;
        },
        async markAsReadFromDialog() {
            if (!this.selectedLead) return;

            try {
                await this.$axios.post(`/api/v1/leads/${this.selectedLead.id}/mark-as-read`, {}, {
                    headers: this.getAuthHeaders()
                });

                // Update the selected lead
                this.selectedLead.is_read = true;
                this.selectedLead.read_at = new Date().toISOString();

                this.showSuccess('Lead marked as read');
                await this.loadLeads();

                // Refresh unread count using inject
                if (this.refreshUnreadCount) {
                    this.refreshUnreadCount();
                }
            } catch (error) {
                this.handleApiError(error, 'Failed to mark lead as read');
            }
        },
        async markAsRead(lead) {
            try {
                await this.$axios.post(`/api/v1/leads/${lead.id}/mark-as-read`, {}, {
                    headers: this.getAuthHeaders()
                });

                this.showSuccess('Lead marked as read');
                await this.loadLeads();

                // Refresh unread count using inject
                if (this.refreshUnreadCount) {
                    this.refreshUnreadCount();
                }
            } catch (error) {
                this.handleApiError(error, 'Failed to mark lead as read');
            }
        },
        async exportLeads() {
            try {
                const response = await this.$axios.get('/api/v1/leads/export/csv', {
                    headers: this.getAuthHeaders(),
                    responseType: 'blob'
                });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'leads.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                this.showSuccess('Leads exported successfully');
            } catch (error) {
                this.handleApiError(error, 'Error exporting leads');
            }
        },
        getStatusColor(status) {
            const colors = {
                'new': 'info',
                'contacted': 'warning',
                'qualified': 'primary',
                'converted': 'success',
                'lost': 'error'
            };
            return colors[status] || 'grey';
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadLeads();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadLeads();
        }
    }
};
</script>

<style scoped>
.unread-row {
    background-color: rgba(25, 118, 210, 0.05);
    font-weight: 500;
}
</style>
