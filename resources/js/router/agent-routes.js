// resources/js/router/agent-routes.js

/**
 * Agent Routes Configuration
 * Routes for agent dashboard and property management
 * 
 * All routes require authentication and agent role
 */

export const agentRoutes = [
  // ============================================
  // AGENT DASHBOARD
  // ============================================
  {
    path: '/agent/dashboard',
    component: () => import('../pages/AgentDashboard.vue'),
    name: 'agent.dashboard',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'Agent Dashboard'
    }
  },

  // ============================================
  // MESSAGING (NEW)
  // ============================================
  {
    path: '/agent/messages',
    component: () => import('../pages/Agent/MessagesPage.vue'),
    name: 'agent.messages',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'Messages'
    }
  },

  {
    path: '/agent/messages/:buyerId',
    component: () => import('../pages/Agent/MessageConversation.vue'),
    name: 'agent.conversation',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'Conversation'
    }
  },

  {
    path: '/buyer/messages',
    component: () => import('../pages/Buyer/MessagesPage.vue'),
    name: 'buyer.messages',
    meta: {
      requiresAuth: true,
      role: 'buyer',
      transition: 'fade',
      title: 'Messages'
    }
  },

  {
    path: '/buyer/messages/:agentId',
    component: () => import('../pages/Buyer/MessageConversation.vue'),
    name: 'buyer.conversation',
    meta: {
      requiresAuth: true,
      role: 'buyer',
      transition: 'fade',
      title: 'Conversation'
    }
  },

  // ============================================
  // PROPERTIES
  // ============================================
  {
    path: '/agent/properties',
    component: () => import('../pages/Agent/MyProperties.vue'),
    name: 'agent.properties',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'My Properties'
    }
  },

  {
    path: '/agent/properties/create',
    component: () => import('../pages/Agent/PropertyListingForm.vue'),
    name: 'agent.properties.create',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'Add New Property'
    }
  },

  {
    path: '/agent/properties/:id/edit',
    component: () => import('../pages/Agent/PropertyListingForm.vue'),
    name: 'agent.properties.edit',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'Edit Property'
    }
  },

  // ============================================
  // DOCUMENTS
  // ============================================
  {
    path: '/agent/documents',
    component: () => import('../pages/Agent/DocumentsPage.vue'),
    name: 'agent.documents',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'Documents'
    }
  },

  // ============================================
  // VIEWINGS
  // ============================================
  {
    path: '/agent/viewings',
    component: () => import('../pages/Agent/ViewingsPage.vue'),
    name: 'agent.viewings',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
      title: 'Viewings'
    }
  },

  {
    path: '/property/:id',
    component: () => import('../pages/PropertyDetailsPage.vue'),
    name: 'PropertyDetails',
    meta: {
      requiresAuth: true,
      transition: 'fade',
    }
  },
  
  // Agent create property
  {
    path: '/agent/properties/create',
    component: () => import('../pages/Agent/PropertyListingForm.vue'),
    name: 'CreateProperty',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
    }
  },
  
  // Agent edit property
  {
    path: '/agent/properties/:id/edit',
    component: () => import('../pages/Agent/PropertyListingForm.vue'),
    name: 'EditProperty',
    meta: {
      requiresAuth: true,
      role: 'agent',
      transition: 'fade',
    }
  },
];