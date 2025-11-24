<template>
    <div class="services-page">
        <!-- Hero Section -->
        <section class="page-hero position-relative d-flex align-center justify-center text-center">
            <div class="hero-bg-gradient"></div>
            <div class="hero-pattern"></div>
            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-4 py-2 rounded-pill mb-6">
                            <v-icon icon="mdi-cogs" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">OUR EXPERTISE</span>
                        </div>
                        <h1 class="text-h4 text-lg-h3 font-weight-black text-white mb-6 lh-tight text-shadow-sm">
                            Comprehensive Power Solutions</h1>
                        <p class="text-h6 text-white opacity-80 mw-800 mx-auto font-weight-light">
                            From installation to maintenance, we provide end-to-end technical support to keep your
                            operations running smoothly.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Services Grid -->
        <section class="py-20 bg-grey-lighten-5 position-relative">
            <div class="bg-pattern-dots"></div>
            <v-container class="position-relative z-index-1">
                <v-row>
                    <v-col v-for="(service, i) in services" :key="service.id" cols="12" md="6">
                        <v-hover v-slot="{ isHovering, props }">
                            <v-card v-bind="props" :elevation="isHovering ? 12 : 2"
                                class="h-100 service-card-detailed rounded-xl transition-all overflow-hidden"
                                :to="`/services/${service.slug}`">
                                <div class="d-flex flex-column flex-md-row h-100">
                                    <div
                                        class="service-icon-side bg-primary-darken-1 d-flex align-center justify-center pa-6">
                                        <v-icon :icon="getServiceIcon(i)" size="48" color="amber-accent-4"></v-icon>
                                    </div>
                                    <div class="pa-8 flex-grow-1 bg-white">
                                        <h3 class="text-h5 font-weight-bold mb-3 text-grey-darken-3">{{ service.title }}
                                        </h3>
                                        <p class="text-body-1 text-medium-emphasis mb-6 lh-relaxed">
                                            {{ service.short_description }}
                                        </p>
                                        <div class="d-flex align-center">
                                            <span class="text-primary font-weight-bold link-hover-effect">
                                                Explore Service <v-icon icon="mdi-arrow-right" size="small"
                                                    class="ml-1"></v-icon>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </v-card>
                        </v-hover>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Our Process -->
        <section class="py-20 bg-surface">
            <v-container>
                <div class="text-center mb-16">
                    <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">HOW WE WORK</div>
                    <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">Our Process</h2>
                </div>

                <v-row class="position-relative process-timeline">
                    <!-- Connecting Line (Desktop) -->
                    <div class="d-none d-md-block process-line"></div>

                    <v-col v-for="(step, i) in process" :key="i" cols="12" md="4"
                        class="text-center position-relative z-index-2">
                        <div
                            class="process-step mb-6 mx-auto bg-white rounded-circle elevation-6 d-flex align-center justify-center text-h4 font-weight-black text-primary border-thin">
                            {{ i + 1 }}
                        </div>
                        <h4 class="text-h5 font-weight-bold mb-3">{{ step.title }}</h4>
                        <p class="text-body-1 text-medium-emphasis px-4">{{ step.desc }}</p>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-primary-darken-2 position-relative overflow-hidden">
            <div class="cta-pattern"></div>
            <v-container class="position-relative z-index-2 text-center">
                <h2 class="text-h3 font-weight-bold text-white mb-6">Need a Custom Solution?</h2>
                <p class="text-h6 text-white opacity-80 mb-8 font-weight-light">
                    We understand that every business is unique. Contact us for a tailored power strategy.
                </p>
                <v-btn size="x-large" color="amber-accent-4" variant="flat" rounded="pill"
                    class="px-10 font-weight-bold text-black hover-lift" :to="{ name: 'Contact' }">
                    Get a Free Consultation
                </v-btn>
            </v-container>
        </section>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ServicesPage',
    data() {
        return {
            services: [],
            process: [
                {
                    title: 'Consultation',
                    desc: 'We assess your power needs and current infrastructure to recommend the best solution.'
                },
                {
                    title: 'Installation',
                    desc: 'Our certified technicians handle the installation with precision and adherence to safety standards.'
                },
                {
                    title: 'Support',
                    desc: 'We provide ongoing maintenance and 24/7 support to ensure your systems never fail.'
                }
            ]
        };
    },
    async mounted() {
        await this.loadServices();
    },
    methods: {
        async loadServices() {
            try {
                const response = await axios.get('/api/openapi/services');
                this.services = response.data;
            } catch (error) {
                console.error('Error loading services:', error);
                // Fallback data
                this.services = [
                    { id: 1, title: 'UPS Systems', slug: 'ups-systems', short_description: 'Reliable Uninterruptible Power Supply systems for critical equipment protection.' },
                    { id: 2, title: 'Industrial Backup', slug: 'industrial-backup', short_description: 'Heavy-duty power backup solutions designed for demanding industrial applications.' },
                    { id: 3, title: 'Home Power Solutions', slug: 'home-power', short_description: 'Keep your home running smoothly during outages with our residential backup systems.' },
                    { id: 4, title: 'Battery Maintenance', slug: 'battery-maintenance', short_description: 'Professional testing, replacement, and disposal services for all battery types.' },
                    { id: 5, title: 'Power Management', slug: 'power-management', short_description: 'Smart energy monitoring and efficiency optimization to reduce costs.' },
                    { id: 6, title: 'Installation Services', slug: 'installation', short_description: 'Expert installation of all power systems, wiring, and safety equipment.' }
                ];
            }
        },
        getServiceIcon(index) {
            const icons = ['mdi-battery-charging-high', 'mdi-factory', 'mdi-home-lightning', 'mdi-battery-sync', 'mdi-lightning-bolt', 'mdi-tools'];
            return icons[index % icons.length];
        }
    }
};
</script>

<style scoped>
.page-hero {
    height: 400px;
    background: #0f172a;
}

.hero-bg-gradient {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
    opacity: 0.9;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
    background-size: 30px 30px;
    opacity: 0.3;
}

.glass-pill {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.bg-pattern-dots {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.4;
}

.service-card-detailed {
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.service-icon-side {
    width: 100%;
    min-height: 120px;
}

@media (min-width: 960px) {
    .service-icon-side {
        width: 140px;
        min-height: auto;
    }
}

.link-hover-effect {
    display: inline-flex;
    align-items: center;
    transition: transform 0.2s ease;
}

.service-card-detailed:hover .link-hover-effect {
    transform: translateX(4px);
}

.process-step {
    width: 80px;
    height: 80px;
    border: 4px solid white;
}

.process-line {
    position: absolute;
    top: 40px;
    left: 16%;
    right: 16%;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}

.cta-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.05) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.05) 50%, rgba(255, 255, 255, 0.05) 75%, transparent 75%, transparent);
    background-size: 40px 40px;
    opacity: 0.3;
}

.hover-lift {
    transition: transform 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.mw-800 {
    max-width: 800px;
}

.lh-relaxed {
    line-height: 1.6;
}

.lh-tight {
    line-height: 1.2;
}

.tracking-wide {
    letter-spacing: 0.05em;
}

.tracking-widest {
    letter-spacing: 0.15em;
}

.text-shadow-sm {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.z-index-1 {
    z-index: 1;
}

.z-index-2 {
    z-index: 2;
}
</style>
