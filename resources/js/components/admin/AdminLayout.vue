<template>
    <div>
        <!-- Navigation Drawer - Menu items are conditionally shown based on user permissions -->
        <v-navigation-drawer v-model="drawer" class="gradient_color" location="left" permanent hide-overlay app dark>
            <div class="sidebar-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>

            <v-list-item v-if="currentUser" class="user-profile-header"
                :prepend-avatar="resolvedBrandingLogo || resolvedUserAvatar || '/assets/logo/logo.png'"
                :title="siteName || 'Admin Panel'"
                :subtitle="userRoles && userRoles.length > 0 ? userRoles.map(r => r.name).join(', ') : 'No roles assigned'">
            </v-list-item>

            <v-divider class="my-0 divider-glow"></v-divider>

            <v-list density="compact" nav v-if="currentUser && userPermissions.length > 0">
                <!-- ============================================ -->
                <!-- OVERVIEW -->
                <!-- ============================================ -->
                <v-list-item v-if="hasPermission('access-dashboard')" link router prepend-icon="mdi-view-dashboard"
                    title="Dashboard" :to="{ name: 'AdminDashboard' }" value="Dashboard" exact>
                </v-list-item>

                <!-- ============================================ -->
                <!-- CONTENT MANAGEMENT -->
                <!-- ============================================ -->
                <v-list-group v-if="hasPermission('manage-products')" value="products"
                    prepend-icon="mdi-package-variant" no-action>
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" title="Products"></v-list-item>
                    </template>
                    <v-list-item prepend-icon="mdi-package-variant" title="Products" :to="{ name: 'AdminProducts' }">
                    </v-list-item>
                    <v-list-item prepend-icon="mdi-folder" title="Categories" :to="{ name: 'AdminCategories' }">
                    </v-list-item>
                    <v-list-item prepend-icon="mdi-tag" title="Tags" :to="{ name: 'AdminTags' }">
                    </v-list-item>
                </v-list-group>

                <v-list-item v-if="hasPermission('manage-services')" link router prepend-icon="mdi-wrench"
                    title="Services" :to="{ name: 'AdminServices' }" value="Services" exact>
                </v-list-item>

                <v-list-item v-if="hasPermission('manage-about')" link router prepend-icon="mdi-information"
                    title="About Page" :to="{ name: 'AdminAbout' }" value="About" exact>
                </v-list-item>

                <v-list-group v-if="hasPermission('manage-blog')" value="blog" prepend-icon="mdi-post" no-action>
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" title="Blog"></v-list-item>
                    </template>
                    <v-list-item prepend-icon="mdi-post" title="Blog Posts" :to="{ name: 'AdminBlog' }">
                    </v-list-item>
                    <v-list-item prepend-icon="mdi-folder" title="Categories" :to="{ name: 'AdminBlogCategories' }">
                    </v-list-item>
                </v-list-group>

                <v-list-group v-if="hasPermission('manage-careers') || hasPermission('manage-applications')"
                    value="careers" prepend-icon="mdi-briefcase" no-action>
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" title="Careers"></v-list-item>
                    </template>
                    <v-list-item v-if="hasPermission('manage-careers')" prepend-icon="mdi-briefcase" title="Careers"
                        :to="{ name: 'AdminCareers' }">
                    </v-list-item>
                    <v-list-item v-if="hasPermission('manage-applications')" prepend-icon="mdi-file-document-edit"
                        title="Job Applications" :to="{ name: 'AdminJobApplications' }">
                    </v-list-item>
                </v-list-group>

                <!-- ============================================ -->
                <!-- USER MANAGEMENT -->
                <!-- ============================================ -->
                <v-list-item v-if="hasPermission('manage-users')" link router prepend-icon="mdi-account-group"
                    title="Users" :to="{ name: 'AdminUsers' }" value="Users" exact>
                </v-list-item>

                <v-list-group v-if="hasPermission('manage-roles')" value="roles" prepend-icon="mdi-shield-account"
                    no-action>
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" title="Roles & Permissions"></v-list-item>
                    </template>
                    <v-list-item prepend-icon="mdi-shield-account" title="Roles" :to="{ name: 'AdminRoles' }">
                    </v-list-item>
                    <v-list-item prepend-icon="mdi-key" title="Permissions" :to="{ name: 'AdminPermissions' }">
                    </v-list-item>
                </v-list-group>

                <!-- ============================================ -->
                <!-- COMMUNICATION -->
                <!-- ============================================ -->
                <v-list-item v-if="canAccessLeads()" link router prepend-icon="mdi-email" :to="{ name: 'AdminLeads' }"
                    value="Leads" exact class="leads-menu-item">
                    <template v-slot:title>
                        <div class="d-flex align-center justify-space-between w-100">
                            <span>Leads</span>
                            <span v-if="unreadCount > 0" class="unread-badge">
                                {{ unreadCount > 99 ? '99+' : unreadCount }}
                            </span>
                        </div>
                    </template>
                </v-list-item>

                <v-list-item v-if="hasPermission('manage-newsletters')" link router prepend-icon="mdi-email-newsletter"
                    :to="{ name: 'AdminNewsletters' }" value="Newsletters" exact title="Newsletters">
                </v-list-item>

                <!-- ============================================ -->
                <!-- SYSTEM & ADMINISTRATION -->
                <!-- ============================================ -->
                <v-list-item v-if="hasPermission('manage-settings')" link router prepend-icon="mdi-cog" title="Settings"
                    :to="{ name: 'AdminSettings' }" value="Settings" exact>
                </v-list-item>

                <v-list-group v-if="hasPermission('view-login-logs') || hasPermission('view-visitor-logs')" value="logs"
                    prepend-icon="mdi-file-document-multiple" no-action>
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" title="Logs"></v-list-item>
                    </template>
                    <v-list-item v-if="hasPermission('view-login-logs')" prepend-icon="mdi-login" title="Login Logs"
                        :to="{ name: 'AdminLoginLogs' }">
                    </v-list-item>
                    <v-list-item v-if="hasPermission('view-visitor-logs')" prepend-icon="mdi-account-group"
                        title="Visitor Logs" :to="{ name: 'AdminVisitorLogs' }">
                    </v-list-item>
                </v-list-group>
            </v-list>

            <template v-slot:append>
                <div class="pa-2">
                    <v-btn link router @click="logout()" class="logout-btn text-black" size="small" block>
                        <v-icon left>mdi-logout</v-icon>Logout
                    </v-btn>
                </div>
            </template>
        </v-navigation-drawer>

        <v-app-bar app flat density="compact" class="gradient_color app-bar-modern">
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

            <v-chip class="mx-2 date-chip">{{ currentDate }}</v-chip>

            <v-spacer></v-spacer>
            <v-chip prepend-icon="mdi-shield-account"
                v-if="currentUser && (currentUser.role === 'admin' || (userRoles && userRoles.some(r => r.slug === 'administrator')))">Administrator</v-chip>
            <v-spacer></v-spacer>

            <div class="d-flex align-center mr-4" v-if="currentUser">
                <v-menu open-on-hover>
                    <template v-slot:activator="{ props }">
                        <v-avatar v-bind="props" size="42" class="fill-image mr-2" :title="currentUser.name">
                            <v-img cover v-if="currentUser.avatar" :src="resolvedUserAvatar" :alt="currentUser.name" />
                            <v-img cover v-else src="/assets/logo/logo.png" alt="image" />
                        </v-avatar>
                    </template>
                    <v-list>
                        <v-list-item>
                            <v-list-item-title>{{ currentUser.name }}</v-list-item-title>
                        </v-list-item>
                        <v-list-item link router @click="logout()">
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
        </v-app-bar>

        <v-main>
            <v-container fluid>
                <router-view />
            </v-container>
        </v-main>

        <v-footer class="footer-modern gradient_color rtl-footer" app>
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="logo-wrapper">
                        <img src="/assets/logo/logo.png" alt="cpb-it" height="24" class="footer-logo" />
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">Powered By CPB-IT</span>
                    </div>
                </div>

                <div class="footer-divider"></div>

                <div class="footer-info">
                    <span class="version-badge">v2.0</span>
                    <span class="copyright-text">© {{ currentYear }} All Rights Reserved</span>
                </div>
            </div>
        </v-footer>
    </div>
