<template>
    <v-dialog v-model="dialog" max-width="1000" scrollable persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                <span class="text-h5 font-weight-bold">
                    {{ editingCareer ? 'Edit Career' : 'Create New Career' }}
                </span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                    :disabled="saving || loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading career data...</p>
                    </div>
                </div>

                <!-- Form Content -->
                <v-form ref="formRef" v-else>
                    <v-tabs v-model="activeTab" bg-color="grey-lighten-4">
                        <v-tab value="basic">Basic Information</v-tab>
                        <v-tab value="details">Job Details</v-tab>
                    </v-tabs>

                    <v-window v-model="activeTab">
                        <!-- Basic Information Tab -->
                        <v-window-item value="basic">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.title" label="Job Title *" variant="outlined"
                                            :rules="[v => !!v || 'Title is required']" hint="Enter the job title"
                                            @blur="generateSlug" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.slug" label="Slug *" variant="outlined"
                                            :rules="[v => !!v || 'Slug is required', v => !v || /^[a-z0-9-]+$/.test(v) || 'Slug can only contain lowercase letters, numbers, and hyphens']"
                                            hint="URL-friendly version (auto-generated from title)" persistent-hint>
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateSlug" :disabled="!form.title"></v-btn>
                                            </template>
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.order" label="Order" type="number"
                                            variant="outlined" hint="Display order (lower numbers first)"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <v-text-field v-model="form.department" label="Department" variant="outlined"
                                            hint="e.g., Engineering, Sales, Marketing" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <v-text-field v-model="form.location" label="Location" variant="outlined"
                                            hint="e.g., New York, Remote, Hybrid" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <v-select v-model="form.employment_type" :items="employmentTypes" label="Employment Type"
                                            variant="outlined" hint="Type of employment" persistent-hint clearable></v-select>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.deadline" label="Application Deadline" type="date"
                                            variant="outlined" hint="Deadline for applications" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-switch v-model="form.published" label="Published" color="success"
                                            hint="Publish this career on the public website"
                                            persistent-hint></v-switch>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Job Details Tab -->
                        <v-window-item value="details">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Job Description</v-label>
                                        <RichTextEditor v-model="form.description"
                                            :active="dialog && activeTab === 'details'"
                                            placeholder="Enter detailed job description..." />
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Responsibilities</v-label>
                                        <RichTextEditor v-model="form.responsibilities"
                                            :active="dialog && activeTab === 'details'"
                                            placeholder="Enter job responsibilities..." />
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Requirements</v-label>
                                        <RichTextEditor v-model="form.requirements"
                                            :active="dialog && activeTab === 'details'"
                                            placeholder="Enter job requirements..." />
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Benefits</v-label>
                                        <RichTextEditor v-model="form.benefits"
                                            :active="dialog && activeTab === 'details'"
                                            placeholder="Enter job benefits..." />
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>
                    </v-window>
                </v-form>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-4">
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="closeDialog" :disabled="saving">Cancel</v-btn>
                <v-btn color="primary" variant="flat" @click="saveCareer" :loading="saving">
                    {{ editingCareer ? 'Update' : 'Create' }} Career
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import RichTextEditor from '../../common/RichTextEditor.vue';

export default {
    name: 'CareerFormDialog',
    components: {
        RichTextEditor
    },
    mixins: [adminPaginationMixin],
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        editingCareer: {
            type: Object,
            default: null
        }
    },
    emits: ['update:modelValue', 'saved'],
    data() {
        return {
            activeTab: 'basic',
            loading: false,
            saving: false,
            employmentTypes: [
                { title: 'Full-time', value: 'full-time' },
                { title: 'Part-time', value: 'part-time' },
                { title: 'Contract', value: 'contract' },
                { title: 'Internship', value: 'internship' },
                { title: 'Freelance', value: 'freelance' }
            ],
            form: {
                title: '',
                slug: '',
                department: '',
                location: '',
                employment_type: '',
                description: '',
                responsibilities: '',
                requirements: '',
                benefits: '',
                deadline: '',
                published: false,
                order: 0
            }
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
            if (newVal) {
                this.activeTab = 'basic';
                if (this.editingCareer) {
                    this.loadCareerForEdit();
                } else {
                    this.resetForm();
                }
            }
        },
        editingCareer: {
            handler(newVal) {
                if (newVal && this.dialog) {
                    this.loadCareerForEdit();
                } else if (!newVal && this.dialog) {
                    this.resetForm();
                }
            },
            immediate: false
        }
    },
    methods: {
        resetForm() {
            this.form = {
                title: '',
                slug: '',
                department: '',
                location: '',
                employment_type: '',
                description: '',
                responsibilities: '',
                requirements: '',
                benefits: '',
                deadline: '',
                published: false,
                order: 0
            };

            if (this.$refs.formRef) {
                this.$refs.formRef.resetValidation();
            }
        },
        async loadCareerForEdit() {
            if (!this.editingCareer) return;

            this.loading = true;
            try {
                const response = await this.$axios.get(`/api/v1/careers/${this.editingCareer.id}`, {
                    headers: this.getAuthHeaders()
                });
                const data = response.data;

                this.form = {
                    title: data.title || '',
                    slug: data.slug || '',
                    department: data.department || '',
                    location: data.location || '',
                    employment_type: data.employment_type || '',
                    description: data.description || '',
                    responsibilities: data.responsibilities || '',
                    requirements: data.requirements || '',
                    benefits: data.benefits || '',
                    deadline: data.deadline || '',
                    published: data.published || false,
                    order: data.order || 0
                };
            } catch (error) {
                this.handleApiError(error, 'Failed to load career data');
                this.closeDialog();
            } finally {
                this.loading = false;
            }
        },
        closeDialog() {
            if (!this.saving && !this.loading) {
                this.dialog = false;
                this.resetForm();
            }
        },
        generateSlug() {
            if (this.form.title && !this.editingCareer) {
                this.form.slug = this.form.title
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        },
        async saveCareer() {
            // Validate form
            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            this.saving = true;

            try {
                const payload = {
                    title: this.form.title,
                    slug: this.form.slug || null,
                    department: this.form.department || null,
                    location: this.form.location || null,
                    employment_type: this.form.employment_type || null,
                    description: this.form.description || null,
                    responsibilities: this.form.responsibilities || null,
                    requirements: this.form.requirements || null,
                    benefits: this.form.benefits || null,
                    deadline: this.form.deadline || null,
                    published: this.form.published || false,
                    order: this.form.order || 0
                };

                // Save career
                if (this.editingCareer) {
                    await this.$axios.put(`/api/v1/careers/${this.editingCareer.id}`, payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Career updated successfully');
                } else {
                    await this.$axios.post('/api/v1/careers', payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Career created successfully');
                }

                this.$emit('saved');
                this.closeDialog();
            } catch (error) {
                this.handleApiError(error, this.editingCareer ? 'Failed to update career' : 'Failed to create career');
            } finally {
                this.saving = false;
            }
        }
    }
};
</script>

