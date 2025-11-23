<template>
    <v-app>
        <!-- Topbar -->
        <v-app-bar app class="glass-app-bar px-md-12" height="80" elevation="0">
            <div class="d-flex align-center w-100">
                <!-- Logo Area -->
                <router-link to="/" class="text-decoration-none d-flex align-center mr-8">
                    <div class="logo-box mr-3">
                        <v-icon icon="mdi-flash" color="amber-accent-4" size="28"></v-icon>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-h6 font-weight-black text-grey-darken-4 lh-1 tracking-tight">POWERTECH</span>
                        <span class="text-caption font-weight-bold text-primary tracking-widest">SOLUTIONS</span>
                    </div>
                </router-link>

                <v-spacer></v-spacer>

                <!-- Desktop Navigation -->
                <div class="d-none d-md-flex align-center gap-6">
                    <router-link 
                        v-for="item in menuItems" 
                        :key="item.id" 
                        :to="item.url" 
                        class="nav-link text-body-2 font-weight-bold text-grey-darken-2"
                        active-class="active-nav-link"
                    >
                        {{ item.label }}
                    </router-link>
                </div>

                <v-spacer></v-spacer>

                <!-- CTA Button -->
                <div class="d-none d-md-flex">
                    <v-btn 
                        color="primary" 
                        variant="flat" 
                        rounded="pill" 
                        class="font-weight-bold px-6 text-capitalize"
                        :to="{ name: 'Contact' }"
                    >
                        Get a Quote
                    </v-btn>
                </div>

                <!-- Mobile Menu Button -->
                <v-app-bar-nav-icon class="d-md-none ml-2" @click="drawer = !drawer"></v-app-bar-nav-icon>
            </div>
        </v-app-bar>

        <!-- Mobile Navigation Drawer -->
        <v-navigation-drawer v-model="drawer" location="right" temporary class="mobile-drawer">
            <div class="pa-6">
                <div class="d-flex align-center mb-8">
                    <div class="logo-box mr-3">
                        <v-icon icon="mdi-flash" color="amber-accent-4" size="24"></v-icon>
                    </div>
                    <span class="text-h6 font-weight-black text-grey-darken-4">POWERTECH</span>
                    <v-spacer></v-spacer>
                    <v-btn icon="mdi-close" variant="text" @click="drawer = false"></v-btn>
                </div>

                <v-list nav class="bg-transparent">
                    <v-list-item 
                        v-for="item in menuItems" 
                        :key="item.id" 
                        :to="item.url" 
                        class="mb-2 rounded-lg"
                        active-class="bg-primary-lighten-5 text-primary"
                    >
                        <v-list-item-title class="font-weight-bold">{{ item.label }}</v-list-item-title>
                    </v-list-item>
                </v-list>

                <div class="mt-8">
                    <v-btn block color="primary" size="large" rounded="lg" :to="{ name: 'Contact' }">
                        Get a Quote
                    </v-btn>
                </div>
            </div>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main class="bg-grey-lighten-5">
            <router-view />
        </v-main>

        <!-- Footer -->
        <v-footer class="bg-footer text-white pt-16 pb-8">
            <v-container>
                <v-row>
                    <!-- Column 1: About -->
                    <v-col cols="12" md="4" class="mb-8 mb-md-0 pr-md-12">
                        <div class="d-flex align-center mb-6">
                            <div class="logo-box-light mr-3">
                                <v-icon icon="mdi-flash" color="amber-accent-4" size="24"></v-icon>
                            </div>
                            <span class="text-h5 font-weight-black text-white">POWERTECH</span>
                        </div>
                        <p class="text-body-2 text-grey-lighten-1 mb-6 lh-relaxed">
                            Leading provider of technical power support solutions. We ensure your business stays powered with reliable UPS systems, backup generators, and professional maintenance.
                        </p>
                        <div class="d-flex gap-4">
                            <v-btn icon="mdi-facebook" variant="text" color="white" class="social-btn"></v-btn>
                            <v-btn icon="mdi-twitter" variant="text" color="white" class="social-btn"></v-btn>
                            <v-btn icon="mdi-linkedin" variant="text" color="white" class="social-btn"></v-btn>
                            <v-btn icon="mdi-instagram" variant="text" color="white" class="social-btn"></v-btn>
                        </div>
                    </v-col>

                    <!-- Column 2: Quick Links -->
                    <v-col cols="6" md="2" class="mb-8 mb-md-0">
                        <h4 class="text-subtitle-1 font-weight-bold mb-6 text-white">Quick Links</h4>
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
                        <h4 class="text-subtitle-1 font-weight-bold mb-6 text-white">Our Services</h4>
                        <ul class="list-unstyled">
                            <li class="mb-3"><a href="#" class="footer-link text-body-2 text-grey-lighten-1">UPS Installation</a></li>
                            <li class="mb-3"><a href="#" class="footer-link text-body-2 text-grey-lighten-1">Battery Replacement</a></li>
                            <li class="mb-3"><a href="#" class="footer-link text-body-2 text-grey-lighten-1">Industrial Backup</a></li>
                            <li class="mb-3"><a href="#" class="footer-link text-body-2 text-grey-lighten-1">24/7 Support</a></li>
                        </ul>
                    </v-col>

                    <!-- Column 4: Newsletter -->
                    <v-col cols="12" md="3">
                        <h4 class="text-subtitle-1 font-weight-bold mb-6 text-white">Newsletter</h4>
                        <p class="text-body-2 text-grey-lighten-1 mb-4">Subscribe to get the latest power tips and updates.</p>
                        <v-text-field
                            placeholder="Enter your email"
                            variant="outlined"
                            density="compact"
                            bg-color="rgba(255,255,255,0.05)"
                            color="white"
                            hide-details
                            class="mb-3 footer-input"
                        >
                            <template v-slot:append-inner>
                                <v-btn icon="mdi-send" size="small" color="primary" variant="text"></v-btn>
                            </template>
                        </v-text-field>
                    </v-col>
                </v-row>

                <v-divider class="my-8 border-opacity-10"></v-divider>

                <div class="d-flex flex-column flex-md-row justify-space-between align-center text-caption text-grey-darken-1">
                    <div class="mb-4 mb-md-0">
                        © {{ new Date().getFullYear() }} PowerTech Solutions. All rights reserved.
                    </div>
                    <div class="d-flex gap-6">
                        <a href="#" class="footer-link-sm">Privacy Policy</a>
                        <a href="#" class="footer-link-sm">Terms of Service</a>
                        <a href="#" class="footer-link-sm">Sitemap</a>
                    </div>
                </div>
            </v-container>
        </v-footer>
    </v-app>