</template>

<script>
import moment from 'moment';
import { useAuthStore } from '../../stores/auth';
import { resolveUploadUrl } from '../../utils/uploads';

export default {
    data() {
        return {
            drawer: true, // Sidebar drawer state (open/closed)
            authStore: null, // Shared auth store (roles/permissions)
            currentUser: null, // Current authenticated user object
            userRoles: [], // Array of roles assigned to the current user
            userPermissions: [], // Array of all permissions extracted from user's roles
            currentDate: moment().format("Do MMMM YYYY"), // Formatted current date
            currentYear: new Date().getFullYear(), // Current year for copyright
            unreadCount: 0, // Count of unread leads/messages
            unreadCountInterval: null, // Interval for polling unread count
            brandingLogo: null, // Logo from branding settings (normalized path)
            siteName: null, // Site name from general settings
        };
    },
    computed: {
        resolvedBrandingLogo() {
            if (!this.brandingLogo) return null;
            return this.resolveImageUrl(this.brandingLogo);
        },
        resolvedUserAvatar() {
            if (!this.currentUser || !this.currentUser.avatar) return null;
            return this.resolveImageUrl(this.currentUser.avatar);
        }
    },
    methods: {
        /**
         * Load current authenticated user data including roles and permissions
         * This is called on component mount to get user information for permission checks
         */
        async loadUser() {
            try {
                const token = localStorage.getItem('admin_token');
                if (!token) {
                    // No token found, redirect to login
                    this.$router.push('/admin/login');
                    return;
                }

                // Fetch current user data from API
                // Backend returns user with roles and their associated permissions
                const response = await this.$axios.get('/api/v1/auth/user', {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                // Store user data
                this.currentUser = response.data;
                this.userRoles = response.data.roles || [];

                // Sync auth store state so sidebar uses the same permissions as route guards
                if (this.authStore) {
                    this.authStore.user = response.data;
                    this.authStore.token = token;
                    this.authStore.isAuthenticated = true;
                    this.authStore.extractRolesAndPermissions(response.data);
                }

                // Extract all unique permissions from user's roles
                // A user can have multiple roles, each with different permissions
                // We flatten all permissions into a single array for easy checking
                this.userPermissions = [];
                if (this.userRoles && this.userRoles.length > 0) {
                    this.userRoles.forEach(role => {
                        if (role.permissions && role.permissions.length > 0) {
                            role.permissions.forEach(permission => {
                                // Avoid duplicate permissions (user might have multiple roles with same permission)
                                if (!this.userPermissions.includes(permission.slug)) {
                                    this.userPermissions.push(permission.slug);
                                }
                            });
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading user:', error);
                // If unauthorized, clear token and redirect to login
                if (error.response?.status === 401) {
                    localStorage.removeItem('admin_token');
                    this.$router.push('/admin/login');
                }
            }
        },
        /**
         * Load branding settings to get the logo and site name
         */
        async loadBrandingSettings() {
            try {
                const token = localStorage.getItem('admin_token');
                if (!token) {
                    return;
                }

                const response = await this.$axios.get('/api/v1/settings', {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                // Extract site name from general settings
                if (response.data.general && response.data.general.site_name && response.data.general.site_name.value) {
                    this.siteName = response.data.general.site_name.value;
                }

                // Extract logo from branding settings (store normalized path)
                if (response.data.branding && response.data.branding.logo && response.data.branding.logo.value) {
                    // Store the normalized path - it will be resolved in computed property
                    this.brandingLogo = response.data.branding.logo.value;
                }
            } catch (error) {
                console.error('Error loading branding settings:', error);
                // Don't show error to user, just use default logo
            }
        },
        /**
         * Logout current user and clear all stored data
         * Clears authentication token, user data, roles, and permissions
         */
        /**
         * Logout current user and clear all stored data
         * Clears authentication token, user data, roles, and permissions
         */
        async logout() {
            const authStore = useAuthStore();
            await authStore.logout();

            // Clear local component state (optional but good for cleanup)
            this.currentUser = null;
            this.userRoles = [];
            this.userPermissions = [];

            // Redirect to login page
            this.$router.push('/admin/login');
        },
        /**
         * Check if the current user has a specific permission
         * 
         * @param {string} permissionSlug - The permission slug to check (e.g., 'manage-about', 'view-leads')
         * @returns {boolean} - True if user has the permission, false otherwise
         * 
         * Permission hierarchy:
         * 1. Administrator role (legacy 'admin' or new 'administrator') has full access to everything
         * 2. Otherwise, check if user has the specific permission through any of their roles
         * 
         * Returns false if:
         * - User is not loaded
         * - Permissions are not loaded yet
         * - User doesn't have the permission
         */
        hasPermission(permissionSlug) {
            // Prefer shared auth store checks for consistency across app
            if (this.authStore) {
                if (this.authStore.hasRole && this.authStore.hasRole(['administrator'])) {
                    return true;
                }
                if (this.authStore.hasPermission && this.authStore.hasPermission(permissionSlug)) {
                    return true;
                }
            }

            // Safety check: If user or permissions are not loaded, deny access
            if (!this.currentUser || !this.userPermissions || this.userPermissions.length === 0) {
                return false;
            }

            // Administrator role grants full access
            if (this.userRoles && this.userRoles.length > 0 && this.userRoles.some(role => role.slug === 'administrator')) {
                return true;
            }

            // Second check: Verify if user has the specific permission through their assigned roles
            // This checks the flattened permissions array we created in loadUser()
            return this.userPermissions.includes(permissionSlug);
        },
        /**
         * Check if user can access leads section
         * User needs at least one of: view-leads, manage-leads, or export-leads
         * 
         * @returns {boolean} - True if user has any lead-related permission
         */
        canAccessLeads() {
            if (!this.currentUser || !this.userPermissions || this.userPermissions.length === 0) {
                return false;
            }
            return this.hasPermission('view-leads') ||
                this.hasPermission('manage-leads') ||
                this.hasPermission('export-leads');
        },
        /**
         * Load unread leads count
         */
        async loadUnreadCount() {
            if (!this.canAccessLeads()) {
                return;
            }

            try {
                const token = localStorage.getItem('admin_token');
                if (!token) return;

                const response = await this.$axios.get('/api/v1/leads/unread-count', {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                this.unreadCount = response.data.count || 0;
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        },
        /**
         * Start polling for unread count updates
         */
        startUnreadCountPolling() {
            // Load immediately
            this.loadUnreadCount();

            // Then poll every 30 seconds
            this.unreadCountInterval = setInterval(() => {
                this.loadUnreadCount();
            }, 30000);
        },
        /**
         * Stop polling for unread count updates
         */
        stopUnreadCountPolling() {
            if (this.unreadCountInterval) {
                clearInterval(this.unreadCountInterval);
                this.unreadCountInterval = null;
            }
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        }
    },
    /**
     * Component lifecycle hook - called when component is mounted
     * Initializes the layout by checking authentication and loading user data
     */
    mounted() {
        // Initialize shared auth store reference
        this.authStore = useAuthStore();

        // Check if user is authenticated before loading data
        if (!localStorage.getItem('admin_token')) {
            // No authentication token found, redirect to login
            this.$router.push('/admin/login');
        } else {
            // Token exists, load user data including roles and permissions
            // This will populate userRoles and userPermissions arrays
            // which are used by hasPermission() method to show/hide menu items
            this.loadUser().then(() => {
                // Start polling for unread count after user is loaded
                this.startUnreadCountPolling();
            });
            // Load branding settings for logo
            this.loadBrandingSettings();
        }
    },
    provide() {
        // Provide refresh method to child components
        // Use arrow function to maintain 'this' context
        return {
            refreshUnreadCount: () => {
                this.loadUnreadCount();
            }
        };
    },
    watch: {
        // Watch for route changes to refresh unread count
        '$route'() {
            this.loadUnreadCount();
        }
    },
    beforeUnmount() {
        // Clean up polling interval when component is destroyed
        this.stopUnreadCountPolling();
    }
};
</script>

<style scoped>
.gradient_color {
    background: var(--admin-gradient-primary, var(--project-gradient-primary)) !important;
    color: var(--admin-text-primary) !important;
    position: relative;
}

.sidebar-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
}

.sidebar-shapes .shape {
    position: absolute;
    border-radius: 50%;
    background: var(--admin-overlay-very-light);
    filter: blur(2px);
}

.sidebar-shapes .shape-1 {
    width: 100px;
    height: 100px;
    top: 15%;
    left: -30px;
    animation: float-shape 10s ease-in-out infinite;
}

.sidebar-shapes .shape-2 {
    width: 60px;
    height: 60px;
    top: 50%;
    right: -20px;
    animation: float-shape 8s ease-in-out infinite reverse;
}

.sidebar-shapes .shape-3 {
    width: 120px;
    height: 120px;
    bottom: 20%;
    left: -40px;
    animation: float-shape 12s ease-in-out infinite;
    animation-delay: 2s;
}

@keyframes float-shape {

    0%,
    100% {
        transform: translateY(0) translateX(0) rotate(0deg);
        opacity: 0.3;
    }

    25% {
        transform: translateY(-30px) translateX(-15px) rotate(90deg);
        opacity: 0.5;
    }

    50% {
        transform: translateY(-15px) translateX(15px) rotate(180deg);
        opacity: 0.3;
    }

    75% {
        transform: translateY(-40px) translateX(-10px) rotate(270deg);
        opacity: 0.6;
    }
}

/* Unread badge in sidebar - Red background with white text */
.leads-menu-item .unread-badge {
    background-color: #f44336 !important;
    color: white !important;
    min-width: 22px !important;
    height: 22px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    padding: 0 6px !important;
    border-radius: 11px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 2px 4px rgba(244, 67, 54, 0.4) !important;
    margin-left: auto !important;
    flex-shrink: 0 !important;
    line-height: 22px !important;
}

.leads-menu-item :deep(.v-list-item-title) {
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

:deep(.v-navigation-drawer) {
    background: var(--admin-gradient-primary, var(--project-gradient-primary)) !important;
    border-right: 1px solid var(--admin-overlay-dark) !important;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1) !important;
}

:deep(.v-navigation-drawer .v-navigation-drawer__content) {
    background: transparent !important;
}

:deep(.v-list-item) {
    border-radius: 12px;
    margin: 4px 8px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;
    overflow: hidden;
}

:deep(.v-list-item::before) {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, var(--admin-overlay-medium), transparent);
    transition: left 0.6s ease;
    pointer-events: none;
    z-index: -1;
}

:deep(.v-list-item:hover::before) {
    left: 100%;
}

:deep(.v-list-item::after) {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    transform: translate(-50%, -50%);
    transition: width 0.5s ease, height 0.5s ease;
    pointer-events: none;
    z-index: -1;
}

:deep(.v-list-item:hover::after) {
    width: 300px;
    height: 300px;
}

:deep(.v-list-item:hover) {
    background: var(--admin-overlay-light) !important;
    transform: translateX(8px) scale(1.02);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

:deep(.v-list-item--active) {
    background: linear-gradient(90deg, var(--admin-overlay-medium-plus) 0%, var(--admin-overlay-light) 100%) !important;
    box-shadow:
        0 6px 20px rgba(0, 0, 0, 0.25),
        inset 0 0 20px rgba(255, 255, 255, 0.1);
    border-left: 4px solid var(--admin-overlay-very-strong);
    transform: translateX(4px);
    animation: activeGlow 2s ease-in-out infinite;
}

@keyframes activeGlow {

    0%,
    100% {
        box-shadow:
            0 6px 20px rgba(0, 0, 0, 0.25),
            inset 0 0 20px rgba(255, 255, 255, 0.1);
    }

    50% {
        box-shadow:
            0 8px 25px rgba(0, 0, 0, 0.3),
            inset 0 0 30px rgba(255, 255, 255, 0.2);
    }
}

:deep(.v-list-item__prepend > .v-icon) {
    opacity: 0.9;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

:deep(.v-list-item:hover .v-list-item__prepend > .v-icon) {
    opacity: 1;
    transform: scale(1.2) rotate(5deg);
    filter: drop-shadow(0 4px 8px rgba(255, 255, 255, 0.3));
    animation: iconBounce 0.6s ease;
}

@keyframes iconBounce {

    0%,
    100% {
        transform: scale(1.2) rotate(5deg);
    }

    25% {
        transform: scale(1.3) rotate(-5deg);
    }

    50% {
        transform: scale(1.15) rotate(5deg);
    }

    75% {
        transform: scale(1.25) rotate(-5deg);
    }
}

:deep(.v-list-item--active .v-list-item__prepend > .v-icon) {
    animation: iconPulse 2s ease-in-out infinite;
    filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
}

@keyframes iconPulse {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.15);
    }
}

:deep(.v-list-group__items) {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    margin: 4px 8px;
    padding: 4px 0;
}

:deep(.v-list-group__items .v-list-item) {
    padding-left: 24px !important;
    margin: 2px 4px;
}

:deep(.v-divider) {
    border-color: var(--admin-overlay-medium) !important;
    margin: 8px 0 !important;
}

:deep(.v-list-item-subtitle) {
    opacity: 0.85;
    font-size: 12px;
    font-weight: 500;
}

:deep(.v-list-item-title) {
    font-weight: 600;
    letter-spacing: 0.3px;
}

:deep(.v-avatar) {
    border: 2px solid var(--admin-overlay-strong);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    border-radius: 12px !important;
    overflow: hidden;
}

:deep(.v-avatar .v-img) {
    border-radius: 12px !important;
    object-fit: cover;
}

:deep(.v-avatar:hover) {
    border-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    transform: scale(1.05);
}

:deep(.v-btn) {
    border-radius: 10px !important;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.v-btn:hover) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

/* Keep themed button colors (e.g., primary) visible; only lighten neutral buttons */
:deep(.v-btn:not([class*="bg-"]):not(.v-btn--variant-text):not(.v-btn--variant-plain)) {
    background: var(--admin-overlay-solid) !important;
}

:deep(.v-btn:not([class*="bg-"]):not(.v-btn--variant-text):not(.v-btn--variant-plain):hover) {
    background: var(--admin-overlay-full) !important;
}

.app-bar-modern {
    position: relative;
    z-index: 1000;
}

:deep(.v-app-bar) {
    background: var(--admin-gradient-primary, var(--project-gradient-primary)) !important;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1) !important;
    backdrop-filter: blur(10px);
}

:deep(.v-app-bar .v-toolbar__content) {
    background: transparent !important;
}

:deep(.v-app-bar-nav-icon) {
    background: var(--admin-overlay-medium) !important;
    border-radius: 12px;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    color: var(--admin-text-primary) !important;
    position: relative;
    overflow: hidden;
}

:deep(.v-app-bar-nav-icon::before) {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

:deep(.v-app-bar-nav-icon:hover::before) {
    width: 100px;
    height: 100px;
}

:deep(.v-app-bar-nav-icon .v-icon) {
    color: white !important;
    opacity: 1;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    position: relative;
    z-index: 1;
}

:deep(.v-app-bar-nav-icon:hover) {
    background: rgba(255, 255, 255, 0.35) !important;
    transform: rotate(-180deg) scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

:deep(.v-app-bar-nav-icon:active) {
    transform: rotate(-180deg) scale(0.95);
}

.fill-image {
    cursor: pointer;
}

:deep(.v-menu .v-list) {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

:deep(.v-menu .v-list-item) {
    transition: all 0.2s ease;
    margin: 0;
    border-radius: 0;
}

:deep(.v-menu .v-list-item:hover) {
    background: linear-gradient(180deg,
            rgba(var(--admin-gradient-start-rgb), 0.1) 0%,
            rgba(var(--admin-gradient-end-rgb), 0.1) 100%);
    transform: translateX(0);
}

:deep(.v-menu .v-list-item-title) {
    font-weight: 500;
    color: #334155;
    text-align: right;
}

.date-chip {
    background: var(--admin-overlay-medium) !important;
    color: var(--admin-text-primary) !important;
    border-radius: 12px !important;
    padding: 0 12px !important;
}

.user-profile-header {
    position: relative;
    z-index: 1;
    margin: 8px;
    border-radius: 16px !important;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.user-profile-header::before {
    content: "";
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, transparent 25%, rgba(255, 255, 255, 0.3) 50%, transparent 75%);
    border-radius: 16px;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
    animation: rotateBorder 3s linear infinite;
}

@keyframes rotateBorder {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.user-profile-header:hover::before {
    opacity: 1;
}

.user-profile-header:hover {
    background: rgba(255, 255, 255, 0.18);
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.4);
}

.user-profile-header :deep(.v-list-item) {
    border-radius: 16px !important;
}

.user-profile-header :deep(.v-avatar) {
    animation: avatarFloat 3s ease-in-out infinite;
}

@keyframes avatarFloat {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-5px);
    }
}

.divider-glow {
    position: relative;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
}

.logout-btn {
    position: relative;
    z-index: 1;
    overflow: hidden;
    border: 2px solid transparent;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.logout-btn::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: linear-gradient(135deg,
            rgba(var(--admin-gradient-start-rgb), 0.3) 0%,
            rgba(var(--admin-gradient-end-rgb), 0.3) 100%);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.logout-btn:hover::before {
    width: 400px;
    height: 400px;
}

.logout-btn::after {
    content: "";
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg,
            var(--admin-gradient-start),
            var(--admin-gradient-end),
            var(--admin-gradient-start));
    background-size: 300% 300%;
    border-radius: 10px;
    opacity: 0;
    z-index: -1;
    transition: opacity 0.4s ease;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
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

.logout-btn:hover::after {
    opacity: 1;
}

.logout-btn:hover {
    transform: translateY(-4px) scale(1.05);
    border-color: transparent;
}

.logout-btn:active {
    transform: translateY(-2px) scale(1.02);
}

.logout-btn :deep(.v-icon) {
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.logout-btn:hover :deep(.v-icon) {
    transform: translateX(-5px) rotate(-15deg);
    animation: logoutShake 0.5s ease;
}

@keyframes logoutShake {

    0%,
    100% {
        transform: translateX(-5px) rotate(-15deg);
    }

    25% {
        transform: translateX(-8px) rotate(-20deg);
    }

    50% {
        transform: translateX(-3px) rotate(-10deg);
    }

    75% {
        transform: translateX(-7px) rotate(-18deg);
    }
}

:deep(.v-navigation-drawer__content)::-webkit-scrollbar {
    width: 6px;
}

:deep(.v-navigation-drawer__content)::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

:deep(.v-navigation-drawer__content)::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    transition: background 0.3s ease;
}

:deep(.v-navigation-drawer__content)::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

@media (max-width: 960px) {
    :deep(.v-list-item) {
        margin: 3px 6px;
        font-size: 14px;
    }

    :deep(.v-list-group__items .v-list-item) {
        padding-left: 16px !important;
    }
}

/* Footer Styles */
.rtl-footer {
    direction: rtl;
    text-align: right;
}

.footer-modern {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.1);
    padding: 8px 16px !important;
    min-height: 48px !important;
    position: relative;
}

.footer-modern.gradient_color {
    background: var(--admin-gradient-primary, var(--project-gradient-primary)) !important;
    color: var(--admin-text-primary) !important;
}

:deep(.v-footer.footer-modern.gradient_color) {
    background: var(--admin-gradient-primary, var(--project-gradient-primary)) !important;
}

:deep(.v-footer.footer-modern) {
    background: var(--admin-gradient-primary, var(--project-gradient-primary)) !important;
}

.footer-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    gap: 16px;
    flex-wrap: wrap;
}

.footer-brand {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    background: var(--admin-overlay-light);
    border-radius: 8px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.logo-wrapper:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.footer-logo {
    display: block;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    transition: all 0.3s ease;
}

.logo-wrapper:hover .footer-logo {
    transform: scale(1.05);
}

.brand-text {
    display: flex;
    align-items: center;
}

.brand-name {
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.3px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.footer-divider {
    flex: 1;
    height: 1px;
    background: linear-gradient(270deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
    min-width: 20px;
}

.footer-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.copyright-text {
    font-size: 12px;
    font-weight: 500;
    opacity: 0.9;
    letter-spacing: 0.3px;
}

.version-badge {
    padding: 4px 10px;
    background: var(--admin-overlay-medium);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.version-badge:hover {
    background: var(--admin-overlay-strong);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .footer-modern {
        padding: 6px 12px !important;
    }

    .footer-content {
        gap: 12px;
    }

    .brand-name {
        font-size: 13px;
    }

    .copyright-text {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .footer-modern {
        padding: 6px 10px !important;
    }

    .logo-wrapper {
        padding: 3px;
    }

    .footer-logo {
        height: 20px !important;
    }

    .brand-name {
        font-size: 12px;
    }

    .copyright-text {
        font-size: 10px;
    }

    .version-badge {
        font-size: 10px;
        padding: 3px 8px;
    }

    .footer-divider {
        display: none;
    }
}

@media (prefers-reduced-motion: reduce) {

    .logo-wrapper,
    .footer-logo,
    .version-badge {
        transition: none !important;
    }
}
</style>
