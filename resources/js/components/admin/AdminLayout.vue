<template>
    <div>
        <v-navigation-drawer v-model="drawer" app>
            <v-list>
                <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard" :to="{ name: 'AdminDashboard' }"></v-list-item>
                <v-list-item prepend-icon="mdi-file-document" title="Pages" :to="{ name: 'AdminPages' }"></v-list-item>
                <v-list-item prepend-icon="mdi-wrench" title="Services" :to="{ name: 'AdminServices' }"></v-list-item>
                <v-list-item prepend-icon="mdi-package-variant" title="Products" :to="{ name: 'AdminProducts' }"></v-list-item>
                <v-list-item prepend-icon="mdi-email" title="Leads" :to="{ name: 'AdminLeads' }"></v-list-item>
                <v-list-item prepend-icon="mdi-account-group" title="Users" :to="{ name: 'AdminUsers' }"></v-list-item>
                <v-list-item prepend-icon="mdi-cog" title="Settings" :to="{ name: 'AdminSettings' }"></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>Admin Panel</v-toolbar-title>
            <v-spacer></v-spacer>
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
        };
    },
    methods: {
        async logout() {
            try {
                await axios.post('/api/v1/auth/logout', {}, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('admin_token')}`
                    }
                });
                localStorage.removeItem('admin_token');
                this.$router.push('/admin/login');
            } catch (error) {
                console.error('Logout error:', error);
                localStorage.removeItem('admin_token');
                this.$router.push('/admin/login');
            }
        }
    },
    mounted() {
        // Check if user is authenticated
        if (!localStorage.getItem('admin_token')) {
            this.$router.push('/admin/login');
        }
    }
};
</script>

