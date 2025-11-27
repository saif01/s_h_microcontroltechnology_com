<template>
    <v-dialog v-model="dialog" max-width="1200" scrollable>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-info text-white pa-4">
                <div class="d-flex align-center">
                    <v-icon icon="mdi-eye" class="mr-3"></v-icon>
                    <span class="text-h5 font-weight-bold">Service Details</span>
                </div>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog" :disabled="loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="info" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading service details...</p>
                    </div>
                </div>

                <!-- Details Content -->
                <div v-else-if="service">
                    <v-tabs v-model="tab" color="info" bg-color="grey-lighten-4">
                        <v-tab value="basic">
                            <v-icon icon="mdi-information" class="mr-2"></v-icon>
                            Basic Info
                        </v-tab>
                        <v-tab value="content">
                            <v-icon icon="mdi-text" class="mr-2"></v-icon>
                            Content
                        </v-tab>
                        <v-tab value="seo">
                            <v-icon icon="mdi-search-web" class="mr-2"></v-icon>
                            SEO & Meta
                        </v-tab>
                    </v-tabs>

                    <v-divider></v-divider>

                    <v-window v-model="tab" class="pa-6">
                        <!-- Basic Info Tab -->
                        <v-window-item value="basic">
                            <v-row>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Service Title</div>
                                    <div class="text-h6 font-weight-bold mb-4">{{ service.title || '-' }}</div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Slug</div>
                                    <div class="text-body-1 mb-4">
                                        <v-chip size="small" variant="outlined">{{ service.slug || '-' }}</v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Order</div>
                                    <div class="text-body-1 mb-4">{{ service.order || 0 }}</div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Short Description</div>
                                    <div class="text-body-1 mb-4">{{ service.short_description || '-' }}</div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Icon</div>
                                    <div class="text-body-1 mb-4">
                                        <v-icon v-if="service.icon" :icon="service.icon" size="large"></v-icon>
                                        <span v-else class="text-grey">-</span>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Price Range</div>
                                    <div class="text-body-1 mb-4">{{ service.price_range || '-' }}</div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Status</div>
                                    <div class="mb-4">
                                        <v-chip :color="service.published ? 'success' : 'error'" size="small">
                                            {{ service.published ? 'Published' : 'Draft' }}
                                        </v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" v-if="service.features && service.features.length > 0">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Features</div>
                                    <div class="mb-4">
                                        <v-chip v-for="(feature, i) in service.features" :key="i" class="ma-1"
                                            size="small" color="primary" variant="flat">
                                            {{ feature }}
                                        </v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" v-if="service.benefits && service.benefits.length > 0">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Benefits</div>
                                    <div class="mb-4">
                                        <v-chip v-for="(benefit, i) in service.benefits" :key="i" class="ma-1"
                                            size="small" color="success" variant="flat">
                                            {{ benefit }}
                                        </v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" v-if="service.created_at">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Created At</div>
                                    <div class="text-body-1 mb-4">{{ formatDateTime(service.created_at) }}</div>
                                </v-col>
                                <v-col cols="12" v-if="service.updated_at">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Updated At</div>
                                    <div class="text-body-1 mb-4">{{ formatDateTime(service.updated_at) }}</div>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Content Tab -->
                        <v-window-item value="content">
                            <v-row>
                                <v-col cols="12" v-if="service.image">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Service Image</div>
                                    <v-img :src="service.image" max-width="600" max-height="400"
                                        class="rounded elevation-2 mb-4" cover></v-img>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Full Description</div>
                                    <div class="text-body-1" v-html="service.description || '-'"></div>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- SEO Tab -->
                        <v-window-item value="seo">
                            <v-row>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Title</div>
                                    <div class="text-body-1 mb-4">{{ service.meta_title || '-' }}</div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Description</div>
                                    <div class="text-body-1 mb-4">{{ service.meta_description || '-' }}</div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Keywords</div>
                                    <div class="text-body-1 mb-4">{{ service.meta_keywords || '-' }}</div>
                                </v-col>
                                <v-col cols="12" v-if="service.og_image">
                                    <div class="text-subtitle-1 text-medium-emphasis mb-2">Open Graph Image</div>
                                    <v-img :src="service.og_image" max-width="600" max-height="400"
                                        class="rounded elevation-2 mb-4" cover></v-img>
                                </v-col>
                            </v-row>
                        </v-window-item>
                    </v-window>
                </div>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-5">
                <v-spacer></v-spacer>
                <v-btn color="primary" prepend-icon="mdi-pencil" @click="handleEdit">
                    Edit Service
                </v-btn>
                <v-btn color="grey" variant="text" @click="closeDialog">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'ServiceDetailsDialog',
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        serviceId: {
            type: [Number, String],
            default: null
        }
    },
    emits: ['update:modelValue', 'edit'],
    data() {
        return {
            loading: false,
            service: null,
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
            if (newVal && this.serviceId) {
                this.loadService();
            }
        },
        serviceId() {
            if (this.dialog && this.serviceId) {
                this.loadService();
            }
        }
    },
    methods: {
        async loadService() {
            if (!this.serviceId) return;

            this.loading = true;
            this.service = null;
            this.tab = 'basic';

            try {
                const response = await this.$axios.get(`/api/v1/services/${this.serviceId}`, {
                    headers: this.getAuthHeaders()
                });

                const data = response.data;
                if (!data) {
                    throw new Error('No service data received');
                }

                this.service = data;
            } catch (error) {
                console.error('Failed to load service details:', error);
                alert('Failed to load service details. Please try again.');
                this.closeDialog();
            } finally {
                this.loading = false;
            }
        },
        closeDialog() {
            this.dialog = false;
            this.service = null;
            this.tab = 'basic';
        },
        handleEdit() {
            if (this.service) {
                this.$emit('edit', this.service);
                this.closeDialog();
            }
        },
        getAuthHeaders() {
            const token = localStorage.getItem('admin_token');
            return {
                Authorization: `Bearer ${token}`
            };
        },
        formatDateTime(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString();
        }
    }
};
</script>
