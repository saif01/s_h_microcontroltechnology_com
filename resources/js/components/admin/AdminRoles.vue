<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Role Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog(null)">
                Add New Role
            </v-btn>
        </div>

        <!-- Roles List -->
        <v-card>
            <v-card-title>Roles</v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Permissions</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles" :key="role.id">
                            <td>
                                <div class="font-weight-medium">{{ role.name }}</div>
                                <div class="text-caption text-grey">{{ role.description || 'No description' }}</div>
                            </td>
                            <td>
                                <v-chip size="small" variant="outlined">{{ role.slug }}</v-chip>
                            </td>
                            <td>
                                <v-chip size="small" color="primary" variant="text">
                                    {{ role.permissions ? role.permissions.length : 0 }} permissions
                                </v-chip>
                            </td>
                            <td>
                                <v-chip :color="role.is_active ? 'success' : 'error'" size="small">
                                    {{ role.is_active ? 'Active' : 'Inactive' }}
                                </v-chip>
                            </td>
                            <td>
                                <v-chip v-if="role.is_system" color="warning" size="small">
                                    System
                                </v-chip>
                                <span v-else class="text-caption">Custom</span>
                            </td>
                            <td>
                                <v-btn size="small" icon="mdi-pencil" @click="openDialog(role)" variant="text"
                                    :disabled="role.is_system"></v-btn>
                                <v-btn size="small" icon="mdi-shield-key" @click="openPermissionDialog(role)"
                                    variant="text"></v-btn>
                                <v-btn size="small" icon="mdi-delete" @click="deleteRole(role)" variant="text"
                                    color="error" :disabled="role.is_system"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="roles.length === 0">
                            <td colspan="6" class="text-center py-4">No roles found</td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card-text>
        </v-card>

        <!-- Role Dialog -->
        <v-dialog v-model="dialog" max-width="600" persistent>
            <v-card>
                <v-card-title>
                    {{ editingRole ? 'Edit Role' : 'Add New Role' }}
                </v-card-title>
                <v-card-text>
                    <v-form ref="roleForm" @submit.prevent="saveRole">
                        <v-text-field v-model="form.name" label="Role Name" :rules="[rules.required]" required
                            hint="Display name for the role" persistent-hint class="mb-4"></v-text-field>

                        <v-text-field v-model="form.slug" label="Slug"
                            hint="URL-friendly identifier (auto-generated if empty)" persistent-hint
                            class="mb-4"></v-text-field>

                        <v-textarea v-model="form.description" label="Description" hint="Brief description of the role"
                            persistent-hint rows="2" class="mb-4"></v-textarea>

                        <v-text-field v-model.number="form.order" label="Order" type="number"
                            hint="Display order (lower numbers first)" persistent-hint class="mb-4"></v-text-field>

                        <v-switch v-model="form.is_active" label="Active"
                            hint="Inactive roles cannot be assigned to users" persistent-hint class="mb-4"></v-switch>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="closeDialog" variant="text">Cancel</v-btn>
                    <v-btn @click="saveRole" color="primary" :loading="saving">
                        {{ editingRole ? 'Update' : 'Create' }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Permissions Dialog -->
        <v-dialog v-model="permissionDialog" max-width="800" persistent>
            <v-card>
                <v-card-title>
                    Manage Permissions - {{ selectedRole?.name }}
                </v-card-title>
                <v-card-text>
                    <div v-if="loadingPermissions" class="text-center py-4">
                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    </div>
                    <div v-else>
                        <div v-for="(permissions, group) in groupedPermissions" :key="group" class="mb-6">
                            <h3 class="text-h6 mb-3">{{ group.charAt(0).toUpperCase() + group.slice(1) }}</h3>
                            <v-row>
                                <v-col v-for="permission in permissions" :key="permission.id" cols="12" md="6">
                                    <v-checkbox :model-value="isPermissionSelected(permission.id)"
                                        @update:model-value="togglePermission(permission.id)" :label="permission.name"
                                        :hint="permission.description" persistent-hint density="compact"></v-checkbox>
                                </v-col>
                            </v-row>
                        </div>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="closePermissionDialog" variant="text">Cancel</v-btn>
                    <v-btn @click="savePermissions" color="primary" :loading="savingPermissions">
                        Save Permissions
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
            roles: [],
            permissions: [],
            groupedPermissions: {},
            dialog: false,
            permissionDialog: false,
            editingRole: null,
            selectedRole: null,
            loadingPermissions: false,
            saving: false,
            savingPermissions: false,
            selectedPermissions: [],
            form: {
                name: '',
                slug: '',
                description: '',
                is_active: true,
                order: 0
            },
            rules: {
                required: value => !!value || 'This field is required'
            }
        };
    },
    async mounted() {
        await this.loadRoles();
        await this.loadPermissions();
    },
    methods: {
        async loadRoles() {
            try {
                const token = localStorage.getItem('admin_token');
                const response = await axios.get('/api/v1/roles', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                this.roles = response.data;
            } catch (error) {
                console.error('Error loading roles:', error);
                this.showError('Failed to load roles');
            }
        },
        async loadPermissions() {
            try {
                const token = localStorage.getItem('admin_token');
                const response = await axios.get('/api/v1/permissions', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                this.groupedPermissions = response.data;

                // Flatten for easier access
                this.permissions = [];
                Object.values(response.data).forEach(group => {
                    this.permissions.push(...group);
                });
            } catch (error) {
                console.error('Error loading permissions:', error);
                this.showError('Failed to load permissions');
            }
        },
        openDialog(role) {
            if (role) {
                this.editingRole = role;
                this.form = {
                    name: role.name,
                    slug: role.slug,
                    description: role.description || '',
                    is_active: role.is_active,
                    order: role.order || 0
                };
            } else {
                this.editingRole = null;
                this.form = {
                    name: '',
                    slug: '',
                    description: '',
                    is_active: true,
                    order: 0
                };
            }
            this.dialog = true;
        },
        closeDialog() {
            this.dialog = false;
            this.editingRole = null;
            this.form = {
                name: '',
                slug: '',
                description: '',
                is_active: true,
                order: 0
            };
            if (this.$refs.roleForm) {
                this.$refs.roleForm.resetValidation();
            }
        },
        async saveRole() {
            if (!this.$refs.roleForm.validate()) {
                return;
            }

            this.saving = true;
            try {
                const token = localStorage.getItem('admin_token');
                const url = this.editingRole
                    ? `/api/v1/roles/${this.editingRole.id}`
                    : '/api/v1/roles';

                const method = this.editingRole ? 'put' : 'post';

                await axios[method](url, this.form, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showSuccess(
                    this.editingRole ? 'Role updated successfully' : 'Role created successfully'
                );
                this.closeDialog();
                await this.loadRoles();
            } catch (error) {
                console.error('Error saving role:', error);
                const message = error.response?.data?.message || 'Error saving role';
                this.showError(message);
            } finally {
                this.saving = false;
            }
        },
        async deleteRole(role) {
            if (role.is_system) {
                this.showError('System roles cannot be deleted');
                return;
            }

            if (!confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
                return;
            }

            try {
                const token = localStorage.getItem('admin_token');
                await axios.delete(`/api/v1/roles/${role.id}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showSuccess('Role deleted successfully');
                await this.loadRoles();
            } catch (error) {
                console.error('Error deleting role:', error);
                const message = error.response?.data?.message || 'Error deleting role';
                this.showError(message);
            }
        },
        async openPermissionDialog(role) {
            this.selectedRole = role;
            this.selectedPermissions = role.permissions ? role.permissions.map(p => p.id) : [];
            this.permissionDialog = true;
        },
        closePermissionDialog() {
            this.permissionDialog = false;
            this.selectedRole = null;
            this.selectedPermissions = [];
        },
        isPermissionSelected(permissionId) {
            return this.selectedPermissions.includes(permissionId);
        },
        togglePermission(permissionId) {
            const index = this.selectedPermissions.indexOf(permissionId);
            if (index > -1) {
                this.selectedPermissions.splice(index, 1);
            } else {
                this.selectedPermissions.push(permissionId);
            }
        },
        async savePermissions() {
            this.savingPermissions = true;
            try {
                const token = localStorage.getItem('admin_token');
                await axios.put(`/api/v1/roles/${this.selectedRole.id}/permissions`, {
                    permissions: this.selectedPermissions
                }, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showSuccess('Permissions updated successfully');
                this.closePermissionDialog();
                await this.loadRoles();
            } catch (error) {
                console.error('Error saving permissions:', error);
                const message = error.response?.data?.message || 'Error saving permissions';
                this.showError(message);
            } finally {
                this.savingPermissions = false;
            }
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
