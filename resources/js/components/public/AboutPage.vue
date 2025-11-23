<template>
    <div class="about-page">
        <!-- Hero Section -->
        <section class="page-hero position-relative d-flex align-center justify-center text-center">
            <div class="hero-bg-image"></div>
            <div class="hero-overlay"></div>
            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-4 py-2 rounded-pill mb-6">
                            <v-icon icon="mdi-information-outline" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">WHO WE ARE</span>
                        </div>
                        <h1 class="text-h3 text-md-h2 font-weight-black text-white mb-6">Empowering Your World</h1>
                        <p class="text-h6 text-white opacity-80 mw-800 mx-auto font-weight-light">
                            We are dedicated to providing reliable, efficient, and sustainable power solutions for businesses and homes across the globe.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Our Story Section -->
        <section class="py-20 bg-surface">
            <v-container>
                <v-row align="center">
                    <v-col cols="12" md="6" class="pr-md-12">
                        <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">OUR STORY</div>
                        <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">Decades of Excellence in Power Solutions</h2>
                        <p class="text-body-1 text-medium-emphasis mb-6 lh-relaxed">
                            Founded in 2010, Micro Control Technology began with a simple mission: to ensure that no business or home has to suffer from power interruptions. What started as a small local service has grown into a leading provider of comprehensive power support solutions.
                        </p>
                        <p class="text-body-1 text-medium-emphasis mb-8 lh-relaxed">
                            We specialize in UPS systems, industrial backup power, battery maintenance, and smart energy management. Our team of certified engineers works tirelessly to deliver products and services that you can trust.
                        </p>
                        
                        <div class="d-flex gap-8 mt-8">
                            <div>
                                <div class="text-h3 font-weight-black text-primary mb-1">15+</div>
                                <div class="text-caption font-weight-bold text-uppercase text-medium-emphasis">Years Experience</div>
                            </div>
                            <div>
                                <div class="text-h3 font-weight-black text-primary mb-1">500+</div>
                                <div class="text-caption font-weight-bold text-uppercase text-medium-emphasis">Projects Completed</div>
                            </div>
                            <div>
                                <div class="text-h3 font-weight-black text-primary mb-1">50+</div>
                                <div class="text-caption font-weight-bold text-uppercase text-medium-emphasis">Team Members</div>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="6">
                        <div class="position-relative pl-md-8">
                            <div class="image-border-effect"></div>
                            <v-img 
                                src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                class="rounded-xl elevation-10 position-relative z-index-2"
                                cover
                                height="500"
                            ></v-img>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Core Values -->
        <section class="py-20 bg-grey-lighten-5 position-relative">
            <div class="bg-pattern-dots"></div>
            <v-container class="position-relative z-index-1">
                <div class="text-center mb-16">
                    <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">Our Core Values</h2>
                    <p class="text-h6 text-medium-emphasis mx-auto mw-600 font-weight-regular">
                        The principles that guide every project we undertake.
                    </p>
                </div>

                <v-row>
                    <v-col v-for="(value, i) in values" :key="i" cols="12" md="4">
                        <v-card class="value-card h-100 pa-8 rounded-xl elevation-2 border-thin text-center">
                            <div class="value-icon mb-6 mx-auto rounded-circle bg-white elevation-4 text-primary d-flex align-center justify-center">
                                <v-icon :icon="value.icon" size="36"></v-icon>
                            </div>
                            <h3 class="text-h5 font-weight-bold mb-4 text-grey-darken-3">{{ value.title }}</h3>
                            <p class="text-body-1 text-medium-emphasis lh-relaxed">{{ value.desc }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Team Section -->
        <section class="py-20 bg-surface">
            <v-container>
                <div class="text-center mb-16">
                    <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">OUR TEAM</div>
                    <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">Meet the Experts</h2>
                </div>

                <v-row justify="center">
                    <v-col v-for="(member, i) in team" :key="i" cols="12" sm="6" md="3">
                        <v-card class="team-card text-center elevation-0 bg-transparent">
                            <div class="team-img-wrapper mb-6 mx-auto rounded-circle overflow-hidden elevation-6 position-relative">
                                <v-img :src="member.image" cover height="100%"></v-img>
                                <div class="team-socials d-flex align-center justify-center gap-2">
                                    <v-btn icon="mdi-linkedin" size="small" color="white" variant="text"></v-btn>
                                    <v-btn icon="mdi-twitter" size="small" color="white" variant="text"></v-btn>
                                </div>
                            </div>
                            <h4 class="text-h6 font-weight-bold text-grey-darken-3">{{ member.name }}</h4>
                            <div class="text-subtitle-2 text-primary font-weight-bold text-uppercase tracking-wide">{{ member.role }}</div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>
    </div>
</template>

<script>
export default {
    name: 'AboutPage',
    data() {
        return {
            values: [
                {
                    title: 'Reliability',
                    desc: 'We deliver systems you can count on. Our backup solutions are tested rigorously to ensure 99.9% uptime.',
                    icon: 'mdi-shield-check'
                },
                {
                    title: 'Innovation',
                    desc: 'We stay ahead of the curve by adopting the latest technologies in power management and battery efficiency.',
                    icon: 'mdi-lightbulb-on-outline'
                },
                {
                    title: 'Customer First',
                    desc: 'Your satisfaction is our priority. We offer personalized solutions and 24/7 support for all our clients.',
                    icon: 'mdi-account-heart-outline'
                }
            ],
            team: [
                {
                    name: 'James Wilson',
                    role: 'CEO & Founder',
                    image: 'https://i.pravatar.cc/300?img=11'
                },
                {
                    name: 'Elena Rodriguez',
                    role: 'Chief Engineer',
                    image: 'https://i.pravatar.cc/300?img=5'
                },
                {
                    name: 'Michael Chang',
                    role: 'Operations Manager',
                    image: 'https://i.pravatar.cc/300?img=3'
                },
                {
                    name: 'Sarah Patel',
                    role: 'Head of Support',
                    image: 'https://i.pravatar.cc/300?img=9'
                }
            ]
        };
    }
};
</script>

<style scoped>
.page-hero {
    height: 500px;
    background: #0f172a;
    overflow: hidden;
}

.hero-bg-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('https://images.unsplash.com/photo-1487887235947-a955ef187fcc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');
    background-size: cover;
    background-position: center;
    filter: grayscale(100%);
    opacity: 0.4;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.95) 100%);
}

.glass-pill {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.image-border-effect {
    position: absolute;
    top: 20px;
    right: -20px;
    width: 100%;
    height: 100%;
    border: 2px solid #f59e0b; /* Amber */
    border-radius: 24px;
    z-index: 1;
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

.value-card {
    background: white;
    transition: transform 0.3s ease;
}

.value-card:hover {
    transform: translateY(-8px);
}

.value-icon {
    width: 80px;
    height: 80px;
}

.team-img-wrapper {
    width: 200px;
    height: 200px;
    border: 4px solid white;
    transition: transform 0.3s ease;
}

.team-card:hover .team-img-wrapper {
    transform: scale(1.05);
    border-color: #2563eb; /* Primary Blue */
}

.team-socials {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 10px;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.team-img-wrapper:hover .team-socials {
    transform: translateY(0);
}

.mw-600 { max-width: 600px; }
.mw-800 { max-width: 800px; }
.lh-relaxed { line-height: 1.6; }
.tracking-wide { letter-spacing: 0.05em; }
.tracking-widest { letter-spacing: 0.15em; }
.gap-8 { gap: 32px; }
.gap-2 { gap: 8px; }
.z-index-1 { z-index: 1; }
.z-index-2 { z-index: 2; }
</style>
