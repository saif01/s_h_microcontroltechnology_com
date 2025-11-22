<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">User Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog(null)">
                Add New User
            </v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="search"
                            label="Search by name or email"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="compact"
                            clearable
                            @input="loadUsers"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-select
                            v-model="roleFilter"
                            :items="roleOptions"
                            label="Filter by Role"
                            variant="outlined"
                            density="compact"
                            clearable
                            @update:model-value="loadUsers"
                        ></v-select>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Users Table -->
        <v-card>
            <v-card-title>Users</v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>
                                <div class="d-flex align-center gap-2">
                                    <v-avatar size="32" color="primary">
                                        <span class="text-white">{{ user.name.charAt(0).toUpperCase() }}</span>
                                    </v-avatar>
                                    {{ user.name }}
                                </div>
                            </td>
                            <td>{{ user.email }}</td>
                            <td>
                                <v-chip :color="getRoleColor(user.role)" size="small">
                                    {{ getRoleLabel(user.role) }}
                                </v-chip>
                            </td>
                            <td>{{ formatDate(user.created_at) }}</td>
                            <td>
                                <v-btn size="small" icon="mdi-pencil" @click="openDialog(user)" variant="text"></v-btn>
                                <v-btn 
                                    size="small" 
                                    icon="mdi-delete" 
                                    @click="deleteUser(user)" 
                                    variant="text"
                                    color="error"
                                    :disabled="user.id === currentUserId"
                                ></v-btn>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="5" class="text-center py-4">No users found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <v-pagination
                    v-if="pagination.last_page > 1"
                    v-model="currentPage"
                    :length="pagination.last_page"
                    @update:model-value="loadUsers"
                    class="mt-4"
                ></v-pagination>
            </v-card-text>
        </v-card>

        <!-- User Dialog -->
        <v-dialog v-model="dialog" max-width="600" persistent>
            <v-card>
                <v-card-title>
                    {{ editingUser ? 'Edit User' : 'Add New User' }}
                </v-card-title>
                <v-card-text>
                    <v-form ref="form" @submit.prevent="saveUser">
                        <v-text-field
                            v-model="form.name"
                            label="Full Name"
                            :rules="[rules.required]"
                            required
                            class="mb-4"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.email"
                            label="Email"
                            type="email"
                            :rules="[rules.required, rules.email]"
                            required
                            class="mb-4"
                        ></v-text-field>

                        <v-select
                            v-model="form.role"
                            :items="roles"
                            item-title="label"
                            item-value="value"
                            label="Role"
                            :rules="[rules.required]"
                            required
                            class="mb-4"
                        >
                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props">
                                    <template v-slot:title>
                                        {{ item.raw.label }}
                                    </template>
                                    <template v-slot:subtitle>
                                        {{ item.raw.description }}
                                    </template>
                                </v-list-item>
                            </template>
                        </v-select>

                        <v-text-field
                            v-model="form.password"
                            label="Password"
                            type="password"
                            :rules="editingUser ? [] : [rules.required, rules.minLength]"
                            :required="!editingUser"
                            hint="Leave blank to keep current password"
                            persistent-hint
                            class="mb-4"
                        ></v-text-field>

                        <v-text-field
                            v-if="!editingUser || form.password"
                            v-model="form.password_confirmation"
                            label="Confirm Password"
                            type="password"
                            :rules="form.password ? [rules.required, rules.matchPassword] : []"
                            class="mb-4"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.avatar"
                            label="Avatar URL"
                            hint="URL to user's avatar image"
                            persistent-hint
                            class="mb-4"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="closeDialog" variant="text">Cancel</v-btn>
                    <v-btn @click="saveUser" color="primary" :loading="saving">
                        {{ editingUser ? 'Update' : 'Create' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            users: [],
            roles: [],
            search: '',
            roleFilter: null,
            roleOptions: [
                { title: 'Administrator', value: 'admin' },
                { title: 'Editor', value: 'editor' },
                { title: 'HR User', value: 'hr' },
                { title: 'Staff', value: 'staff' },
            ],
            currentPage: 1,
            pagination: {
                last_page: 1
            },
            dialog: false,
            editingUser: null,
            saving: false,
            form: {
                name: '',
                email: '',
                role: 'editor',
                password: '',
                password_confirmation: '',
                avatar: ''
            },
            rules: {
                required: value => !!value || 'This field is required',
                email: value => {
                    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
                    return pattern.test(value) || 'Invalid email'
                },
                minLength: value => {
                    return value.length >= 8 || 'Password must be at least 8 characters'
                },
                matchPassword: value => {
                    return value === this.form.password || 'Passwords do not match'
                }
            },
            currentUserId: null
        };
    },
    async mounted() {
        await this.loadRoles();
        await this.loadUsers();
        this.loadCurrentUser();
    },
    methods: {
        async loadUsers() {
            try {
                const token = localStorage.getItem('admin_token');
                const params = {
                    page: this.currentPage
                };
                if (this.search) {
                    params.search = this.search;
                }
                if (this.roleFilter) {
                    params.role = this.roleFilter;
                }

                const response = await axios.get('/api/v1/users', {
                    params,
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.users = response.data.data;
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    per_page: response.data.per_page,
                    total: response.data.total
                };
            } catch (error) {
                console.error('Error loading users:', error);
                this.showError('Failed to load users');
            }
        },
        async loadRoles() {
            try {
                const token = localStorage.getItem('admin_token');
                const response = await axios.get('/api/v1/users/roles', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                this.roles = response.data.roles;
            } catch (error) {
                console.error('Error loading roles:', error);
            }
        },
        loadCurrentUser() {
            // Get current user ID from token or API
            // For now, we'll get it from the auth user endpoint
            const token = localStorage.getItem('admin_token');
            axios.get('/api/v1/auth/user', {
                headers: { Authorization: `Bearer ${token}` }
            }).then(response => {
                this.currentUserId = response.data.id;
            }).catch(() => {
                // Ignore if can't load current user
            });
        },
        openDialog(user) {
            if (user) {
                this.editingUser = user;
                this.form = {
                    name: user.name,
                    email: user.email,
                    role: user.role,
                    password: '',
                    password_confirmation: '',
                    avatar: user.avatar || ''
                };
            } else {
                this.editingUser = null;
                this.form = {
                    name: '',
                    email: '',
                    role: 'editor',
                    password: '',
                    password_confirmation: '',
                    avatar: ''
                };
            }
            this.dialog = true;
        },
        closeDialog() {
            this.dialog = false;
            this.editingUser = null;
            this.form = {
                name: '',
                email: '',
                role: 'editor',
                password: '',
                password_confirmation: '',
                avatar: ''
            };
            if (this.$refs.form) {
                this.$refs.form.resetValidation();
            }
        },
        async saveUser() {
            if (!this.$refs.form.validate()) {
                return;
            }

            this.saving = true;
            try {
                const token = localStorage.getItem('admin_token');
                const url = this.editingUser 
                    ? `/api/v1/users/${this.editingUser.id}`
                    : '/api/v1/users';

                const data = { ...this.form };
                
                // Remove password confirmation from data
                delete data.password_confirmation;
                
                // If editing and password is empty, don't send it
                if (this.editingUser && !data.password) {
                    delete data.password;
                }

                const method = this.editingUser ? 'put' : 'post';
                
                await axios[method](url, data, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showSuccess(
                    this.editingUser ? 'User updated successfully' : 'User created successfully'
                );
                this.closeDialog();
                await this.loadUsers();
            } catch (error) {
                console.error('Error saving user:', error);
                const message = error.response?.data?.message || 'Error saving user';
                this.showError(message);
            } finally {
                this.saving = false;
            }
        },
        async deleteUser(user) {
            if (user.id === this.currentUserId) {
                this.showError('You cannot delete your own account');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${user.name}?`)) {
                return;
            }

            try {
                const token = localStorage.getItem('admin_token');
                await axios.delete(`/api/v1/users/${user.id}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showSuccess('User deleted successfully');
                await this.loadUsers();
            } catch (error) {
                console.error('Error deleting user:', error);
                const message = error.response?.data?.message || 'Error deleting user';
                this.showError(message);
            }
        },
        getRoleLabel(role) {
            const roleObj = this.roles.find(r => r.value === role);
            return roleObj ? roleObj.label : role;
        },
        getRoleColor(role) {
            const colors = {
                admin: 'error',
                editor: 'primary',
                hr: 'success',
                staff: 'info'
            };
            return colors[role] || 'default';
        },
        formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleDateString();
        },
        showSuccess(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: message
                });
            } else {
                alert(message);
            }
        },
        showError(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'error',
                    title: message
                });
            } else {
                alert(message);
            }
        }
    }
};
</script>

<style scoped>
.gap-2 {
    gap: 8px;
}
</style>

