import {
    createRouter,
    createWebHistory
} from 'vue-router'


// All Routes define
const routes = [
    // Public Routes
    {
        path: '/',
        component: () => import('./components/public/PublicLayout.vue'),
        children: [
            {
                path: '',
                component: () => import('./components/public/HomePage.vue'),
                name: 'Home',
                meta: { title: 'Home' }
            },
            {
                path: 'about',
                component: () => import('./components/public/AboutPage.vue'),
                name: 'About',
                meta: { title: 'About Us' }
            },
            {
                path: 'services',
                component: () => import('./components/public/ServicesPage.vue'),
                name: 'Services',
                meta: { title: 'Services' }
            },
            {
                path: 'services/:slug',
                component: () => import('./components/public/ServiceDetailPage.vue'),
                name: 'ServiceDetail',
                meta: { title: 'Service Details' }
            },
            {
                path: 'products',
                component: () => import('./components/public/ProductsPage.vue'),
                name: 'Products',
                meta: { title: 'Products' }
            },
            {
                path: 'products/:slug',
                component: () => import('./components/public/ProductDetailPage.vue'),
                name: 'ProductDetail',
                meta: { title: 'Product Details' }
            },
            {
                path: 'contact',
                component: () => import('./components/public/ContactPage.vue'),
                name: 'Contact',
                meta: { title: 'Contact' }
            },
        ]
    },

    // Admin Routes
    {
        path: '/admin',
        component: () => import('./components/admin/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: { name: 'AdminDashboard' }
            },
            {
                path: 'dashboard',
                component: () => import('./components/admin/AdminDashboard.vue'),
                name: 'AdminDashboard',
                meta: { title: 'Admin Dashboard' }
            },
            {
                path: 'pages',
                component: () => import('./components/admin/AdminPages.vue'),
                name: 'AdminPages',
                meta: { title: 'Pages Management' }
            },
            {
                path: 'services',
                component: () => import('./components/admin/AdminServices.vue'),
                name: 'AdminServices',
                meta: { title: 'Services Management' }
            },
            {
                path: 'products',
                component: () => import('./components/admin/AdminProducts.vue'),
                name: 'AdminProducts',
                meta: { title: 'Products Management' }
            },
            {
                path: 'categories',
                component: () => import('./components/admin/AdminCategories.vue'),
                name: 'AdminCategories',
                meta: { title: 'Categories Management' }
            },
            {
                path: 'tags',
                component: () => import('./components/admin/AdminTags.vue'),
                name: 'AdminTags',
                meta: { title: 'Tags Management' }
            },
            {
                path: 'leads',
                component: () => import('./components/admin/AdminLeads.vue'),
                name: 'AdminLeads',
                meta: { title: 'Leads Management' }
            },
            {
                path: 'users',
                component: () => import('./components/admin/AdminUsers.vue'),
                name: 'AdminUsers',
                meta: { title: 'User Management' }
            },
            {
                path: 'roles',
                component: () => import('./components/admin/AdminRoles.vue'),
                name: 'AdminRoles',
                meta: { title: 'Role Management' }
            },
            {
                path: 'permissions',
                component: () => import('./components/admin/AdminPermissions.vue'),
                name: 'AdminPermissions',
                meta: { title: 'Permission Management' }
            },
            {
                path: 'settings',
                component: () => import('./components/admin/AdminSettings.vue'),
                name: 'AdminSettings',
                meta: { title: 'Settings' }
            },
            {
                path: 'login-logs',
                component: () => import('./components/admin/AdminLoginLogs.vue'),
                name: 'AdminLoginLogs',
                meta: { title: 'Login Logs Management' }
            },
            {
                path: 'visitor-logs',
                component: () => import('./components/admin/AdminVisitorLogs.vue'),
                name: 'AdminVisitorLogs',
                meta: { title: 'Visitor Logs Management' }
            },
        ]
    },

    // Admin Login
    {
        path: '/admin/login',
        component: () => import('./components/admin/AdminLogin.vue'),
        name: 'AdminLogin',
        meta: { title: 'Admin Login' }
    },














    {
        path: '/:pathMatch(.*)*',
        component: () => import('./components/public/HomePage.vue'),
        name: 'NotFound',
        meta: {
            title: 'Page Not Found',
        },
    }



];

const router = createRouter({
    history: createWebHistory(),
    //history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});


// Helper to handle progress bar
const progressBar = {
    start: () => {
        const bar = router.getProgressBar ? router.getProgressBar() : null;
        if (bar && typeof bar.start === 'function') bar.start();
    },
    finish: () => {
        const bar = router.getProgressBar ? router.getProgressBar() : null;
        if (bar && typeof bar.finish === 'function') bar.finish();
    },
    fail: () => {
        const bar = router.getProgressBar ? router.getProgressBar() : null;
        if (bar && typeof bar.fail === 'function') bar.fail();
    }
};

// Run before every route request
router.beforeEach(async (to, from, next) => {
    // Start progress bar on route change
    progressBar.start();

    const appName = import.meta.env.VITE_APP_NAME || 'Micro Control Technology';
    const title = to.meta && to.meta.title ? to.meta.title : '';
    document.title = `${title ? title + ' - ' : ''}${appName}`;

    // Import auth store
    const { useAuthStore } = await import('./stores/auth');
    const authStore = useAuthStore();

    // Check authentication for admin routes
    if (to.meta.requiresAuth) {
        if (!authStore.isAuthenticated || !authStore.token) {
            // Try to fetch user if token exists
            if (authStore.token) {
                try {
                    await authStore.fetchUser();
                    if (!authStore.isAuthenticated) {
                        progressBar.finish();
                        next({ name: 'AdminLogin' });
                        return;
                    }
                } catch (error) {
                    progressBar.finish();
                    next({ name: 'AdminLogin' });
                    return;
                }
            } else {
                progressBar.finish();
                next({ name: 'AdminLogin' });
                return;
            }
        }
    }

    // Redirect logged-in admin away from login page
    if (to.name === 'AdminLogin') {
        if (authStore.isAuthenticated && authStore.token) {
            progressBar.finish();
            next({ name: 'AdminDashboard' });
            return;
        }
    }

    next();
});

// Run after route is resolved
router.afterEach((to, from) => {
    // Finish progress bar after route change
    setTimeout(() => {
        progressBar.finish();
    }, 100);
});

// Handle route errors
router.onError((error) => {
    progressBar.fail();
    console.error('Router error:', error);
});



export default router;
