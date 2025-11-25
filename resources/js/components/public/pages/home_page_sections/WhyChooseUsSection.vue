<template>
    <section class="py-20 bg-surface position-relative overflow-hidden">
        <!-- Decorative blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <v-container class="position-relative z-index-1">
            <v-row align="center">
                <v-col cols="12" md="6" class="pr-md-12">
                    <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">WHY CHOOSE US
                    </div>
                    <h2 class="text-h3 font-weight-bold mb-8 text-grey-darken-3">Reliable Power, Guaranteed</h2>

                    <div class="feature-list">
                        <div v-for="(feature, i) in features" :key="i" class="feature-item d-flex align-start mb-8">
                            <div class="feature-icon mr-6 rounded-circle bg-primary-lighten-5 pa-3 text-primary">
                                <v-icon :icon="feature.icon" size="24"></v-icon>
                            </div>
                            <div>
                                <h4 class="text-h6 font-weight-bold mb-2">{{ feature.title }}</h4>
                                <p class="text-body-1 text-medium-emphasis">{{ feature.desc }}</p>
                            </div>
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" md="6">
                    <div class="image-composition position-relative">
                        <div class="composition-bg rounded-xl bg-grey-lighten-3"></div>
                        <v-img
                            src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            class="rounded-xl elevation-10 position-relative z-index-2 composition-img"
                            :cover="true" height="100%" width="100%">
                            <template v-slot:placeholder>
                                <div class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                </div>
                            </template>
                        </v-img>
                        <div class="composition-card glass-card-dark pa-6 rounded-lg elevation-10">
                            <div class="d-flex align-center mb-2">
                                <v-icon icon="mdi-check-decagram" color="success" class="mr-2"></v-icon>
                                <span class="text-h6 font-weight-bold text-white">100% Verified</span>
                            </div>
                            <div class="text-caption text-white opacity-80">Quality Assurance</div>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script>
export default {
    name: 'WhyChooseUsSection',
    props: {
        features: {
            type: Array,
            default: () => [
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
            ]
        }
    }
};
</script>

<style scoped>
.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    z-index: 0;
}

.blob-1 {
    width: 500px;
    height: 500px;
    background: #e0f2fe;
    top: -100px;
    right: -100px;
}

.blob-2 {
    width: 400px;
    height: 400px;
    background: #fef3c7;
    bottom: -50px;
    left: -100px;
}

.image-composition {
    width: 100%;
    height: 520px;
    position: relative;
    display: flex;
    align-items: stretch;
}

.composition-img {
    width: 100%;
    max-width: 100%;
    height: 100%;
    position: relative;
    z-index: 2;
    overflow: hidden;
    border-radius: 24px;
    box-shadow: 0 24px 64px rgba(15, 23, 42, 0.22);
    background: radial-gradient(circle at 25% 15%, rgba(37, 99, 235, 0.14), transparent 40%),
        radial-gradient(circle at 80% 0%, rgba(217, 119, 6, 0.18), transparent 38%),
        #0f172a;
}

.composition-img::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.38) 0%, rgba(15, 23, 42, 0) 35%);
    pointer-events: none;
}

:deep(.composition-img .v-img__img) {
    height: 100%;
    object-fit: cover;
    object-position: center 45%;
}

.composition-bg {
    position: absolute;
    top: 30px;
    right: -20px;
    width: 100%;
    height: calc(100% - 30px);
    z-index: 1;
    background: linear-gradient(135deg, #dbeafe 0%, #fff7ed 100%);
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
}

.glass-card-dark {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(12px);
    position: absolute;
    bottom: 40px;
    left: -30px;
    z-index: 3;
    min-width: 200px;
}

@media (max-width: 960px) {
    .image-composition {
        height: 420px;
    }

    .composition-img {
        border-radius: 20px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
    }

    .composition-bg {
        display: none;
    }

    .glass-card-dark {
        left: 20px;
        bottom: 20px;
    }
}

@media (max-width: 600px) {
    .image-composition {
        height: 360px;
    }

    .composition-img {
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.16);
    }

    .glass-card-dark {
        left: 10px;
        bottom: 10px;
        min-width: 160px;
        padding: 16px !important;
    }
}
</style>

