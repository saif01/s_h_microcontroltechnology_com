<template>
    <div class="product-reviews-section">
        <!-- Reviews Summary -->
        <v-card class="mb-6 elevation-2">
            <v-card-title class="text-h5 font-weight-bold d-flex align-center">
                <v-icon color="amber" class="mr-2">mdi-star</v-icon>
                Customer Reviews
            </v-card-title>
            <v-card-text v-if="ratingStats">
                <v-row>
                    <v-col cols="12" md="4" class="text-center">
                        <div class="text-h2 font-weight-bold">{{ ratingStats.average || 0 }}</div>
                        <v-rating
                            :model-value="ratingStats.average || 0"
                            readonly
                            :length="5"
                            :size="24"
                            active-color="amber"
                            color="grey-lighten-1"
                            half-increments
                            density="compact"
                        ></v-rating>
                        <div class="text-body-1 text-grey mt-2">
                            Based on {{ ratingStats.count }} {{ ratingStats.count === 1 ? 'review' : 'reviews' }}
                        </div>
                    </v-col>
                    <v-col cols="12" md="8">
                        <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="d-flex align-center mb-2">
                            <div class="star-label">{{ star }} <v-icon color="amber" size="small">mdi-star</v-icon></div>
                            <v-progress-linear
                                :model-value="ratingStats.percentage_distribution[star] || 0"
                                height="8"
                                color="amber"
                                class="mx-3"
                                rounded
                            ></v-progress-linear>
                            <div class="distribution-count">{{ ratingStats.distribution[star] || 0 }}</div>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Write Review Button -->
        <v-btn
            color="primary"
            size="large"
            prepend-icon="mdi-pencil"
            @click="showReviewDialog = true"
            class="mb-6"
        >
            Write a Review
        </v-btn>

        <!-- Filters -->
        <v-card class="mb-4" v-if="reviews.length > 0">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="4">
                        <v-select
                            v-model="filterRating"
                            :items="ratingFilterOptions"
                            label="Filter by Rating"
                            variant="outlined"
                            density="compact"
                            clearable
                            @update:model-value="loadReviews"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                        <v-select
                            v-model="sortBy"
                            :items="sortOptions"
                            label="Sort by"
                            variant="outlined"
                            density="compact"
                            @update:model-value="loadReviews"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                        <v-checkbox
                            v-model="verifiedOnly"
                            label="Verified Purchases Only"
                            density="compact"
                            @update:model-value="loadReviews"
                        ></v-checkbox>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Reviews List -->
        <div v-if="loading" class="text-center py-8">
            <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
            <p class="text-grey mt-4">Loading reviews...</p>
        </div>

        <div v-else-if="reviews.length === 0" class="text-center py-8">
            <v-icon size="64" color="grey-lighten-1">mdi-comment-text-outline</v-icon>
            <p class="text-h6 text-grey mt-4">No reviews yet</p>
            <p class="text-body-2 text-grey">Be the first to review this product!</p>
        </div>

        <div v-else>
            <v-card
                v-for="review in reviews"
                :key="review.id"
                class="mb-4 elevation-1"
            >
                <v-card-text>
                    <div class="d-flex align-start">
                        <v-avatar color="primary" size="48" class="mr-3">
                            <span class="text-h6 text-white">{{ getInitials(review.reviewer_display_name) }}</span>
                        </v-avatar>
                        <div class="flex-grow-1">
                            <div class="d-flex align-center mb-1">
                                <span class="text-h6 font-weight-bold mr-2">{{ review.reviewer_display_name }}</span>
                                <v-chip
                                    v-if="review.verified_purchase"
                                    size="x-small"
                                    color="success"
                                    variant="flat"
                                >
                                    <v-icon size="x-small" class="mr-1">mdi-check-decagram</v-icon>
                                    Verified Purchase
                                </v-chip>
                            </div>
                            <div class="d-flex align-center mb-2">
                                <v-rating
                                    :model-value="review.rating"
                                    readonly
                                    :length="5"
                                    :size="16"
                                    active-color="amber"
                                    color="grey-lighten-1"
                                    half-increments
                                    density="compact"
                                    class="mr-2"
                                ></v-rating>
                                <span class="text-caption text-grey">{{ formatDate(review.created_at) }}</span>
                            </div>
                            <div v-if="review.title" class="text-h6 font-weight-bold mb-2">{{ review.title }}</div>
                            <div class="text-body-1 mb-3">{{ review.comment }}</div>
                            <div class="d-flex align-center">
                                <v-btn
                                    size="small"
                                    variant="text"
                                    @click="markHelpful(review.id, true)"
                                    :disabled="hasVoted(review.id)"
                                >
                                    <v-icon size="small" class="mr-1">mdi-thumb-up</v-icon>
                                    Helpful ({{ review.helpful_count }})
                                </v-btn>
                                <v-btn
                                    size="small"
                                    variant="text"
                                    @click="markHelpful(review.id, false)"
                                    :disabled="hasVoted(review.id)"
                                    class="ml-2"
                                >
                                    <v-icon size="small" class="mr-1">mdi-thumb-down</v-icon>
                                    Not Helpful ({{ review.not_helpful_count }})
                                </v-btn>
                            </div>
                        </div>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Pagination -->
            <v-pagination
                v-if="totalPages > 1"
                v-model="currentPage"
                :length="totalPages"
                :total-visible="7"
                class="my-4"
                @update:model-value="loadReviews"
            ></v-pagination>
        </div>

        <!-- Write Review Dialog -->
        <v-dialog v-model="showReviewDialog" max-width="600" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">Write a Review</span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeReviewDialog"></v-btn>
                </v-card-title>

                <v-card-text class="pa-6">
                    <v-alert v-if="submitSuccess" type="success" variant="tonal" class="mb-4">
                        Thank you for your review! It will be visible after approval by our team.
                    </v-alert>

                    <v-alert v-if="submitError" type="error" variant="tonal" class="mb-4">
                        {{ submitError }}
                    </v-alert>

                    <v-form ref="reviewForm" v-model="formValid">
                        <v-row>
                            <v-col cols="12">
                                <v-label class="text-body-1 font-weight-bold mb-2">Your Rating *</v-label>
                                <v-rating
                                    v-model="reviewForm.rating"
                                    :length="5"
                                    :size="32"
                                    active-color="amber"
                                    color="grey-lighten-1"
                                    hover
                                    class="mb-2"
                                ></v-rating>
                                <div class="text-caption text-grey">{{ getRatingLabel(reviewForm.rating) }}</div>
                            </v-col>

                            <v-col cols="12" sm="6">
                                <v-text-field
                                    v-model="reviewForm.reviewer_name"
                                    label="Your Name *"
                                    variant="outlined"
                                    :rules="[v => !!v || 'Name is required']"
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6">
                                <v-text-field
                                    v-model="reviewForm.reviewer_email"
                                    label="Your Email *"
                                    variant="outlined"
                                    type="email"
                                    :rules="[
                                        v => !!v || 'Email is required',
                                        v => /.+@.+\..+/.test(v) || 'Email must be valid'
                                    ]"
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12">
                                <v-text-field
                                    v-model="reviewForm.title"
                                    label="Review Title"
                                    variant="outlined"
                                    placeholder="Summarize your experience"
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12">
                                <v-textarea
                                    v-model="reviewForm.comment"
                                    label="Your Review"
                                    variant="outlined"
                                    rows="4"
                                    placeholder="Tell us about your experience with this product..."
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="closeReviewDialog" :disabled="submitting">Cancel</v-btn>
                    <v-btn
                        color="primary"
                        variant="flat"
                        @click="submitReview"
                        :loading="submitting"
                        :disabled="!formValid || !reviewForm.rating"
                    >
                        Submit Review
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    name: 'ProductReviewSection',
    props: {
        productId: {
            type: [Number, String],
            required: true
        }
    },
    data() {
        return {
            reviews: [],
            ratingStats: null,
            loading: false,
            currentPage: 1,
            totalPages: 1,
            perPage: 10,
            filterRating: null,
            sortBy: 'created_at',
            verifiedOnly: false,
            showReviewDialog: false,
            submitting: false,
            submitSuccess: false,
            submitError: null,
            formValid: false,
            reviewForm: {
                rating: 0,
                reviewer_name: '',
                reviewer_email: '',
                title: '',
                comment: ''
            },
            ratingFilterOptions: [
                { title: '5 Stars', value: 5 },
                { title: '4 Stars & Up', value: 4 },
                { title: '3 Stars & Up', value: 3 },
                { title: '2 Stars & Up', value: 2 },
                { title: '1 Star & Up', value: 1 }
            ],
            sortOptions: [
                { title: 'Most Recent', value: 'created_at' },
                { title: 'Highest Rating', value: 'rating' },
                { title: 'Most Helpful', value: 'helpful' }
            ],
            votedReviews: []
        };
    },
    mounted() {
        this.loadReviews();
        this.loadRatingStats();
        this.loadVotedReviews();
    },
    methods: {
        async loadReviews() {
            try {
                this.loading = true;
                const params = {
                    page: this.currentPage,
                    per_page: this.perPage,
                    sort_by: this.sortBy,
                    sort_order: this.sortBy === 'rating' ? 'desc' : 'desc'
                };

                if (this.filterRating) {
                    params.rating = this.filterRating;
                }

                if (this.verifiedOnly) {
                    params.verified_only = true;
                }

                const response = await this.$axios.get(`/api/openapi/products/${this.productId}/reviews`, { params });

                this.reviews = response.data.data || [];
                this.totalPages = response.data.last_page || 1;
            } catch (error) {
                console.error('Error loading reviews:', error);
            } finally {
                this.loading = false;
            }
        },
        async loadRatingStats() {
            try {
                const response = await this.$axios.get(`/api/openapi/products/${this.productId}/reviews/stats`);
                this.ratingStats = response.data.stats || null;
            } catch (error) {
                console.error('Error loading rating stats:', error);
            }
        },
        async submitReview() {
            if (!this.formValid || !this.reviewForm.rating) {
                return;
            }

            try {
                this.submitting = true;
                this.submitError = null;

                await this.$axios.post(`/api/openapi/products/${this.productId}/reviews`, this.reviewForm);

                this.submitSuccess = true;
                
                // Reset form after 2 seconds and close dialog
                setTimeout(() => {
                    this.closeReviewDialog();
                    this.loadReviews();
                    this.loadRatingStats();
                }, 2000);
            } catch (error) {
                this.submitError = error.response?.data?.message || 'Failed to submit review. Please try again.';
            } finally {
                this.submitting = false;
            }
        },
        async markHelpful(reviewId, helpful) {
            if (this.hasVoted(reviewId)) {
                return;
            }

            try {
                await this.$axios.post(`/api/openapi/products/${this.productId}/reviews/${reviewId}/helpful`, {
                    helpful
                });

                // Mark as voted
                this.votedReviews.push(reviewId);
                this.saveVotedReviews();

                // Reload reviews to show updated counts
                await this.loadReviews();
            } catch (error) {
                console.error('Error marking review as helpful:', error);
            }
        },
        hasVoted(reviewId) {
            return this.votedReviews.includes(reviewId);
        },
        loadVotedReviews() {
            const stored = localStorage.getItem('voted_reviews');
            if (stored) {
                try {
                    this.votedReviews = JSON.parse(stored);
                } catch (e) {
                    this.votedReviews = [];
                }
            }
        },
        saveVotedReviews() {
            localStorage.setItem('voted_reviews', JSON.stringify(this.votedReviews));
        },
        closeReviewDialog() {
            this.showReviewDialog = false;
            this.submitSuccess = false;
            this.submitError = null;
            this.reviewForm = {
                rating: 0,
                reviewer_name: '',
                reviewer_email: '',
                title: '',
                comment: ''
            };
            if (this.$refs.reviewForm) {
                this.$refs.reviewForm.reset();
            }
        },
        getInitials(name) {
            if (!name) return '?';
            return name
                .split(' ')
                .map(word => word[0])
                .join('')
                .toUpperCase()
                .substring(0, 2);
        },
        getRatingLabel(rating) {
            const labels = {
                1: 'Poor',
                2: 'Fair',
                3: 'Good',
                4: 'Very Good',
                5: 'Excellent'
            };
            return rating > 0 ? labels[Math.ceil(rating)] : 'Select a rating';
        },
        formatDate(date) {
            return moment(date).format('MMM DD, YYYY');
        }
    }
};
</script>

<style scoped>
.star-label {
    min-width: 50px;
    text-align: right;
    font-weight: 500;
}

.distribution-count {
    min-width: 40px;
    text-align: left;
    font-weight: 500;
}
</style>

