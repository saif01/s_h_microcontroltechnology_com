<template>
    <v-app>
        <!-- Topbar -->
        <v-app-bar 
            app 
            class="main-app-bar px-md-12 transition-all" 
            :class="{ 'scrolled-app-bar': isScrolled }"
            height="80" 
            elevation="0"
        >
            <div class="d-flex align-center w-100 position-relative z-index-2">
                <!-- Logo Area -->
                <router-link to="/" class="text-decoration-none d-flex align-center mr-8 logo-group">
                    <div class="logo-box mr-3 elevation-4">
                        <v-icon icon="mdi-flash" color="amber-accent-4" size="28" class="logo-icon"></v-icon>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-h6 font-weight-black text-grey-darken-4 lh-1 tracking-tight logo-text">MICRO CONTROL</span>
                        <span class="text-caption font-weight-bold text-primary tracking-widest">TECHNOLOGY</span>
                    </div>
                </router-link>

                <v-spacer></v-spacer>

                <!-- Desktop Navigation -->
                <div class="d-none d-md-flex align-center gap-8">
                    <router-link 
                        v-for="item in menuItems" 
                        :key="item.id" 
                        :to="item.url" 
                        class="nav-link text-body-2 font-weight-bold text-grey-darken-3"
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
                        class="font-weight-bold px-6 text-capitalize elevation-3 hover-glow"
                        :to="{ name: 'Contact' }"
                    >
                        Get a Quote
                    </v-btn>
                </div>

                <!-- Mobile Menu Button -->
                <v-app-bar-nav-icon class="d-md-none ml-2" @click="drawer = !drawer"></v-app-bar-nav-icon>
            </div>
            
            <!-- Gradient Border Bottom (Visible on Scroll) -->
            <div class="app-bar-border"></div>
        </v-app-bar>

        <!-- Mobile Navigation Drawer -->
        <v-navigation-drawer v-model="drawer" location="right" temporary class="mobile-drawer">
            <div class="pa-6">
                <div class="d-flex align-center mb-8">
                    <div class="logo-box mr-3">
                        <v-icon icon="mdi-flash" color="amber-accent-4" size="24"></v-icon>
                    </div>
                    <span class="text-h6 font-weight-black text-grey-darken-4">MICRO CONTROL</span>
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
                    <v-btn block color="primary" size="large" rounded="lg" :to="{ name: 'Contact' }" class="elevation-4">
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
        <v-footer class="bg-footer text-white pt-20 pb-10 position-relative overflow-hidden">
            <!-- Footer Background Effects -->
            <div class="footer-pattern"></div>
            <div class="footer-glow"></div>
            
            <v-container class="position-relative z-index-2">
                <v-row>
                    <!-- Column 1: About -->
                    <v-col cols="12" md="4" class="mb-10 mb-md-0 pr-md-12">
                        <div class="d-flex align-center mb-6">
                            <div class="logo-box-light mr-3 elevation-10">
                                <v-icon icon="mdi-flash" color="amber-accent-4" size="24"></v-icon>
                            </div>
                            <span class="text-h5 font-weight-black text-white tracking-tight">MICRO CONTROL</span>
                        </div>
                        <p class="text-body-2 text-grey-lighten-1 mb-8 lh-relaxed opacity-80">
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
                            <li class="mb-3"><router-link to="/services/ups-installation" class="footer-link text-body-2 text-grey-lighten-1">UPS Installation</router-link></li>
                            <li class="mb-3"><router-link to="/services/battery-replacement" class="footer-link text-body-2 text-grey-lighten-1">Battery Replacement</router-link></li>
                            <li class="mb-3"><router-link to="/services/industrial-backup" class="footer-link text-body-2 text-grey-lighten-1">Industrial Backup</router-link></li>
                            <li class="mb-3"><router-link to="/services/support" class="footer-link text-body-2 text-grey-lighten-1">24/7 Support</router-link></li>
                        </ul>
                    </v-col>

                    <!-- Column 4: Newsletter -->
                    <v-col cols="12" md="3">
                        <h4 class="text-subtitle-1 font-weight-bold mb-6 text-white position-relative d-inline-block">
                            Newsletter
                            <div class="heading-underline"></div>
                        </h4>
                        <p class="text-body-2 text-grey-lighten-1 mb-6 opacity-80">Subscribe to get the latest power tips and updates.</p>
                        <v-text-field
                            placeholder="Enter your email"
                            variant="outlined"
                            density="comfortable"
                            bg-color="rgba(255,255,255,0.05)"
                            color="amber-accent-4"
                            hide-details
                            class="mb-3 footer-input rounded-lg"
                        >
                            <template v-slot:append-inner>
                                <v-btn icon="mdi-send" size="small" color="amber-accent-4" variant="text" class="mr-n2"></v-btn>
                            </template>
                        </v-text-field>
                    </v-col>
                </v-row>

                <v-divider class="my-10 border-opacity-10"></v-divider>

                <div class="d-flex flex-column flex-md-row justify-space-between align-center text-caption text-grey-darken-1">
                    <div class="mb-4 mb-md-0">
                        © {{ new Date().getFullYear() }} Micro Control Technology. All rights reserved.
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
            isScrolled: false,
            siteName: 'Micro Control Technology',
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
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
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
        },
        handleScroll() {
            this.isScrolled = window.scrollY > 20;
        }
    }
};
</script>

