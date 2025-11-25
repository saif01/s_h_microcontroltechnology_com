<template>
    <v-footer class="bg-footer text-white pt-20 pb-10 position-relative overflow-hidden">
        <!-- Footer Background Effects -->
        <div class="footer-pattern"></div>
        <div class="footer-glow"></div>

        <v-container class="position-relative z-index-2">
            <v-row>
                <!-- Column 1: About -->
                <v-col cols="12" md="4" class="mb-10 mb-md-0 pr-md-12">
                    <div class="d-flex align-center mb-6">
                        <div class="logo-box-light mr-3 elevation-10" v-if="logo">
                            <v-img :src="logo" alt="Logo" cover class="footer-logo-image rounded-logo"></v-img>
                        </div>
                        <div class="logo-box-light mr-3 elevation-10" v-else>
                            <v-icon icon="mdi-flash" color="amber-accent-4" size="24"></v-icon>
                        </div>
                        <span class="text-h5 font-weight-black text-white tracking-tight">{{ siteName.toUpperCase()
                        }}</span>
                    </div>
                    <p class="text-body-2 text-grey-lighten-1 mb-8 lh-relaxed opacity-80">
                        {{ footerDescription }}
                    </p>
                    <div class="d-flex gap-4">
                        <v-btn v-if="hasSocialLink('facebook')" :href="settings.facebook_url" target="_blank"
                            icon="mdi-facebook" variant="text" color="white" class="social-btn"></v-btn>
                        <v-btn v-if="hasSocialLink('twitter')" :href="settings.twitter_url" target="_blank"
                            icon="mdi-twitter" variant="text" color="white" class="social-btn"></v-btn>
                        <v-btn v-if="hasSocialLink('linkedin')" :href="settings.linkedin_url" target="_blank"
                            icon="mdi-linkedin" variant="text" color="white" class="social-btn"></v-btn>
                        <v-btn v-if="hasSocialLink('instagram')" :href="settings.instagram_url" target="_blank"
                            icon="mdi-instagram" variant="text" color="white" class="social-btn"></v-btn>
                    </div>
                </v-col>

                <!-- Column 2: Quick Links -->
                <v-col cols="6" md="2" class="mb-8 mb-md-0">
                    <h4 class="text-subtitle-1 font-weight-bold mb-6 text-white position-relative d-inline-block">
                        Quick Links
                        <div class="heading-underline"></div>
                    </h4>
                    <ul class="list-unstyled">
                        <li v-for="item in menuItems" :key="item.id" class="mb-3">
                            <router-link :to="item.url" class="footer-link text-body-2 text-grey-lighten-1">
                                {{ item.label }}
                            </router-link>
                        </li>
                    </ul>
                </v-col>

                <!-- Column 3: Services -->
                <v-col cols="6" md="3" class="mb-8 mb-md-0">
                    <h4 class="text-subtitle-1 font-weight-bold mb-6 text-white position-relative d-inline-block">
                        Our Services
                        <div class="heading-underline"></div>
                    </h4>
                    <ul class="list-unstyled">
                        <li class="mb-3"><router-link to="/services/ups-installation"
                                class="footer-link text-body-2 text-grey-lighten-1">UPS Installation</router-link>
                        </li>
                        <li class="mb-3"><router-link to="/services/battery-replacement"
                                class="footer-link text-body-2 text-grey-lighten-1">Battery
                                Replacement</router-link></li>
                        <li class="mb-3"><router-link to="/services/industrial-backup"
                                class="footer-link text-body-2 text-grey-lighten-1">Industrial Backup</router-link>
                        </li>
                        <li class="mb-3"><router-link to="/services/support"
                                class="footer-link text-body-2 text-grey-lighten-1">24/7 Support</router-link></li>
                    </ul>
                </v-col>

                <!-- Column 4: Newsletter -->
                <v-col cols="12" md="3">
                    <h4 class="text-subtitle-1 font-weight-bold mb-6 text-white position-relative d-inline-block">
                        Newsletter
                        <div class="heading-underline"></div>
                    </h4>
                    <p class="text-body-2 text-grey-lighten-1 mb-6 opacity-80">{{ newsletterDescription }}</p>
                    <v-form ref="newsletterForm" @submit.prevent="subscribeNewsletter">
                        <v-text-field v-model="newsletterEmail" placeholder="Enter your email" variant="outlined"
                            density="comfortable" bg-color="rgba(255,255,255,0.05)" color="amber-accent-4"
                            :rules="emailRules" :loading="newsletterLoading" :disabled="newsletterLoading"
                            class="mb-3 footer-input rounded-lg" hide-details="auto" @keyup.enter="subscribeNewsletter">
                            <template v-slot:append-inner>
                                <v-btn icon="mdi-send" size="small" color="amber-accent-4" variant="text" class="mr-n2"
                                    :loading="newsletterLoading" :disabled="newsletterLoading"
                                    @click="subscribeNewsletter"></v-btn>
                            </template>
                        </v-text-field>
                    </v-form>
                    <v-alert v-if="newsletterMessage" :type="newsletterMessageType" density="compact"
                        class="mt-2 newsletter-alert" variant="tonal" :closable="true"
                        @click:close="newsletterMessage = ''">
                        {{ newsletterMessage }}
                    </v-alert>
                </v-col>
            </v-row>

            <v-divider class="my-10 border-opacity-10"></v-divider>

            <div
                class="d-flex flex-column flex-md-row justify-space-between align-center text-caption text-grey-darken-1">
                <div class="mb-4 mb-md-0">
                    © {{ new Date().getFullYear() }} {{ siteName }}. All rights reserved.
                </div>
                <div class="d-flex gap-6">
                    <a href="#" class="footer-link-sm">Privacy Policy</a>
                    <a href="#" class="footer-link-sm">Terms of Service</a>
                    <a href="#" class="footer-link-sm">Sitemap</a>
                </div>
            </div>
        </v-container>
    </v-footer>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Footer',
    props: {
        siteName: {
            type: String,
            required: true
        },
        footerDescription: {
            type: String,
            required: true
        },
        menuItems: {
            type: Array,
            required: true
        },
        settings: {
            type: Object,
            required: true
        },
        logo: {
            type: String,
            default: null
        },
        newsletterDescription: {
            type: String,
            default: 'Subscribe to get the latest power tips and updates.'
        }
    },
    data() {
        return {
            newsletterEmail: '',
            newsletterLoading: false,
            newsletterMessage: '',
            newsletterMessageType: 'success',
            emailRules: [
                v => !!v || 'Email is required',
                v => /.+@.+\..+/.test(v) || 'Email must be valid'
            ]
        };
    },
    methods: {
        hasSocialLink(platform) {
            return this.settings[`${platform}_url`] && this.settings[`${platform}_url`].trim() !== '';
        },
        async subscribeNewsletter(event) {
            // Prevent default form submission if called from form
            if (event) {
                event.preventDefault();
            }

            // Validate email manually if form ref is not available
            if (!this.newsletterEmail || !this.newsletterEmail.trim()) {
                this.newsletterMessage = 'Email is required';
                this.newsletterMessageType = 'error';
                return;
            }

            // Validate email format
            const emailRegex = /.+@.+\..+/;
            if (!emailRegex.test(this.newsletterEmail)) {
                this.newsletterMessage = 'Please enter a valid email address';
                this.newsletterMessageType = 'error';
                return;
            }

            // If form ref exists, validate it
            if (this.$refs.newsletterForm) {
                const { valid } = await this.$refs.newsletterForm.validate();
                if (!valid) {
                    return;
                }
            }

            this.newsletterLoading = true;
            this.newsletterMessage = '';

            try {
                const response = await axios.post('/api/openapi/newsletter/subscribe', {
                    email: this.newsletterEmail.trim()
                });

                this.newsletterMessage = response.data.message || 'Thank you for subscribing!';
                this.newsletterMessageType = 'success';
                this.newsletterEmail = '';

                // Reset form if ref exists
                if (this.$refs.newsletterForm) {
                    this.$refs.newsletterForm.reset();
                }

                // Clear message after 5 seconds
                setTimeout(() => {
                    this.newsletterMessage = '';
                }, 5000);
            } catch (error) {
                console.error('Error subscribing to newsletter:', error);
                if (error.response && error.response.data) {
                    if (error.response.data.message) {
                        this.newsletterMessage = error.response.data.message;
                    } else if (error.response.data.errors) {
                        const errors = error.response.data.errors;
                        const errorMessages = Object.values(errors).flat();
                        this.newsletterMessage = errorMessages.join(', ') || 'Failed to subscribe. Please try again.';
                    } else {
                        this.newsletterMessage = 'Failed to subscribe. Please try again.';
                    }
                } else {
                    this.newsletterMessage = 'An error occurred. Please try again later.';
                }
                this.newsletterMessageType = 'error';
            } finally {
                this.newsletterLoading = false;
            }
        }
    }
};
</script>

