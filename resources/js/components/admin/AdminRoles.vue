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
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="activeFilter" :items="activeOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadRoles"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search roles" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadRoles"></v-text-field>
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
                            <th class="sortable" @click="onSort('name')">
                                <div class="d-flex align-center">
                                    Name
                                    <v-icon :icon="getSortIcon('name')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('slug')">
                                <div class="d-flex align-center">
                                    Slug
                                    <v-icon :icon="getSortIcon('slug')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Permissions</th>
                            <th class="sortable" @click="onSort('is_active')">
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('is_active')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
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

                        <!-- Permissions Section -->
                        <v-divider class="my-4"></v-divider>
                        <div class="mb-2">
                            <h3 class="text-h6 mb-2">Permissions</h3>
                            <p class="text-caption text-grey mb-4">Select permissions to assign to this role</p>
                        </div>
                        <v-expansion-panels v-if="Object.keys(safeGroupedPermissions).length > 0" variant="accordion"
                            class="mb-4">
                            <v-expansion-panel v-for="(permissions, group) in safeGroupedPermissions" :key="group">
                                <v-expansion-panel-title>
                                    <div class="d-flex justify-space-between align-center w-100">
                                        <span>{{ group.charAt(0).toUpperCase() + group.slice(1) }}</span>
                                        <v-chip size="small" color="primary" variant="text">
                                            {{ getSelectedCountInGroup(group) }} / {{ permissions.length }} selected
                                        </v-chip>
                                    </div>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <v-row v-if="Array.isArray(permissions)">
                                        <v-col v-for="permission in permissions" :key="permission.id" cols="12" md="6">
                                            <v-checkbox :model-value="isFormPermissionSelected(permission.id)"
                                                @update:model-value="toggleFormPermission(permission.id)"
                                                :label="permission.name" :hint="permission.description" persistent-hint
                                                density="compact">
                                            </v-checkbox>
                                        </v-col>
                                    </v-row>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-alert v-else type="info" variant="tonal" class="mb-4">
                            No permissions available. Please ensure permissions are loaded.
                        </v-alert>
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
                        <div v-if="Object.keys(safeGroupedPermissions).length === 0" class="text-center py-4">
                            <p class="text-grey">No permissions available</p>
                        </div>
                        <div v-else>
                            <div v-for="(permissions, group) in safeGroupedPermissions" :key="group" class="mb-6">
                                <h3 class="text-h6 mb-3">{{ group.charAt(0).toUpperCase() + group.slice(1) }}</h3>
                                <v-row v-if="Array.isArray(permissions)">
                                    <v-col v-for="permission in permissions" :key="permission.id" cols="12" md="6">
                                        <v-checkbox :model-value="isPermissionSelected(permission.id)"
                                            @update:model-value="togglePermission(permission.id)"
                                            :label="permission.name" :hint="permission.description" persistent-hint
                                            density="compact"></v-checkbox>
                                    </v-col>
                                </v-row>
                                <v-alert v-else type="warning" variant="tonal" class="mt-2">
                                    Invalid permission group structure
                                </v-alert>
                            </div>
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
import adminPaginationMixin from '../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
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
            savingPermissions: false,
            selectedPermissions: [],
            activeFilter: null,
            activeOptions: [
                { title: 'Active', value: true },
                { title: 'Inactive', value: false }
            ],
            form: {
                name: '',
                slug: '',
                description: '',
                is_active: true,
                order: 0,
                permissions: []
            },
            rules: {
                required: value => !!value || 'This field is required'
            },
            autoGeneratingSlug: false
        };
    },
    computed: {
        safeGroupedPermissions() {
            // Ensure groupedPermissions is always an object
            if (!this.groupedPermissions || typeof this.groupedPermissions !== 'object') {
                return {};
            }
            // Filter out any non-array values
            const safe = {};
            Object.keys(this.groupedPermissions).forEach(key => {
                if (Array.isArray(this.groupedPermissions[key])) {
                    safe[key] = this.groupedPermissions[key];
                }
            });
            return safe;
        }
    },
    async mounted() {
        await this.loadRoles();
        await this.loadPermissions();
    },
    methods: {
        async loadRoles() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.activeFilter !== null) {
                    params.active = this.activeFilter;
                }

                const response = await axios.get('/api/v1/roles', {
                    params,
                    headers: this.getAuthHeaders()
                });

                // Handle both paginated and non-paginated responses
                if (response.data.data) {
                    // Paginated response
                    this.roles = response.data.data || [];
                    this.updatePagination(response.data);
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
                if (error.response?.status === 404) {
                    this.handleApiError(error, 'Roles endpoint not found. Please ensure migrations and seeders have run.');
                } else {
                    this.handleApiError(error, 'Failed to load roles');
                }
            } finally {
                this.loading = false;
            }
        },
        async loadPermissions() {
            try {
                const response = await axios.get('/api/v1/permissions', {
                    headers: this.getAuthHeaders()
                });

                // Handle different response structures
                let permissionsData = response.data;

                // If response has a data property, use it
                if (response.data && response.data.data) {
                    permissionsData = response.data.data;
                }

                // Initialize as empty object if not valid
                if (!permissionsData || typeof permissionsData !== 'object') {
                    console.warn('Invalid permissions response structure:', permissionsData);
                    this.groupedPermissions = {};
                    this.permissions = [];
                    return;
                }

                // Check if it's an array (ungrouped)
                if (Array.isArray(permissionsData)) {
                    // Group by group property if available, otherwise use 'general'
                    this.groupedPermissions = {};
                    permissionsData.forEach(permission => {
                        const group = permission.group || permission.group_name || 'general';
                        if (!this.groupedPermissions[group]) {
                            this.groupedPermissions[group] = [];
                        }
                        this.groupedPermissions[group].push(permission);
                    });
                } else {
                    // It's an object (grouped)
                    this.groupedPermissions = permissionsData;
                }

                // Flatten for easier access
                this.permissions = [];
                Object.values(this.groupedPermissions).forEach(group => {
                    // Ensure group is an array before spreading
                    if (Array.isArray(group)) {
                        this.permissions.push(...group);
                    } else {
                        console.warn('Invalid group structure:', group);
                    }
                });
            } catch (error) {
                console.error('Error loading permissions:', error);
                this.groupedPermissions = {};
                this.permissions = [];
                this.handleApiError(error, 'Failed to load permissions');
            }
        },
        async openDialog(role) {
            // Ensure permissions are loaded before opening dialog
            if (Object.keys(this.groupedPermissions).length === 0) {
                await this.loadPermissions();
            }

            if (role) {
                this.editingRole = role;
                // Load full role data if permissions are not loaded
                let rolePermissions = [];
                if (role.permissions) {
                    // Handle both array of objects and array of IDs
                    rolePermissions = role.permissions.map(p => {
                        if (typeof p === 'object' && p.id) {
                            return p.id;
                        }
                        return p;
                    });
                } else {
                    // Try to fetch role with permissions if not included
                    try {
                        const response = await axios.get(`/api/v1/roles/${role.id}`, {
                            headers: this.getAuthHeaders()
                        });
                        if (response.data && response.data.permissions) {
                            rolePermissions = response.data.permissions.map(p => {
                                if (typeof p === 'object' && p.id) {
                                    return p.id;
                                }
                                return p;
                            });
                        }
                    } catch (error) {
                        console.warn('Could not load role permissions:', error);
                    }
                }

                this.form = {
                    name: role.name,
                    slug: role.slug,
                    description: role.description || '',
                    is_active: role.is_active,
                    order: role.order || 0,
                    permissions: rolePermissions
                };
            } else {
                this.editingRole = null;
                this.form = {
                    name: '',
                    slug: '',
                    description: '',
                    is_active: true,
                    order: 0,
                    permissions: []
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
                order: 0,
                permissions: []
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

                // Include permissions in the request
                if (this.form.permissions && Array.isArray(this.form.permissions)) {
                    data.permissions = this.form.permissions;
                }

                await axios[method](url, data, {
                    headers: this.getAuthHeaders()
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

                this.handleApiError(error, 'Error saving role');
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
                await axios.delete(`/api/v1/roles/${role.id}`, {
                    headers: this.getAuthHeaders()
                });

                this.showSuccess('Role deleted successfully');
                await this.loadRoles();
            } catch (error) {
                this.handleApiError(error, 'Error deleting role');
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
        // Form permission methods (for role dialog)
        isFormPermissionSelected(permissionId) {
            return this.form.permissions.includes(permissionId);
        },
        toggleFormPermission(permissionId) {
            const index = this.form.permissions.indexOf(permissionId);
            if (index > -1) {
                this.form.permissions.splice(index, 1);
            } else {
                this.form.permissions.push(permissionId);
            }
        },
        getSelectedCountInGroup(group) {
            if (!this.safeGroupedPermissions[group] || !Array.isArray(this.safeGroupedPermissions[group])) {
                return 0;
            }
            return this.safeGroupedPermissions[group].filter(p => this.form.permissions.includes(p.id)).length;
        },
        async savePermissions() {
            this.savingPermissions = true;
            try {
                await axios.put(`/api/v1/roles/${this.selectedRole.id}/permissions`, {
                    permissions: this.selectedPermissions
                }, {
                    headers: this.getAuthHeaders()
                });

                this.showSuccess('Permissions updated successfully');
                this.closePermissionDialog();
                await this.loadRoles();
            } catch (error) {
                this.handleApiError(error, 'Error saving permissions');
            } finally {
                this.savingPermissions = false;
            }
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadRoles();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadRoles();
        }
    }
};
</script>
