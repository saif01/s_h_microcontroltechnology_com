<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="4">
                <v-card>
                    <v-card-title class="text-h5">Admin Login</v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="handleLogin">
                            <v-text-field
                                v-model="form.email"
                                label="Email"
                                type="email"
                                required
                                prepend-inner-icon="mdi-email"
                            ></v-text-field>
                            <v-text-field
                                v-model="form.password"
                                label="Password"
                                type="password"
                                required
                                prepend-inner-icon="mdi-lock"
                            ></v-text-field>
                            <v-btn type="submit" color="primary" block :loading="loading" @click="handleLogin">Login</v-btn>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { useAuthStore } from '../../stores/auth';
import { mapActions } from 'pinia';

export default {
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            loading: false
        };
    },
    methods: {
        ...mapActions(useAuthStore, ['login']),
        async handleLogin() {
            this.loading = true;
            try {
                await this.login(this.form);
                this.$router.push({ name: 'AdminDashboard' });
            } catch (error) {
                let message = 'Login failed';
                if (error.response) {
                    if (error.response.data?.message) {
                        message = error.response.data.message;
                    } else if (error.response.data?.errors) {
                        const errors = error.response.data.errors;
                        message = Object.values(errors).flat().join(', ');
                    }
                }
                
                // Use SweetAlert if available, otherwise use alert
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: message
                    });
                } else {
                    alert(message);
                }
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

