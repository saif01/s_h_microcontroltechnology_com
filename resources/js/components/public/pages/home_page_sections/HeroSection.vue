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
                            <div class="hero-actions d-flex flex-column flex-sm-row gap-3 gap-md-4 flex-wrap">
                                <v-btn size="large" color="amber-accent-4" variant="flat" rounded="pill"
                                    class="px-6 px-md-8 text-black font-weight-bold elevation-4 hero-btn hero-btn-primary"
                                    :to="{ name: 'Contact' }">
                                    <span class="btn-text">Get a Quote</span>
                                    <span class="btn-shine"></span>
                                    <span class="btn-particles">
                                        <span class="particle"></span>
                                        <span class="particle"></span>
                                        <span class="particle"></span>
                                    </span>
                                    <v-icon end icon="mdi-arrow-right" class="ml-2 arrow-icon"></v-icon>
                                </v-btn>
                                <v-btn size="large" variant="outlined" color="white" rounded="pill"
                                    class="px-6 px-md-8 font-weight-bold border-2 hero-btn hero-btn-secondary"
                                    to="/services">
                                    <span class="btn-text">Our Solutions</span>
                                    <span class="btn-glow"></span>
                                </v-btn>
                            </div>
                        </div>
                    </v-fade-transition>
                </v-col>

                <v-col cols="12" md="5" class="d-flex justify-center align-center hero-visual-col">
                    <div class="hero-visual-3d">
                        <div class="glass-card card-main">
                            <v-icon icon="mdi-battery-charging-high" size="48" color="amber-accent-3"
                                class="mb-4 icon-pulse"></v-icon>
                            <div class="text-h5 font-weight-bold text-white mb-2">Always On</div>
                            <div class="text-body-2 text-white opacity-80">Uninterrupted power supply for critical
                                operations.</div>
                        </div>
                        <div class="glass-card card-float-1">
                            <v-icon icon="mdi-shield-check" size="32" color="success" class="icon-bounce"></v-icon>
                            <div class="text-subtitle-2 font-weight-bold text-white mt-2">Protected</div>
                        </div>
                        <div class="glass-card card-float-2">
                            <v-icon icon="mdi-lightning-bolt" size="32" color="amber-accent-3"
                                class="icon-shake"></v-icon>
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
    padding-top: 96px;
    padding-bottom: 120px;
}

/* Ensure wave divider is always visible */
.hero-section::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 120px;
    pointer-events: none;
    z-index: 1;
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
    max-width: 680px;
}

.hero-btn {
    min-width: 140px;
    white-space: nowrap;
    position: relative;
    overflow: hidden;
    transform-style: preserve-3d;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-actions {
    width: 100%;
    max-width: 520px;
    animation: slideInUp 0.8s ease-out 0.6s both;
}

/* Primary Button Animations */
.hero-btn-primary {
    animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.8s both;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
    box-shadow: 0 10px 30px rgba(245, 158, 11, 0.4) !important;
}

.hero-btn-primary:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 15px 40px rgba(245, 158, 11, 0.6) !important;
}

.hero-btn-primary:active {
    transform: translateY(-2px) scale(1.02);
}

/* Secondary Button Animations */
.hero-btn-secondary {
    animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 1s both;
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.5) !important;
}

.hero-btn-secondary:hover {
    transform: translateY(-4px) scale(1.05);
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: white !important;
    box-shadow: 0 15px 40px rgba(255, 255, 255, 0.3);
}

.hero-btn-secondary:active {
    transform: translateY(-2px) scale(1.02);
}

/* Button Text Animation */
.btn-text {
    position: relative;
    z-index: 2;
    display: inline-block;
}

.hero-btn:hover .btn-text {
    animation: textBounce 0.6s ease;
}

/* Shine Effect */
.btn-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.6s ease;
    z-index: 1;
}

.hero-btn-primary:hover .btn-shine {
    left: 100%;
}

/* Glow Effect for Secondary Button */
.btn-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    transition: width 0.4s ease, height 0.4s ease;
    z-index: 0;
}

