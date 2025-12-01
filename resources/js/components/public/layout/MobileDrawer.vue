<template>
    <v-navigation-drawer v-model="localDrawer" location="right" temporary
        :class="['mobile-drawer', { 'mobile-drawer--open': localDrawer }]" @keydown.esc="closeDrawer">
        <div class="pa-6">
            <div class="d-flex align-center mb-8">
                <div class="logo-box mr-3">
                    <v-img :src="logo || '/assets/logo/default.png'" alt="Logo" cover
                        class="logo-image rounded-logo"></v-img>
                </div>

                <span class="text-h6 font-weight-black text-grey-darken-4">{{ siteName.toUpperCase() }}</span>
                <v-spacer></v-spacer>
                <v-btn icon="mdi-close" variant="text" @click="closeDrawer"></v-btn>
            </div>

            <v-list nav class="bg-transparent">
                <v-list-item v-for="item in menuItems" :key="item.id" :to="item.url" class="mb-2 rounded-lg"
                    :class="{ 'bg-primary-lighten-5 text-primary': isActiveRoute(item.url) }" @click="handleNavigate">
                    <v-list-item-title class="font-weight-bold">{{ item.label }}</v-list-item-title>
                </v-list-item>
            </v-list>

            <div class="mt-8">
                <v-btn block color="primary" size="large" rounded="lg" :to="{ name: 'Contact' }" class="elevation-4"
                    @click="handleNavigate">
                    Get a Quote
                </v-btn>
            </div>
        </div>
    </v-navigation-drawer>
</template>

<script>
export default {
    name: 'MobileDrawer',
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        siteName: {
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
    emits: ['update:modelValue'],
    watch: {
        // Close the drawer when route changes to avoid stale open state
        $route() {
            this.closeDrawer();
        }
    },
    computed: {
        localDrawer: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    },
    methods: {
        closeDrawer() {
            this.$emit('update:modelValue', false);
        },
        handleNavigate() {
            // Defer close slightly to allow navigation to start
            this.$nextTick(() => this.closeDrawer());
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
/* logo-box styles moved to app.css */

.logo-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rounded-logo {
    border-radius: 8px !important;
    overflow: hidden;
}

/* Hide drawer on desktop */
@media (min-width: 961px) {
    .mobile-drawer {
        display: none !important;
    }
}

/* Responsive Styles - Mobile Only */
@media (max-width: 960px) {
    .mobile-drawer {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        height: 100vh !important;
        max-width: 320px !important;
        width: min(320px, 90vw) !important;
        z-index: 3000 !important;
    }

    /* Hide drawer by default when closed - override Vuetify's default behavior */
    .mobile-drawer:not(.v-navigation-drawer--active):not(.mobile-drawer--open) {
        transform: translateX(110%) !important;
        transition: transform 0.28s ease !important;
    }

    /* Show drawer when open */
    .mobile-drawer.v-navigation-drawer--active,
    .mobile-drawer--open {
        transform: translateX(0) !important;
        transition: transform 0.28s ease !important;
    }
}

@media (max-width: 600px) {
    .mobile-drawer {
        width: 280px !important;
    }

    .logo-box {
        width: 36px;
        height: 36px;
    }

    .logo-box .v-icon {
        font-size: 20px !important;
    }

    .text-h6 {
        font-size: 0.875rem !important;
    }
}
</style>
