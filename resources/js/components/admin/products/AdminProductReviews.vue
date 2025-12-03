<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Product Reviews Management</h1>
        </div>

        <!-- Filter Cards -->
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
                            @update:model-value="loadReviews"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="ratingFilter" :items="ratingOptions" label="Filter by Rating"
                            prepend-inner-icon="mdi-star" variant="outlined" density="compact" clearable
                            @update:model-value="loadReviews"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="onSearchChange"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Reviews Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="reviews.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                            pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody v-if="!loading && reviews.length > 0">
                        <tr v-for="review in reviews" :key="review.id">
                            <td>
                                <div class="text-body-2 font-weight-bold">{{ review.product?.title || 'N/A' }}</div>
                                <div class="text-caption text-grey">#{{ review.product_id }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-center">
                                    <v-avatar size="32" class="mr-2">
                                        <v-icon>mdi-account-circle</v-icon>
                                    </v-avatar>
                                    <div>
                                        <div class="text-body-2">{{ review.reviewer_display_name || 'Anonymous' }}</div>
                                        <v-chip v-if="review.verified_purchase" size="x-small" color="success"
                                            variant="flat">
                                            <v-icon size="x-small" class="mr-1">mdi-check-decagram</v-icon>
                                            Verified
                                        </v-chip>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <v-rating :model-value="review.rating" readonly :length="5" :size="16"
                                    active-color="amber" color="grey-lighten-1" density="compact" half-increments>
                                </v-rating>
                                <div class="text-caption">{{ review.rating }} / 5.0</div>
                            </td>
                            <td style="max-width: 300px;">
                                <div v-if="review.title" class="text-body-2 font-weight-bold">{{ review.title }}</div>
                                <div v-if="review.comment" class="text-caption text-grey text-truncate">
                                    {{ review.comment }}
                                </div>
                                <div v-if="review.helpful_count > 0" class="text-caption mt-1">
                                    <v-icon size="small" color="success">mdi-thumb-up</v-icon>
                                    {{ review.helpful_count }}
                                    <v-icon size="small" color="error" class="ml-2">mdi-thumb-down</v-icon>
                                    {{ review.not_helpful_count }}
                                </div>
                            </td>
                            <td>
                                <v-chip :color="getStatusColor(review.status)" size="small" variant="flat">
                                    {{ getStatusLabel(review.status) }}
                                </v-chip>
                            </td>
                            <td>
                                <div class="text-caption">{{ formatDate(review.created_at) }}</div>
                            </td>
                            <td class="text-center">
                                <v-menu>
                                    <template v-slot:activator="{ props }">
                                        <v-btn v-bind="props" icon="mdi-dots-vertical" size="small" variant="text">
                                        </v-btn>
                                    </template>
                                    <v-list density="compact">
                                        <v-list-item @click="viewReview(review)" prepend-icon="mdi-eye">
                                            <v-list-item-title>View Details</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item v-if="review.status !== 'approved'" @click="approveReview(review)"
                                            prepend-icon="mdi-check-circle" :disabled="processing">
                                            <v-list-item-title>Approve</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item v-if="review.status !== 'rejected'" @click="rejectReview(review)"
                                            prepend-icon="mdi-close-circle" :disabled="processing">
                                            <v-list-item-title>Reject</v-list-item-title>
                                        </v-list-item>
                                        <v-divider></v-divider>
                                        <v-list-item @click="confirmDelete(review)" prepend-icon="mdi-delete"
                                            :disabled="processing">
                                            <v-list-item-title class="text-error">Delete</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else-if="!loading && reviews.length === 0">
                        <tr>
                            <td colspan="7" class="text-center pa-8">
                                <v-icon size="64" color="grey-lighten-1">mdi-star-off</v-icon>
                                <p class="text-h6 text-grey mt-4">No reviews found</p>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="7" class="text-center pa-8">
                                <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                <p class="text-grey mt-4">Loading reviews...</p>
                            </td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                    :total-visible="7" class="mt-4" @update:model-value="loadReviews"></v-pagination>
            </v-card-text>
        </v-card>

        <!-- Review Details Dialog -->
        <v-dialog v-model="showDetailsDialog" max-width="700" scrollable>
            <v-card v-if="selectedReview">
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">Review Details</span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="showDetailsDialog = false"></v-btn>
                </v-card-title>

                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Product</div>
                            <div class="text-body-1">{{ selectedReview.product?.title || 'N/A' }}</div>
                        </v-col>

                        <v-col cols="12" md="6">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Reviewer</div>
                            <div class="text-body-1">{{ selectedReview.reviewer_display_name || 'Anonymous' }}</div>
                            <v-chip v-if="selectedReview.verified_purchase" size="small" color="success" class="mt-1">
                                <v-icon size="small" class="mr-1">mdi-check-decagram</v-icon>
                                Verified Purchase
                            </v-chip>
                        </v-col>

                        <v-col cols="12" md="6">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Rating</div>
                            <v-rating :model-value="selectedReview.rating" readonly :length="5" :size="24"
                                active-color="amber" color="grey-lighten-1" half-increments>
                            </v-rating>
                            <div class="text-body-2 mt-1">{{ selectedReview.rating }} / 5.0</div>
                        </v-col>

                        <v-col cols="12" v-if="selectedReview.title">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Title</div>
                            <div class="text-body-1">{{ selectedReview.title }}</div>
                        </v-col>

                        <v-col cols="12" v-if="selectedReview.comment">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Comment</div>
                            <div class="text-body-1">{{ selectedReview.comment }}</div>
                        </v-col>

                        <v-col cols="12" md="4">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Status</div>
                            <v-chip :color="getStatusColor(selectedReview.status)" variant="flat">
                                {{ getStatusLabel(selectedReview.status) }}
                            </v-chip>
                        </v-col>

                        <v-col cols="12" md="4">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Helpful</div>
                            <div class="text-body-1">
                                <v-icon color="success">mdi-thumb-up</v-icon> {{ selectedReview.helpful_count }}
                                <v-icon color="error" class="ml-2">mdi-thumb-down</v-icon> {{
                                    selectedReview.not_helpful_count
                                }}
                            </div>
                        </v-col>

                        <v-col cols="12" md="4">
                            <div class="text-subtitle-1 font-weight-bold mb-2">Submitted</div>
                            <div class="text-body-1">{{ formatDate(selectedReview.created_at) }}</div>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4">
                    <v-spacer></v-spacer>
                    <v-btn v-if="selectedReview.status !== 'approved'" color="success" variant="flat"
                        @click="approveReview(selectedReview)" :loading="processing">
                        <v-icon class="mr-2">mdi-check-circle</v-icon>
                        Approve
                    </v-btn>
                    <v-btn v-if="selectedReview.status !== 'rejected'" color="error" variant="flat"
                        @click="rejectReview(selectedReview)" :loading="processing">
                        <v-icon class="mr-2">mdi-close-circle</v-icon>
                        Reject
                    </v-btn>
                    <v-btn variant="text" @click="showDetailsDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="showDeleteDialog" max-width="400">
            <v-card>
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete this review? This action cannot be undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="showDeleteDialog = false">Cancel</v-btn>
                    <v-btn color="error" variant="flat" @click="deleteReview" :loading="processing">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from 'moment';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';

