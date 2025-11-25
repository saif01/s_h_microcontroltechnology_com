<template>
    <section class="py-20 bg-grey-lighten-5 position-relative">
        <div class="bg-pattern-dots"></div>
        <v-container class="position-relative z-index-1">
            <div class="text-center mb-16">
                <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">WHAT WE DO</div>
                <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">Power Support Solutions</h2>
                <div class="divider-center mx-auto mb-6"></div>
                <p class="text-h6 text-medium-emphasis mx-auto mw-800 font-weight-regular">
                    We ensure uninterrupted operations for businesses and households with high-quality power
                    products and services.
                </p>
            </div>

            <v-row v-if="services.length > 0">
                <v-col v-for="(service, i) in services" :key="service.id" cols="12" md="4">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 16 : 2"
                            class="h-100 service-card-modern rounded-xl transition-all"
                            :to="`/services/${service.slug}`">
                            <div class="card-gradient-border"></div>
                            <v-card-item class="pt-8 px-6 pb-6">
                                <div
                                    class="service-icon-box mb-6 rounded-lg elevation-3 d-inline-flex pa-4 bg-white text-primary">
                                    <v-icon :icon="service.icon || getServiceIcon(i)" size="32"></v-icon>
                                </div>
                                <v-card-title class="text-h5 font-weight-bold mb-3 text-grey-darken-3">
                                    {{ service.title }}
                                </v-card-title>
                                <v-card-subtitle class="text-body-1 text-wrap mb-4 opacity-70 lh-relaxed">
                                    {{ service.short_description }}
                                </v-card-subtitle>
                            </v-card-item>
                            <v-card-actions class="px-6 pb-8">
                                <span class="text-primary font-weight-bold link-hover-effect">
                                    Learn More <v-icon icon="mdi-arrow-right" size="small" class="ml-1"></v-icon>
                                </span>
                            </v-card-actions>
                        </v-card>
                    </v-hover>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script>
export default {
    name: 'ServicesSection',
    props: {
        services: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        getServiceIcon(index) {
            const icons = ['mdi-battery-charging-high', 'mdi-factory', 'mdi-home-lightning', 'mdi-battery-sync', 'mdi-lightning-bolt', 'mdi-tools'];
            return icons[index % icons.length];
        }
    }
};
</script>

<style scoped>
.divider-center {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #d97706);
    border-radius: 2px;
}

.service-card-modern {
    background: white;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.card-gradient-border {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #f59e0b);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.service-card-modern:hover .card-gradient-border {
    opacity: 1;
}

.service-card-modern:hover {
    transform: translateY(-8px);
}

@media (max-width: 600px) {
    .service-card-modern {
        margin-bottom: 16px;
    }
}
</style>

