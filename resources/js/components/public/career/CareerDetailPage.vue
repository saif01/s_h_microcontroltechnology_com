<template>
    <div class="career-detail-page">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-16">
            <v-progress-circular indeterminate color="primary" size="64" />
            <p class="text-body-1 text-medium-emphasis mt-4">Loading career details...</p>
        </div>

        <!-- Career Details -->
        <div v-else-if="career">
            <!-- Hero Section -->
            <section
                class="mb-10 page-hero position-relative d-flex align-center justify-center text-center overflow-hidden">
                <div class="hero-bg-gradient"></div>
                <div class="hero-pattern"></div>
                <v-container class="position-relative z-index-2">
                    <v-fade-transition appear>
                        <div>
                            <h1 class="text-h4 text-lg-h2 font-weight-black text-white mb-4 text-shadow-sm">
                                {{ career.title }}
                            </h1>
                            <div class="d-flex flex-wrap justify-center gap-4">
                                <v-chip v-if="career.department" color="white" variant="flat" size="large">
                                    <v-icon icon="mdi-office-building" class="mr-2"></v-icon>
                                    {{ career.department }}
                                </v-chip>
                                <v-chip v-if="career.location" color="white" variant="flat" size="large">
                                    <v-icon icon="mdi-map-marker" class="mr-2"></v-icon>
                                    {{ career.location }}
                                </v-chip>
                                <v-chip v-if="career.employment_type" color="white" variant="flat" size="large">
                                    <v-icon icon="mdi-briefcase-clock" class="mr-2"></v-icon>
                                    <span class="text-capitalize">{{ career.employment_type }}</span>
                                </v-chip>
                            </div>
                        </div>
                    </v-fade-transition>
                </v-container>
            </section>

            <!-- Main Content -->
            <section class="py-12">
                <v-container>
                    <v-row>
                        <!-- Career Details -->
                        <v-col cols="12" md="8">
                            <v-card class="mb-6" elevation="2">
                                <v-card-title class="bg-primary text-white">Job Description</v-card-title>
                                <v-card-text class="pa-6">
                                    <div v-html="career.description || 'No description available.'"></div>
                                </v-card-text>
                            </v-card>

                            <v-card class="mb-6" elevation="2" v-if="career.responsibilities">
                                <v-card-title class="bg-primary text-white">Responsibilities</v-card-title>
                                <v-card-text class="pa-6">
                                    <div v-html="career.responsibilities"></div>
                                </v-card-text>
                            </v-card>

                            <v-card class="mb-6" elevation="2" v-if="career.requirements">
                                <v-card-title class="bg-primary text-white">Requirements</v-card-title>
                                <v-card-text class="pa-6">
                                    <div v-html="career.requirements"></div>
                                </v-card-text>
                            </v-card>

                            <v-card class="mb-6" elevation="2" v-if="career.benefits">
                                <v-card-title class="bg-primary text-white">Benefits</v-card-title>
                                <v-card-text class="pa-6">
                                    <div v-html="career.benefits"></div>
                                </v-card-text>
                            </v-card>
                        </v-col>

                        <!-- Application Form Sidebar -->
                        <v-col cols="12" md="4">
                            <v-card class="sticky-top" elevation="2"
                                :class="{ 'application-closed': !career.is_active }">
                                <v-card-title class="bg-primary text-white">
                                    <v-icon icon="mdi-file-document-edit" class="mr-2"></v-icon>
                                    Apply for this Position
                                </v-card-title>
                                <v-card-text class="pa-6">
                                    <v-alert v-if="!career.is_active" type="warning" variant="tonal" class="mb-4">
                                        This position is no longer accepting applications.
                                    </v-alert>

                                    <v-alert v-else-if="career.deadline" type="info" variant="tonal" class="mb-4">
                                        <div class="text-caption">
                                            <strong>Application Deadline:</strong><br>
                                            {{ formatDate(career.deadline) }}
                                        </div>
                                    </v-alert>

                                    <v-form ref="applicationForm" v-if="career.is_active">
                                        <v-text-field v-model="applicationForm.name" label="Full Name *"
                                            variant="outlined" :rules="[v => !!v || 'Name is required']"
                                            density="compact" prepend-inner-icon="mdi-account"></v-text-field>

                                        <v-text-field v-model="applicationForm.email" label="Email *" type="email"
                                            variant="outlined"
                                            :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Email must be valid']"
                                            density="compact" prepend-inner-icon="mdi-email"></v-text-field>

                                        <v-text-field v-model="applicationForm.phone" label="Phone" variant="outlined"
                                            density="compact" prepend-inner-icon="mdi-phone"
                                            placeholder="01707080401 or +8801707080401"
                                            :rules="[v => !v || this.validateBangladeshPhone(v) || 'Please enter a valid Bangladesh phone number (e.g., 01707080401 or +8801707080401)']"
                                            hint="Bangladesh phone number format" persistent-hint></v-text-field>

                                        <v-textarea v-model="applicationForm.cover_letter" label="Cover Letter"
                                            variant="outlined" rows="4" density="compact" prepend-inner-icon="mdi-text"
                                            hint="Tell us why you're interested in this position"
                                            persistent-hint></v-textarea>

                                        <v-file-input v-model="applicationForm.resume" label="Resume (PDF, DOC, DOCX)"
                                            variant="outlined" density="compact" prepend-icon=""
                                            prepend-inner-icon="mdi-file-document" accept=".pdf,.doc,.docx"
                                            :rules="[v => !v || v.size < 5242880 || 'File size must be less than 5MB']"
                                            hint="Max file size: 5MB" persistent-hint show-size></v-file-input>

                                        <v-btn color="primary" variant="flat" block size="large"
                                            @click="submitApplication" :loading="submitting"
                                            :disabled="!career.is_active">
                                            <v-icon icon="mdi-send" class="mr-2"></v-icon>
                                            Submit Application
                                        </v-btn>
                                    </v-form>
                                </v-card-text>
                            </v-card>

                            <!-- Back Button -->
                            <v-btn color="grey" variant="outlined" block class="mt-4" :to="{ name: 'Careers' }">
                                <v-icon icon="mdi-arrow-left" class="mr-2"></v-icon>
                                Back to Careers
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </section>
        </div>

        <!-- Error State -->
        <div v-else class="text-center py-16">
            <v-icon icon="mdi-alert-circle" size="64" color="error" class="mb-4" />
            <h3 class="text-h6 font-weight-bold mb-2">Career not found</h3>
            <p class="text-body-2 text-medium-emphasis mb-6">
                The career opportunity you're looking for doesn't exist or has been removed.
            </p>
            <v-btn color="primary" :to="{ name: 'Careers' }">View All Careers</v-btn>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'CareerDetailPage',
    data() {
        return {
            career: null,
            loading: true,
            submitting: false,
            applicationForm: {
                name: '',
                email: '',
                phone: '',
                cover_letter: '',
                resume: null
            }
        };
    },
    mounted() {
        this.loadCareer();
    },
    watch: {
        '$route'() {
            this.loadCareer();
        }
    },
    methods: {
        async loadCareer() {
            try {
                this.loading = true;
                const slug = this.$route.params.slug;
                const response = await axios.get(`/api/openapi/careers/${slug}`);

                if (response.data) {
                    this.career = response.data;
                } else {
                    console.warn('No career data received');
                    this.career = null;
                }
            } catch (error) {
                console.error('Error loading career:', error);
                console.error('Error response:', error.response);
                this.career = null;
            } finally {
                this.loading = false;
            }
        },
        async submitApplication() {
            // Validate form
            const { valid } = await this.$refs.applicationForm.validate();
            if (!valid) {
                return;
            }

            if (!this.career || !this.career.is_active) {
                alert('This position is no longer accepting applications.');
                return;
            }

            this.submitting = true;

            try {
                const formData = new FormData();
                formData.append('career_id', this.career.id);
                formData.append('name', this.applicationForm.name);
                formData.append('email', this.applicationForm.email);
                formData.append('phone', this.applicationForm.phone || '');
                formData.append('cover_letter', this.applicationForm.cover_letter || '');

                if (this.applicationForm.resume) {
                    formData.append('resume', this.applicationForm.resume);
                }

                const response = await axios.post('/api/openapi/careers/apply', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    // Show success message
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'success',
                            title: 'Application Submitted!',
                            text: response.data.message || 'Your application has been submitted successfully.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reset form
                            this.applicationForm = {
                                name: '',
                                email: '',
                                phone: '',
                                cover_letter: '',
                                resume: null
                            };
                            this.$refs.applicationForm.resetValidation();
                        });
                    } else {
                        alert(response.data.message || 'Application submitted successfully!');
                        // Reset form
                        this.applicationForm = {
                            name: '',
                            email: '',
                            phone: '',
                            cover_letter: '',
                            resume: null
                        };
                        this.$refs.applicationForm.resetValidation();
                    }
                }
            } catch (error) {
                console.error('Error submitting application:', error);
                let errorMessage = 'Failed to submit application. Please try again.';

                if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    errorMessage = Object.values(errors).flat().join(', ');
                }

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Application Failed',
                        text: errorMessage
                    });
                } else {
                    alert(errorMessage);
                }
            } finally {
                this.submitting = false;
            }
        },
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        validateBangladeshPhone(phone) {
            if (!phone) return true; // Phone is optional, so return true if empty

            // Remove spaces, dashes, and other formatting characters
            const cleaned = phone.replace(/[\s\-\(\)]/g, '');

            // Check for local format: 01707080401 (11 digits starting with 0)
            // Pattern: 0 followed by 1, then 9 more digits where the third digit is 3-9 (total 11 digits)
            const localFormat = /^01[3-9]\d{8}$/;

            // Check for international format: +8801707080401 (14 characters including +)
            // Pattern: +880 followed by 1, then 9 more digits where the 5th digit is 3-9 (total 14 characters)
            const internationalFormat = /^\+8801[3-9]\d{8}$/;

            return localFormat.test(cleaned) || internationalFormat.test(cleaned);
        }
    }
};
</script>

<style scoped>
.sticky-top {
    position: sticky;
    top: 80px;
}

.application-closed {
    opacity: 0.7;
}

.career-detail-page {
    min-height: 100vh;
}
</style>
