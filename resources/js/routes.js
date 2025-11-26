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
                component: () => import('./components/public/pages/HomePage.vue'),
                name: 'Home',
                meta: { title: 'Home' }
            },
            {
                path: 'about',
                component: () => import('./components/public/pages/AboutPage.vue'),
                name: 'About',
                meta: { title: 'About Us' }
            },
            {
                path: 'services',
                component: () => import('./components/public/services/ServicesPage.vue'),
                name: 'Services',
                meta: { title: 'Services' }
            },
            {
                path: 'services/:slug',
                component: () => import('./components/public/services/ServiceDetailPage.vue'),
                name: 'ServiceDetail',
                meta: { title: 'Service Details' }
            },
            {
                path: 'products',
                component: () => import('./components/public/products/ProductsPage.vue'),
                name: 'Products',
                meta: { title: 'Products' }
            },
            {
                path: 'products/:slug',
                component: () => import('./components/public/products/ProductDetailPage.vue'),
                name: 'ProductDetail',
                meta: { title: 'Product Details' }
            },
            {
                path: 'contact',
                component: () => import('./components/public/pages/ContactPage.vue'),
                name: 'Contact',
                meta: { title: 'Contact' }
            },
            {
                path: 'blog',
                component: () => import('./components/public/blog/BlogPage.vue'),
                name: 'Blog',
                meta: { title: 'Blog' }
            },
            {
                path: 'blog/:slug',
                component: () => import('./components/public/blog/BlogDetailPage.vue'),
                name: 'BlogDetail',
                meta: { title: 'Blog Post' }
            },
            {
                path: 'careers',
                component: () => import('./components/public/career/CareerPage.vue'),
                name: 'Careers',
                meta: { title: 'Careers' }
            },
            {
                path: 'careers/:slug',
                component: () => import('./components/public/career/CareerDetailPage.vue'),
                name: 'CareerDetail',
                meta: { title: 'Career Details' }
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
                path: 'services',
                component: () => import('./components/admin/service/AdminServices.vue'),
                name: 'AdminServices',
                meta: { title: 'Services Management' }
            },
            {
                path: 'about',
                component: () => import('./components/admin/about/AdminAbout.vue'),
                name: 'AdminAbout',
                meta: { title: 'About Page Management' }
            },
            {
                path: 'products',
                component: () => import('./components/admin/products/AdminProducts.vue'),
                name: 'AdminProducts',
                meta: { title: 'Products Management' }
            },
            {
                path: 'categories',
                component: () => import('./components/admin/products/AdminCategories.vue'),
                name: 'AdminCategories',
                meta: { title: 'Categories Management' }
            },
            {
                path: 'tags',
                component: () => import('./components/admin/products/AdminTags.vue'),
                name: 'AdminTags',
                meta: { title: 'Tags Management' }
            },
            {
                path: 'leads',
                component: () => import('./components/admin/leads/AdminLeads.vue'),
                name: 'AdminLeads',
                meta: { title: 'Leads Management' }
            },
            {
                path: 'newsletters',
                component: () => import('./components/admin/newsletters/AdminNewsletters.vue'),
                name: 'AdminNewsletters',
                meta: { title: 'Newsletter Subscriptions' }
            },
            {
                path: 'users',
                component: () => import('./components/admin/users/AdminUsers.vue'),
                name: 'AdminUsers',
                meta: { title: 'User Management' }
            },
            {
                path: 'roles',
                component: () => import('./components/admin/users/AdminRoles.vue'),
                name: 'AdminRoles',
                meta: { title: 'Role Management' }
            },
            {
                path: 'permissions',
                component: () => import('./components/admin/users/AdminPermissions.vue'),
                name: 'AdminPermissions',
                meta: { title: 'Permission Management' }
            },
            {
                path: 'settings',
                component: () => import('./components/admin/settings/AdminSettings.vue'),
                name: 'AdminSettings',
                meta: { title: 'Settings' }
            },
            {
                path: 'login-logs',
                component: () => import('./components/admin/logs/AdminLoginLogs.vue'),
                name: 'AdminLoginLogs',
                meta: { title: 'Login Logs Management' }
            },
            {
                path: 'visitor-logs',
                component: () => import('./components/admin/logs/AdminVisitorLogs.vue'),
                name: 'AdminVisitorLogs',
                meta: { title: 'Visitor Logs Management' }
            },
            {
                path: 'blog',
                component: () => import('./components/admin/blog/AdminBlog.vue'),
                name: 'AdminBlog',
                meta: { title: 'Blog Management' }
            },
            {
                path: 'blog/categories',
                component: () => import('./components/admin/blog/AdminBlogCategories.vue'),
                name: 'AdminBlogCategories',
                meta: { title: 'Blog Categories Management' }
            },
            {
                path: 'careers',
                component: () => import('./components/admin/career/AdminCareers.vue'),
                name: 'AdminCareers',
                meta: { title: 'Careers Management' }
            },
            {
                path: 'job-applications',
                component: () => import('./components/admin/career/AdminJobApplications.vue'),
                name: 'AdminJobApplications',
                meta: { title: 'Job Applications Management' }
            },
        ]
    },

    // Admin Login
    {
        path: '/admin/login',
        component: () => import('./components/admin/auth/AdminLogin.vue'),
        name: 'AdminLogin',
        meta: { title: 'Admin Login' }
    },














    {
        path: '/:pathMatch(.*)*',
        component: () => import('./components/public/pages/HomePage.vue'),
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
