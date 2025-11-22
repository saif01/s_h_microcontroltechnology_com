<template>
    <div>
        <v-navigation-drawer v-model="drawer" app>
            <v-list>
                <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard"
                    :to="{ name: 'AdminDashboard' }"></v-list-item>
                <v-list-item prepend-icon="mdi-file-document" title="Pages" :to="{ name: 'AdminPages' }"></v-list-item>
                <v-list-item prepend-icon="mdi-wrench" title="Services" :to="{ name: 'AdminServices' }"></v-list-item>
                <v-list-item prepend-icon="mdi-package-variant" title="Products"
                    :to="{ name: 'AdminProducts' }"></v-list-item>
                <v-list-item prepend-icon="mdi-email" title="Leads" :to="{ name: 'AdminLeads' }"></v-list-item>
                <v-list-item prepend-icon="mdi-account-group" title="Users" :to="{ name: 'AdminUsers' }"></v-list-item>
                <v-list-item prepend-icon="mdi-shield-account" title="Roles" :to="{ name: 'AdminRoles' }"></v-list-item>
                <v-list-item prepend-icon="mdi-cog" title="Settings" :to="{ name: 'AdminSettings' }"></v-list-item>
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
            drawer: true,
            currentUser: null,
            userRoles: [],
        };
    },
    methods: {
        async loadUser() {
            try {
                const token = localStorage.getItem('admin_token');
                if (!token) {
                    this.$router.push('/admin/login');
                    return;
                }

                const response = await axios.get('/api/v1/auth/user', {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                this.currentUser = response.data;
                this.userRoles = response.data.roles || [];
            } catch (error) {
                console.error('Error loading user:', error);
                if (error.response?.status === 401) {
                    localStorage.removeItem('admin_token');
                    this.$router.push('/admin/login');
                }
            }
        },
        async logout() {
            try {
                await axios.post('/api/v1/auth/logout', {}, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('admin_token')}`
                    }
                });
                localStorage.removeItem('admin_token');
                this.currentUser = null;
                this.userRoles = [];
                this.$router.push('/admin/login');
            } catch (error) {
                console.error('Logout error:', error);
                localStorage.removeItem('admin_token');
                this.currentUser = null;
                this.userRoles = [];
                this.$router.push('/admin/login');
            }
        }
    },
    mounted() {
        // Check if user is authenticated
        if (!localStorage.getItem('admin_token')) {
            this.$router.push('/admin/login');
        } else {
            // Load user data including roles
            this.loadUser();
        }
    }
};
</script>