<style scoped>
/* Footer Styles */
.bg-footer {
    background: #0f172a;
    /* Slate 900 */
}

.footer-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    opacity: 0.5;
    z-index: 1;
}

.footer-glow {
    position: absolute;
    top: -200px;
    left: -200px;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
    filter: blur(60px);
    z-index: 1;
}

/* Common styles (logo-box-light, social-btn, list-unstyled, footer-link, heading-underline, footer-input, footer-link-sm) moved to app.css */

.footer-logo-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rounded-logo {
    border-radius: 8px !important;
    overflow: hidden;
}

.newsletter-alert {
    background-color: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px);
}

.newsletter-alert.v-alert--success {
    border-left: 3px solid #4caf50;
}

.newsletter-alert.v-alert--error {
    border-left: 3px solid #f44336;
}

/* Responsive Styles */
@media (max-width: 960px) {
    .bg-footer {
        padding-top: 48px !important;
        padding-bottom: 32px !important;
    }

    .pr-md-12 {
        padding-right: 0 !important;
    }

    .mb-10 {
        margin-bottom: 32px !important;
    }

    .mb-8 {
        margin-bottom: 24px !important;
    }

    .gap-6 {
        gap: 16px;
    }
}

@media (max-width: 600px) {
    .bg-footer {
        padding-top: 40px !important;
        padding-bottom: 24px !important;
    }

    .logo-box-light {
        width: 32px;
        height: 32px;
    }

    .logo-box-light .v-icon {
        font-size: 18px !important;
    }

    .text-h5 {
        font-size: 1rem !important;
    }

    .mb-6 {
        margin-bottom: 16px !important;
    }

    .mb-8 {
        margin-bottom: 16px !important;
    }

    .gap-4 {
        gap: 12px;
    }

    .gap-6 {
        gap: 12px;
        flex-wrap: wrap;
    }

    .footer-link-sm {
        font-size: 0.75rem;
    }

    .my-10 {
        margin-top: 24px !important;
        margin-bottom: 24px !important;
    }
}
</style>
