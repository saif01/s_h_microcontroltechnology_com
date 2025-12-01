<template>
    <v-app-bar app class="main-app-bar px-md-12 transition-all" :class="{ 'scrolled-app-bar': isScrolled }" height="80"
        elevation="0">
        <div class="d-flex align-center w-100 position-relative z-index-2">
            <!-- Logo Area -->
            <router-link to="/" class="text-decoration-none d-flex align-center mr-8 logo-group">
                <div class="logo-box mr-3 elevation-4">
                    <v-img :src="logo || '/assets/logo/default.png'" alt="Logo" cover
                        class="logo-image rounded-logo"></v-img>
                </div>

                <div class="d-flex flex-column">
                    <span class="text-h6 font-weight-black text-grey-darken-4 lh-1 tracking-tight logo-text">{{
                        siteName.toUpperCase() }}</span>
                    <span class="text-caption font-weight-bold text-primary tracking-widest">{{ siteTagline
                        }}</span>
                </div>
            </router-link>

            <v-spacer></v-spacer>

            <!-- Desktop Navigation -->
            <div class="d-none d-md-flex align-center gap-8">
                <router-link v-for="item in menuItems" :key="item.id" :to="item.url"
                    :class="['nav-link', 'text-body-2', 'font-weight-bold', 'text-grey-darken-3', { 'active-nav-link': isActiveRoute(item.url) }]">
                    {{ item.label }}
                </router-link>
            </div>

            <v-spacer></v-spacer>

            <!-- CTA Button -->
            <div class="d-none d-md-flex">
                <v-btn color="primary" variant="flat" rounded="pill"
                    class="font-weight-bold px-6 text-capitalize elevation-3 hover-glow" :to="{ name: 'Contact' }">
                    Get a Quote
                </v-btn>
            </div>

            <!-- Mobile Menu Button -->
            <v-app-bar-nav-icon class="d-md-none ml-2 mobile-menu-btn" @click="handleDrawerToggle"
                aria-label="Toggle navigation menu"></v-app-bar-nav-icon>
        </div>

        <!-- Gradient Border Bottom (Visible on Scroll) -->
        <div class="app-bar-border"></div>
    </v-app-bar>
</template>

<script>
export default {
    name: 'AppBar',
    props: {
        isScrolled: {
            type: Boolean,
            default: false
        },
        siteName: {
            type: String,
            required: true
        },
        siteTagline: {
            type: String,
            required: true
        },
        menuItems: {
            type: Array,
            required: true
        },
        logo: {
            type: String,
            default: null
        }
    },
    emits: ['toggle-drawer', 'close-drawer'],
    data() {
        return {
            mobileBreakpoint: 960,
            isMobileViewport: typeof window !== 'undefined' ? window.innerWidth < 960 : false
        };
    },
    mounted() {
        this.checkViewport();
        window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        handleDrawerToggle() {
            // Only toggle the drawer in mobile/tablet view
            if (this.isMobileViewport) {
                this.$emit('toggle-drawer');
            }
        },
        handleResize() {
            const wasMobile = this.isMobileViewport;
            this.checkViewport();

            // If the viewport grows to desktop, make sure the mobile drawer is closed
            if (!this.isMobileViewport && wasMobile) {
                this.$emit('close-drawer');
            }
        },
        checkViewport() {
            if (typeof window !== 'undefined') {
                this.isMobileViewport = window.innerWidth < this.mobileBreakpoint;
            }
        },
        isActiveRoute(url) {
            const currentPath = this.$route.path;
            // For home route, only match exactly "/"
            if (url === '/') {
                return currentPath === '/';
            }
            // For other routes, match if current path starts with the route
            return currentPath.startsWith(url);
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
    background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.scrolled-app-bar .app-bar-border {
    opacity: 1;
}

/* logo-box styles moved to app.css */

.logo-icon {
    transition: transform 0.3s ease;
}

.logo-group:hover .logo-icon {
    transform: scale(1.1);
}

.logo-image {
    transition: transform 0.3s ease;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rounded-logo {
    border-radius: 8px !important;
    overflow: hidden;
}

.logo-group:hover .logo-image {
    transform: scale(1.05);
}

/* Utility classes moved to app.css */

.nav-link {
    text-decoration: none;
    position: relative;
    padding: 8px 0;
    transition: color 0.2s ease;
}

.nav-link:hover,
.active-nav-link {
    color: #2563eb !important;
    /* Primary Blue */
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

.nav-link:hover::after,
.active-nav-link::after {
    width: 100%;
}

.hover-glow {
    transition: all 0.3s ease;
}

.hover-glow:hover {
    box-shadow: 0 0 15px rgba(37, 99, 235, 0.4) !important;
    transform: translateY(-1px);
}

.mobile-menu-btn {
    color: #0f172a !important;
    border-radius: 12px;
}

.mobile-menu-btn:hover {
    background-color: rgba(15, 23, 42, 0.06);
}

/* Transitions */
.transition-all {
    transition: all 0.3s ease;
}

/* Responsive Styles */
@media (max-width: 960px) {
    .main-app-bar {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }

    .logo-text {
        font-size: 0.875rem !important;
    }

    .logo-box {
        width: 36px;
        height: 36px;
    }

    .logo-icon {
        font-size: 20px !important;
    }
}

@media (max-width: 600px) {
    .main-app-bar {
        height: 64px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
    }

    .logo-text {
        font-size: 0.75rem !important;
    }

    .logo-box {
        width: 32px;
        height: 32px;
        margin-right: 8px !important;
    }

    .logo-icon {
        font-size: 18px !important;
    }

    .mr-8 {
        margin-right: 12px !important;
    }

    .mobile-menu-btn {
        padding: 8px;
        margin-left: 6px !important;
    }
}
</style>