.hero-btn-secondary:hover .btn-glow {
    width: 300px;
    height: 300px;
}

/* Arrow Icon Animation */
.arrow-icon {
    transition: transform 0.3s ease;
    display: inline-block;
}

.hero-btn-primary:hover .arrow-icon {
    animation: arrowSlide 0.8s ease infinite;
}

/* Particles */
.btn-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 3;
}

.btn-particles .particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: white;
    border-radius: 50%;
    opacity: 0;
}

.btn-particles .particle:nth-child(1) {
    top: 20%;
    left: 30%;
}

.btn-particles .particle:nth-child(2) {
    top: 60%;
    left: 70%;
}

.btn-particles .particle:nth-child(3) {
    top: 40%;
    right: 20%;
}

.hero-btn-primary:hover .particle {
    animation: particleFloat 1.5s ease-out;
}

.hero-btn-primary:hover .particle:nth-child(2) {
    animation-delay: 0.2s;
}

.hero-btn-primary:hover .particle:nth-child(3) {
    animation-delay: 0.4s;
}

/* Keyframe Animations */
@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3) translateY(20px);
    }

    50% {
        opacity: 1;
        transform: scale(1.05) translateY(0);
    }

    70% {
        transform: scale(0.95);
    }

    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes textBounce {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-3px);
    }
}

@keyframes arrowSlide {

    0%,
    100% {
        transform: translateX(0);
    }

    50% {
        transform: translateX(5px);
    }
}

@keyframes particleFloat {
    0% {
        opacity: 0;
        transform: translate(0, 0) scale(0);
    }

    50% {
        opacity: 1;
    }

    100% {
        opacity: 0;
        transform: translate(var(--x, 20px), var(--y, -30px)) scale(1);
    }
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
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: cardFadeIn 0.8s ease-out both;
}

.card-main {
    width: 280px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
    animation-delay: 0.3s;
}

.card-main:hover {
    transform: translate(-50%, -50%) scale(1.05);
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
}

.card-float-1 {
    top: 20%;
    right: 0;
    width: 140px;
    animation: float 6s ease-in-out infinite, cardFadeIn 0.8s ease-out 0.5s both;
    z-index: 1;
}

.card-float-1:hover {
    transform: scale(1.08);
}

.card-float-2 {
    bottom: 20%;
    left: 0;
    width: 140px;
    animation: float 6s ease-in-out infinite 2s, cardFadeIn 0.8s ease-out 0.7s both;
    z-index: 3;
}

.card-float-2:hover {
    transform: scale(1.08);
}

/* Icon animations */
.icon-pulse {
    animation: iconPulse 3s ease-in-out infinite;
}

.icon-bounce {
    animation: iconBounce 2s ease-in-out infinite;
}

.icon-shake {
    animation: iconShake 2.5s ease-in-out infinite;
}

/* Animation keyframes for icons */
@keyframes iconPulse {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }
}

@keyframes iconBounce {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-8px);
    }
}

@keyframes iconShake {

    0%,
    100% {
        transform: rotate(0deg);
    }

    25% {
        transform: rotate(-10deg);
    }

    75% {
        transform: rotate(10deg);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.8);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes cardFadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
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
    z-index: 2;
}

.wave-divider svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 100px;
    filter: drop-shadow(0 -4px 8px rgba(0, 0, 0, 0.1));
}

.wave-divider .shape-fill {
    fill: rgb(var(--v-theme-surface));
}

/* Mobile wave divider enhancements */
@media (max-width: 960px) {
    .wave-divider {
        z-index: 10;
        bottom: -2px;
    }

    .wave-divider svg {
        height: 90px;
    }
}

@media (max-width: 600px) {
    .wave-divider {
        bottom: -2px;
        z-index: 10;
    }

    .wave-divider svg {
        height: 80px;
        filter: drop-shadow(0 -3px 8px rgba(0, 0, 0, 0.2));
    }

    /* Ensure wave is visible on mobile with solid white */
    .wave-divider .shape-fill {
        fill: #f5f5f5;
    }
}

@media (max-width: 400px) {
    .wave-divider svg {
        height: 60px;
    }
}