<style scoped>
/* Topbar Styles */
.main-app-bar {
    background: rgba(255, 255, 255, 0.0) !important;
    backdrop-filter: blur(0px);
    border-bottom: 1px solid transparent;
}

.scrolled-app-bar {
    background: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(16px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05) !important;
}

.app-bar-border {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.scrolled-app-bar .app-bar-border {
    opacity: 1;
}

.logo-box {
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.logo-group:hover .logo-box {
    transform: rotate(10deg) scale(1.05);
}

.logo-icon {
    transition: transform 0.3s ease;
}

.logo-group:hover .logo-icon {
    transform: scale(1.1);
}

.lh-1 { line-height: 1; }
.tracking-tight { letter-spacing: -0.025em; }
.tracking-widest { letter-spacing: 0.1em; }
.gap-8 { gap: 32px; }

.nav-link {
    text-decoration: none;
    position: relative;
    padding: 8px 0;
    transition: color 0.2s ease;
}

.nav-link:hover, .active-nav-link {
    color: #2563eb !important; /* Primary Blue */
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #2563eb, #f59e0b);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateX(-50%);
    border-radius: 2px;
}

.nav-link:hover::after, .active-nav-link::after {
    width: 100%;
}

.hover-glow {
    transition: all 0.3s ease;
}

.hover-glow:hover {
    box-shadow: 0 0 15px rgba(37, 99, 235, 0.4) !important;
    transform: translateY(-1px);
}

/* Footer Styles */
.bg-footer {
    background: #0f172a; /* Slate 900 */
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

.logo-box-light {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lh-relaxed { line-height: 1.6; }
.gap-4 { gap: 16px; }

.social-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.social-btn:hover {
    background: #2563eb;
    border-color: #2563eb;
    transform: translateY(-3px);
}

.list-unstyled {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-link {
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-block;
    position: relative;
}

.footer-link:hover {
    color: #f59e0b !important; /* Amber */
    transform: translateX(4px);
}

.heading-underline {
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 40px;
    height: 3px;
    background: #2563eb;
    border-radius: 2px;
}

.footer-input :deep(.v-field__outline__start),
.footer-input :deep(.v-field__outline__end) {
    border-color: rgba(255,255,255,0.1) !important;
}

.footer-input :deep(.v-field--focused .v-field__outline__start),
.footer-input :deep(.v-field--focused .v-field__outline__end) {
    border-color: #f59e0b !important;
}

.footer-link-sm {
    text-decoration: none;
    color: inherit;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.footer-link-sm:hover {
    color: #f59e0b;
}

/* Transitions */
.transition-all {
    transition: all 0.3s ease;
}
</style>
