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
router.beforeEach((to, from, next) => {
    let appName = 'Business Website';
    let title = to.meta && to.meta.title ? to.meta.title : '';
    document.title = `${title ? title + ' - ' : ''}${appName}`;

    // Check authentication for admin routes
    if (to.meta.requiresAuth) {
        const token = localStorage.getItem('admin_token');
        if (!token) {
            next({ name: 'AdminLogin' });
            return;
        }
    }

    // Redirect logged-in admin away from login page
    if (to.name === 'AdminLogin') {
        const token = localStorage.getItem('admin_token');
        if (token) {
            next({ name: 'AdminDashboard' });
            return;
        }
    }

    next();
});



export default router;
