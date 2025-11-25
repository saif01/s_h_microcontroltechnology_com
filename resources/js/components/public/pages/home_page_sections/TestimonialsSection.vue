<template>
    <section class="py-24 bg-dark-gradient position-relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="testimonial-bg-shape shape-t1"></div>
        <div class="testimonial-bg-shape shape-t2"></div>
        <div class="huge-quote-mark">"</div>

        <v-container class="position-relative z-index-1">
            <div class="text-center mb-16">
                <div class="glass-pill-sm d-inline-flex align-center px-4 py-1 rounded-pill mb-4">
                    <span class="text-caption font-weight-bold text-white tracking-widest">{{ overline || 'TESTIMONIALS'
                        }}</span>
                </div>
                <h2 class="text-h3 font-weight-bold mb-6 text-white">{{ title || 'Client Success Stories' }}</h2>
                <p class="text-h6 text-white opacity-70 mx-auto mw-600 font-weight-light" v-if="subtitle">
                    {{ subtitle }}
                </p>
            </div>

            <v-carousel cycle height="auto" hide-delimiters show-arrows="hover" interval="8000"
                class="testimonial-carousel pb-12">
                <template v-slot:prev="{ props }">
                    <v-btn variant="outlined" color="white" icon="mdi-chevron-left" @click="props.onClick"
                        class="carousel-nav-btn"></v-btn>
                </template>
                <template v-slot:next="{ props }">
                    <v-btn variant="outlined" color="white" icon="mdi-chevron-right" @click="props.onClick"
                        class="carousel-nav-btn"></v-btn>
                </template>

                <v-carousel-item v-for="(testimonial, i) in testimonials" :key="i">
                    <v-row justify="center">
                        <v-col cols="12" md="9" lg="8">
                            <v-card
                                class="testimonial-glass-card text-center pa-10 pa-md-16 rounded-xl elevation-0 position-relative">
                                <div class="d-flex flex-column align-center position-relative z-index-2">
                                    <div class="avatar-glow mb-8">
                                        <v-avatar size="90" class="elevation-4 border-2-white">
                                            <v-img :src="testimonial.avatar" cover></v-img>
                                        </v-avatar>
                                    </div>

                                    <v-icon icon="mdi-format-quote-open" size="48" color="white"
                                        class="mb-6 opacity-50"></v-icon>

                                    <p
                                        class="text-h5 text-md-h4 font-weight-light font-italic mb-8 text-white lh-relaxed testimonial-text">
                                        "{{ testimonial.text }}"
                                    </p>

                                    <div class="text-center">
                                        <div class="text-h6 font-weight-bold text-white mb-2">{{ testimonial.name }}
                                        </div>
                                        <v-chip
                                            class="testimonial-role-chip text-subtitle-2 font-weight-bold text-uppercase tracking-wide"
                                            variant="outlined" color="white" size="small">
                                            {{ testimonial.role }}
                                        </v-chip>
                                    </div>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-carousel-item>
            </v-carousel>
        </v-container>
    </section>
</template>

<script>
export default {
    name: 'TestimonialsSection',
    props: {
        overline: {
            type: String,
            default: 'TESTIMONIALS'
        },
        title: {
            type: String,
            default: 'Client Success Stories'
        },
        subtitle: {
            type: String,
            default: "See how we've helped businesses and households stay powered up and protected."
        },
        testimonials: {
            type: Array,
            default: () => [
                {
                    text: "Their UPS installation saved our manufacturing line during a critical power outage. Highly recommended for industrial needs.",
                    name: "Robert Fox",
                    role: "Plant Manager, InduTech",
                    avatar: "https://i.pravatar.cc/150?img=12"
                },
                {
                    text: "Excellent home backup solution. The installation was professional, and the system works seamlessly.",
                    name: "Sarah Jenkins",
                    role: "Homeowner",
                    avatar: "https://i.pravatar.cc/150?img=5"
                },
                {
                    text: "We rely on them for all our battery maintenance. Responsive, knowledgeable, and efficient team.",
                    name: "David Miller",
                    role: "IT Director, NetCorp",
                    avatar: "https://i.pravatar.cc/150?img=3"
                }
            ]
        }
    }
};
</script>

<style scoped>
.bg-dark-gradient {
    background: linear-gradient(135deg, #0f172a 0%, #172554 100%);
}

.testimonial-glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.avatar-glow {
    position: relative;
}

.avatar-glow::after {
    content: '';
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    border-radius: 50%;
    background: linear-gradient(45deg, #3b82f6, #f59e0b);
    z-index: -1;
    opacity: 0.7;
}

.testimonial-text {
    font-family: 'Georgia', serif;
}

.huge-quote-mark {
    position: absolute;
    top: -50px;
    right: 10%;
    font-size: 400px;
    line-height: 1;
    font-family: 'Georgia', serif;
    color: rgba(255, 255, 255, 0.03);
    pointer-events: none;
    z-index: 0;
}

.testimonial-bg-shape {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.3;
}

.shape-t1 {
    width: 400px;
    height: 400px;
    background: #3b82f6;
    top: -100px;
    left: -100px;
}

.shape-t2 {
    width: 300px;
    height: 300px;
    background: #f59e0b;
    bottom: -50px;
    right: -50px;
}

.carousel-nav-btn {
    opacity: 0.5;
    transition: all 0.3s ease;
}

.carousel-nav-btn:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.1);
}

.border-2-white {
    border: 2px solid white !important;
}

.testimonial-role-chip {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: white !important;
    opacity: 0.9;
}

@media (max-width: 960px) {
    .huge-quote-mark {
        font-size: 200px;
        top: 0;
    }
}

@media (max-width: 600px) {
    .huge-quote-mark {
        font-size: 120px;
    }

    .testimonial-glass-card {
        padding: 20px !important;
    }
}
</style>
