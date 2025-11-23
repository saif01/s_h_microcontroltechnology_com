<template>
    <div>
        <!-- Navigation Drawer - Menu items are conditionally shown based on user permissions -->
        <v-navigation-drawer v-model="drawer" app>
            <v-list>
                <!-- Dashboard - Requires 'access-dashboard' permission -->
                <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard" :to="{ name: 'AdminDashboard' }"
                    v-if="hasPermission('access-dashboard')"></v-list-item>

                <!-- Pages Management - Requires 'manage-pages' permission -->
                <v-list-item prepend-icon="mdi-file-document" title="Pages" :to="{ name: 'AdminPages' }"
                    v-if="hasPermission('manage-pages')"></v-list-item>

                <!-- Services Management - Requires 'manage-services' permission -->
                <v-list-item prepend-icon="mdi-wrench" title="Services" :to="{ name: 'AdminServices' }"
                    v-if="hasPermission('manage-services')"></v-list-item>

                <!-- Products Management - Requires 'manage-products' permission -->
                <v-list-item prepend-icon="mdi-package-variant" title="Products" :to="{ name: 'AdminProducts' }"
                    v-if="hasPermission('manage-products')"></v-list-item>

                <!-- Leads Management - Requires 'view-leads' permission -->
                <v-list-item prepend-icon="mdi-email" title="Leads" :to="{ name: 'AdminLeads' }"
                    v-if="hasPermission('view-leads')"></v-list-item>

                <!-- Users Management - Requires 'manage-users' permission -->
                <v-list-item prepend-icon="mdi-account-group" title="Users" :to="{ name: 'AdminUsers' }"
                    v-if="hasPermission('manage-users')"></v-list-item>

                <!-- Roles & Permissions Management - Requires 'manage-roles' permission -->
                <v-list-item prepend-icon="mdi-shield-account" title="Roles" :to="{ name: 'AdminRoles' }"
                    v-if="hasPermission('manage-roles')"></v-list-item>
                
                <!-- Permissions Management - Requires 'manage-roles' permission -->
                <v-list-item prepend-icon="mdi-key" title="Permissions" :to="{ name: 'AdminPermissions' }"
                    v-if="hasPermission('manage-roles')"></v-list-item>

                <!-- Settings Management - Requires 'manage-settings' permission -->
                <v-list-item prepend-icon="mdi-cog" title="Settings" :to="{ name: 'AdminSettings' }"
                    v-if="hasPermission('manage-settings')"></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>Admin Panel</v-toolbar-title>
            <v-spacer></v-spacer>
            <div class="d-flex align-center mr-4" v-if="currentUser">
                <v-avatar v-if="currentUser.avatar" size="32" class="mr-3">
                    <v-img :src="currentUser.avatar" :alt="currentUser.name"></v-img>
                </v-avatar>
                <div class="d-flex flex-column mr-4">
                    <div class="text-body-2 font-weight-medium">{{ currentUser.name }}</div>
                    <div class="d-flex align-center mt-1" v-if="userRoles && userRoles.length > 0">
                        <v-chip v-for="role in userRoles" :key="role.id" size="small"
                            :color="role.is_system ? 'warning' : 'primary'" variant="flat" class="mr-1">
                            {{ role.name }}
                        </v-chip>
                    </div>
                    <span v-else class="text-caption text-grey">No roles assigned</span>
                </div>
            </div>
            <v-btn @click="logout" prepend-icon="mdi-logout">Logout</v-btn>
        </v-app-bar>

        <v-main>
            <v-container fluid>
                <router-view />
            </v-container>
        </v-main>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            drawer: true, // Sidebar drawer state (open/closed)
            currentUser: null, // Current authenticated user object
            userRoles: [], // Array of roles assigned to the current user
            userPermissions: [], // Array of all permissions extracted from user's roles
        };
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
                const response = await axios.get('/api/v1/auth/user', {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                // Store user data
                this.currentUser = response.data;
                this.userRoles = response.data.roles || [];

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
         * Logout current user and clear all stored data
         * Clears authentication token, user data, roles, and permissions
         */
        async logout() {
            try {
                // Call logout endpoint to revoke token on server
                await axios.post('/api/v1/auth/logout', {}, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('admin_token')}`
                    }
                });

                // Clear all local data
                localStorage.removeItem('admin_token');
                this.currentUser = null;
                this.userRoles = [];
                this.userPermissions = [];

                // Redirect to login page
                this.$router.push('/admin/login');
            } catch (error) {
                console.error('Logout error:', error);
                // Even if logout API call fails, clear local data and redirect
                localStorage.removeItem('admin_token');
                this.currentUser = null;
                this.userRoles = [];
                this.userPermissions = [];
                this.$router.push('/admin/login');
            }
        },
        /**
         * Check if the current user has a specific permission
         * 
         * @param {string} permissionSlug - The permission slug to check (e.g., 'manage-pages', 'view-leads')
         * @returns {boolean} - True if user has the permission, false otherwise
         * 
         * Permission hierarchy:
         * 1. Administrator role (legacy 'admin' or new 'administrator') has full access to everything
         * 2. Otherwise, check if user has the specific permission through any of their roles
         */
        hasPermission(permissionSlug) {
            // First check: Administrator role grants full access (bypasses all permission checks)
            if (this.currentUser) {
                // Check legacy admin role (backward compatibility)
                if (this.currentUser.role === 'admin') {
                    return true;
                }

                // Check if user has administrator role in new role-based system
                // Administrator role has all permissions by default
                if (this.userRoles && this.userRoles.some(role => role.slug === 'administrator')) {
                    return true;
                }
            }

            // Second check: Verify if user has the specific permission through their assigned roles
            // This checks the flattened permissions array we created in loadUser()
            return this.userPermissions.includes(permissionSlug);
        }
    },
    /**
     * Component lifecycle hook - called when component is mounted
     * Initializes the layout by checking authentication and loading user data
     */
    mounted() {
        // Check if user is authenticated before loading data
        if (!localStorage.getItem('admin_token')) {
            // No authentication token found, redirect to login
            this.$router.push('/admin/login');
        } else {
            // Token exists, load user data including roles and permissions
            // This will populate userRoles and userPermissions arrays
            // which are used by hasPermission() method to show/hide menu items
            this.loadUser();
        }
    }
};
</script>
