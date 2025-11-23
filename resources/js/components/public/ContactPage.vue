<template>
    <div class="contact-page">
        <!-- Hero Section -->
        <section class="page-hero position-relative d-flex align-center justify-center text-center">
            <div class="hero-bg-gradient"></div>
            <div class="hero-pattern"></div>
            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-4 py-2 rounded-pill mb-6">
                            <v-icon icon="mdi-message-text-outline" color="amber-accent-4" size="small" class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">GET IN TOUCH</span>
                        </div>
                        <h1 class="text-h3 text-md-h2 font-weight-black text-white mb-6">Contact Us</h1>
                        <p class="text-h6 text-white opacity-80 mw-600 mx-auto font-weight-light">
                            Have a question or need a quote? Our team is ready to help you with all your power support needs.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Contact Info Cards -->
        <section class="py-12 mt-n16 position-relative z-index-3">
            <v-container>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-card class="contact-card h-100 text-center pa-8 rounded-xl elevation-6" hover>
                            <div class="icon-box mb-6 mx-auto bg-blue-lighten-5 text-primary">
                                <v-icon icon="mdi-phone-in-talk" size="32"></v-icon>
                            </div>
                            <h3 class="text-h6 font-weight-bold mb-2 text-grey-darken-3">Call Us</h3>
                            <p class="text-body-2 text-medium-emphasis mb-4">Available 24/7 for support</p>
                            <a href="tel:+1234567890" class="text-h6 font-weight-bold text-primary text-decoration-none hover-link">
                                +1 (234) 567-890
                            </a>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-card class="contact-card h-100 text-center pa-8 rounded-xl elevation-6" hover>
                            <div class="icon-box mb-6 mx-auto bg-amber-lighten-5 text-amber-darken-3">
                                <v-icon icon="mdi-email-outline" size="32"></v-icon>
                            </div>
                            <h3 class="text-h6 font-weight-bold mb-2 text-grey-darken-3">Email Us</h3>
                            <p class="text-body-2 text-medium-emphasis mb-4">We'll respond within 24 hours</p>
                            <a href="mailto:info@powertech.com" class="text-h6 font-weight-bold text-primary text-decoration-none hover-link">
                                info@powertech.com
                            </a>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-card class="contact-card h-100 text-center pa-8 rounded-xl elevation-6" hover>
                            <div class="icon-box mb-6 mx-auto bg-green-lighten-5 text-green-darken-3">
                                <v-icon icon="mdi-map-marker-radius" size="32"></v-icon>
                            </div>
                            <h3 class="text-h6 font-weight-bold mb-2 text-grey-darken-3">Visit Us</h3>
                            <p class="text-body-2 text-medium-emphasis mb-4">Our Corporate Office</p>
                            <div class="text-body-1 font-weight-bold text-grey-darken-3">
                                123 Power Lane, Tech City
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>

        <!-- Form & Map Section -->
        <section class="py-20 bg-grey-lighten-5 position-relative overflow-hidden">
            <div class="bg-pattern-dots"></div>
            <v-container class="position-relative z-index-2">
                <v-row>
                    <!-- Contact Form -->
                    <v-col cols="12" md="6" class="mb-8 mb-md-0">
                        <div class="pr-md-8">
                            <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">SEND A MESSAGE</div>
                            <h2 class="text-h4 font-weight-bold mb-6 text-grey-darken-3">How Can We Help?</h2>
                            <p class="text-body-1 text-medium-emphasis mb-8">
                                Fill out the form below and our team will get back to you shortly.
                            </p>

                            <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.name"
                                            label="Your Name"
                                            variant="outlined"
                                            color="primary"
                                            bg-color="white"
                                            :rules="[v => !!v || 'Name is required']"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.email"
                                            label="Email Address"
                                            variant="outlined"
                                            color="primary"
                                            bg-color="white"
                                            :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'E-mail must be valid']"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="form.subject"
                                            label="Subject"
                                            variant="outlined"
                                            color="primary"
                                            bg-color="white"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea
                                            v-model="form.message"
                                            label="Message"
                                            variant="outlined"
                                            color="primary"
                                            bg-color="white"
                                            rows="4"
                                            :rules="[v => !!v || 'Message is required']"
                                        ></v-textarea>
                                    </v-col>
                                </v-row>
                                <v-btn
                                    type="submit"
                                    size="x-large"
                                    color="primary"
                                    rounded="pill"
                                    class="mt-4 px-8 font-weight-bold elevation-4 hover-lift"
                                    :loading="loading"
                                >
                                    Send Message
                                    <v-icon end icon="mdi-send" class="ml-2"></v-icon>
                                </v-btn>
                            </v-form>
                        </div>
                    </v-col>

                    <!-- Map -->
                    <v-col cols="12" md="6">
                        <v-card class="map-card h-100 rounded-xl overflow-hidden elevation-10 border-thin">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.1422937950147!2d-73.98731968482413!3d40.75889497932681!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1622646639943!5m2!1sen!2sus" 
                                width="100%" 
                                height="100%" 
                                style="border:0; min-height: 400px;" 
                                allowfullscreen="" 
                                loading="lazy"
                            ></iframe>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </section>
    </div>
</template>

<script>
export default {
    name: 'ContactPage',
    data() {
        return {
            valid: false,
            loading: false,
            form: {
                name: '',
                email: '',
                subject: '',
                message: ''
            }
        };
    },
    methods: {
        async submitForm() {
            if (!this.valid) return;
            
            this.loading = true;
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            // Reset form
            this.loading = false;
            this.form = { name: '', email: '', subject: '', message: '' };
            alert('Message sent successfully!'); // Replace with a proper toast notification in a real app
        }
    }
};
</script>

<style scoped>
.page-hero {
    height: 400px;
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

.contact-card {
    background: white;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contact-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.1) !important;
}

.icon-box {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.contact-card:hover .icon-box {
    transform: scale(1.1) rotate(5deg);
}

.hover-link {
    transition: color 0.2s ease;
}

.hover-link:hover {
    color: #f59e0b !important; /* Amber */
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

.map-card {
    border: 4px solid white;
}

.hover-lift {
    transition: transform 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.mw-600 { max-width: 600px; }
.tracking-wide { letter-spacing: 0.05em; }
.tracking-widest { letter-spacing: 0.15em; }
.z-index-2 { z-index: 2; }
.z-index-3 { z-index: 3; }
</style>
