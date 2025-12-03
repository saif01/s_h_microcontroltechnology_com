<template>
    <section class="cta-section py-16 py-md-20 position-relative overflow-hidden">
        <!-- Animated Background Layers -->
        <div class="cta-bg-base"></div>
        <div class="cta-gradient-animated"></div>
        <div class="cta-grid-pattern"></div>
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="floating-orb orb-3"></div>

        <v-container class="position-relative">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="9" xl="8">
                    <div class="cta-content text-center">
                        <!-- Badge -->
                        <div class="cta-badge d-inline-flex align-center px-4 py-2 rounded-pill mb-6">
                            <v-icon icon="mdi-lightning-bolt" size="18" class="mr-2 pulse-icon"></v-icon>
                            <span class="text-caption font-weight-bold text-uppercase">Limited Time Offer</span>
                        </div>

                        <!-- Title with Gradient -->
                        <h2 class="cta-title text-h4 text-sm-h3 text-md-h2 font-weight-black mb-6">
                            {{ title || 'Secure Your Power Today' }}
                        </h2>

                        <!-- Subtitle -->
                        <p class="cta-subtitle text-body-1 text-sm-h6 font-weight-regular mb-10 mx-auto"
                            v-if="subtitle">
                            {{ subtitle }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="cta-actions d-flex flex-column flex-sm-row justify-center align-center gap-4 mb-8">
                            <v-btn size="x-large" class="cta-btn-primary font-weight-bold px-8 px-sm-10 rounded-xl"
                                :to="primaryButtonLink || { name: 'Contact' }" elevation="0">
                                <v-icon icon="mdi-rocket-launch" class="mr-2"></v-icon>
                                {{ primaryButtonText || 'Get Started' }}
                                <v-icon icon="mdi-arrow-right" class="ml-2"></v-icon>
                            </v-btn>
                            <v-btn size="x-large" class="cta-btn-secondary font-weight-bold px-8 px-sm-10 rounded-xl"
                                :to="secondaryButtonLink || { name: 'Contact' }" elevation="0">
                                <v-icon icon="mdi-phone" class="mr-2"></v-icon>
                                {{ secondaryButtonText || 'Contact Support' }}
                            </v-btn>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="trust-indicators d-flex flex-wrap justify-center align-center gap-6 gap-sm-8">
                            <div class="trust-item d-flex align-center">
                                <v-icon icon="mdi-shield-check" color="success" size="24" class="mr-2"></v-icon>
                                <span class="text-body-2 text-white-80">100% Secure</span>
                            </div>
                            <div class="trust-item d-flex align-center">
                                <v-icon icon="mdi-clock-fast" color="warning" size="24" class="mr-2"></v-icon>
                                <span class="text-body-2 text-white-80">Quick Response</span>
                            </div>
                            <div class="trust-item d-flex align-center">
                                <v-icon icon="mdi-star" color="amber" size="24" class="mr-2"></v-icon>
                                <span class="text-body-2 text-white-80">Top Rated</span>
                            </div>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-container>

        <!-- Decorative Wave -->
        <div class="wave-bottom">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path
                    d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                    opacity=".25"></path>
                <path
                    d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                    opacity=".5"></path>
                <path
                    d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z">
                </path>
            </svg>
        </div>
    </section>
</template>

<script>
export default {
    name: 'CTASection',
    props: {
        title: {
            type: String,
            default: 'Secure Your Power Today'
        },
        subtitle: {
            type: String,
            default: "Don't let power outages disrupt your life or business. Contact us for reliable backup solutions."
        },
        primaryButtonText: {
            type: String,
            default: 'Get Started'
        },
        primaryButtonLink: {
            type: [String, Object],
            default: () => ({ name: 'Contact' })
        },
        secondaryButtonText: {
            type: String,
            default: 'Contact Support'
        },
        secondaryButtonLink: {
            type: [String, Object],
            default: () => ({ name: 'Contact' })
        }
    }
};
</script>

<style scoped>
/* Section Base */
.cta-section {
    position: relative;
    background: #0f172a;
    min-height: 500px;
}

/* Background Layers */
.cta-bg-base {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e3a5f 100%);
}

