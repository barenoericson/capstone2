<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>

        <div v-if="userRole === 'agent'" class="nav-section">
          <h3 class="section-title">Management</h3>
          <router-link to="/agent/dashboard" class="nav-item active">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Overview</span>
          </router-link>
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
            <span v-if="pendingViewingsCount > 0" class="badge-dot">{{ pendingViewingsCount }}</span>
          </router-link>
          <router-link to="/agent/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Messages</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">Settings</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profile</span>
          </router-link>
          <a href="#" class="nav-item">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">Settings</span>
          </a>
        </div>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card">
          <!-- ✅ UPDATED: Profile Photo Display -->
          <div v-if="profilePhotoUrl" class="user-avatar-lg">
            <img :src="profilePhotoUrl" :alt="userName" class="avatar-photo" />
          </div>
          <div v-else class="user-avatar-lg">{{ userName.charAt(0).toUpperCase() }}</div>
          
          <div class="user-info">
            <p class="user-name-card">{{ userName }}</p>
            <p class="user-role-card">{{ capitalizeRole(userRole) }}</p>
          </div>
          <button @click="toggleUserMenu" class="btn-options">⋮</button>

          <div v-if="showUserMenu" class="user-dropdown">
            <router-link to="/profile" class="dropdown-item">👤 My Profile</router-link>
            <a href="#" @click.prevent="logout" class="dropdown-item logout">🚪 Logout</a>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Top Navbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">Agent Overview</h1>
          </div>
          <div class="topbar-right">
            <!-- Notification Bell -->
            <div class="notif-bell-wrapper">
              <button class="btn-icon notif-btn" @click="toggleNotifBell">
                🔔
                <span v-if="totalNotifCount > 0" class="notif-badge">{{ totalNotifCount > 99 ? '99+' : totalNotifCount }}</span>
              </button>
              <div v-if="showNotifBell" class="notif-dropdown">
                <div class="notif-header">
                  <span>Notifications</span>
                  <button v-if="notifications.length > 0" class="notif-mark-all" @click="markAllNotificationsRead">Mark all read</button>
                </div>

                <!-- Notifications list -->
                <div v-if="notifications.length === 0 && recentConversations.length === 0" class="notif-empty">
                  No notifications yet
                </div>

                <div
                  v-for="notif in notifications.slice(0, 6)"
                  :key="'notif-' + notif.id"
                  class="notif-item"
                  :class="{ 'notif-unread-bg': !notif.is_read }"
                  @click="handleNotifClick(notif)"
                >
                  <div class="notif-avatar notif-icon-avatar">{{ notif.icon || '🔔' }}</div>
                  <div class="notif-body">
                    <p class="notif-name">{{ notif.title }}</p>
                    <p class="notif-preview">{{ notif.message }}</p>
                    <p class="notif-time">{{ notif.relative_time }}</p>
                  </div>
                  <span v-if="!notif.is_read" class="notif-dot"></span>
                </div>

                <!-- Messages section -->
                <div v-if="recentConversations.length > 0" class="notif-header notif-header-sub">
                  <span>💬 Messages</span>
                  <router-link to="/conversations" @click="showNotifBell = false" class="notif-view-all">View All</router-link>
                </div>
                <div
                  v-for="conv in recentConversations.slice(0, 3)"
                  :key="'conv-' + convOtherId(conv)"
                  class="notif-item"
                  @click="goToConversation(conv)"
                >
                  <div class="notif-avatar">{{ convOtherName(conv).charAt(0).toUpperCase() }}</div>
                  <div class="notif-body">
                    <p class="notif-name">{{ convOtherName(conv) }}</p>
                    <p class="notif-preview">{{ conv.last_message?.message_content || '📎 Attachment' }}</p>
                  </div>
                  <span v-if="conv.unread_count > 0" class="notif-unread">{{ conv.unread_count }}</span>
                </div>
              </div>
            </div>
            <router-link to="/agent/properties/create" class="btn-primary">
              ➕ Add Property
            </router-link>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <div class="page-wrapper">
        <!-- Stats Cards -->
        <div class="stats-section">
          <div class="stat-card">
            <div class="stat-icon">🏠</div>
            <div class="stat-info">
              <h4>Total Properties</h4>
              <p class="stat-number">{{ stats.totalProperties }}</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
              <h4>Active Listings</h4>
              <p class="stat-number">{{ stats.activeListings }}</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">👁️</div>
            <div class="stat-info">
              <h4>Total Views</h4>
              <p class="stat-number">{{ stats.totalViews }}</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">📸</div>
            <div class="stat-info">
              <h4>Total Photos</h4>
              <p class="stat-number">{{ stats.totalPhotos }}</p>
            </div>
          </div>
        </div>

        <!-- Recent Properties -->
        <div class="card-section">
          <div class="section-header">
            <div>
              <h3>Recent Properties</h3>
              <p class="section-subtitle">Your latest listings</p>
            </div>
            <router-link to="/agent/properties" class="see-all-link">View all →</router-link>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <p>⏳ Loading properties...</p>
          </div>

          <!-- Empty State: no properties at all -->
          <div v-else-if="properties.length === 0" class="empty-state">
            <div class="empty-content">
              <h3>📭 No Properties Yet</h3>
              <p>Start by creating your first property listing</p>
              <router-link to="/agent/properties/create" class="btn-primary">
                Create First Property
              </router-link>
            </div>
          </div>

          <!-- Empty State: all properties are sold -->
          <div v-else-if="recentProperties.length === 0" class="empty-state">
            <div class="empty-content">
              <h3>🏷️ No Active Listings</h3>
              <p>All your properties are marked as Sold. Change a status back to Available or add a new listing.</p>
              <router-link to="/agent/properties" class="btn-primary">
                Manage Properties
              </router-link>
            </div>
          </div>

          <!-- Properties Grid -->
          <div v-else class="properties-grid">
            <div v-for="property in recentProperties" :key="property.id" class="property-card">
              <!-- Image -->
              <div class="card-image-container">
                <img 
                  v-if="getCoverPhoto(property)" 
                  :src="getCoverPhoto(property)" 
                  :alt="property.title"
                  class="card-image"
                />
                <div v-else class="card-image-placeholder">📷</div>
                
                <span :class="['status-badge', 'status-' + property.status]">
                  {{ formatStatus(property.status) }}
                </span>

                <span v-if="property.photos && property.photos.length > 0" class="photo-count">
                  📸 {{ property.photos.length }}
                </span>
              </div>

              <!-- Content -->
              <div class="card-content">
                <h4 class="property-title">{{ property.title }}</h4>
                <p class="property-location">📍 {{ property.city }}, {{ property.province }}</p>

                <div class="property-specs-row">
                  <span class="spec">🛏️ {{ property.bedrooms }}</span>
                  <span class="spec">🚿 {{ property.bathrooms }}</span>
                  <span class="spec">👁️ {{ property.view_count || 0 }}</span>
                </div>

                <div class="property-price">₱{{ formatPrice(property.price) }}</div>
              </div>

              <!-- Actions -->
              <div class="card-footer">
                <router-link
                  :to="`/agent/properties/${property.id}/edit`"
                  class="btn-card btn-edit"
                >
                  ✏️ Edit
                </router-link>
                <button
                  @click="deleteProperty(property.id)"
                  class="btn-card btn-delete"
                >
                  🗑️ Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Messages -->
        <div class="card-section messages-section">
          <div class="section-header">
            <div>
              <h3>Recent Messages</h3>
              <p class="section-subtitle">Buyers contacting you about properties</p>
            </div>
            <router-link to="/conversations" class="see-all-link">View all →</router-link>
          </div>

          <div v-if="recentConversations.length === 0" class="empty-state-sm">
            No messages yet. Buyers will contact you through your property listings.
          </div>

          <div v-else class="conv-list-widget">
            <div
              v-for="conv in recentConversations.slice(0, 5)"
              :key="convOtherId(conv)"
              class="conv-widget-item"
              @click="goToConversation(conv)"
            >
              <div class="conv-widget-avatar">{{ convOtherName(conv).charAt(0).toUpperCase() }}</div>
              <div class="conv-widget-body">
                <div class="conv-widget-top">
                  <span class="conv-widget-name">{{ convOtherName(conv) }}</span>
                  <span class="conv-widget-time">{{ formatConvTime(conv.last_message?.created_at) }}</span>
                </div>
                <p class="conv-widget-preview">{{ conv.last_message?.message_content || '📎 Attachment' }}</p>
                <div v-if="conv.last_message?.property" class="conv-widget-prop">
                  🏠 {{ conv.last_message.property.title }}
                </div>
              </div>
              <span v-if="conv.unread_count > 0" class="conv-widget-badge">{{ conv.unread_count }}</span>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div v-if="properties.length > 0" class="summary-stats">
          <div class="stats-grid">
            <div class="stat">
              <span class="stat-label">Total Properties</span>
              <span class="stat-value">{{ properties.length }}</span>
            </div>
            <div class="stat">
              <span class="stat-label">Active Listings</span>
              <span class="stat-value">{{ stats.activeListings }}</span>
            </div>
            <div class="stat">
              <span class="stat-label">Total Views</span>
              <span class="stat-value">{{ stats.totalViews }}</span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Success/Error Messages -->
    <transition name="fade">
      <div v-if="successMessage" class="success-message">
        ✓ {{ successMessage }}
      </div>
    </transition>

    <transition name="fade">
      <div v-if="errorMessage" class="error-message">
        ✗ {{ errorMessage }}
      </div>
    </transition>

    <!-- RealtyLinkPH Buddy Chat -->
    <ChatBubble />
  </div>
