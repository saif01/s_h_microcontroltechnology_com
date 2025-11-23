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
                component: () => import('./components/pages/about/index.vue'),
                name: 'About',
                meta: { title: 'About' }
            },
            {
                path: 'services',
                component: () => import('./components/public/ServicesPage.vue'),
                name: 'Services',
                meta: { title: 'Services' }
            },
            {
                path: 'products',
                component: () => import('./components/public/ProductsPage.vue'),
                name: 'Products',
                meta: { title: 'Products' }
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


// Run before every route request
router.beforeEach(async (to, from, next) => {
    // Start progress bar on route change
    try {
        const progressBar = router.getProgressBar ? router.getProgressBar() : null;
        if (progressBar) {
            if (typeof progressBar.start === 'function') {
                progressBar.start();
                console.log('Progress bar started');
            } else {
                console.warn('Progress bar start method not found');
            }
        } else {
            console.warn('Progress bar instance not available in router hook');
        }
    } catch (error) {
        console.error('Error starting progress bar:', error);
    }

    let appName = 'Business Website';
    let title = to.meta && to.meta.title ? to.meta.title : '';
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
                        const progressBar = router.getProgressBar ? router.getProgressBar() : null;
                        if (progressBar && typeof progressBar.finish === 'function') {
                            progressBar.finish();
                        }
                        next({ name: 'AdminLogin' });
                        return;
                    }
                } catch (error) {
                    const progressBar = router.getProgressBar ? router.getProgressBar() : null;
                    if (progressBar && typeof progressBar.finish === 'function') {
                        progressBar.finish();
                    }
                    next({ name: 'AdminLogin' });
                    return;
                }
            } else {
                const progressBar = router.getProgressBar ? router.getProgressBar() : null;
                if (progressBar && typeof progressBar.finish === 'function') {
                    progressBar.finish();
                }
                next({ name: 'AdminLogin' });
                return;
            }
        }
    }

    // Redirect logged-in admin away from login page
    if (to.name === 'AdminLogin') {
        if (authStore.isAuthenticated && authStore.token) {
            const progressBar = router.getProgressBar ? router.getProgressBar() : null;
            if (progressBar && typeof progressBar.finish === 'function') {
                progressBar.finish();
            }
            next({ name: 'AdminDashboard' });
            return;
        }
    }

    next();
});

// Run after route is resolved
router.afterEach((to, from) => {
    // Finish progress bar after route change
    try {
        const progressBar = router.getProgressBar ? router.getProgressBar() : null;
        if (progressBar && typeof progressBar.finish === 'function') {
            setTimeout(() => {
                progressBar.finish();
                console.log('Progress bar finished');
            }, 100);
        }
    } catch (error) {
        console.error('Error finishing progress bar:', error);
    }
});

// Handle route errors
router.onError((error) => {
    try {
        const progressBar = router.getProgressBar ? router.getProgressBar() : null;
        if (progressBar && typeof progressBar.fail === 'function') {
            progressBar.fail();
        }
    } catch (err) {
        console.error('Error handling progress bar failure:', err);
    }
    console.error('Router error:', error);
});



export default router;
