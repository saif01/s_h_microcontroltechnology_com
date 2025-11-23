<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Role Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog(null)">
                Add New Role
            </v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search roles" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadRoles"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="activeFilter" :items="activeOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadRoles"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

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

                <!-- Pagination -->
                <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                    @update:model-value="loadRoles" class="mt-4"></v-pagination>
            </v-card-text>
        </v-card>

        <!-- Role Dialog -->
        <v-dialog v-model="dialog" max-width="600" persistent>
            <v-card>
                <v-card-title>
                    {{ editingRole ? 'Edit Role' : 'Add New Role' }}
                </v-card-title>
                <v-card-text>
                    <v-alert v-if="editingRole && editingRole.is_system" type="info" variant="tonal" class="mb-4">
                        <strong>System Role:</strong> This is a system role. Core properties (name, slug, description,
                        status, order) cannot be modified. Only permissions can be updated using the permissions button.
                    </v-alert>
                    <v-form ref="roleForm" @submit.prevent="saveRole">
                        <v-text-field v-model="form.name" label="Role Name" :rules="[rules.required]" required
                            hint="Display name for the role" persistent-hint class="mb-4"
                            :disabled="editingRole && editingRole.is_system"
                            @blur="autoGenerateSlugFromName"></v-text-field>

                        <v-text-field v-model="form.slug" label="Slug"
                            hint="URL-friendly identifier (auto-generated if empty)" persistent-hint class="mb-4"
                            :disabled="editingRole && editingRole.is_system"></v-text-field>

                        <v-textarea v-model="form.description" label="Description" hint="Brief description of the role"
                            persistent-hint rows="2" class="mb-4"
                            :disabled="editingRole && editingRole.is_system"></v-textarea>

                        <v-text-field v-model.number="form.order" label="Order" type="number"
                            hint="Display order (lower numbers first)" persistent-hint class="mb-4"
                            :disabled="editingRole && editingRole.is_system"></v-text-field>

                        <v-switch v-model="form.is_active" label="Active"
                            hint="Inactive roles cannot be assigned to users" persistent-hint class="mb-4"
                            :disabled="editingRole && editingRole.is_system"></v-switch>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="closeDialog" variant="text">Cancel</v-btn>
                    <v-btn v-if="editingRole && editingRole.is_system" @click="closeDialog" color="primary">
                        Close
                    </v-btn>
                    <v-btn v-else @click="saveRole" color="primary" :loading="saving">
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
                    <v-chip v-if="selectedRole?.is_system" color="warning" size="small" class="ml-2">
                        System Role
                    </v-chip>
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
            search: '',
            activeFilter: null,
            activeOptions: [
                { title: 'Active', value: true },
                { title: 'Inactive', value: false }
            ],
            currentPage: 1,
            perPage: 10,
            perPageOptions: [10, 25, 50, 100, 500],
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 0
            },
            form: {
                name: '',
                slug: '',
                description: '',
                is_active: true,
                order: 0
            },
            rules: {
                required: value => !!value || 'This field is required'
            },
            autoGeneratingSlug: false
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
                const params = {
                    page: this.currentPage,
                    per_page: this.perPage
                };

                if (this.search) {
                    params.search = this.search;
                }

                if (this.activeFilter !== null) {
                    params.active = this.activeFilter;
                }

                const response = await axios.get('/api/v1/roles', {
                    params,
                    headers: { Authorization: `Bearer ${token}` }
                });

                // Handle both paginated and non-paginated responses
                if (response.data.data) {
                    // Paginated response
                    this.roles = response.data.data || [];
                    this.pagination = {
                        current_page: response.data.current_page,
                        last_page: response.data.last_page,
                        per_page: response.data.per_page,
                        total: response.data.total
                    };
                } else {
                    // Non-paginated response (fallback)
                    this.roles = response.data || [];
                    this.pagination = {
                        current_page: 1,
                        last_page: 1,
                        per_page: this.roles.length,
                        total: this.roles.length
                    };
                }

                // If no roles exist, show a message
                if (this.roles.length === 0) {
                    console.warn('No roles found. Run the seeder to create default roles.');
                }
            } catch (error) {
                console.error('Error loading roles:', error);
                if (error.response?.status === 404) {
                    this.showError('Roles endpoint not found. Please ensure migrations and seeders have run.');
                } else {
                    this.showError('Failed to load roles');
                }
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
        autoGenerateSlugFromName() {
            // Auto-generate slug from name if slug is empty and user is not editing slug
            if (!this.form.slug && this.form.name && !this.editingRole) {
                this.form.slug = this.form.name
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        },
        async saveRole() {
            // Validate form first
            if (!this.$refs.roleForm) {
                this.showError('Form reference not found');
                return;
            }

            if (!this.$refs.roleForm.validate()) {
                return;
            }

            // Auto-generate slug if empty
            if (!this.form.slug && this.form.name) {
                this.form.slug = this.form.name
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            this.saving = true;
            try {
                const token = localStorage.getItem('admin_token');
                const url = this.editingRole
                    ? `/api/v1/roles/${this.editingRole.id}`
                    : '/api/v1/roles';

                const method = this.editingRole ? 'put' : 'post';

                // Prepare data - ensure boolean and integer values are properly formatted
                const data = {
                    name: this.form.name.trim(),
                    description: this.form.description ? this.form.description.trim() : null,
                    is_active: this.form.is_active === true || this.form.is_active === 'true' || this.form.is_active === 1,
                    order: parseInt(this.form.order) || 0
                };

                // Handle slug
                if (this.editingRole) {
                    // When editing, only send slug if it changed or is empty
                    if (this.form.slug !== this.editingRole.slug) {
                        if (this.form.slug && this.form.slug.trim()) {
                            data.slug = this.form.slug.trim();
                        } else {
                            // Send empty string to trigger regeneration
                            data.slug = '';
                        }
                    }
                    // If slug didn't change, don't send it (backend keeps existing)
                } else {
                    // When creating, send slug if provided, otherwise omit (backend will generate)
                    if (this.form.slug && this.form.slug.trim()) {
                        data.slug = this.form.slug.trim();
                    }
                }

                await axios[method](url, data, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showSuccess(
                    this.editingRole ? 'Role updated successfully' : 'Role created successfully'
                );
                this.closeDialog();
                await this.loadRoles();
            } catch (error) {
                console.error('Error saving role:', error);
                console.error('Error response:', error.response);
                let message = 'Error saving role';

                if (error.response?.data?.errors) {
                    // Handle validation errors
                    const errors = error.response.data.errors;
                    const errorMessages = [];
                    Object.keys(errors).forEach(key => {
                        if (Array.isArray(errors[key])) {
                            errorMessages.push(`${key}: ${errors[key][0]}`);
                        } else {
                            errorMessages.push(`${key}: ${errors[key]}`);
                        }
                    });
                    message = errorMessages.join('\n');
                } else if (error.response?.data?.message) {
                    message = error.response.data.message;
                }

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
        },
        onPerPageChange() {
            this.currentPage = 1;
            this.loadRoles();
        }
    }
};
</script>
