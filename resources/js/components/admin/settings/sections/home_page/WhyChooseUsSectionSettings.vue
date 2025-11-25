<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Why Choose Us Section Content</div>
        </v-col>
        <v-col cols="12" md="4">
            <v-text-field v-model="settings.why_choose_us_overline.value" label="Overline"
                variant="outlined" density="comfortable" color="primary" hint="e.g., WHY CHOOSE US"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
            <v-text-field v-model="settings.why_choose_us_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Reliable Power, Guaranteed"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-text-field v-model="settings.why_choose_us_image.value" label="Image URL" variant="outlined"
                density="comfortable" color="primary" hint="URL for the section image"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-divider class="my-4"></v-divider>
            <div class="d-flex justify-space-between align-center mb-4">
                <div class="text-subtitle-1 font-weight-bold">Features</div>
                <v-btn color="primary" prepend-icon="mdi-plus" @click="addWhyChooseUsFeature" size="small">
                    Add Feature
                </v-btn>
            </div>
            <div v-if="whyChooseUsFeatures.length === 0" class="text-center py-8 text-medium-emphasis">
                No features added. Click "Add Feature" to get started.
            </div>
            <v-card v-for="(feature, index) in whyChooseUsFeatures" :key="index" class="mb-4"
                variant="outlined">
                <v-card-text>
                    <div class="d-flex justify-space-between align-start mb-2">
                        <div class="text-subtitle-2 font-weight-bold">Feature {{ index + 1 }}</div>
                        <v-btn icon="mdi-delete" variant="text" color="error" size="small"
                            @click="removeWhyChooseUsFeature(index)"></v-btn>
                    </div>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="feature.title" label="Title" variant="outlined"
                                density="comfortable" color="primary" hint="e.g., Uninterrupted Operations"
                                persistent-hint @input="updateWhyChooseUsFeatures"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="feature.icon" label="Icon (Material Design)"
                                variant="outlined" density="comfortable" color="primary"
                                hint="e.g., mdi-lightning-bolt-circle" persistent-hint
                                prepend-inner-icon="mdi-information" @input="updateWhyChooseUsFeatures">
                                <template v-slot:append-inner v-if="feature.icon">
                                    <v-icon :icon="feature.icon" size="small"></v-icon>
                                </template>
                            </v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="feature.desc" label="Description" variant="outlined"
                                density="comfortable" color="primary" persistent-hint rows="2" auto-grow
                                @input="updateWhyChooseUsFeatures"></v-textarea>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'WhyChooseUsSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            whyChooseUsFeatures: []
        };
    },
    mounted() {
        this.initializeWhyChooseUsFeatures();
    },
    methods: {
        initializeWhyChooseUsFeatures() {
            const featuresJson = this.settings.why_choose_us_features.value;
            if (featuresJson) {
                try {
                    const parsed = typeof featuresJson === 'string' ? JSON.parse(featuresJson) : featuresJson;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.whyChooseUsFeatures = parsed;
                    } else {
                        this.whyChooseUsFeatures = this.getDefaultFeatures();
                    }
                } catch (e) {
                    console.warn('Error parsing features:', e);
                    this.whyChooseUsFeatures = this.getDefaultFeatures();
                }
            } else {
                this.whyChooseUsFeatures = this.getDefaultFeatures();
            }
        },
        getDefaultFeatures() {
            return [
                {
                    title: 'Uninterrupted Operations',
                    desc: 'We ensure your business never stops with our reliable backup power solutions and UPS systems.',
                    icon: 'mdi-lightning-bolt-circle'
                },
                {
                    title: 'High-Quality Products',
                    desc: 'We supply top-tier batteries, inverters, and power management systems built for long-term performance.',
                    icon: 'mdi-shield-star'
                },
                {
                    title: 'Responsive Support',
                    desc: 'Our professional maintenance team is available 24/7 to handle installation and repairs.',
                    icon: 'mdi-headset'
                }
            ];
        },
        addWhyChooseUsFeature() {
            this.whyChooseUsFeatures.push({
                title: '',
                desc: '',
                icon: 'mdi-star'
            });
            this.updateWhyChooseUsFeatures();
        },
        removeWhyChooseUsFeature(index) {
            this.whyChooseUsFeatures.splice(index, 1);
            this.updateWhyChooseUsFeatures();
        },
        updateWhyChooseUsFeatures() {
            this.settings.why_choose_us_features.value = JSON.stringify(this.whyChooseUsFeatures);
        }
    }
};
</script>