</template>

<script>
import axios from 'axios';

export default {
    name: 'PublicLayout',
    data() {
        return {
            drawer: false,
            siteName: 'PowerTech Solutions',
            menuItems: [
                { id: 1, label: 'Home', url: '/' },
                { id: 2, label: 'Services', url: '/services' },
                { id: 3, label: 'Products', url: '/products' },
                { id: 4, label: 'About Us', url: '/about' },
                { id: 5, label: 'Contact', url: '/contact' },
            ]
        };
    },
    async mounted() {
        await this.loadSettings();
    },
    methods: {
        async loadSettings() {
            try {
                const response = await axios.get('/api/public/settings');
                if (response.data.site_name) {
                    this.siteName = response.data.site_name;
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }
        }
    }
};
</script>

<style scoped>
/* Topbar Styles */
.glass-app-bar {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.logo-box {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.lh-1 { line-height: 1; }
.tracking-tight { letter-spacing: -0.025em; }
.tracking-widest { letter-spacing: 0.1em; }
.gap-6 { gap: 24px; }

.nav-link {
    text-decoration: none;
    position: relative;
    transition: color 0.2s ease;
}

.nav-link:hover, .active-nav-link {
    color: rgb(var(--v-theme-primary)) !important;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: rgb(var(--v-theme-primary));
    transition: width 0.3s ease;
}

.nav-link:hover::after, .active-nav-link::after {
    width: 100%;
}

/* Footer Styles */
.bg-footer {
    background: #0f172a; /* Slate 900 */
}

.logo-box-light {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lh-relaxed { line-height: 1.6; }
.gap-4 { gap: 16px; }

.social-btn {
    opacity: 0.7;
    transition: all 0.2s ease;
}

.social-btn:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.1);
}

.list-unstyled {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-link {
    text-decoration: none;
    transition: color 0.2s ease;
    display: inline-block;
}

.footer-link:hover {
    color: white !important;
    transform: translateX(4px);
}

.footer-input :deep(.v-field__outline__start),
.footer-input :deep(.v-field__outline__end) {
    border-color: rgba(255,255,255,0.1) !important;
}

.footer-link-sm {
    text-decoration: none;
    color: inherit;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.footer-link-sm:hover {
    color: white;
}
</style>
