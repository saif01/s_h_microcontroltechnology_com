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
                        <v-text-field v-model="search" label="Search leads" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadLeads"></v-text-field>
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
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Leads Table -->
        <v-card>
            <v-card-title>Leads</v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
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

                <!-- Pagination -->
                <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                    @update:model-value="loadLeads" class="mt-4"></v-pagination>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            leads: [],
            search: '',
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
            ],
            currentPage: 1,
            perPage: 10,
            perPageOptions: [10, 25, 50, 100, 500],
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 0
            }
        };
    },
    async mounted() {
        await this.loadLeads();
    },
    methods: {
        async loadLeads() {
            try {
                const token = localStorage.getItem('admin_token');
                const params = {
                    page: this.currentPage,
                    per_page: this.perPage
                };

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
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.leads = response.data.data || [];
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    per_page: response.data.per_page,
                    total: response.data.total
                };
            } catch (error) {
                console.error('Error loading leads:', error);
                this.showError('Failed to load leads');
            }
        },
        viewLead(lead) {
            alert(`Lead Details:\nName: ${lead.name}\nEmail: ${lead.email}\nPhone: ${lead.phone || 'N/A'}\nType: ${lead.type}\nStatus: ${lead.status}\nMessage: ${lead.message || 'N/A'}`);
        },
        async exportLeads() {
            try {
                const token = localStorage.getItem('admin_token');
                const response = await axios.get('/api/v1/leads/export/csv', {
                    headers: { Authorization: `Bearer ${token}` },
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
                this.showError('Error exporting leads');
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
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString();
        },
        showSuccess(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: message
                });
            } else {
                alert(message);
            }
        },
        showError(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'error',
                    title: message
                });
            } else {
                alert(message);
            }
        },
        onPerPageChange() {
            this.currentPage = 1;
            this.loadLeads();
        }
    }
};
</script>