@media (max-width: 960px) {
    .hero-section {
        min-height: auto;
        padding-top: 72px;
        padding-bottom: 120px;
    }

    .hero-content {
        margin: 0 auto;
        text-align: center;
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

    /* Tablet visual cards */
    .hero-visual-col {
        margin-top: 40px;
        order: 2;
    }

    .hero-visual-3d {
        width: 320px;
        height: 320px;
    }

    .card-main {
        width: 240px;
    }

    .card-float-1,
    .card-float-2 {
        width: 130px;
    }

    .hero-actions {
        justify-content: center;
    }

    .hero-btn {
        min-width: 0;
    }

    .hero-btn-primary:hover,
    .hero-btn-secondary:hover {
        transform: translateY(-2px) scale(1.02);
    }

    .mw-600 {
        max-width: 100% !important;
    }
}

@media (max-width: 600px) {
    .hero-section {
        min-height: auto;
        padding-top: 56px;
        padding-bottom: 120px;
        overflow: visible;
    }

    .hero-shapes .shape {
        filter: blur(30px);
        opacity: 0.22;
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

    /* Mobile visual cards - horizontal layout */
    .hero-visual-col {
        margin-top: 32px;
        margin-bottom: 0;
        order: 2;
        padding: 0 12px;
    }

    .hero-visual-3d {
        width: 100%;
        max-width: 380px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        perspective: none;
    }

    /* Mobile cards - side by side layout */
    .glass-card {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
        transform: none !important;
        flex: 1;
        min-width: 0;
        animation: fadeInUp 0.8s ease-out both;
        border-radius: 16px;
        padding: 16px;
    }

    .card-main {
        width: auto !important;
        flex: 2;
        animation-delay: 0.2s;
    }

    .card-main .text-h5 {
        font-size: 1rem !important;
    }

    .card-main .text-body-2 {
        font-size: 0.7rem !important;
        line-height: 1.3;
    }

    .card-main .mb-4 {
        margin-bottom: 8px !important;
    }

    .card-main .mb-2 {
        margin-bottom: 4px !important;
    }

    .card-main v-icon,
    .card-main .icon-pulse {
        font-size: 32px !important;
    }

    .card-float-1,
    .card-float-2 {
        width: auto !important;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 8px;
        animation: none;
    }

    .card-float-1 {
        animation: fadeInUp 0.8s ease-out 0.4s both;
    }

    .card-float-2 {
        animation: fadeInUp 0.8s ease-out 0.6s both;
    }

    .card-float-1 .text-subtitle-2,
    .card-float-2 .text-subtitle-2 {
        font-size: 0.7rem !important;
        margin-top: 4px !important;
    }

    .card-float-1 v-icon,
    .card-float-2 v-icon {
        font-size: 24px !important;
    }

    /* Disable hover effects on mobile */
    .card-main:hover,
    .card-float-1:hover,
    .card-float-2:hover {
        transform: none !important;
    }

    /* Mobile icon animations */
    .icon-pulse,
    .icon-bounce,
    .icon-shake {
        animation-duration: 2s;
    }

    .hero-content {
        padding: 0 8px;
        text-align: center;
    }

    .glass-pill {
        padding: 6px 12px !important;
        font-size: 0.7rem !important;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-btn {
        width: 100%;
        min-width: auto;
        justify-content: center;
    }

    .hero-btn-primary:hover,
    .hero-btn-secondary:hover {
        transform: scale(1.02);
    }

    .text-shadow-sm {
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .hero-actions {
        align-items: stretch;
    }

    .btn-particles,
    .btn-shine {
        display: none;
    }
}

/* Extra small mobile optimization */
@media (max-width: 380px) {
    .hero-visual-3d {
        flex-direction: column;
        height: auto;
        gap: 8px;
    }

    .card-main,
    .card-float-1,
    .card-float-2 {
        flex: none;
        width: 100% !important;
    }

    .card-main {
        order: 1;
    }

    .card-float-1 {
        order: 2;
    }

    .card-float-2 {
        order: 3;
    }
}
</style>