.cta-gradient-animated {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.25) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.2) 0%, transparent 60%);
    animation: gradientFlow 15s ease infinite;
    filter: blur(40px);
}

@keyframes gradientFlow {

    0%,
    100% {
        opacity: 0.8;
        transform: scale(1) rotate(0deg);
    }

    50% {
        opacity: 1;
        transform: scale(1.1) rotate(10deg);
    }
}

.cta-grid-pattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.5;
}

/* Floating Orbs */
.floating-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.4;
    animation: float 8s ease-in-out infinite;
}

.orb-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    top: -100px;
    right: -100px;
    animation-delay: 0s;
}

.orb-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #ec4899, #f59e0b);
    bottom: -80px;
    left: -80px;
    animation-delay: 2s;
}

.orb-3 {
    width: 250px;
    height: 250px;
    background: linear-gradient(135deg, #10b981, #3b82f6);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: 4s;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0) scale(1);
    }

    50% {
        transform: translateY(-30px) scale(1.1);
    }
}

/* Content Styling */
.cta-content {
    position: relative;
    z-index: 10;
}

.cta-badge {
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.3);
    color: #60a5fa;
    backdrop-filter: blur(10px);
    animation: fadeInDown 0.6s ease;
}

.pulse-icon {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.7;
        transform: scale(1.2);
    }
}

.cta-title {
    color: #ffffff;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #ddd6fe 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInUp 0.7s ease 0.2s backwards;
    line-height: 1.2;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.cta-subtitle {
    color: rgba(255, 255, 255, 0.85);
    max-width: 700px;
    line-height: 1.6;
    animation: fadeInUp 0.7s ease 0.4s backwards;
}

/* Button Styling */
.cta-actions {
    animation: fadeInUp 0.7s ease 0.6s backwards;
}

.cta-btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%) !important;
    color: white !important;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
}

.cta-btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.cta-btn-primary:hover::before {
    left: 100%;
}

.cta-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(59, 130, 246, 0.6);
}

.cta-btn-secondary {
    background: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.cta-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
}

/* Trust Indicators */
.trust-indicators {
    animation: fadeInUp 0.7s ease 0.8s backwards;
}

.trust-item {
    opacity: 0.9;
    transition: all 0.3s ease;
}

.trust-item:hover {
    opacity: 1;
    transform: scale(1.05);
}

.text-white-80 {
    color: rgba(255, 255, 255, 0.8);
}

/* Wave Bottom */
.wave-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
    transform: rotate(180deg);
}

.wave-bottom svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 80px;
}

.wave-bottom path {
    fill: #f8fafc;
}

/* Responsive Design */
@media (max-width: 960px) {
    .floating-orb {
        filter: blur(50px);
    }

    .orb-1 {
        width: 300px;
        height: 300px;
    }

    .orb-2 {
        width: 250px;
        height: 250px;
    }

    .orb-3 {
        width: 200px;
        height: 200px;
    }

    .wave-bottom svg {
        height: 60px;
    }
}

@media (max-width: 600px) {
    .cta-section {
        min-height: 400px;
    }

    .cta-title {
        font-size: 1.75rem !important;
        line-height: 1.3;
    }

    .cta-subtitle {
        font-size: 1rem !important;
    }

    .cta-badge {
        font-size: 0.7rem;
        padding: 6px 12px !important;
    }

    .cta-btn-primary,
    .cta-btn-secondary {
        width: 100%;
        max-width: 300px;
    }

    .trust-indicators {
        gap: 16px !important;
    }

    .trust-item {
        font-size: 0.875rem;
    }

    .floating-orb {
        filter: blur(40px);
        opacity: 0.3;
    }

    .orb-1,
    .orb-2 {
        width: 200px;
        height: 200px;
    }

    .orb-3 {
        display: none;
    }

    .wave-bottom svg {
        height: 40px;
    }

    .cta-gradient-animated {
        filter: blur(30px);
    }
}

@media (max-width: 400px) {
    .cta-title {
        font-size: 1.5rem !important;
    }

    .cta-subtitle {
        font-size: 0.9rem !important;
    }
}
</style>
