<template>
    <section class="hero-section position-relative overflow-hidden">
        <div class="animated-gradient-bg"></div>
        <div class="hero-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <v-container class="h-100 position-relative z-index-2">
            <v-row class="h-100 align-center">
                <v-col cols="12" md="7" class="text-white">
                    <v-fade-transition appear>
                        <div class="hero-content">
                            <div
                                class="glass-pill d-inline-flex align-center px-3 px-md-4 py-2 rounded-pill mb-4 mb-md-6">
                                <v-icon icon="mdi-flash" color="amber-accent-3" size="small" class="mr-2"></v-icon>
                                <span class="text-caption text-md-subtitle-2 font-weight-bold tracking-wide">POWERING
                                    YOUR
                                    SUCCESS</span>
                            </div>
                            <h1
                                class="text-h5 text-md-h4 text-lg-h3 font-weight-black mb-4 mb-md-6 lh-tight text-shadow-sm">
                                {{ heroTitle }}
                            </h1>
                            <p
                                class="text-body-1 text-md-h6 text-lg-h5 font-weight-light mb-6 mb-md-8 opacity-90 mw-600 lh-relaxed">
                                {{ heroSubtitle }}
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-3 gap-md-4 flex-wrap">
                                <v-btn size="large" color="amber-accent-4" variant="flat" rounded="pill"
                                    class="px-6 px-md-8 text-black font-weight-bold elevation-4 hover-scale hero-btn"
                                    :to="{ name: 'Contact' }">
                                    Get a Quote
                                    <v-icon end icon="mdi-arrow-right" class="ml-2"></v-icon>
                                </v-btn>
                                <v-btn size="large" variant="outlined" color="white" rounded="pill"
                                    class="px-6 px-md-8 font-weight-bold border-2 hover-fill hero-btn" to="/services">
                                    Our Solutions
                                </v-btn>
                            </div>
                        </div>
                    </v-fade-transition>
                </v-col>

                <v-col cols="12" md="5" class="d-none d-md-flex justify-center align-center">
                    <div class="hero-visual-3d">
                        <div class="glass-card card-main">
                            <v-icon icon="mdi-battery-charging-high" size="48" color="amber-accent-3"
                                class="mb-4"></v-icon>
                            <div class="text-h5 font-weight-bold text-white mb-2">Always On</div>
                            <div class="text-body-2 text-white opacity-80">Uninterrupted power supply for critical
                                operations.</div>
                        </div>
                        <div class="glass-card card-float-1">
                            <v-icon icon="mdi-shield-check" size="32" color="success"></v-icon>
                            <div class="text-subtitle-2 font-weight-bold text-white mt-2">Protected</div>
                        </div>
                        <div class="glass-card card-float-2">
                            <v-icon icon="mdi-lightning-bolt" size="32" color="amber-accent-3"></v-icon>
                            <div class="text-subtitle-2 font-weight-bold text-white mt-2">Efficient</div>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-container>

        <!-- Wave Divider -->
        <div class="wave-divider">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"
                preserveAspectRatio="none">
                <path
                    d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                    class="shape-fill"></path>
            </svg>
        </div>
    </section>
</template>

<script>
export default {
    name: 'HeroSection',
    props: {
        heroTitle: {
            type: String,
            default: 'Uninterrupted Power for Your Business & Home'
        },
        heroSubtitle: {
            type: String,
            default: 'Reliable technical power support solutions, including UPS systems, industrial backup, batteries, and professional maintenance services.'
        }
    }
};
</script>

<style scoped>
.hero-section {
    min-height: 800px;
    display: flex;
    align-items: center;
    position: relative;
}

.animated-gradient-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(-45deg, #1e3a8a, #2563eb, #d97706, #1e3a8a);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

@keyframes gradientBG {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.hero-shapes .shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.6;
    animation: floatShape 20s infinite alternate;
}

.shape-1 {
    width: 400px;
    height: 400px;
    background: #3b82f6;
    top: -100px;
    left: -100px;
}

.shape-2 {
    width: 300px;
    height: 300px;
    background: #f59e0b;
    bottom: 10%;
    right: 10%;
    animation-delay: -5s;
}

.shape-3 {
    width: 200px;
    height: 200px;
    background: #60a5fa;
    top: 20%;
    right: 30%;
    animation-delay: -10s;
}

.glass-pill {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-content {
    width: 100%;
}

.hero-btn {
    min-width: 140px;
    white-space: nowrap;
}

@keyframes floatShape {
    0% {
        transform: translate(0, 0) rotate(0deg);
    }

    100% {
        transform: translate(50px, 50px) rotate(20deg);
    }
}

.hero-visual-3d {
    position: relative;
    width: 400px;
    height: 400px;
    perspective: 1000px;
}

.glass-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 24px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    padding: 24px;
    transition: transform 0.3s ease;
}

.card-main {
    width: 280px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
}

.card-float-1 {
    top: 20%;
    right: 0;
    width: 140px;
    animation: float 6s ease-in-out infinite;
    z-index: 1;
}

.card-float-2 {
    bottom: 20%;
    left: 0;
    width: 140px;
    animation: float 6s ease-in-out infinite 2s;
    z-index: 3;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

.wave-divider {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
    transform: rotate(180deg);
}

.wave-divider svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 100px;
}

@media (max-width: 600px) {
    .wave-divider svg {
        height: 60px;
    }
}

.wave-divider .shape-fill {
    fill: rgb(var(--v-theme-surface));
}

@media (max-width: 960px) {
    .hero-section {
        min-height: auto;
        padding-top: 60px;
        padding-bottom: 60px;
    }

    .hero-shapes .shape {
        filter: blur(60px);
        opacity: 0.4;
    }

    .shape-1 {
        width: 250px;
        height: 250px;
        top: -50px;
        left: -50px;
    }

    .shape-2 {
        width: 200px;
        height: 200px;
    }

    .shape-3 {
        width: 150px;
        height: 150px;
    }

    .hero-visual-3d {
        width: 300px;
        height: 300px;
    }

    .card-main {
        width: 220px;
    }

    .card-float-1,
    .card-float-2 {
        width: 120px;
    }

    .mw-600 {
        max-width: 100% !important;
    }
}

@media (max-width: 600px) {
    .hero-section {
        min-height: auto;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .hero-shapes .shape {
        filter: blur(40px);
        opacity: 0.3;
    }

    .shape-1 {
        width: 200px;
        height: 200px;
        top: -30px;
        left: -30px;
    }

    .shape-2 {
        width: 150px;
        height: 150px;
        bottom: 5%;
        right: 5%;
    }

    .shape-3 {
        width: 100px;
        height: 100px;
        top: 15%;
        right: 20%;
    }

    .hero-visual-3d {
        width: 250px;
        height: 250px;
    }

    .card-main {
        width: 180px;
    }

    .card-float-1,
    .card-float-2 {
        width: 100px;
    }

    .hero-content {
        padding: 0 8px;
    }

    .glass-pill {
        padding: 6px 12px !important;
        font-size: 0.7rem !important;
    }

    .hero-btn {
        width: 100%;
        min-width: auto;
    }

    .text-shadow-sm {
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
}
</style>
