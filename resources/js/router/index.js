// resources/js/router/index.js

import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('../pages/Landing.vue'),
        name: 'Landing',
        meta: { transition: 'fade' },
    },
    {
        path: '/login',
        component: () => import('../pages/Login.vue'),
        name: 'Login',
        meta: { transition: 'slide-left' },
    },
    {
        path: '/register',
        component: () => import('../pages/Register.vue'),
        name: 'Register',
        meta: { transition: 'slide-right' },
    },
    {
        path: '/reset-password',
        component: () => import('../pages/ResetPassword.vue'),
        name: 'ResetPassword',
        meta: { transition: 'fade' },
    },
    {
        path: '/verify-email',
        component: () => import('../pages/VerifyEmail.vue'),
        name: 'VerifyEmail',
        meta: { transition: 'fade' },
    },

    // ============================================
    // BUYER ROUTES
    // ============================================
    // ✅ FIXED: Removed role: 'buyer' so agents can access too
    {
        path: '/dashboard',
        component: () => import('../pages/Dashboard.vue'),
        name: 'Dashboard',
        meta: {
            requiresAuth: true,
            transition: 'fade',
        },
    },

    // ✅ Property Details Page (public - visitors can browse)
    {
        path: '/property/:id',
        component: () => import('../pages/PropertyDetailsPage.vue'),
        name: 'PropertyDetails',
        meta: {
            requiresAuth: false,
            transition: 'fade',
        },
    },

    // ✅ Properties List (buyer browse)
    {
        path: '/properties',
        component: () => import('../pages/PropertiesList.vue'),
        name: 'PropertiesList',
        meta: {
            requiresAuth: true,
            transition: 'fade',
        },
    },

    // ✅ Become an Agent (buyer → agent application with AI verification)
    {
        path: '/become-agent',
        component: () => import('../pages/BecomeAgent.vue'),
        name: 'BecomeAgent',
        meta: {
            requiresAuth: true,
            role: 'buyer',
            transition: 'fade',
        },
    },

    // ✅ Saved Properties (buyer wishlist)
    {
        path: '/saved-properties',
        component: () => import('../pages/SavedProperties.vue'),
        name: 'SavedProperties',
        meta: {
            requiresAuth: true,
            role: 'buyer',
            transition: 'fade',
        },
    },

    // ✅ Settings page (accessible to both buyers and agents)
    {
        path: '/settings',
        component: () => import('../pages/Settings.vue'),
        name: 'Settings',
        meta: {
            requiresAuth: true,
            transition: 'fade',
        },
    },

    // ✅ Viewings (buyer)
    {
        path: '/viewings',
        component: () => import('../pages/Viewings.vue'),
        name: 'Viewings',
        meta: {
            requiresAuth: true,
            role: 'buyer',
            transition: 'fade',
        },
    },

    // ✅ Documents (buyer)
    {
        path: '/documents',
        component: () => import('../pages/BuyerDocuments.vue'),
        name: 'BuyerDocuments',
        meta: {
            requiresAuth: true,
            role: 'buyer',
            transition: 'fade',
        },
    },

    // User Profile
    {
        path: '/profile',
        component: () => import('../pages/Profile.vue'),
        name: 'Profile',
        meta: { 
            requiresAuth: true,
            transition: 'fade',
        }
    },

    // ============================================
    // AGENT ROUTES
    // ============================================
    {
        path: '/agent/dashboard',
        component: () => import('../pages/AgentDashboard.vue'),
        name: 'AgentDashboard',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    {
        path: '/agent/properties',
        component: () => import('../pages/Agent/MyProperties.vue'),
        name: 'MyProperties',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    // ✅ FIXED: Import from Agent subfolder
    {
        path: '/agent/properties/create',
        component: () => import('../pages/Agent/PropertyListingForm.vue'),
        name: 'CreateProperty',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    // ✅ FIXED: Import from Agent subfolder
    {
        path: '/agent/properties/:id/edit',
        component: () => import('../pages/Agent/PropertyListingForm.vue'),
        name: 'EditProperty',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    // Agent Saved Properties (My Wallet)
    {
        path: '/agent/saved-properties',
        component: () => import('../pages/Agent/AgentSavedProperties.vue'),
        name: 'AgentSavedProperties',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    {
        path: '/agent/viewings',
        component: () => import('../pages/AgentViewings.vue'),
        name: 'AgentViewings',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    {
        path: '/agent/calendar',
        component: () => import('../pages/Agent/AgentCalendar.vue'),
        name: 'AgentCalendar',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    // ✅ Documents (agent)
    {
        path: '/agent/documents',
        component: () => import('../pages/Agent/DocumentsPage.vue'),
        name: 'AgentDocuments',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    // ✅ Agent Public Profile (buyer views agent + rates)
    {
        path: '/agent-profile/:userId',
        component: () => import('../pages/AgentPublicProfile.vue'),
        name: 'AgentPublicProfile',
        meta: {
            requiresAuth: true,
            role: 'buyer',
            transition: 'fade',
        },
    },

    // ✅ Buyer Profile (agent views buyer info)
    {
        path: '/agent/buyer-profile/:userId',
        component: () => import('../pages/BuyerProfileView.vue'),
        name: 'BuyerProfileView',
        meta: {
            requiresAuth: true,
            role: 'agent',
            transition: 'fade',
        },
    },

    // ============================================
    // MESSAGING ROUTES (buyer & agent)
    // ============================================
    {
        path: '/conversations',
        component: () => import('../pages/ConversationsList.vue'),
        name: 'ConversationsList',
        meta: {
            requiresAuth: true,
            transition: 'fade',
        },
    },
    {
        path: '/conversations/:userId',
        component: () => import('../pages/MessageConversation.vue'),
        name: 'MessageConversation',
        meta: {
            requiresAuth: true,
            transition: 'fade',
        },
    },
    {
        path: '/agent/conversations',
        redirect: '/conversations',
    },

    // ============================================
    // ADMIN ROUTES
    // ============================================
    {
        path: '/admin/dashboard',
        component: () => import('../pages/AdminDashboard.vue'),
        name: 'AdminDashboard',
        meta: {
            requiresAuth: true,
            role: 'admin',
            transition: 'fade',
        },
    },

    // ============================================
    // DOCUMENT EDITOR (buyer & agent)
    // ============================================
    {
        path: '/documents/:id/edit',
        component: () => import('../pages/DocumentEditor.vue'),
        name: 'DocumentEditor',
        meta: {
            requiresAuth: true,
            transition: 'fade',
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// ============================================
// AUTH GUARD
// ============================================
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    console.log('🔐 Checking route:', to.path, 'User role:', user.role);

    // Check if route requires authentication
    if (to.meta.requiresAuth && !token) {
        console.log('❌ No token, redirecting to login');
        next('/login');
        return;
    }

    // Check if route requires specific role
    if (to.meta.role && user.role !== to.meta.role) {
        console.log(`❌ Role mismatch. Required: ${to.meta.role}, Got: ${user.role}`);
        // Redirect to appropriate dashboard based on role
        if (user.role === 'admin') {
            next('/admin/dashboard');
        } else if (user.role === 'agent') {
            next('/agent/dashboard');
        } else {
            next('/dashboard');
        }
        return;
    }

    next();
});

export default router;