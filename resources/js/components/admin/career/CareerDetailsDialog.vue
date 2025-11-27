<template>
    <v-dialog v-model="dialog" max-width="1200" scrollable>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-info text-white pa-4">
                <div class="d-flex align-center">
                    <v-icon icon="mdi-eye" class="mr-3"></v-icon>
                    <span class="text-h5 font-weight-bold">Career Details</span>
                </div>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog" :disabled="loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="info" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading career details...</p>
                    </div>
                </div>

                <!-- Details Content -->
                <div v-else-if="career">
                    <v-tabs v-model="tab" color="info" bg-color="grey-lighten-4">
                        <v-tab value="basic">
                            <v-icon icon="mdi-information" class="mr-2"></v-icon>
                            Basic Info
                        </v-tab>
                        <v-tab value="details">
                            <v-icon icon="mdi-text" class="mr-2"></v-icon>
                            Job Details
                        </v-tab>
                    </v-tabs>

                    <v-divider></v-divider>

                    <v-window v-model="tab" class="pa-6">
                        <!-- Basic Info Tab -->
                        <v-window-item value="basic">
                            <v-row>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Job Title</div>
                                    <div class="text-h6 font-weight-bold mb-4">{{ career.title || '-' }}</div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Slug</div>
                                    <div class="text-body-1 mb-4">
                                        <v-chip size="small" variant="outlined">{{ career.slug || '-' }}</v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Order</div>
                                    <div class="text-body-1 mb-4">{{ career.order || 0 }}</div>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Department</div>
                                    <div class="text-body-1 mb-4">
                                        <v-chip size="small" variant="outlined" v-if="career.department">
                                            {{ career.department }}
                                        </v-chip>
                                        <span v-else class="text-grey">-</span>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Location</div>
                                    <div class="text-body-1 mb-4">
                                        <v-chip size="small" variant="outlined" v-if="career.location">
                                            {{ career.location }}
                                        </v-chip>
                                        <span v-else class="text-grey">-</span>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Employment Type</div>
                                    <div class="text-body-1 mb-4">
                                        <v-chip size="small" variant="outlined" v-if="career.employment_type">
                                            {{ career.employment_type }}
                                        </v-chip>
                                        <span v-else class="text-grey">-</span>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Application Deadline</div>
                                    <div class="text-body-1 mb-4">
                                        {{ career.deadline ? formatDate(career.deadline) : '-' }}
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Status</div>
                                    <div class="mb-4">
                                        <v-chip :color="career.published ? 'success' : 'error'" size="small">
                                            {{ career.published ? 'Published' : 'Draft' }}
                                        </v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Applications Count</div>
                                    <div class="mb-4">
                                        <v-chip size="small" color="primary" variant="flat">
                                            {{ career.applications_count || 0 }} Applications
                                        </v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Created At</div>
                                    <div class="text-body-1 mb-4">{{ formatDateTime(career.created_at) }}</div>
                                </v-col>
                                <v-col cols="12" v-if="career.updated_at">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Updated At</div>
                                    <div class="text-body-1 mb-4">{{ formatDateTime(career.updated_at) }}</div>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Job Details Tab -->
                        <v-window-item value="details">
                            <v-row>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Job Description</div>
                                    <div class="text-body-1 mb-4" v-html="career.description || '-'"></div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Responsibilities</div>
                                    <div class="text-body-1 mb-4" v-html="career.responsibilities || '-'"></div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Requirements</div>
                                    <div class="text-body-1 mb-4" v-html="career.requirements || '-'"></div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Benefits</div>
                                    <div class="text-body-1 mb-4" v-html="career.benefits || '-'"></div>
                                </v-col>
                            </v-row>
                        </v-window-item>
                    </v-window>
                </div>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-5">
                <v-btn color="primary" prepend-icon="mdi-briefcase-account" @click="viewApplications" v-if="career && career.applications_count > 0">
                    View Applications ({{ career.applications_count }})
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" prepend-icon="mdi-pencil" @click="handleEdit">
                    Edit Career
                </v-btn>
                <v-btn color="grey" variant="text" @click="closeDialog">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'CareerDetailsDialog',
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        careerId: {
            type: [Number, String],
            default: null
        }
    },
    emits: ['update:modelValue', 'edit'],
    data() {
        return {
            loading: false,
            career: null,
            tab: 'basic'
        };
    },
    computed: {
        dialog: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    },
    watch: {
        modelValue(newVal) {
            if (newVal && this.careerId) {
                this.loadCareer();
            }
        },
        careerId() {
            if (this.dialog && this.careerId) {
                this.loadCareer();
            }
        }
    },
    methods: {
        async loadCareer() {
            if (!this.careerId) return;

            this.loading = true;
            this.career = null;
            this.tab = 'basic';

            try {
                const response = await this.$axios.get(`/api/v1/careers/${this.careerId}`, {
                    headers: this.getAuthHeaders()
                });

                const data = response.data;
                if (!data) {
                    throw new Error('No career data received');
                }

                this.career = data;
            } catch (error) {
                console.error('Failed to load career details:', error);
                alert('Failed to load career details. Please try again.');
                this.closeDialog();
            } finally {
                this.loading = false;
            }
        },
        closeDialog() {
            this.dialog = false;
            this.career = null;
            this.tab = 'basic';
        },
        handleEdit() {
            if (this.career) {
                this.$emit('edit', this.career);
                this.closeDialog();
            }
        },
        viewApplications() {
            if (this.career) {
                this.closeDialog();
                this.$router.push({ 
                    name: 'AdminJobApplications', 
                    query: { career_id: this.career.id } 
                });
            }
        },
        getAuthHeaders() {
            const token = localStorage.getItem('admin_token');
            return {
                Authorization: `Bearer ${token}`
            };
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString();
        },
        formatDateTime(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString();
        }
    }
};
</script>

