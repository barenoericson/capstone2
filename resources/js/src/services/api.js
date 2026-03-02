// resources/js/pages/src/services/api.js
import axios from 'axios';

// Get the base URL from .env or default to your Laravel app
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

// Create axios instance
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor - Add auth token to headers
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - Handle token refresh or redirect to login
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Unauthorized - clear token and redirect to login
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// ============================================
// AUTHENTICATION ENDPOINTS
// ============================================
export const authAPI = {
  // Register new user
  register: (data) => {
    return apiClient.post('/auth/register', {
      name: data.name,
      email: data.email,
      password: data.password,
      password_confirmation: data.password_confirmation,
      role: data.role || 'buyer',
    });
  },

  // Login user
  login: (data) => {
    return apiClient.post('/auth/login', {
      email: data.email,
      password: data.password,
    });
  },

  // Get current user profile
  getProfile: () => {
    return apiClient.get('/auth/me');
  },

  // Logout user
  logout: () => {
    return apiClient.post('/auth/logout');
  },

  // Request password reset
  forgotPassword: (email) => {
    return apiClient.post('/auth/forgot-password', { email });
  },

  // Reset password with token
  resetPassword: (data) => {
    return apiClient.post('/auth/reset-password', {
      email: data.email,
      token: data.token,
      password: data.password,
      password_confirmation: data.password_confirmation,
    });
  },

  // Verify email
  verifyEmail: (token) => {
    return apiClient.post('/auth/verify-email', { token });
  },

  // Refresh auth token
  refreshToken: () => {
    return apiClient.post('/auth/refresh-token');
  },

  // Update user profile
  updateProfile: (data) => {
    return apiClient.put('/user/profile', data);
  },

  // Change password
  changePassword: (data) => {
    return apiClient.post('/user/change-password', {
      current_password: data.currentPassword,
      new_password: data.newPassword,
      new_password_confirmation: data.confirmPassword,
    });
  },
};

