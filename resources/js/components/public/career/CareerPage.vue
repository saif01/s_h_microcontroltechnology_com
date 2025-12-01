<template>
    <div class="career-page-modern">
        <!-- Hero Section -->
        <section
            class="mb-10 page-hero position-relative d-flex align-center justify-center text-center overflow-hidden">
            <div class="hero-bg-gradient"></div>
            <div class="hero-pattern"></div>

            <!-- Animated gradient orbs -->
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>

            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-5 py-3 rounded-pill mb-8 animate-float">
                            <div class="pulse-dot mr-2"></div>
                            <v-icon icon="mdi-briefcase" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">JOIN OUR TEAM</span>
                        </div>
                        <h1
                            class="text-h4 text-lg-h2 font-weight-black text-white mb-6 lh-tight text-shadow-sm animate-slide-up">
                            Career <span class="text-amber-accent-3">Opportunities</span>
                        </h1>
                        <p
                            class="text-h6 text-white opacity-90 mw-700 mx-auto font-weight-light animate-slide-up-delay">
                            Explore exciting career opportunities and be part of our growing team.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Main Content -->
        <section class="py-12 bg-grey-lighten-5 min-vh-100">
            <v-container>
                <!-- Search and Filter Bar -->
                <v-card class="mb-6" elevation="2">
                    <v-card-text class="pa-4">
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-text-field v-model="searchQuery" label="Search careers"
                                    prepend-inner-icon="mdi-magnify" variant="outlined" density="compact" clearable
                                    @update:model-value="handleSearch"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select v-model="selectedDepartment" :items="departmentOptions" label="Department"
                                    prepend-inner-icon="mdi-office-building" variant="outlined" density="compact"
                                    clearable @update:model-value="loadCareers"></v-select>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select v-model="selectedLocation" :items="locationOptions" label="Location"
                                    prepend-inner-icon="mdi-map-marker" variant="outlined" density="compact" clearable
                                    @update:model-value="loadCareers"></v-select>
                            </v-col>
                            <v-col cols="12" md="2">
                                <v-select v-model="selectedEmploymentType" :items="employmentTypeOptions" label="Type"
                                    prepend-inner-icon="mdi-briefcase-clock" variant="outlined" density="compact"
                                    clearable @update:model-value="loadCareers"></v-select>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-16">
                    <v-progress-circular indeterminate color="primary" size="64" />
                    <p class="text-body-1 text-medium-emphasis mt-4">Loading career opportunities...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="careers.length === 0" class="text-center py-16">
                    <v-icon icon="mdi-briefcase-off" size="64" color="grey-lighten-1" class="mb-4" />
                    <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No career opportunities found</h3>
                    <p class="text-body-2 text-medium-emphasis mb-6">
                        Try adjusting your search or filters.
                    </p>
                    <v-btn color="primary" @click="clearFilters">Clear Filters</v-btn>
                </div>

                <!-- Careers Grid -->
                <v-row v-else>
                    <v-col v-for="career in careers" :key="career.id" cols="12" md="6" lg="4">
                        <v-hover v-slot="{ isHovering, props }">
                            <v-card v-bind="props" :elevation="isHovering ? 8 : 2"
                                class="career-card h-100 rounded-xl transition-all overflow-hidden"
                                :to="`/careers/${career.slug}`">
                                <v-card-title class="bg-primary text-white pa-4">
                                    <div class="d-flex align-center justify-space-between w-100">
                                        <h3 class="text-h6 font-weight-bold text-white">{{ career.title }}</h3>
                                        <v-chip size="small" color="white" variant="flat" v-if="career.department">
                                            {{ career.department }}
                                        </v-chip>
                                    </div>
                                </v-card-title>
                                <v-card-text class="pa-4">
                                    <div class="mb-3">
                                        <v-icon icon="mdi-map-marker" size="small" class="mr-1 text-primary"></v-icon>
                                        <span class="text-body-2">{{ career.location || 'Not specified' }}</span>
                                    </div>
                                    <div class="mb-3" v-if="career.employment_type">
                                        <v-icon icon="mdi-briefcase-clock" size="small"
                                            class="mr-1 text-primary"></v-icon>
                                        <span class="text-body-2 text-capitalize">{{ career.employment_type }}</span>
                                    </div>
                                    <div class="mb-3" v-if="career.deadline">
                                        <v-icon icon="mdi-calendar-clock" size="small"
                                            class="mr-1 text-primary"></v-icon>
                                        <span class="text-body-2">Deadline: {{ formatDate(career.deadline) }}</span>
                                    </div>
                                    <p class="text-body-2 text-medium-emphasis mt-3 line-clamp-3"
                                        v-html="truncateDescription(career.description)"></p>
                                </v-card-text>
                                <v-card-actions class="pa-4 bg-grey-lighten-4">
                                    <v-spacer></v-spacer>
                                    <v-btn color="primary" variant="flat" size="small">
                                        View Details
                                        <v-icon icon="mdi-arrow-right" size="small" class="ml-1"></v-icon>
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-hover>
                    </v-col>
                </v-row>

                <!-- Pagination -->
                <div v-if="pagination && pagination.last_page > 1" class="d-flex justify-center mt-8">
                    <v-pagination v-model="currentPage" :length="pagination.last_page" :total-visible="7"
                        @update:model-value="loadCareers"></v-pagination>
                </div>
            </v-container>
        </section>
    </div>
