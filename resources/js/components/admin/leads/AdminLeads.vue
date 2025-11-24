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
                    <v-col cols="12" md="3">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="statusFilter" :items="statusOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadLeads"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="typeFilter" :items="typeOptions" label="Filter by Type"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadLeads"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
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
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage, pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th class="sortable" @click="onSort('name')" >
                                <div class="d-flex align-center">
                                    Name
                                    <v-icon :icon="getSortIcon('name')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('email')" >
                                <div class="d-flex align-center">
                                    Email
                                    <v-icon :icon="getSortIcon('email')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('phone')" >
                                <div class="d-flex align-center">
                                    Phone
                                    <v-icon :icon="getSortIcon('phone')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('type')" >
                                <div class="d-flex align-center">
                                    Type
                                    <v-icon :icon="getSortIcon('type')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('status')" >
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('status')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('created_at')" >
                                <div class="d-flex align-center">
                                    Date
                                    <v-icon :icon="getSortIcon('created_at')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lead in leads" :key="lead.id">
                            <td>{{ lead.name }}</td>
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
                                <v-btn size="small" icon="mdi-eye" @click="viewLead(lead)" variant="text"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="leads.length === 0">
                            <td colspan="7" class="text-center py-4">No leads found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex justify-space-between align-center mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="leads.length > 0">
                            Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage, pagination.total) }} of {{ pagination.total }} records
                        </span>
                        <span v-else>
                            No records found
                        </span>
                    </div>
                    <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                        @update:model-value="loadLeads"></v-pagination>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            leads: [],
            statusFilter: null,
            typeFilter: null,
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

                const response = await axios.get('/api/v1/leads', {
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
        viewLead(lead) {
            alert(`Lead Details:\nName: ${lead.name}\nEmail: ${lead.email}\nPhone: ${lead.phone || 'N/A'}\nType: ${lead.type}\nStatus: ${lead.status}\nMessage: ${lead.message || 'N/A'}`);
        },
        async exportLeads() {
            try {
                const response = await axios.get('/api/v1/leads/export/csv', {
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