</template>

<script>
import ChatBubble from '../components/ChatBubble.vue';

export default {
  name: 'AgentDashboard',
  components: { ChatBubble },
  data() {
    return {
      // User
      userName: '',
      userRole: '',
      showUserMenu: false,
      profilePhotoUrl: null, // ✅ ADDED: Profile photo URL

      // Properties
      properties: [],
      loading: false,

      // Stats
      stats: {
        totalProperties: 0,
        activeListings: 0,
        totalViews: 0,
        totalPhotos: 0,
      },

      // Messaging
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      myId: null,
      recentConversations: [],
      unreadMessagesCount: 0,
      echoChannel: null,
      showNotifBell: false,

      // Notifications
      notifications: [],
      unreadNotificationsCount: 0,
      pendingViewingsCount: 0,
      viewingNotifs: [],
      notifEchoChannel: null,

      // UI Messages
      successMessage: '',
      errorMessage: '',
    };
  },

  computed: {
    recentProperties() {
      return this.properties.filter(p => p.status === 'available').slice(0, 6);
    },
    totalNotifCount() {
      return this.unreadNotificationsCount + this.unreadMessagesCount;
    },
  },

  methods: {
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },

    capitalizeRole(role) {
      return role.charAt(0).toUpperCase() + role.slice(1);
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH').format(price);
    },

    formatStatus(status) {
      const statusMap = {
        available: 'Available',
        sold: 'Sold',
      };
      return statusMap[status] || status;
    },

    getCoverPhoto(property) {
      if (!property.photos || property.photos.length === 0) return null;
      const cover = property.photos.find(p => p.is_cover);
      return cover ? cover.photo_url : property.photos[0].photo_url;
    },

    async loadProperties() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');

        const response = await fetch(window.__API_URL__ + '/api/agent/properties', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();

        if (data.success) {
          this.properties = data.data.data || data.data || [];
          this.calculateStats();
          console.log('✅ Properties loaded:', this.properties);
        } else {
          this.showError(data.message || 'Failed to load properties');
        }
      } catch (error) {
        console.error('Load error:', error);
        this.showError('Failed to load properties');
      } finally {
        this.loading = false;
      }
    },

    calculateStats() {
      this.stats.totalProperties = this.properties.length;
      this.stats.activeListings = this.properties.filter(p => p.status === 'available').length;
      this.stats.totalViews = this.properties.reduce((sum, p) => sum + (p.view_count || 0), 0);
      this.stats.totalPhotos = this.properties.reduce((sum, p) => sum + (p.photos ? p.photos.length : 0), 0);
    },

    async deleteProperty(id) {
      if (!confirm('Are you sure you want to delete this property?')) return;

      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`${window.__API_URL__}/api/agent/properties/${id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();

        if (data.success) {
          this.showSuccess('Property deleted successfully');
          this.loadProperties();
        } else {
          this.showError(data.message || 'Failed to delete property');
        }
      } catch (error) {
        console.error('Delete error:', error);
        this.showError('Failed to delete property');
      }
    },

    // ✅ UPDATED: Load profile photo from localStorage
    loadProfilePhoto() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__}/storage/${user.profile_photo_path}`;
        }
      } catch (error) {
        console.error('Error loading profile photo:', error);
      }
    },

    // ── Conversations ──────────────────────────────────────────
    convOtherName(conv) {
      return conv.agent?.name ?? conv.buyer?.name ?? 'Unknown';
    },

    convOtherId(conv) {
      return conv.agent?.id ?? conv.buyer?.id ?? 0;
    },

    formatConvTime(iso) {
      if (!iso) return '';
      const d = new Date(iso);
      const now = new Date();
      const diffDays = Math.floor((now - d) / 86400000);
      if (diffDays === 0) return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      if (diffDays === 1) return 'Yesterday';
      return d.toLocaleDateString();
    },

    toggleNotifBell() {
      this.showNotifBell = !this.showNotifBell;
      if (this.showNotifBell) {
        this.loadNotifications();
      }
    },

    async loadNotifications() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/notifications`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.notifications = data.notifications || [];
          this.unreadNotificationsCount = this.notifications.filter(n => !n.is_read).length;
        }
      } catch (e) {
        console.error('Load notifications error:', e);
      }
    },

    async markAllNotificationsRead() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(`${this.apiUrl}/api/notifications/mark-all-read`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        this.notifications.forEach(n => n.is_read = true);
        this.unreadNotificationsCount = 0;
      } catch (e) { /* silent */ }
    },

    async handleNotifClick(notif) {
      if (!notif.is_read) {
        try {
          const token = localStorage.getItem('auth_token');
          await fetch(`${this.apiUrl}/api/notifications/${notif.id}/read`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
          });
          notif.is_read = true;
          this.unreadNotificationsCount = Math.max(0, this.unreadNotificationsCount - 1);
        } catch (e) { /* silent */ }
      }

      this.showNotifBell = false;

      if (notif.notification_type === 'viewing_request') {
        this.$router.push('/agent/viewings');
      } else if (notif.notification_type === 'message') {
        this.$router.push('/conversations');
      } else if (notif.notification_type === 'document') {
        this.$router.push('/agent/documents');
      } else {
        this.$router.push('/agent/dashboard');
      }
    },

    goToConversation(conv) {
      this.showNotifBell = false;
      this.$router.push(`/conversations/${this.convOtherId(conv)}`);
    },

    async loadConversations() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/conversations`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.recentConversations = data.data;
          this.unreadMessagesCount = data.data.reduce((sum, c) => sum + (c.unread_count || 0), 0);
        }
      } catch (e) {
        console.error('Load conversations error:', e);
      }
    },

    subscribeToEcho() {
      if (!window.Echo || !this.myId) return;
      try {
        window.Echo.connector.pusher.config.auth.headers.Authorization =
          'Bearer ' + localStorage.getItem('auth_token');
      } catch (e) { /* ignore */ }

      // Subscribe to message notifications
      this.echoChannel = window.Echo.private(`chat.${this.myId}`)
        .listen('.MessageSent', () => {
          this.loadConversations();
        });

      // Subscribe to notifications channel (viewings, documents, reviews, messages)
      try {
        this.notifEchoChannel = window.Echo.private(`notifications.${this.myId}`)
          .listen('.NotificationSent', (data) => {
            this.notifications.unshift(data);
            this.unreadNotificationsCount++;
          })
          .listen('.ViewingRequested', (data) => {
            this.viewingNotifs.unshift(data);
            this.pendingViewingsCount++;
          })
          .listen('.DocumentSigned', (data) => {
            this.viewingNotifs.unshift({
              ...data,
              type: 'document_signed',
            });
          });
      } catch (e) { /* ignore */ }
    },

    async loadPendingViewingsCount() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/viewings`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          const list = data.data || data.viewings || [];
          this.pendingViewingsCount = list.filter(v => v.status === 'requested').length;
        }
      } catch (e) { /* non-critical */ }
    },

    leaveEcho() {
      if (this.echoChannel) {
        window.Echo.leave(`chat.${this.myId}`);
        this.echoChannel = null;
      }
      if (this.notifEchoChannel) {
        window.Echo.leave(`notifications.${this.myId}`);
        this.notifEchoChannel = null;
      }
    },
    // ───────────────────────────────────────────────────────────

    showSuccess(message) {
      this.successMessage = message;
      setTimeout(() => {
        this.successMessage = '';
      }, 3000);
    },

    showError(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 3000);
    },

    loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName = user.name || 'Agent';
        this.userRole = user.role || 'agent';
      } catch (error) {
        console.error('Error loading user data:', error);
      }
    },

    async logout() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(window.__API_URL__ + '/api/auth/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/');
      } catch (error) {
        console.error('Logout error:', error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/');
      }
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/');
      return;
    }

    this.loadUserData();
    this.myId = JSON.parse(localStorage.getItem('user') || '{}').id || null;
    this.loadProfilePhoto();
    this.loadProperties();
    this.loadConversations();
    this.loadPendingViewingsCount();
    this.loadNotifications();
    this.subscribeToEcho();

    // Watch for photo changes every 500ms
    this.photoWatcher = setInterval(() => {
      const user = JSON.parse(localStorage.getItem('user') || '{}');
      if (user.profile_photo_path && user.profile_photo_path !== this.profilePhotoUrl?.split('/storage/')[1]) {
        this.loadProfilePhoto();
      }
    }, 500);
  },

  beforeUnmount() {
    this.showUserMenu = false;
    this.leaveEcho();
    if (this.photoWatcher) {
      clearInterval(this.photoWatcher);
    }
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --smoky-black: #100c08;
  --white-smoke: #f5f5f5;
  --palace-gold: #e6ae0d;
  --palace-gold-dark: #d4a000;
  --light-gray: #e0e0e0;
  --font-display: 'Poppins', sans-serif;
  --font-body: 'Inter', sans-serif;
}

.dashboard-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
}

/* ============================================================================
   SIDEBAR
   ============================================================================ */

.sidebar {
  width: 280px;
  background: white;
  border-right: 1px solid var(--light-gray);
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.08);
  z-index: 1000;
}

.sidebar-header {
  padding: 24px 16px;
  border-bottom: 1px solid var(--light-gray);
}

.sidebar-logo {
  font-size: 24px;
  font-weight: 800;
  margin: 0;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
  display: flex;
  align-items: center;
}

.logo-realty {
  color: var(--smoky-black);
}

.logo-ph {
  color: var(--palace-gold);
  margin-left: 2px;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.nav-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #999;
  padding: 8px 16px;
  margin: 0;
  letter-spacing: 1.2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  margin: 0 8px;
  color: var(--smoky-black);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 14px;
  border: none;
  background: none;
  cursor: pointer;
  width: calc(100% - 16px);
  text-align: left;
}

.nav-item:hover {
  background: var(--white-smoke);
  color: var(--palace-gold);
}

.nav-item.active {
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  font-weight: 600;
}

.nav-icon {
  font-size: 18px;
  min-width: 24px;
  text-align: center;
}

.nav-label {
  flex: 1;
}

.badge-dot {
  background: #ff4444;
  color: white;
  border-radius: 50%;
  font-size: 11px;
  font-weight: 700;
  min-width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid var(--light-gray);
}

.user-card {
  background: var(--white-smoke);
  border-radius: 8px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
}

.user-avatar-lg {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  flex-shrink: 0;
  overflow: hidden; /* ✅ ADDED: For image clipping */
}

/* ✅ ADDED: Profile photo styles */
.avatar-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name-card {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-role-card {
  font-size: 11px;
  color: #999;
  margin: 4px 0 0 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-options {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 4px 8px;
  color: #999;
  transition: color 0.3s;
}

.btn-options:hover {
  color: var(--palace-gold);
}

.user-dropdown {
  position: absolute;
  bottom: 100%;
  right: 0;
  background: white;
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 180px;
  margin-bottom: 8px;
  overflow: hidden;
  z-index: 1001;
}

.dropdown-item {
  display: block;
  padding: 12px 16px;
  color: var(--smoky-black);
  text-decoration: none;
  font-size: 14px;
  transition: all 0.3s;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.dropdown-item:hover {
  background: var(--white-smoke);
  color: var(--palace-gold);
}

.dropdown-item.logout {
  color: #d32f2f;
  border-top: 1px solid var(--light-gray);
}

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.main-content {
  flex: 1;
  margin-left: 280px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.topbar {
  background: white;
  border-bottom: 2px solid var(--light-gray);
  padding: 16px 32px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.topbar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 30px;
  flex: 1;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  margin: 0;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
  color: var(--smoky-black);
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.btn-icon {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 8px;
  transition: transform 0.3s;
  color: var(--smoky-black);
}

.btn-icon:hover {
  transform: scale(1.1);
  color: var(--palace-gold);
}

.btn-primary {
  padding: 12px 24px;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(230, 174, 13, 0.3);
}

.page-wrapper {
  flex: 1;
  padding: 32px;
  overflow-y: auto;
}

/* Stats Section */
.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
  border: 1px solid var(--light-gray);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  border-color: var(--palace-gold);
}

.stat-icon {
  font-size: 32px;
  min-width: 50px;
}

.stat-info h4 {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  margin: 0 0 8px 0;
  font-weight: 700;
  letter-spacing: 0.8px;
}

.stat-number {
  font-size: 32px;
  font-weight: 800;
  color: var(--palace-gold);
  margin: 0;
  font-family: var(--font-display);
}

/* Card Section */
.card-section {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  margin-bottom: 40px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--light-gray);
}

.section-header h3 {
  font-size: 22px;
  font-weight: 800;
  margin: 0;
  color: var(--smoky-black);
  font-family: var(--font-display);
}

.section-subtitle {
  font-size: 13px;
  color: #999;
  margin: 8px 0 0 0;
  font-weight: 500;
}

.see-all-link {
  color: var(--palace-gold);
  text-decoration: none;
  font-weight: 700;
  font-size: 14px;
  transition: color 0.3s;
}

.see-all-link:hover {
  color: var(--palace-gold-dark);
}

/* Properties Grid */
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.property-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s;
  border: 1px solid var(--light-gray);
  display: flex;
  flex-direction: column;
}

.property-card:hover {
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
  border-color: var(--palace-gold);
  transform: translateY(-8px);
}

.card-image-container {
  position: relative;
  width: 100%;
  height: 180px;
  background: #f0f0f0;
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.property-card:hover .card-image {
  transform: scale(1.05);
}

.card-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 64px;
  color: #ddd;
}

.status-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.status-available {
  background: #d4edda;
  color: #155724;
}

.status-sold {
  background: #f8d7da;
  color: #721c24;
}

.status-rented {
  background: #cce5ff;
  color: #004085;
}

.status-unlisted {
  background: #e2e3e5;
  color: #383d41;
}

.photo-count {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 700;
}

.card-content {
  padding: 16px;
  flex: 1;
}

.property-title {
  font-size: 16px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 0 0 8px 0;
  line-height: 1.3;
}

.property-location {
  font-size: 13px;
  color: #999;
  margin: 0 0 12px 0;
}

.property-specs-row {
  display: flex;
  gap: 12px;
  margin: 12px 0;
  flex-wrap: wrap;
}

.spec {
  font-size: 12px;
  color: #666;
  font-weight: 600;
  padding: 4px 8px;
  background: var(--white-smoke);
  border-radius: 4px;
}

.property-price {
  font-size: 18px;
  font-weight: 800;
  color: var(--palace-gold);
  margin: 12px 0 0 0;
}

.card-footer {
  padding: 12px 16px;
  border-top: 1px solid var(--light-gray);
  display: flex;
  gap: 8px;
}

.btn-card {
  flex: 1;
  padding: 10px 12px;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-edit {
  background: var(--palace-gold);
  color: var(--smoky-black);
}

.btn-edit:hover {
  background: var(--palace-gold-dark);
  transform: translateY(-1px);
}

.btn-delete {
  background: #f44336;
  color: white;
}

.btn-delete:hover {
  background: #da190b;
  transform: translateY(-1px);
}

/* Loading & Empty States */
.loading-state,
.empty-state {
  padding: 60px 20px;
  text-align: center;
  color: #999;
  font-size: 14px;
}

.empty-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}

.empty-content h3 {
  font-size: 20px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
}

/* Summary Stats */
.summary-stats {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  text-align: center;
  padding: 16px;
  background: var(--white-smoke);
  border-radius: 8px;
  border-left: 3px solid var(--palace-gold);
}

.stat-label {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.stat-value {
  font-size: 28px;
  font-weight: 800;
  color: var(--palace-gold);
  font-family: var(--font-display);
}

/* Notification Bell */
.notif-bell-wrapper { position: relative; }
.notif-btn { position: relative; }
.notif-badge {
  position: absolute; top: -4px; right: -4px;
  background: #ef4444; color: white;
  border-radius: 999px; padding: 1px 5px;
  font-size: 10px; font-weight: 700;
  min-width: 16px; text-align: center;
  pointer-events: none;
}
.notif-dropdown {
  position: absolute; top: calc(100% + 8px); right: 0;
  width: 300px; background: white;
  border-radius: 12px; border: 1px solid #e0e0e0;
  box-shadow: 0 8px 24px rgba(0,0,0,.12);
  z-index: 999; overflow: hidden;
}
.notif-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 16px; border-bottom: 1px solid #f0f0f0;
  font-weight: 700; font-size: 13px; color: #100c08;
}
.notif-view-all { font-size: 12px; color: #e6ae0d; text-decoration: none; font-weight: 600; }
.notif-empty { padding: 20px 16px; text-align: center; color: #999; font-size: 13px; }
.notif-item {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 16px; cursor: pointer; transition: background .15s;
}
.notif-item:hover { background: #faf9f5; }
.notif-avatar {
  width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, #e6ae0d, #d4a000);
  color: #100c08; display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 14px;
}
.notif-body { flex: 1; min-width: 0; }
.notif-name { font-size: 13px; font-weight: 700; color: #100c08; margin: 0; }
.notif-preview {
  font-size: 12px; color: #666; margin: 2px 0 0;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.notif-unread {
  background: #e6ae0d; color: #100c08;
  border-radius: 999px; padding: 1px 7px;
  font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.notif-mark-all {
  background: none; border: none; cursor: pointer;
  font-size: 11px; color: #e6ae0d; font-weight: 600;
  transition: color .2s;
}
.notif-mark-all:hover { color: #d4a000; }
.notif-unread-bg { background: #fffdf5; }
.notif-icon-avatar { font-size: 18px; background: #f5f5f5; }
.notif-time { font-size: 11px; color: #aaa; margin: 2px 0 0; }
.notif-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #e6ae0d; flex-shrink: 0;
}
.notif-header-sub { border-top: 1px solid #f0f0f0; margin-top: 4px; }
.notif-dropdown { max-height: 420px; overflow-y: auto; }

/* Recent Messages Section */
.messages-section { margin-top: 24px; }
.empty-state-sm {
  padding: 24px; text-align: center; color: #999;
  font-size: 13px; background: white;
  border-radius: 10px; border: 1px solid #e0e0e0;
}
.conv-list-widget {
  background: white; border-radius: 10px;
  border: 1px solid #e0e0e0; overflow: hidden;
}
.conv-widget-item {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 20px; cursor: pointer; transition: background .2s;
  border-bottom: 1px solid #f5f5f5;
}
.conv-widget-item:last-child { border-bottom: none; }
.conv-widget-item:hover { background: #faf9f5; }
.conv-widget-avatar {
  width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, #e6ae0d, #d4a000);
  color: #100c08; display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 17px;
}
.conv-widget-body { flex: 1; min-width: 0; }
.conv-widget-top {
  display: flex; justify-content: space-between; align-items: baseline; gap: 8px;
}
.conv-widget-name { font-weight: 700; font-size: 14px; color: #100c08; }
.conv-widget-time { font-size: 11px; color: #999; white-space: nowrap; flex-shrink: 0; }
.conv-widget-preview {
  font-size: 13px; color: #666; margin: 2px 0;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.conv-widget-prop {
  font-size: 11px; color: #7a5c00;
  background: #fef9e7; border: 1px solid #e6ae0d;
  border-radius: 4px; padding: 2px 8px;
  display: inline-block; margin-top: 3px;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  max-width: 200px;
}
.conv-widget-badge {
  background: #e6ae0d; color: #100c08;
  border-radius: 999px; padding: 2px 8px;
  font-size: 11px; font-weight: 700; flex-shrink: 0;
}

/* Messages */
.success-message {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #4caf50;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3000;
  animation: slideIn 0.3s ease;
}

.error-message {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #f44336;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3000;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    transform: translateX(400px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: -280px;
    transition: left 0.3s ease;
    width: 280px;
    z-index: 1001;
  }

  .main-content {
    margin-left: 0;
  }

  .topbar {
    padding: 16px;
  }

  .topbar-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .page-title {
    font-size: 24px;
  }

  .page-wrapper {
    padding: 16px;
  }

  .stats-section {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  .stat-card {
    padding: 16px;
  }

  .stat-number {
    font-size: 24px;
  }

  .properties-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 20px;
  }

  .stats-section {
    grid-template-columns: 1fr;
  }

  .btn-primary {
    padding: 10px 16px;
    font-size: 12px;
  }

  .properties-grid {
    grid-template-columns: 1fr;
  }

  .card-section {
    padding: 16px;
  }
}
</style>