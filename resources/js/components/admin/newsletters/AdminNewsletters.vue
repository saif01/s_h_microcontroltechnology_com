<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Newsletter Subscriptions</h1>
            <v-btn color="primary" prepend-icon="mdi-download" @click="exportSubscriptions">Export to CSV</v-btn>
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
                            @update:model-value="loadSubscriptions"></v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="search" label="Search by email" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadSubscriptions"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Subscriptions Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Subscriptions</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="subscriptions.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                            pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th class="sortable" @click="onSort('email')">
                                <div class="d-flex align-center">
                                    Email
                                    <v-icon :icon="getSortIcon('email')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('status')">
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('status')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('subscribed_at')">
                                <div class="d-flex align-center">
                                    Subscribed At
                                    <v-icon :icon="getSortIcon('subscribed_at')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('unsubscribed_at')">
                                <div class="d-flex align-center">
                                    Unsubscribed At
                                    <v-icon :icon="getSortIcon('unsubscribed_at')" size="small" class="ml-1"></v-icon>
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
                                <v-skeleton-loader type="chip" width="90" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="140"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="140"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32" class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32" class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Subscription Data -->
                        <template v-else>
                            <tr v-for="subscription in subscriptions" :key="subscription.id">
                                <td>
                                    <a :href="`mailto:${subscription.email}`" class="text-primary text-decoration-none">
                                        {{ subscription.email }}
                                    </a>
                                </td>
                                <td>
                                    <v-chip :color="getStatusColor(subscription.status)" size="small">
                                        {{ subscription.status }}
                                    </v-chip>
                                </td>
                                <td>{{ formatDate(subscription.subscribed_at) }}</td>
                                <td>{{ subscription.unsubscribed_at ? formatDate(subscription.unsubscribed_at) : '-' }}</td>
                                <td>
                                    <v-btn size="small" icon="mdi-eye" @click="viewSubscription(subscription)" variant="text"
                                        class="mr-1" title="View Details"></v-btn>
                                    <v-btn v-if="subscription.status === 'active'" size="small" icon="mdi-email-remove"
                                        @click="unsubscribe(subscription)" variant="text" color="error"
                                        title="Unsubscribe"></v-btn>
                                    <v-btn v-else size="small" icon="mdi-email-check" @click="resubscribe(subscription)"
                                        variant="text" color="success" title="Resubscribe"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="confirmDelete(subscription)" variant="text"
                                        color="error" class="ml-1" title="Delete"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="subscriptions.length === 0">
                                <td colspan="5" class="text-center py-4">No subscriptions found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="subscriptions.length > 0 && pagination.total > 0">
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
                            @update:model-value="loadSubscriptions">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- View Subscription Dialog -->
        <v-dialog v-model="showDialog" max-width="600" scrollable>
            <v-card v-if="selectedSubscription">
                <v-card-title class="d-flex justify-space-between align-center bg-primary text-white">
                    <div class="d-flex align-center">
                        <v-icon icon="mdi-email" class="mr-2"></v-icon>
                        <span>Subscription Details</span>
                    </div>
                    <v-btn icon="mdi-close" variant="text" @click="closeDialog" color="white"></v-btn>
                </v-card-title>

                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Email</div>
                                <div class="text-body-1">
                                    <a :href="`mailto:${selectedSubscription.email}`"
                                        class="text-primary text-decoration-none">
                                        {{ selectedSubscription.email }}
                                    </a>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Status</div>
                                <div class="text-body-1">
                                    <v-chip :color="getStatusColor(selectedSubscription.status)" size="small">
                                        {{ selectedSubscription.status }}
                                    </v-chip>
                                </div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Subscribed At</div>
                                <div class="text-body-2">{{ formatDateTime(selectedSubscription.subscribed_at) }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6" v-if="selectedSubscription.unsubscribed_at">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Unsubscribed At</div>
                                <div class="text-body-2">{{ formatDateTime(selectedSubscription.unsubscribed_at) }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="mb-4">
                                <div class="text-caption text-grey mb-1">Created At</div>
                                <div class="text-body-2">{{ formatDateTime(selectedSubscription.created_at) }}</div>
                            </div>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-5">
                    <v-spacer></v-spacer>
                    <v-btn v-if="selectedSubscription.status === 'active'" color="error"
                        prepend-icon="mdi-email-remove" @click="unsubscribeFromDialog">
                        Unsubscribe
                    </v-btn>
                    <v-btn v-else color="success" prepend-icon="mdi-email-check" @click="resubscribeFromDialog">
                        Resubscribe
                    </v-btn>
                    <v-btn color="grey" variant="text" @click="closeDialog">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="showDeleteDialog" max-width="400">
            <v-card>
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete the subscription for
                    <strong>{{ subscriptionToDelete?.email }}</strong>? This action cannot be undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" variant="text" @click="showDeleteDialog = false">Cancel</v-btn>
                    <v-btn color="error" @click="deleteSubscription" :loading="saving">Delete</v-btn>
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
            subscriptions: [],
            statusFilter: null,
            showDialog: false,
            selectedSubscription: null,
            showDeleteDialog: false,
            subscriptionToDelete: null,
            statusOptions: [
                { title: 'Active', value: 'active' },
                { title: 'Unsubscribed', value: 'unsubscribed' }
            ]
        };
    },
    async mounted() {
        await this.loadSubscriptions();
    },
    methods: {
        onSort(field) {
            this.handleSort(field);
            this.loadSubscriptions();
        },
        async loadSubscriptions() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.statusFilter) {
                    params.status = this.statusFilter;
                }

                const response = await this.$axios.get('/api/v1/newsletters', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.subscriptions = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load subscriptions');
            } finally {
                this.loading = false;
            }
        },
        async viewSubscription(subscription) {
            try {
                const response = await this.$axios.get(`/api/v1/newsletters/${subscription.id}`, {
                    headers: this.getAuthHeaders()
                });

                this.selectedSubscription = response.data;
                this.showDialog = true;
            } catch (error) {
                this.handleApiError(error, 'Failed to load subscription details');
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.selectedSubscription = null;
        },
        async unsubscribe(subscription) {
            try {
                this.saving = true;
                await this.$axios.put(`/api/v1/newsletters/${subscription.id}`, {
                    status: 'unsubscribed'
                }, {
                    headers: this.getAuthHeaders()
                });

                this.showSuccess('Subscription updated successfully');
                await this.loadSubscriptions();
            } catch (error) {
                this.handleApiError(error, 'Failed to unsubscribe');
            } finally {
                this.saving = false;
            }
        },
        async resubscribe(subscription) {
            try {
                this.saving = true;
                await this.$axios.put(`/api/v1/newsletters/${subscription.id}`, {
                    status: 'active'
                }, {
                    headers: this.getAuthHeaders()
                });

                this.showSuccess('Subscription reactivated successfully');
                await this.loadSubscriptions();
            } catch (error) {
                this.handleApiError(error, 'Failed to resubscribe');
            } finally {
                this.saving = false;
            }
        },
        async unsubscribeFromDialog() {
            if (!this.selectedSubscription) return;
            await this.unsubscribe(this.selectedSubscription);
            this.closeDialog();
        },
        async resubscribeFromDialog() {
            if (!this.selectedSubscription) return;
            await this.resubscribe(this.selectedSubscription);
            this.closeDialog();
        },
        confirmDelete(subscription) {
            this.subscriptionToDelete = subscription;
            this.showDeleteDialog = true;
        },
        async deleteSubscription() {
            if (!this.subscriptionToDelete) return;

            try {
                this.saving = true;
                await this.$axios.delete(`/api/v1/newsletters/${this.subscriptionToDelete.id}`, {
                    headers: this.getAuthHeaders()
                });

                this.showSuccess('Subscription deleted successfully');
                this.showDeleteDialog = false;
                this.subscriptionToDelete = null;
                await this.loadSubscriptions();
            } catch (error) {
                this.handleApiError(error, 'Failed to delete subscription');
            } finally {
                this.saving = false;
            }
        },
        async exportSubscriptions() {
            try {
                const response = await this.$axios.get('/api/v1/newsletters/export/csv', {
                    headers: this.getAuthHeaders(),
                    responseType: 'blob'
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `newsletter_subscriptions_${new Date().toISOString().split('T')[0]}.csv`);
                document.body.appendChild(link);
                link.click();
                link.remove();

                this.showSuccess('Subscriptions exported successfully');
            } catch (error) {
                this.handleApiError(error, 'Failed to export subscriptions');
            }
        },
        getStatusColor(status) {
            switch (status) {
                case 'active':
                    return 'success';
                case 'unsubscribed':
                    return 'error';
                default:
                    return 'grey';
            }
        }
    }
};
</script>

<style scoped>
.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: rgba(0, 0, 0, 0.04);
}
</style>

