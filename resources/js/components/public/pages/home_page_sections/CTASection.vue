<template>
    <section class="py-24 position-relative overflow-hidden cta-section">
        <div class="cta-gradient-bg"></div>
        <div class="cta-particles"></div>
        <v-container class="position-relative z-index-1">
            <v-row justify="center" class="text-center">
                <v-col cols="12" md="10" lg="8">
                    <h2 class="text-h3 text-md-h2 font-weight-black text-white mb-6 lh-tight">
                        Secure Your Power Today
                    </h2>
                    <p class="text-h6 text-white opacity-90 mb-10 font-weight-light mw-800 mx-auto">
                        Don't let power outages disrupt your life or business. Contact us for reliable backup
                        solutions.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-center gap-4">
                        <v-btn size="x-large" color="amber-accent-4" variant="flat"
                            class="text-black font-weight-bold px-10 rounded-pill elevation-6 hover-lift"
                            :to="{ name: 'Contact' }">
                            Get Started
                        </v-btn>
                        <v-btn size="x-large" variant="outlined" color="white"
                            class="font-weight-bold px-10 rounded-pill border-2" :to="{ name: 'Contact' }">
                            Contact Support
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script>
export default {
    name: 'CTASection'
};
</script>

<style scoped>
.cta-section {
    background: #111827;
}

.cta-gradient-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at top right, #2563eb, transparent 40%),
        radial-gradient(circle at bottom left, #d97706, transparent 40%);
    opacity: 0.6;
}

.border-2 {
    border-width: 2px !important;
}
</style>
