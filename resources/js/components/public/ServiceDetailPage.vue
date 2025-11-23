<template>
    <div class="service-detail-page">
        <!-- Hero Section -->
        <section class="page-hero position-relative d-flex align-center justify-center text-center">
            <div class="hero-bg-gradient"></div>
            <div class="hero-pattern"></div>
            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-4 py-2 rounded-pill mb-6">
                            <v-icon icon="mdi-cogs" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">SERVICE DETAILS</span>
                        </div>
                        <h1 class="text-h3 text-md-h2 font-weight-black text-white mb-6">{{ service.title }}</h1>
                        <p class="text-h6 text-white opacity-80 mw-800 mx-auto font-weight-light">
                            {{ service.short_description }}
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Content Section -->
        <section class="py-20 bg-surface">
            <v-container>
                <v-row>
                    <v-col cols="12" md="8">
                        <div class="text-body-1 text-grey-darken-3 lh-relaxed mb-8">
                            <p class="mb-6">
                                At Micro Control Technology, our <strong>{{ service.title }}</strong> service is designed to ensure your operations never miss a beat. We understand the critical nature of reliable power, which is why we offer comprehensive solutions tailored to your specific needs.
                            </p>
                            <h3 class="text-h5 font-weight-bold mb-4">Key Benefits</h3>
                            <v-list density="comfortable" class="bg-transparent mb-6">
                                <v-list-item v-for="(benefit, i) in benefits" :key="i" class="px-0">
                                    <template v-slot:prepend>
                                        <v-icon color="primary" icon="mdi-check-circle" class="mr-3"></v-icon>
                                    </template>
                                    <v-list-item-title class="text-body-1">{{ benefit }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                            <p>
                                Contact us today to learn more about how our {{ service.title }} can benefit your business or home.
                            </p>
                        </div>
                        <v-btn color="primary" size="large" rounded="pill" :to="{ name: 'Contact' }" class="font-weight-bold px-8">
                            Get a Quote for this Service
                        </v-btn>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-card class="rounded-xl pa-6 elevation-4 bg-grey-lighten-5">
                            <h4 class="text-h6 font-weight-bold mb-4">Other Services</h4>
                            <v-list class="bg-transparent">
                                <v-list-item 
                                    v-for="item in otherServices" 
                                    :key="item.id" 
                                    :to="`/services/${item.slug}`"
                                    rounded="lg"
                                    active-color="primary"
                                    class="mb-2"
                                >
                                    <template v-slot:prepend>
                                        <v-icon icon="mdi-chevron-right" size="small"></v-icon>
                                    </template>
                                    <v-list-item-title class="font-weight-medium">{{ item.title }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>
    </div>
</template>

<script>
export default {
    name: 'ServiceDetailPage',
    data() {
        return {
            service: {
                title: 'Loading...',
                short_description: 'Please wait while we load the service details.'
            },
            benefits: [
                '24/7 Monitoring and Support',
                'Certified Technical Experts',
                'Cost-Effective Solutions',
                'Extended Warranty Options'
            ],
            otherServices: [
                { id: 1, title: 'UPS Systems', slug: 'ups-systems' },
                { id: 2, title: 'Industrial Backup', slug: 'industrial-backup' },
                { id: 3, title: 'Battery Maintenance', slug: 'battery-maintenance' },
            ]
        };
    },
    mounted() {
        this.loadService();
    },
    watch: {
        '$route.params.slug': 'loadService'
    },
    methods: {
        loadService() {
            // Mock data loading based on slug
            const slug = this.$route.params.slug;
            const title = slug.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            
            this.service = {
                title: title,
                short_description: `Professional ${title} services for your business and home needs.`
            };
        }
    }
};
</script>

<style scoped>
.page-hero {
    height: 300px;
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
    background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 30px 30px;
    opacity: 0.3;
}

.glass-pill {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.lh-relaxed { line-height: 1.8; }
.mw-800 { max-width: 800px; }
.z-index-2 { z-index: 2; }
</style>
