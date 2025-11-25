<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Section Visibility</div>
            <p class="text-body-2 text-medium-emphasis mb-4">Enable or disable sections on the home page</p>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.hero_section_enabled.value" label="Hero Section" color="primary"
                hide-details :true-value="'1'" :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">Hero Section</span>
                </template>
            </v-switch>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.stats_section_enabled.value" label="Stats Section" color="primary"
                hide-details :true-value="'1'" :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">Stats Section</span>
                </template>
            </v-switch>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.trusted_by_section_enabled.value" label="Trusted By Section"
                color="primary" hide-details :true-value="'1'" :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">Trusted By Section</span>
                </template>
            </v-switch>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.services_section_enabled.value" label="Services Section"
                color="primary" hide-details :true-value="'1'" :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">Services Section</span>
                </template>
            </v-switch>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.why_choose_us_section_enabled.value" label="Why Choose Us Section"
                color="primary" hide-details :true-value="'1'" :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">Why Choose Us Section</span>
                </template>
            </v-switch>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.testimonials_section_enabled.value" label="Testimonials Section"
                color="primary" hide-details :true-value="'1'" :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">Testimonials Section</span>
                </template>
            </v-switch>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.featured_products_section_enabled.value"
                label="Featured Products Section" color="primary" hide-details :true-value="'1'"
                :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">Featured Products Section</span>
                </template>
            </v-switch>
        </v-col>
        <v-col cols="12" md="6">
            <v-switch v-model="settings.cta_section_enabled.value" label="CTA Section" color="primary"
                hide-details :true-value="'1'" :false-value="'0'">
                <template v-slot:label>
                    <span class="text-body-1">CTA Section</span>
                </template>
            </v-switch>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'VisibilitySectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

