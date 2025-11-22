<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // General Permissions
            ['name' => 'Access Dashboard', 'slug' => 'access-dashboard', 'group' => 'general', 'description' => 'Access to admin dashboard'],
            
            // Content Permissions
            ['name' => 'Manage Pages', 'slug' => 'manage-pages', 'group' => 'content', 'description' => 'Create, edit, delete pages'],
            ['name' => 'Manage Services', 'slug' => 'manage-services', 'group' => 'content', 'description' => 'Create, edit, delete services'],
            ['name' => 'Manage Products', 'slug' => 'manage-products', 'group' => 'content', 'description' => 'Create, edit, delete products'],
            ['name' => 'Manage Portfolio', 'slug' => 'manage-portfolio', 'group' => 'content', 'description' => 'Create, edit, delete portfolio items'],
            ['name' => 'Manage Blog Posts', 'slug' => 'manage-blog', 'group' => 'content', 'description' => 'Create, edit, delete blog posts'],
            ['name' => 'Manage FAQs', 'slug' => 'manage-faqs', 'group' => 'content', 'description' => 'Create, edit, delete FAQs'],
            ['name' => 'Manage Categories', 'slug' => 'manage-categories', 'group' => 'content', 'description' => 'Manage categories and tags'],
            ['name' => 'Manage Media', 'slug' => 'manage-media', 'group' => 'content', 'description' => 'Upload and manage media files'],
            
            // User & Role Permissions
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'group' => 'users', 'description' => 'Create, edit, delete users'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'group' => 'users', 'description' => 'Create, edit, delete roles and permissions'],
            
            // Lead Permissions
            ['name' => 'View Leads', 'slug' => 'view-leads', 'group' => 'leads', 'description' => 'View all leads'],
            ['name' => 'Manage Leads', 'slug' => 'manage-leads', 'group' => 'leads', 'description' => 'Update and delete leads'],
            ['name' => 'Export Leads', 'slug' => 'export-leads', 'group' => 'leads', 'description' => 'Export leads to CSV'],
            
            // Career Permissions
            ['name' => 'Manage Careers', 'slug' => 'manage-careers', 'group' => 'careers', 'description' => 'Manage job postings'],
            ['name' => 'Manage Applications', 'slug' => 'manage-applications', 'group' => 'careers', 'description' => 'Manage job applications'],
            
            // Settings Permissions
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'group' => 'settings', 'description' => 'Manage site settings'],
            ['name' => 'Manage Modules', 'slug' => 'manage-modules', 'group' => 'settings', 'description' => 'Enable/disable modules'],
            ['name' => 'Manage Menus', 'slug' => 'manage-menus', 'group' => 'settings', 'description' => 'Manage navigation menus'],
            
            // System Permissions
            ['name' => 'System Administration', 'slug' => 'system-admin', 'group' => 'system', 'description' => 'Full system access'],
        ];

        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $createdPermissions[$permission['slug']] = Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Create Roles
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Full access to all features and settings',
                'is_system' => true,
                'is_active' => true,
                'order' => 1,
                'permissions' => array_keys($createdPermissions), // All permissions
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can manage content (pages, posts, services, products) but not system settings',
                'is_system' => true,
                'is_active' => true,
                'order' => 2,
                'permissions' => [
                    'access-dashboard',
                    'manage-pages',
                    'manage-services',
                    'manage-products',
                    'manage-portfolio',
                    'manage-blog',
                    'manage-faqs',
                    'manage-categories',
                    'manage-media',
                    'view-leads',
                ],
            ],
            [
                'name' => 'HR User',
                'slug' => 'hr-user',
                'description' => 'Can manage careers and job applications',
                'is_system' => true,
                'is_active' => true,
                'order' => 3,
                'permissions' => [
                    'access-dashboard',
                    'manage-careers',
                    'manage-applications',
                    'view-leads',
                ],
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Limited access, typically for branch managers or content creators',
                'is_system' => true,
                'is_active' => true,
                'order' => 4,
                'permissions' => [
                    'access-dashboard',
                    'manage-media',
                    'view-leads',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );

            // Attach permissions
            $permissionIds = [];
            foreach ($permissions as $permissionSlug) {
                if (isset($createdPermissions[$permissionSlug])) {
                    $permissionIds[] = $createdPermissions[$permissionSlug]->id;
                }
            }
            $role->permissions()->sync($permissionIds);
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
