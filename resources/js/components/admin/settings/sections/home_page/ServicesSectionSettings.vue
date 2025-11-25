<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Services Section Content</div>
        </v-col>
        <v-col cols="12" md="4">
            <v-text-field v-model="settings.services_overline.value" label="Overline" variant="outlined"
                density="comfortable" color="primary" hint="e.g., WHAT WE DO"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
            <v-text-field v-model="settings.services_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Power Support Solutions"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-textarea v-model="settings.services_subtitle.value" label="Subtitle" variant="outlined"
                density="comfortable" color="primary" persistent-hint rows="2" auto-grow></v-textarea>
        </v-col>
        <v-col cols="12">
            <v-divider class="my-4"></v-divider>
            <div class="d-flex justify-space-between align-center mb-4">
                <div class="text-subtitle-1 font-weight-bold">Services</div>
                <v-btn color="primary" prepend-icon="mdi-plus" @click="addService" size="small">
                    Add Service
                </v-btn>
            </div>
            <div v-if="services.length === 0" class="text-center py-8 text-medium-emphasis">
                No services added. Click "Add Service" to get started.
            </div>
            <v-card v-for="(service, index) in services" :key="index" class="mb-4" variant="outlined">
                <v-card-text>
                    <div class="d-flex justify-space-between align-start mb-2">
                        <div class="text-subtitle-2 font-weight-bold">Service {{ index + 1 }}</div>
                        <v-btn icon="mdi-delete" variant="text" color="error" size="small"
                            @click="removeService(index)"></v-btn>
                    </div>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="service.title" label="Title" variant="outlined"
                                density="comfortable" color="primary" hint="e.g., UPS Systems"
                                persistent-hint @input="updateServices"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="service.slug" label="Slug" variant="outlined"
                                density="comfortable" color="primary"
                                hint="e.g., ups-systems (URL-friendly)" persistent-hint
                                prepend-inner-icon="mdi-link" @input="updateServices">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="service.icon" label="Icon (Material Design)"
                                variant="outlined" density="comfortable" color="primary"
                                hint="e.g., mdi-battery-charging-high" persistent-hint
                                prepend-inner-icon="mdi-information" @input="updateServices">
                                <template v-slot:append-inner v-if="service.icon">
                                    <v-icon :icon="service.icon" size="small"></v-icon>
                                </template>
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="service.id" label="ID (Optional)" variant="outlined"
                                density="comfortable" color="primary" hint="Numeric ID for the service"
                                persistent-hint type="number" @input="updateServices"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="service.short_description" label="Short Description"
                                variant="outlined" density="comfortable" color="primary" persistent-hint
                                rows="2" auto-grow hint="Brief description of the service"
                                @input="updateServices"></v-textarea>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'ServicesSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            services: []
        };
    },
    mounted() {
        this.initializeServices();
    },
    methods: {
        initializeServices() {
            // Create services_data setting if it doesn't exist
            if (!this.settings.services_data) {
                this.settings.services_data = {
                    value: '',
                    type: 'textarea',
                    group: 'home_page'
                };
            }

            const servicesJson = this.settings.services_data.value;
            if (servicesJson) {
                try {
                    const parsed = typeof servicesJson === 'string' ? JSON.parse(servicesJson) : servicesJson;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.services = parsed;
                    } else {
                        this.services = this.getDefaultServices();
                        this.updateServices(); // Save defaults
                    }
                } catch (e) {
                    console.warn('Error parsing services:', e);
                    this.services = this.getDefaultServices();
                    this.updateServices(); // Save defaults
                }
            } else {
                this.services = this.getDefaultServices();
                this.updateServices(); // Save defaults
            }
        },
        getDefaultServices() {
            return [
                {
                    id: 1,
                    title: 'UPS Systems',
                    slug: 'ups-systems',
                    short_description: 'Reliable Uninterruptible Power Supply systems for critical equipment protection.',
                    icon: 'mdi-battery-charging-high'
                },
                {
                    id: 2,
                    title: 'Industrial Backup',
                    slug: 'industrial-backup',
                    short_description: 'Heavy-duty power backup solutions designed for demanding industrial applications.',
                    icon: 'mdi-factory'
                },
                {
                    id: 3,
                    title: 'Home Power Solutions',
                    slug: 'home-power',
                    short_description: 'Keep your home running smoothly during outages with our residential backup systems.',
                    icon: 'mdi-home-lightning'
                }
            ];
        },
        addService() {
            const newId = this.services.length > 0
                ? Math.max(...this.services.map(s => s.id || 0)) + 1
                : 1;
            this.services.push({
                id: newId,
                title: '',
                slug: '',
                short_description: '',
                icon: 'mdi-star'
            });
            this.updateServices();
        },
        removeService(index) {
            this.services.splice(index, 1);
            this.updateServices();
        },
        updateServices() {
            // Ensure all services have required fields
            const servicesData = this.services.map(service => ({
                id: service.id || null,
                title: service.title || '',
                slug: service.slug || '',
                short_description: service.short_description || '',
                icon: service.icon || null
            })).filter(service => service.title || service.slug); // Only include services with at least a title or slug

            // Create services_data setting if it doesn't exist
            if (!this.settings.services_data) {
                this.settings.services_data = {
                    value: '',
                    type: 'textarea',
                    group: 'home_page'
                };
            }
            this.settings.services_data.value = JSON.stringify(servicesData);
        }
    }
};
</script>