</template>

<script>

export default {
    name: 'CareerPage',
    data() {
        return {
            careers: [],
            loading: false,
            searchQuery: '',
            selectedDepartment: null,
            selectedLocation: null,
            selectedEmploymentType: null,
            departmentOptions: [],
            locationOptions: [],
            employmentTypeOptions: [],
            currentPage: 1,
            pagination: null,
            searchTimeout: null
        };
    },
    mounted() {
        this.loadCareers();
    },
    methods: {
        async loadCareers() {
            try {
                this.loading = true;
                this.careers = [];
                const params = {
                    page: this.currentPage,
                    per_page: 12,
                    active_only: true
                };

                if (this.searchQuery) {
                    params.search = this.searchQuery;
                }

                if (this.selectedDepartment) {
                    params.department = this.selectedDepartment;
                }

                if (this.selectedLocation) {
                    params.location = this.selectedLocation;
                }

                if (this.selectedEmploymentType) {
                    params.employment_type = this.selectedEmploymentType;
                }

                const response = await this.$axios.get('/api/openapi/careers', { params });

                // Accept both { data: [...], meta... } and plain arrays
                const raw = response.data;
                const items = Array.isArray(raw?.data) ? raw.data : Array.isArray(raw) ? raw : [];
                this.careers = items;

                // Prefer meta if available, otherwise fall back to top-level pagination fields or defaults
                const meta = raw?.meta || {};
                this.pagination = {
                    current_page: meta.current_page || raw?.current_page || 1,
                    last_page: meta.last_page || raw?.last_page || 1,
                    per_page: meta.per_page || raw?.per_page || 12,
                    total: meta.total || raw?.total || items.length
                };

                // Update filter options if provided
                const filters = raw?.filters;
                if (filters) {
                    this.departmentOptions = (filters.departments || []).map(d => ({ title: d, value: d }));
                    this.locationOptions = (filters.locations || []).map(l => ({ title: l, value: l }));
                    this.employmentTypeOptions = (filters.employment_types || []).map(t => ({
                        title: this.formatEmploymentType(t),
                        value: t
                    }));
                }
            } catch (error) {
                console.error('Error loading careers:', error);
                console.error('Error response:', error.response);
                this.careers = [];
                this.pagination = {
                    current_page: 1,
                    last_page: 1,
                    per_page: 12,
                    total: 0
                };

                // Show user-friendly error message
                if (error.response) {
                    console.error('API Error:', error.response.status, error.response.data);
                }
            } finally {
                this.loading = false;
            }
        },
        handleSearch() {
            // Debounce search
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadCareers();
            }, 500);
        },
        clearFilters() {
            this.searchQuery = '';
            this.selectedDepartment = null;
            this.selectedLocation = null;
            this.selectedEmploymentType = null;
            this.currentPage = 1;
            this.loadCareers();
        },
        truncateDescription(description) {
            if (!description) return 'No description available.';
            const text = description.replace(/<[^>]*>/g, ''); // Strip HTML
            return text.length > 150 ? text.substring(0, 150) + '...' : text;
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        formatEmploymentType(type) {
            return type ? type.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join('-') : '';
        }
    }
};
</script>

<style scoped>
.career-card {
    transition: all 0.3s ease;
}

.career-card:hover {
    transform: translateY(-4px);
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive Styles */
@media (max-width: 960px) {
    .page-hero {
        min-height: 400px !important;
        padding: 48px 16px !important;
    }

    .page-hero h1 {
        font-size: 2rem !important;
        line-height: 1.2 !important;
        margin-bottom: 16px !important;
    }

    .page-hero p {
        font-size: 1.1rem !important;
        line-height: 1.5 !important;
    }

    .glass-pill {
        padding: 10px 20px !important;
        margin-bottom: 24px !important;
    }

    .glass-pill span {
        font-size: 0.75rem !important;
    }

    .gradient-orb {
        width: 200px !important;
        height: 200px !important;
    }
}

@media (max-width: 600px) {
    .page-hero {
        min-height: 350px !important;
        padding: 32px 12px !important;
    }

    .page-hero h1 {
        font-size: 1.75rem !important;
        line-height: 1.3 !important;
        margin-bottom: 12px !important;
    }

    .page-hero p {
        font-size: 1rem !important;
        line-height: 1.4 !important;
        padding: 0 8px !important;
    }

    .glass-pill {
        padding: 8px 16px !important;
        margin-bottom: 20px !important;
    }

    .glass-pill .v-icon {
        font-size: 16px !important;
    }

    .glass-pill span {
        font-size: 0.7rem !important;
    }

    .gradient-orb {
        width: 150px !important;
        height: 150px !important;
    }

    .orb-1 {
        top: -75px !important;
        left: -75px !important;
    }

    .orb-2 {
        bottom: -75px !important;
        right: -75px !important;
    }

    .orb-3 {
        top: 50% !important;
        right: -75px !important;
    }
}
</style>