// ============================================
// PROPERTY ENDPOINTS
// ============================================
export const propertiesAPI = {
  // Get all properties
  getAll: (filters = {}) => {
    return apiClient.get('/properties', { params: filters });
  },

  // Get single property
  get: (id) => {
    return apiClient.get(`/properties/${id}`);
  },

  // Create property (Agent only)
  create: (data) => {
    return apiClient.post('/agent/properties', data);
  },

  // Update property (Agent only)
  update: (id, data) => {
    return apiClient.put(`/agent/properties/${id}`, data);
  },

  // Delete property (Agent only)
  delete: (id) => {
    return apiClient.delete(`/agent/properties/${id}`);
  },

  // Get agent properties
  getAgentProperties: () => {
    return apiClient.get('/agent/properties');
  },

  // Upload property photos
  uploadPhotos: (propertyId, files) => {
    const formData = new FormData();
    files.forEach((file) => {
      formData.append('photos[]', file);
    });
    return apiClient.post(`/agent/properties/${propertyId}/photos`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },

  // Save property for buyer
  saveProperty: (propertyId) => {
    return apiClient.post(`/buyer/properties/${propertyId}/save`);
  },

  // Get saved properties
  getSavedProperties: () => {
    return apiClient.get('/buyer/saved-properties');
  },
};

// ============================================
// AGENT ENDPOINTS
// ============================================
export const agentsAPI = {
  // Get agent profile
  getProfile: (id) => {
    return apiClient.get(`/agents/${id}`);
  },

  // Get current agent profile
  getMyProfile: () => {
    return apiClient.get('/agent/profile');
  },

  // Update agent profile
  updateProfile: (data) => {
    return apiClient.put('/agent/profile', data);
  },

  // Get agent reviews
  getReviews: (agentId) => {
    return apiClient.get(`/agents/${agentId}/reviews`);
  },

  // Submit agent review
  submitReview: (agentId, data) => {
    return apiClient.post(`/agents/${agentId}/reviews`, {
      rating: data.rating,
      review: data.review,
    });
  },

  // Get agent availability
  getAvailability: (agentId) => {
    return apiClient.get(`/agents/${agentId}/availability`);
  },

  // Update agent availability
  updateAvailability: (data) => {
    return apiClient.put('/agent/availability', data);
  },
};

// ============================================
// VIEWING/APPOINTMENT ENDPOINTS
// ============================================
export const viewingsAPI = {
  // Request property viewing
  request: (propertyId, data) => {
    return apiClient.post('/buyer/viewings', {
      property_id: propertyId,
      viewing_date: data.viewing_date,
      viewing_time: data.viewing_time,
      notes: data.notes,
    });
  },

  // Get buyer viewings
  getBuyerViewings: () => {
    return apiClient.get('/buyer/viewings');
  },

  // Get agent viewings
  getAgentViewings: () => {
    return apiClient.get('/agent/viewings');
  },

  // Approve viewing request (Agent)
  approve: (viewingId) => {
    return apiClient.put(`/agent/viewings/${viewingId}/approve`);
  },

  // Reject viewing request (Agent)
  reject: (viewingId, reason) => {
    return apiClient.put(`/agent/viewings/${viewingId}/reject`, { reason });
  },

  // Mark viewing as completed
  markCompleted: (viewingId) => {
    return apiClient.put(`/agent/viewings/${viewingId}/completed`);
  },

  // Send viewing reminder
  sendReminder: (viewingId) => {
    return apiClient.post(`/agent/viewings/${viewingId}/reminder`);
  },
};

// ============================================
// MESSAGING ENDPOINTS
// ============================================
export const messagesAPI = {
  // Get conversations
  getConversations: () => {
    return apiClient.get('/user/messages');
  },

  // Send message
  send: (recipientId, message) => {
    return apiClient.post('/messages', {
      recipient_id: recipientId,
      message: message,
    });
  },

  // Get conversation messages
  getConversation: (userId) => {
    return apiClient.get(`/conversations/${userId}/messages`);
  },

  // Mark message as read
  markAsRead: (messageId) => {
    return apiClient.put(`/messages/${messageId}/read`);
  },

  // Get unread count
  getUnreadCount: () => {
    return apiClient.get('/messages/unread-count');
  },
};

// ============================================
// DOCUMENT ENDPOINTS
// ============================================
export const documentsAPI = {
  // Upload document
  upload: (propertyId, file, documentType) => {
    const formData = new FormData();
    formData.append('document', file);
    formData.append('document_type', documentType);
    formData.append('property_id', propertyId);

    return apiClient.post('/agent/documents', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },

  // Get documents
  getDocuments: () => {
    return apiClient.get('/user/documents');
  },

  // Get agent documents
  getAgentDocuments: () => {
    return apiClient.get('/agent/documents');
  },

  // Get buyer documents
  getBuyerDocuments: () => {
    return apiClient.get('/buyer/documents');
  },

  // Sign document (buyer)
  sign: (documentId, data) => {
    return apiClient.post(`/buyer/documents/${documentId}/sign`, data);
  },

  // Sign document (agent)
  agentSign: (documentId, data) => {
    return apiClient.post(`/agent/documents/${documentId}/sign`, data);
  },

  // Download document
  download: (documentId) => {
    return apiClient.get(`/documents/${documentId}/download`, {
      responseType: 'blob',
    });
  },

  // Delete document
  delete: (documentId) => {
    return apiClient.delete(`/documents/${documentId}`);
  },
};

// ============================================
// ADMIN ENDPOINTS
// ============================================
export const adminAPI = {
  // Dashboard
  getDashboardOverview: () => apiClient.get('/admin/dashboard/overview'),
  getDashboardStats: (params = {}) => apiClient.get('/admin/dashboard/stats', { params }),
  getRecentActivity: (params = {}) => apiClient.get('/admin/dashboard/recent-activity', { params }),

  // User Management
  getUsers: (filters = {}) => apiClient.get('/admin/users', { params: filters }),
  getUserDetail: (userId) => apiClient.get(`/admin/users/${userId}`),
  updateUser: (userId, data) => apiClient.put(`/admin/users/${userId}`, data),
  deleteUser: (userId) => apiClient.delete(`/admin/users/${userId}`),
  suspendUser: (userId) => apiClient.post(`/admin/users/${userId}/suspend`),
  activateUser: (userId) => apiClient.post(`/admin/users/${userId}/activate`),

  // Agent Management
  getAgents: (filters = {}) => apiClient.get('/admin/agents', { params: filters }),
  getPendingAgents: () => apiClient.get('/admin/agents/pending'),
  getVerifiedAgents: () => apiClient.get('/admin/agents/verified'),
  getRejectedAgents: () => apiClient.get('/admin/agents/rejected'),
  getAgentDetail: (agentId) => apiClient.get(`/admin/agents/${agentId}`),
  verifyAgent: (appId) => apiClient.post(`/admin/agents/${appId}/verify`),
  rejectAgent: (appId, rejectionReason) => apiClient.post(`/admin/agents/${appId}/reject`, { rejection_reason: rejectionReason }),

  // Property Management
  getProperties: (filters = {}) => apiClient.get('/admin/properties', { params: filters }),
  getPropertyDetail: (propertyId) => apiClient.get(`/admin/properties/${propertyId}`),
  deleteProperty: (propertyId) => apiClient.delete(`/admin/properties/${propertyId}`),
  featureProperty: (propertyId) => apiClient.post(`/admin/properties/${propertyId}/feature`),
  unfeatureProperty: (propertyId) => apiClient.post(`/admin/properties/${propertyId}/unfeature`),

  // Photo Moderation
  getPendingPhotos: () => apiClient.get('/admin/photos/pending'),
  approvePhoto: (photoId) => apiClient.post(`/admin/photos/${photoId}/approve`),
  rejectPhoto: (photoId) => apiClient.post(`/admin/photos/${photoId}/reject`),

  // Audit Logs
  getAuditLogs: (filters = {}) => apiClient.get('/admin/audit-logs', { params: filters }),
  getAuditLogDetail: (logId) => apiClient.get(`/admin/audit-logs/${logId}`),

  // Create Admin
  createAdmin: (data) => apiClient.post('/admin/users/create-admin', data),

  // Flagged Properties
  getFlaggedProperties: (filters = {}) => apiClient.get('/admin/flagged-properties', { params: filters }),
  reviewFlaggedProperty: (flagId, data) => apiClient.post(`/admin/flagged-properties/${flagId}/review`, data),

  // Analytics
  getAnalyticsData: (params = {}) => apiClient.get('/admin/dashboard/analytics', { params }),
};

// ============================================
// NOTIFICATIONS ENDPOINTS
// ============================================
export const notificationsAPI = {
  // Get notifications
  getNotifications: () => {
    return apiClient.get('/notifications');
  },

  // Mark notification as read
  markAsRead: (notificationId) => {
    return apiClient.put(`/notifications/${notificationId}/read`);
  },

  // Mark all as read
  markAllAsRead: () => {
    return apiClient.put('/notifications/mark-all-read');
  },

  // Delete notification
  delete: (notificationId) => {
    return apiClient.delete(`/notifications/${notificationId}`);
  },

  // Get unread count
  getUnreadCount: () => {
    return apiClient.get('/notifications/unread-count');
  },
};

// ============================================
// SEARCH & FILTER ENDPOINTS
// ============================================
export const searchAPI = {
  // Search properties
  properties: (filters = {}) => {
    return apiClient.get('/properties', { params: filters });
  },

  // Search agents
  agents: (filters = {}) => {
    return apiClient.get('/agents', { params: filters });
  },

  // Get property suggestions (autocomplete)
  propertySuggestions: (query) => {
    return apiClient.get('/search/properties/suggestions', {
      params: { q: query },
    });
  },

  // Get location suggestions
  locationSuggestions: (query) => {
    return apiClient.get('/search/locations/suggestions', {
      params: { q: query },
    });
  },
};

// ============================================
// EXPORT ALL APIs
// ============================================
export default {
  authAPI,
  propertiesAPI,
  agentsAPI,
  viewingsAPI,
  messagesAPI,
  documentsAPI,
  adminAPI,
  notificationsAPI,
  searchAPI,
  apiClient,
};