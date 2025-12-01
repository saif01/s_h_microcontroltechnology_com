<template>
    <section class="py-20 bg-grey-lighten-5 position-relative overflow-hidden">
        <div class="bg-pattern-dots"></div>
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <v-container class="position-relative z-index-1">
            <div class="text-center mb-16">
                <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest animate-fade-in">{{
                    overline || defaultOverline }}</div>
                <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3 animate-fade-in-up">{{ title || defaultTitle
                }}</h2>
                <div class="divider-center mx-auto mb-6 animate-scale-in"></div>
                <p class="text-h6 text-medium-emphasis mx-auto mw-800 font-weight-regular animate-fade-in-up"
                    v-if="subtitle">
                    {{ subtitle }}
                </p>
            </div>

            <v-row v-if="services.length > 0">
                <v-col v-for="(service, i) in services" :key="service.id" cols="12" md="4" class="service-card-wrapper">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="0"
                            class="h-100 service-card-modern rounded-xl transition-all"
                            :class="{ 'service-card-hover': isHovering }" :to="`/services/${service.slug}`"
                            @mouseenter="onCardHover($event)" @mousemove="onCardMove($event)"
                            @mouseleave="onCardLeave($event)">
                            <div class="card-gradient-border"></div>
                            <div class="card-shine"></div>
                            <div class="corner-accent corner-top-right"></div>
                            <div class="corner-accent corner-bottom-left"></div>
                            <div class="particle particle-1"></div>
                            <div class="particle particle-2"></div>
                            <div class="particle particle-3"></div>

                            <v-card-item class="pt-10 px-6 pb-6">
                                <div class="service-icon-wrapper mb-6">
                                    <div class="service-icon-box rounded-xl d-inline-flex pa-5 position-relative">
                                        <div class="icon-glow"></div>
                                        <div class="icon-sparkle sparkle-1"></div>
                                        <div class="icon-sparkle sparkle-2"></div>
                                        <div class="icon-sparkle sparkle-3"></div>
                                        <v-icon :icon="service.icon || getServiceIcon(i)" size="36"
                                            class="icon-main"></v-icon>
                                    </div>
                                </div>
                                <v-card-title
                                    class="text-h5 font-weight-bold mb-3 text-grey-darken-3 px-0 card-title-animated">
                                    <span v-for="(char, idx) in service.title.split('')" :key="idx" class="char-animate"
                                        :style="{ animationDelay: `${idx * 0.03}s`, '--char-index': idx }">
                                        {{ char === ' ' ? '\u00A0' : char }}
                                    </span>
                                </v-card-title>
                                <v-card-subtitle
                                    class="text-body-1 text-wrap mb-4 opacity-80 lh-relaxed px-0 text-grey-darken-1 subtitle-animated">
                                    {{ service.short_description }}
                                </v-card-subtitle>
                            </v-card-item>
                            <v-card-actions class="px-6 pb-8">
                                <div class="learn-more-btn">
                                    <span class="font-weight-bold link-text">
                                        Learn More
                                    </span>
                                    <div class="arrow-icon-wrapper">
                                        <v-icon icon="mdi-arrow-right" size="20" class="arrow-icon"></v-icon>
                                        <v-icon icon="mdi-arrow-right" size="20" class="arrow-icon-clone"></v-icon>
                                    </div>
                                </div>
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
    data() {
        return {
            defaultTitle: 'Power Support Solutions',
            defaultOverline: 'WHAT WE DO'
        };
    },
    props: {
        overline: {
            type: String,
            default: 'WHAT WE DO'
        },
        title: {
            type: String,
            default: 'Power Support Solutions'
        },
        subtitle: {
            type: String,
            default: 'We ensure uninterrupted operations for businesses and households with high-quality power products and services.'
        },
        services: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        getServiceIcon(index) {
            const icons = ['mdi-battery-charging-high', 'mdi-factory', 'mdi-home-lightning', 'mdi-battery-sync', 'mdi-lightning-bolt', 'mdi-tools'];
            return icons[index % icons.length];
        },
        onCardHover(event) {
            const card = event.currentTarget;
            card.style.transition = 'none';
        },
        onCardMove(event) {
            const card = event.currentTarget;
            const rect = card.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 15;
            const rotateY = (centerX - x) / 15;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-12px) scale(1.02)`;
        },
        onCardLeave(event) {
            const card = event.currentTarget;
            card.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
        }
    }
};
</script>

<style scoped>
/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
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

@keyframes scaleIn {
    from {
        transform: scaleX(0);
    }

    to {
        transform: scaleX(1);
    }
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0) rotate(0deg);
    }

    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}

@keyframes pulse {

    0%,
    100% {
        transform: scale(1);
        opacity: 0.6;
    }

    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
}

@keyframes shine {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(30deg);
    }

    100% {
        transform: translateX(100%) translateY(100%) rotate(30deg);
    }
}

@keyframes sparkle {

    0%,
    100% {
        opacity: 0;
        transform: scale(0) rotate(0deg);
    }

    50% {
        opacity: 1;
        transform: scale(1) rotate(180deg);
    }
}

@keyframes particle-float {
    0% {
        opacity: 0;
        transform: translateY(0) scale(0);
    }

    50% {
        opacity: 0.6;
    }

    100% {
        opacity: 0;
        transform: translateY(-100px) scale(1);
    }
}

@keyframes gradient-shift {
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

@keyframes text-reveal {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounce-in {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }

    50% {
        opacity: 1;
        transform: scale(1.05);
    }

    70% {
        opacity: 1;
        transform: scale(0.9);
    }

    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes char-bounce {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-4px);
    }
}

@keyframes arrow-slide {
    0% {
        transform: translateX(0);
        opacity: 1;
    }

    100% {
        transform: translateX(10px);
        opacity: 0;
    }
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
}

.animate-scale-in {
    animation: scaleIn 0.6s ease-out;
}

/* Floating Background Shapes */
.floating-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.05;
    animation: float 15s ease-in-out infinite;
}

.shape-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    top: 10%;
    left: 5%;
    animation-delay: 0s;
}

.shape-2 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    top: 60%;
    right: 10%;
    animation-delay: 5s;
}

.shape-3 {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, #10b981, #3b82f6);
    bottom: 20%;
    left: 15%;
    animation-delay: 10s;
}

/* Divider */
.divider-center {
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #d97706);
    border-radius: 2px;
}

/* Card Wrapper */
.service-card-wrapper {
    animation: fadeInUp 0.8s ease-out;
    animation-fill-mode: both;
}

.service-card-wrapper:nth-child(1) {
    animation-delay: 0.1s;
}

.service-card-wrapper:nth-child(2) {
    animation-delay: 0.2s;
}

.service-card-wrapper:nth-child(3) {
    animation-delay: 0.3s;
}

.service-card-wrapper:nth-child(4) {
    animation-delay: 0.4s;
}

.service-card-wrapper:nth-child(5) {
    animation-delay: 0.5s;
}

.service-card-wrapper:nth-child(6) {
    animation-delay: 0.6s;
}

/* Modern Service Card */
.service-card-modern {
    background: white;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    transform-style: preserve-3d;
    will-change: transform;
}

.service-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.02), rgba(245, 158, 11, 0.02));
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 1;
}

.service-card-modern:hover::before {
    opacity: 1;
}

.service-card-hover {
    box-shadow: 0 20px 60px rgba(37, 99, 235, 0.2);
    border-color: rgba(37, 99, 235, 0.1);
}

/* Card Shine Effect */
.card-shine {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg,
            transparent 30%,
            rgba(255, 255, 255, 0.3) 50%,
            transparent 70%);
    opacity: 0;
    pointer-events: none;
    z-index: 2;
}

.service-card-modern:hover .card-shine {
    animation: shine 1.5s ease-in-out;
}

/* Floating Particles */
.particle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: radial-gradient(circle, #2563eb, #7c3aed);
    border-radius: 50%;
    opacity: 0;
    pointer-events: none;
    z-index: 2;
}

.particle-1 {
    top: 20%;
    left: 20%;
}

.particle-2 {
    top: 60%;
    right: 25%;
}

.particle-3 {
    bottom: 30%;
    left: 30%;
}

.service-card-modern:hover .particle-1 {
    animation: particle-float 2s ease-out;
}

.service-card-modern:hover .particle-2 {
    animation: particle-float 2.3s ease-out 0.2s;
}

.service-card-modern:hover .particle-3 {
    animation: particle-float 2.5s ease-out 0.4s;
}

/* Gradient Border */
.card-gradient-border {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #2563eb, #7c3aed, #f59e0b, #2563eb);
    background-size: 200% 100%;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 3;
}

.service-card-modern:hover .card-gradient-border {
    opacity: 1;
    animation: gradient-shift 3s ease infinite;
}

/* Corner Accents */
.corner-accent {
    position: absolute;
    width: 40px;
    height: 40px;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.corner-top-right {
    top: -2px;
    right: -2px;
    border-top: 3px solid #2563eb;
    border-right: 3px solid #2563eb;
    border-top-right-radius: 16px;
}

.corner-bottom-left {
    bottom: -2px;
    left: -2px;
    border-bottom: 3px solid #f59e0b;
    border-left: 3px solid #f59e0b;
    border-bottom-left-radius: 16px;
}

.service-card-modern:hover .corner-accent {
    opacity: 1;
    width: 60px;
    height: 60px;
}

/* Icon Styling */
.service-icon-wrapper {
    display: inline-block;
    animation: bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.service-icon-box {
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    background-size: 200% 200%;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
    position: relative;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: visible;
}

.service-card-modern:hover .service-icon-box {
    transform: scale(1.15) rotate(8deg);
    box-shadow: 0 12px 32px rgba(37, 99, 235, 0.5);
    animation: gradient-shift 3s ease infinite;
}

.icon-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    border-radius: 16px;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.service-card-modern:hover .icon-glow {
    opacity: 1;
    animation: pulse 2s ease-in-out infinite;
}

/* Icon Sparkles */
.icon-sparkle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: white;
    clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
    opacity: 0;
    pointer-events: none;
}

.sparkle-1 {
    top: -5px;
    right: -5px;
}

.sparkle-2 {
    bottom: -5px;
    left: -5px;
}

.sparkle-3 {
    top: 50%;
    right: -10px;
    transform: translateY(-50%);
}

.service-card-modern:hover .sparkle-1 {
    animation: sparkle 1.5s ease-in-out infinite;
}

.service-card-modern:hover .sparkle-2 {
    animation: sparkle 1.5s ease-in-out 0.5s infinite;
}

.service-card-modern:hover .sparkle-3 {
    animation: sparkle 1.5s ease-in-out 1s infinite;
}

.icon-main {
    color: white;
    position: relative;
    z-index: 1;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.service-card-modern:hover .icon-main {
    transform: scale(1.1) rotate(-8deg);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
}

/* Text Animations */
.card-title-animated {
    overflow: visible;
}

.char-animate {
    display: inline-block;
    opacity: 0;
    animation: text-reveal 0.5s ease-out forwards;
    transition: transform 0.3s ease;
}

.subtitle-animated {
    opacity: 0;
    animation: fadeInUp 0.8s ease-out 0.3s forwards;
}

.service-card-modern:hover .char-animate {
    transform: translateY(-3px);
    transition-delay: calc(var(--char-index, 0) * 0.02s);
}

/* Learn More Button */
.learn-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    background-size: 200% 200%;
    border-radius: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.learn-more-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.learn-more-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
}

.service-card-modern:hover .learn-more-btn::before {
    left: 100%;
}

.service-card-modern:hover .learn-more-btn::after {
    width: 200px;
    height: 200px;
}

.service-card-modern:hover .learn-more-btn {
    transform: translateX(8px) scale(1.05);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
    animation: gradient-shift 2s ease infinite;
}

.link-text {
    color: white;
    font-size: 14px;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 1;
}

.arrow-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;
    overflow: hidden;
}

.service-card-modern:hover .arrow-icon-wrapper {
    transform: translateX(6px) rotate(90deg);
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.arrow-icon {
    color: white;
    transition: all 0.3s ease;
    position: absolute;
}

.arrow-icon-clone {
    color: white;
    position: absolute;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
}

.service-card-modern:hover .arrow-icon {
    animation: arrow-slide 0.6s ease forwards;
}

.service-card-modern:hover .arrow-icon-clone {
    opacity: 1;
    transform: translateX(0);
    transition-delay: 0.2s;
}

/* Responsive */
@media (max-width: 960px) {
    .service-card-modern {
        margin-bottom: 24px;
    }

    .shape {
        display: none;
    }
}

@media (max-width: 600px) {
    .service-card-modern:hover {
        transform: translateY(-6px);
    }

    .service-icon-box {
        padding: 16px !important;
    }

    .learn-more-btn {
        padding: 10px 20px;
    }
}
</style>
