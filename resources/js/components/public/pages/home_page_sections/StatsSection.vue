<template>
    <section class="py-8 py-md-12 bg-surface position-relative z-index-2 mt-n12">
        <v-container>
            <v-card elevation="10" rounded="xl" class="stats-card overflow-hidden">
                <v-row no-gutters>
                    <v-col v-for="(stat, index) in stats" :key="index" cols="6" md="3">
                        <div
                            class="py-8 px-4 text-center stat-item h-100 d-flex flex-column justify-center align-center">
                            <div
                                class="text-h3 text-md-h2 font-weight-black text-transparent bg-clip-text gradient-text mb-2">
                                {{ stat.value }}
                            </div>
                            <div
                                class="text-subtitle-1 text-uppercase font-weight-bold text-medium-emphasis tracking-wide">
                                {{ stat.label }}
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-card>
        </v-container>
    </section>
</template>

<script>
export default {
    name: 'StatsSection',
    props: {
        stats: {
            type: Array,
            default: () => [
                { value: '500+', label: 'Systems Installed' },
                { value: '99.9%', label: 'Power Uptime' },
                { value: '24/7', label: 'Support' },
                { value: '15+', label: 'Years Experience' }
            ]
        }
    }
};
</script>

<style scoped>
.stats-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
}

.stat-item:not(:last-child) {
    border-right: 1px solid rgba(0, 0, 0, 0.05);
}

.gradient-text {
    background: linear-gradient(45deg, #2563eb, #d97706);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

@media (max-width: 960px) {
    .stat-item:not(:last-child) {
        border-right: none;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
}
</style>