export default {
    name: 'AdminProductReviews',
    mixins: [adminPaginationMixin],
    data() {
        return {
            reviews: [],
            selectedReview: null,
            reviewToDelete: null,
            showDetailsDialog: false,
            showDeleteDialog: false,
            processing: false,
            statusFilter: null,
            ratingFilter: null,
            statusOptions: [
                { title: 'Pending', value: 'pending' },
                { title: 'Approved', value: 'approved' },
                { title: 'Rejected', value: 'rejected' }
            ],
            ratingOptions: [
                { title: '5 Stars', value: 5 },
                { title: '4 Stars & Above', value: 4 },
                { title: '3 Stars & Above', value: 3 },
                { title: '2 Stars & Above', value: 2 },
                { title: '1 Star & Above', value: 1 }
            ]
        };
    },
    async mounted() {
        await this.loadReviews();
    },
    methods: {
        async loadReviews() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }
                if (this.statusFilter) {
                    params.status = this.statusFilter;
                }
                if (this.ratingFilter) {
                    params.rating = this.ratingFilter;
                }

                // Get all reviews from all products
                const response = await this.$axios.get('/api/v1/products/reviews/all', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.reviews = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load reviews');
            } finally {
                this.loading = false;
            }
        },
        onSearchChange(value) {
            this.search = value;
            this.resetPagination();

            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            this.searchDebounceTimer = setTimeout(() => {
                this.loadReviews();
            }, 350);
        },
        viewReview(review) {
            this.selectedReview = review;
            this.showDetailsDialog = true;
        },
        async approveReview(review) {
            try {
                this.processing = true;
                await this.$axios.put(
                    `/api/v1/products/${review.product_id}/reviews/${review.id}/approve`,
                    {},
                    { headers: this.getAuthHeaders() }
                );

                this.showSuccess('Review approved successfully');
                this.showDetailsDialog = false;
                await this.loadReviews();
            } catch (error) {
                this.handleApiError(error, 'Failed to approve review');
            } finally {
                this.processing = false;
            }
        },
        async rejectReview(review) {
            try {
                this.processing = true;
                await this.$axios.put(
                    `/api/v1/products/${review.product_id}/reviews/${review.id}/reject`,
                    {},
                    { headers: this.getAuthHeaders() }
                );

                this.showSuccess('Review rejected successfully');
                this.showDetailsDialog = false;
                await this.loadReviews();
            } catch (error) {
                this.handleApiError(error, 'Failed to reject review');
            } finally {
                this.processing = false;
            }
        },
        confirmDelete(review) {
            this.reviewToDelete = review;
            this.showDeleteDialog = true;
        },
        async deleteReview() {
            if (!this.reviewToDelete) return;

            try {
                this.processing = true;
                await this.$axios.delete(
                    `/api/v1/products/${this.reviewToDelete.product_id}/reviews/${this.reviewToDelete.id}`,
                    { headers: this.getAuthHeaders() }
                );

                this.showSuccess('Review deleted successfully');
                this.showDeleteDialog = false;
                this.showDetailsDialog = false;
                await this.loadReviews();
            } catch (error) {
                this.handleApiError(error, 'Failed to delete review');
            } finally {
                this.processing = false;
                this.reviewToDelete = null;
            }
        },
        getStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'approved': 'success',
                'rejected': 'error'
            };
            return colors[status] || 'grey';
        },
        getStatusLabel(status) {
            const labels = {
                'pending': 'Pending',
                'approved': 'Approved',
                'rejected': 'Rejected'
            };
            return labels[status] || status;
        },
        formatDate(date) {
            return moment(date).format('MMM DD, YYYY h:mm A');
        }
    }
};
</script>

<style scoped>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 0 0 15px 0;
    border-bottom: 2px solid #f0f0f0;
}

.page-title {
    font-weight: 700;
    color: #1a1a1a;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
