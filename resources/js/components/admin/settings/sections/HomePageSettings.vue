<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">Home Page Settings</h2>
            <p class="text-body-2 text-medium-emphasis">Customize the hero section and content of your homepage</p>
        </div>

        <!-- Home Page Tabs -->
        <v-card elevation="2" rounded="lg" class="mb-6">
            <v-tabs v-model="homePageTab" color="primary" bg-color="grey-lighten-4" slider-color="primary"
                class="home-page-tabs">
                <v-tab value="hero" class="tab-item">
                    <v-icon icon="mdi-view-dashboard" class="mr-2"></v-icon>
                    <span class="tab-label">Hero Section</span>
                </v-tab>
                <v-tab value="stats" class="tab-item">
                    <v-icon icon="mdi-chart-line" class="mr-2"></v-icon>
                    <span class="tab-label">Statistics</span>
                </v-tab>
                <v-tab value="trusted_by" class="tab-item">
                    <v-icon icon="mdi-account-group" class="mr-2"></v-icon>
                    <span class="tab-label">Trusted By</span>
                </v-tab>
                <v-tab value="services" class="tab-item">
                    <v-icon icon="mdi-cog" class="mr-2"></v-icon>
                    <span class="tab-label">Services</span>
                </v-tab>
                <v-tab value="why_choose_us" class="tab-item">
                    <v-icon icon="mdi-star-circle" class="mr-2"></v-icon>
                    <span class="tab-label">Why Choose Us</span>
                </v-tab>
                <v-tab value="testimonials" class="tab-item">
                    <v-icon icon="mdi-format-quote-close" class="mr-2"></v-icon>
                    <span class="tab-label">Testimonials</span>
                </v-tab>
                <v-tab value="products" class="tab-item">
                    <v-icon icon="mdi-package-variant" class="mr-2"></v-icon>
                    <span class="tab-label">Products</span>
                </v-tab>
                <v-tab value="cta" class="tab-item">
                    <v-icon icon="mdi-bullhorn" class="mr-2"></v-icon>
                    <span class="tab-label">CTA Section</span>
                </v-tab>
                <v-tab value="visibility" class="tab-item">
                    <v-icon icon="mdi-eye-settings" class="mr-2"></v-icon>
                    <span class="tab-label">Visibility</span>
                </v-tab>
            </v-tabs>
        </v-card>

        <v-window v-model="homePageTab">
            <v-window-item value="hero">
                <HeroSectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="stats">
                <StatsSectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="trusted_by">
                <TrustedBySectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="services">
                <ServicesSectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="why_choose_us">
                <WhyChooseUsSectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="testimonials">
                <TestimonialsSectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="products">
                <ProductsSectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="cta">
                <CTASectionSettings :settings="settings" />
            </v-window-item>

            <v-window-item value="visibility">
                <VisibilitySectionSettings :settings="settings" />
            </v-window-item>
        </v-window>
    </div>
</template>

<script>
import HeroSectionSettings from './home_page/HeroSectionSettings.vue';
import StatsSectionSettings from './home_page/StatsSectionSettings.vue';
import TrustedBySectionSettings from './home_page/TrustedBySectionSettings.vue';
import ServicesSectionSettings from './home_page/ServicesSectionSettings.vue';
import WhyChooseUsSectionSettings from './home_page/WhyChooseUsSectionSettings.vue';
import TestimonialsSectionSettings from './home_page/TestimonialsSectionSettings.vue';
import ProductsSectionSettings from './home_page/ProductsSectionSettings.vue';
import CTASectionSettings from './home_page/CTASectionSettings.vue';
import VisibilitySectionSettings from './home_page/VisibilitySectionSettings.vue';

export default {
    name: 'HomePageSettings',
    components: {
        HeroSectionSettings,
        StatsSectionSettings,
        TrustedBySectionSettings,
        ServicesSectionSettings,
        WhyChooseUsSectionSettings,
        TestimonialsSectionSettings,
        ProductsSectionSettings,
        CTASectionSettings,
        VisibilitySectionSettings
    },
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            homePageTab: 'hero'
        };
    }
};
</script>

<style scoped>
.home-page-tabs {
    border-radius: 8px;
}

.home-page-tabs :deep(.v-tab) {
    min-width: 140px;
    text-transform: none;
    font-weight: 500;
    letter-spacing: 0.5px;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.home-page-tabs :deep(.v-tab:hover) {
    background-color: rgba(var(--v-theme-primary), 0.08);
}

.home-page-tabs :deep(.v-tab--selected) {
    color: rgb(var(--v-theme-primary));
    font-weight: 600;
}

.home-page-tabs :deep(.v-tab .v-icon) {
    margin-right: 8px;
    font-size: 20px;
}

.tab-item {
    display: flex;
    align-items: center;
    justify-content: center;
}

.tab-label {
    white-space: nowrap;
}

/* Responsive adjustments */
@media (max-width: 960px) {
    .home-page-tabs :deep(.v-tab) {
        min-width: auto;
        padding: 10px 12px;
        font-size: 0.875rem;
    }

    .home-page-tabs :deep(.v-tab .v-icon) {
        font-size: 18px;
        margin-right: 4px;
    }

    .tab-label {
        display: none;
    }
}

@media (max-width: 600px) {
    .home-page-tabs :deep(.v-tab) {
        padding: 8px 10px;
    }
}
</style>
