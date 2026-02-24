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
        <router-link to="/dashboard" class="nav-item active">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>

        <div v-if="userRole === 'buyer'" class="nav-section">
          <h3 class="section-title">Browse</h3>
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">Activity</h3>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Inbox</span>
            <span v-if="unreadMessagesCount > 0" class="badge-dot">{{ unreadMessagesCount }}</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
            <span v-if="viewingRequestsCount > 0" class="badge-dot">{{ viewingRequestsCount }}</span>
          </router-link>
          <router-link to="/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">Settings</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profile</span>
          </router-link>
          <router-link to="/settings" class="nav-item">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">Settings</span>
          </router-link>
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
            <h1 class="page-title">Dashboard</h1>
          </div>
          <div class="topbar-right">
            <!-- Notification Bell -->
            <div class="notif-bell-wrapper" v-click-outside="() => showNotifBell = false">
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
            <input type="text" placeholder="Search properties..." class="search-input" />
          </div>
        </div>
      </nav>

      <!-- Page Content Wrapper -->
      <div class="page-wrapper">
        <!-- Main Content -->
        <div class="main-panel">
          <!-- Welcome Card -->
          <div class="welcome-card">
            <div class="welcome-content">
              <div class="welcome-text">
                <h2>Welcome back, {{ userName }}! 👋</h2>
                <p>{{ getWelcomeMessage() }}</p>
              </div>
              <div class="welcome-illustration">✨</div>
            </div>
          </div>

          <!-- Quick Stats Cards -->
          <div class="quick-stats">
            <div class="quick-stat-card" style="cursor:pointer" @click="$router.push('/saved-properties')">
              <div class="stat-icon-box saved">❤️</div>
              <div class="stat-detail">
                <p class="stat-label">Saved Properties</p>
                <h3 class="stat-value">{{ savedPropertiesCount }}</h3>
              </div>
              <router-link to="/saved-properties" class="stat-link">View →</router-link>
            </div>

            <div class="quick-stat-card">
              <div class="stat-icon-box viewing">📅</div>
              <div class="stat-detail">
                <p class="stat-label">Viewing Requests</p>
                <h3 class="stat-value">{{ viewingRequestsCount }}</h3>
              </div>
              <router-link to="/viewings" class="stat-link">View →</router-link>
            </div>

            <div class="quick-stat-card" style="cursor:pointer" @click="$router.push('/conversations')">
              <div class="stat-icon-box messages">💬</div>
              <div class="stat-detail">
                <p class="stat-label">New Messages</p>
                <h3 class="stat-value">{{ unreadMessagesCount }}</h3>
              </div>
              <router-link to="/conversations" class="stat-link">Open →</router-link>
            </div>
          </div>

          <!-- Recent Properties Section -->
          <div class="recent-section">
            <div class="section-header">
              <div>
                <h3>Recently Added Properties</h3>
                <p class="section-subtitle">Latest properties from agents</p>
              </div>
              <router-link to="/properties" class="see-all-link">View All</router-link>
            </div>

            <!-- Loading State -->
            <div v-if="loadingProperties" class="loading-state">
              <p>⏳ Loading properties...</p>
            </div>

            <!-- Properties Grid -->
            <div v-else class="properties-grid">
              <div v-for="property in recentProperties" :key="property.id" class="property-card">
                <div class="property-image-wrapper">
                  <img 
                    v-if="getCoverPhoto(property)" 
                    :src="getCoverPhoto(property)" 
                    :alt="property.title"
                    class="property-image"
                  />
                  <div v-else class="property-image-placeholder">📷</div>
                  
                  <div class="property-overlay">
                    <button
                      class="btn-save"
                      :class="{ saved: savedPropertyIds.includes(property.id) }"
                      @click.prevent="toggleSave(property)"
                    >
                      {{ savedPropertyIds.includes(property.id) ? '❤️ Saved' : '🤍 Save' }}
                    </button>
                    <router-link
                      :to="`/property/${property.id}`"
                      class="btn-view"
                    >
                      👁️ View
                    </router-link>
                  </div>
                  <div class="price-tag">₱{{ formatPrice(property.price) }}</div>
                  <div v-if="property.featured" class="featured-badge">⭐ Featured</div>
                </div>
                <div class="property-body">
                  <h4 class="property-title">{{ property.title }}</h4>
                  <p class="property-location">📍 {{ property.city }}, {{ property.province }}</p>
                  <div class="property-specs">
                    <span class="spec">🛏️ {{ property.bedrooms }}</span>
                    <span class="spec">🚿 {{ property.bathrooms }}</span>
                    <span class="spec">👁️ {{ property.view_count || 0 }}</span>
                  </div>
                  <div class="property-type">{{ formatPropertyType(property.property_type) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Featured Properties -->
          <div v-if="featuredProperties.length > 0" class="recommended-section">
            <div class="section-header">
              <div>
                <h3>⭐ Featured Properties</h3>
                <p class="section-subtitle">Premium listings from top agents</p>
              </div>
            </div>

            <div class="recommended-grid">
              <div v-for="property in featuredProperties.slice(0, 3)" :key="`rec-${property.id}`" class="recommended-card">
                <div class="recommended-image">
                  <img 
                    v-if="getCoverPhoto(property)" 
                    :src="getCoverPhoto(property)" 
                    :alt="property.title"
                  />
                  <span class="match-badge">⭐ Featured</span>
                </div>
                <div class="recommended-content">
                  <h4>{{ property.title }}</h4>
                  <p class="rec-location">📍 {{ property.city }}</p>
                  <div class="rec-specs">
                    <span>{{ property.bedrooms }}bd</span>
                    <span>{{ property.bathrooms }}ba</span>
                  </div>
                  <div class="rec-footer">
                    <span class="rec-price">₱{{ formatPrice(property.price) }}</span>
                    <router-link 
                      :to="`/property/${property.id}`"
                      class="btn-explore"
                    >
                      Explore →
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Become an Agent CTA -->
          <div v-if="userRole === 'buyer'" class="become-agent-card">
            <div class="agent-cta-header">
              <div class="agent-cta-content">
                <h3>🏆 Become an Agent</h3>
                <p>Start earning by listing properties on our platform</p>
              </div>
              <div class="agent-icon">🏢</div>
            </div>
            <div class="agent-benefits">
              <div class="benefit">
                <span class="benefit-icon">💰</span>
                <span class="benefit-text">Earn commission on every sale</span>
              </div>
              <div class="benefit">
                <span class="benefit-icon">📈</span>
                <span class="benefit-text">Grow your real estate business</span>
              </div>
              <div class="benefit">
                <span class="benefit-icon">🎯</span>
                <span class="benefit-text">Reach thousands of buyers</span>
              </div>
              <div class="benefit">
                <span class="benefit-icon">🛠️</span>
                <span class="benefit-text">Professional tools & support</span>
              </div>
            </div>
            <router-link to="/become-agent" class="btn-become-agent">
              ➜ Start Your Agent Journey
            </router-link>
          </div>

          <!-- Account Info -->
          <div class="account-info">
            <div class="section-header">
              <h3>Account Information</h3>
            </div>
            <div class="info-grid">
              <div class="info-item">
                <label>Full Name</label>
                <p>{{ userName }}</p>
              </div>
              <div class="info-item">
                <label>Email Address</label>
                <p>{{ userEmail }}</p>
              </div>
              <div class="info-item">
                <label>Account Type</label>
                <span class="role-badge buyer-badge">
                  {{ capitalizeRole(userRole) }}
                </span>
              </div>
              <div class="info-item">
                <label>Member Since</label>
                <p>{{ memberSince }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Side Panel -->
        <aside class="side-panel">
          <div class="panel-tabs">
            <button 
              :class="['panel-tab', { active: activePanelTab === 'messages' }]"
              @click="activePanelTab = 'messages'"
            >
              💬 Messages
            </button>
            <button 
              :class="['panel-tab', { active: activePanelTab === 'map' }]"
              @click="activePanelTab = 'map'"
            >
              🗺️ Map
            </button>
          </div>

          <!-- Messages Tab -->
          <div v-if="activePanelTab === 'messages'" class="panel-content messages-content">
            <div class="panel-header">
              <h3>Recent Messages</h3>
              <span class="unread-count" v-if="unreadMessagesCount > 0">{{ unreadMessagesCount }} new</span>
            </div>
            <div class="messages-list">
              <div v-if="recentConversations.length === 0" class="no-messages">
                No conversations yet. Browse properties and message an agent!
              </div>
              <div
                v-for="conv in recentConversations.slice(0, 5)"
                :key="convOtherId(conv)"
                class="message-item"
                @click="goToConversation(conv)"
              >
                <div class="message-avatar">{{ convOtherName(conv).charAt(0).toUpperCase() }}</div>
                <div class="message-body">
                  <p class="sender-name">{{ convOtherName(conv) }}</p>
                  <p class="message-text">{{ conv.last_message?.message_content || '📎 Attachment' }}</p>
                  <span class="message-time">{{ formatConvTime(conv.last_message?.created_at) }}</span>
                </div>
                <span v-if="conv.unread_count > 0" class="unread-dot">{{ conv.unread_count }}</span>
              </div>
            </div>
            <router-link to="/conversations" class="btn-view-all">View All Messages →</router-link>
          </div>

          <!-- Map Tab -->
          <div v-if="activePanelTab === 'map'" class="panel-content map-content">
            <div class="panel-header">
              <h3>Map View</h3>
            </div>
            <div class="map-placeholder">
              <div class="map-icon">🗺️</div>
              <p>Interactive map coming soon</p>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <!-- Error Message -->
    <transition name="fade">
      <div v-if="errorMessage" class="error-message">
        ✗ {{ errorMessage }}
      </div>
    </transition>

    <!-- Success Message -->
    <transition name="fade">
      <div v-if="successMessage" class="success-message">
        ✓ {{ successMessage }}
      </div>
    </transition>

    <!-- RealtyLinkPH Buddy Chat -->
    <ChatBubble />
  </div>
</template>

<script>
import ChatBubble from '../components/ChatBubble.vue';

export default {
  name: 'Dashboard',
  components: { ChatBubble },
  data() {
    return {
      // User Data
      userName: '',
      userEmail: '',
      userRole: '',
      memberSince: '',
      profilePhotoUrl: null, // ✅ ADDED: Profile photo URL

      // Properties
      properties: [],
      loadingProperties: false,

      // Buyer Stats
      savedPropertiesCount: 0,
      savedPropertyIds: [],
      viewingRequestsCount: 0,

      // API
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      myId: null,

      // Notifications
      unreadMessagesCount: 0,
      notifications: [],
      unreadNotificationsCount: 0,

      // Conversations
      recentConversations: [],
      echoChannel: null,

      // UI State
      activePanelTab: 'messages',
      showUserMenu: false,
      showNotifBell: false,
      // Messages
      successMessage: '',
      errorMessage: '',

    };
  },

  computed: {
    recentProperties() {
      return this.properties.slice(0, 6);
    },

    featuredProperties() {
      return this.properties.filter(p => p.featured);
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

    getWelcomeMessage() {
      const messages = {
        buyer: 'Browse properties, schedule viewings, and find your perfect home.',
        agent: 'Manage your listings, respond to inquiries, and grow your business.',
        admin: 'Keep the platform safe by verifying agents and handling disputes.',
      };
      return messages[this.userRole] || 'Welcome to RealtyLinkPH!';
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH').format(price);
    },

    formatPropertyType(type) {
      const typeMap = {
        house: 'House',
        condo: 'Condo',
        apartment: 'Apartment',
        commercial: 'Commercial',
      };
      return typeMap[type] || type;
    },

    getCoverPhoto(property) {
      if (!property.photos || property.photos.length === 0) return null;
      const cover = property.photos.find(p => p.is_cover);
      return cover ? cover.photo_url : property.photos[0].photo_url;
    },

    async loadProperties() {
      try {
        this.loadingProperties = true;
        const token = localStorage.getItem('auth_token');

        const response = await fetch(window.__API_URL__ + '/api/properties', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();

        if (data.success) {
          this.properties = data.data.data || data.data || [];
          console.log('✅ Properties loaded:', this.properties);
        } else {
          this.showError(data.message || 'Failed to load properties');
        }
      } catch (error) {
        console.error('Load error:', error);
        this.showError('Failed to load properties');
      } finally {
        this.loadingProperties = false;
      }
    },

    async loadSavedIds() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/buyer/saved-properties`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          const list = data.data?.data || data.data || [];
          this.savedPropertyIds = list.map(p => p.id);
          this.savedPropertiesCount = list.length;
        }
      } catch (e) { /* non-critical */ }
    },

    async toggleSave(property) {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/buyer/properties/${property.id}/save`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          if (this.savedPropertyIds.includes(property.id)) {
            this.savedPropertyIds = this.savedPropertyIds.filter(id => id !== property.id);
            this.savedPropertiesCount = Math.max(0, this.savedPropertiesCount - 1);
            this.showSuccess('Removed from saved properties');
          } else {
            this.savedPropertyIds.push(property.id);
            this.savedPropertiesCount++;
            this.showSuccess('Saved to My Wallet! ❤️');
          }
        }
      } catch (e) {
        this.showError('Failed to save property');
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

    loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName = user.name || 'User';
        this.userEmail = user.email || '';
        this.userRole = user.role || 'buyer';

        const createdDate = new Date();
        this.memberSince = createdDate.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        });
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

    showError(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 3000);
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
      // Mark as read
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

      // Navigate based on notification type
      if (notif.notification_type === 'viewing_request') {
        this.$router.push('/viewings');
      } else if (notif.notification_type === 'message') {
        this.$router.push('/conversations');
      } else if (notif.notification_type === 'document') {
        this.$router.push('/documents');
      }
    },

    goToConversation(conv) {
      this.showNotifBell = false;
      this.$router.push(`/conversations/${this.convOtherId(conv)}`);
    },

    async loadConversations() {
      try {
        const token = localStorage.getItem('auth_token');
        const endpoint = this.userRole === 'agent'
          ? `${this.apiUrl}/api/agent/conversations`
          : `${this.apiUrl}/api/buyer/conversations`;
        const res = await fetch(endpoint, {
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

      // Subscribe to notifications channel (viewings, reviews, documents, messages)
      try {
        window.Echo.private(`notifications.${this.myId}`)
          .listen('.NotificationSent', (data) => {
            // Add to notifications list in real-time
            this.notifications.unshift(data);
            this.unreadNotificationsCount++;
          })
          .listen('.ViewingStatusChanged', () => {
            this.loadViewingCount();
          });
      } catch (e) { /* ignore */ }
    },

    leaveEcho() {
      if (this.echoChannel) {
        window.Echo.leave(`chat.${this.myId}`);
        this.echoChannel = null;
      }
      try {
        if (window.Echo && this.myId) {
          window.Echo.leave(`notifications.${this.myId}`);
        }
      } catch (e) { /* ignore */ }
    },
    // ───────────────────────────────────────────────────────────

    async loadViewingCount() {
      try {
        const token  = localStorage.getItem('auth_token');
        const apiUrl = localStorage.getItem('api_url') || window.__API_URL__;
        const res    = await fetch(`${apiUrl}/api/buyer/viewings`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          // Only count pending (requested) viewings — approved/rejected have no badge
          this.viewingRequestsCount = (data.viewings || []).filter(
            v => v.status === 'requested'
          ).length;
        }
      } catch (e) { /* non-critical */ }
    },

    showSuccess(message) {
      this.successMessage = message;
      setTimeout(() => {
        this.successMessage = '';
      }, 3000);
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/login');
      return;
    }
    this.loadUserData();
    this.myId = JSON.parse(localStorage.getItem('user') || '{}').id || null;
    this.loadProfilePhoto();
    this.loadProperties();
    this.loadSavedIds();
    this.loadConversations();
    this.loadViewingCount();
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
  --charcoal-smoke: #2a2420;
  --light-gray: #e0e0e0;
  --border-gray: #ddd;
  
  --font-display: 'Poppins', sans-serif;
  --font-body: 'Inter', sans-serif;
}

.dashboard-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
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
  display: flex;
  flex-direction: column;
  gap: 12px;
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
  border-bottom: 1px solid var(--light-gray);
  padding: 16px 32px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
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
  gap: 20px;
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
  position: relative;
  transition: transform 0.3s;
  color: var(--smoky-black);
}

.btn-icon:hover {
  transform: scale(1.1);
}

.search-input {
  padding: 10px 16px;
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  font-size: 14px;
  width: 220px;
  transition: all 0.3s;
  background: var(--white-smoke);
  color: var(--smoky-black);
}

.search-input::placeholder {
  color: #999;
}

.search-input:focus {
  outline: none;
  border-color: var(--palace-gold);
  background: white;
  box-shadow: 0 0 0 3px rgba(230, 174, 13, 0.1);
}

/* Page Wrapper */
.page-wrapper {
  display: flex;
  flex: 1;
  gap: 20px;
  padding: 32px;
  overflow-y: auto;
}

.main-panel {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 32px;
}

/* Welcome Card */
.welcome-card {
  background: linear-gradient(135deg, var(--palace-gold) 0%, var(--palace-gold-dark) 100%);
  color: var(--smoky-black);
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(230, 174, 13, 0.2);
  position: relative;
  overflow: hidden;
}

.welcome-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  z-index: 0;
}

.welcome-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  z-index: 1;
}

.welcome-text h2 {
  font-size: 32px;
  margin-bottom: 12px;
  font-family: var(--font-display);
  font-weight: 800;
  letter-spacing: -0.5px;
}

.welcome-text p {
  font-size: 15px;
  opacity: 0.95;
  margin: 0;
  font-weight: 500;
}

.welcome-illustration {
  font-size: 80px;
  opacity: 0.3;
}

/* Quick Stats */
.quick-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.quick-stat-card {
  background: white;
  border: 1px solid var(--light-gray);
  border-radius: 12px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  transition: all 0.3s ease;
}

.quick-stat-card:hover {
  border-color: var(--palace-gold);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
  transform: translateY(-4px);
}

.stat-icon-box {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  flex-shrink: 0;
}

.stat-icon-box.saved {
  background: #ffe0f0;
}

.stat-icon-box.viewing {
  background: #e0f2ff;
}

.stat-icon-box.messages {
  background: #f0e0ff;
}

.stat-detail {
  flex: 1;
}

.stat-label {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.5px;
  margin: 0 0 8px 0;
}

.stat-value {
  font-size: 36px;
  font-weight: 800;
  color: var(--palace-gold);
  margin: 0;
  font-family: var(--font-display);
}

.stat-link {
  color: var(--palace-gold);
  text-decoration: none;
  font-weight: 700;
  font-size: 13px;
  transition: all 0.3s;
}

.stat-link:hover {
  color: var(--palace-gold-dark);
}

/* Recent Section */
.recent-section {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.section-header h3 {
  font-size: 22px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 0;
  font-family: var(--font-display);
  letter-spacing: -0.3px;
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
  transition: all 0.3s;
  white-space: nowrap;
}

.see-all-link:hover {
  color: var(--palace-gold-dark);
}

/* Properties Grid */
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 20px;
}

.property-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid var(--light-gray);
}

.property-card:hover {
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
  border-color: var(--palace-gold);
  transform: translateY(-8px);
}

.property-image-wrapper {
  position: relative;
  height: 200px;
  overflow: hidden;
  background: #f0f0f0;
}

.property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.property-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 64px;
  color: #ddd;
}

.property-card:hover .property-image {
  transform: scale(1.08);
}

.property-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  gap: 12px;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.property-card:hover .property-overlay {
  opacity: 1;
}

.btn-save, .btn-view {
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
  text-decoration: none;
  display: inline-block;
}

.btn-save {
  background: white;
  color: var(--smoky-black);
}

.btn-save:hover {
  background: var(--palace-gold);
}

.btn-save.saved {
  background: #ffe0e0;
  color: #c0392b;
}

.btn-view {
  background: var(--palace-gold);
  color: var(--smoky-black);
}

.btn-view:hover {
  background: var(--palace-gold-dark);
}

.price-tag {
  position: absolute;
  top: 12px;
  right: 12px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 800;
  font-size: 14px;
  font-family: var(--font-display);
  box-shadow: 0 4px 12px rgba(230, 174, 13, 0.3);
}

.featured-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: white;
  color: var(--palace-gold);
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 12px;
}

.property-body {
  padding: 20px;
}

.property-title {
  font-size: 16px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 0 0 8px 0;
  font-family: var(--font-display);
}

.property-location {
  font-size: 13px;
  color: #999;
  margin: 0 0 12px 0;
}

.property-specs {
  display: flex;
  gap: 12px;
  margin: 12px 0;
  flex-wrap: wrap;
}

.spec {
  font-size: 12px;
  color: var(--charcoal-smoke);
  font-weight: 600;
  padding: 4px 8px;
  background: var(--white-smoke);
  border-radius: 4px;
}

.property-type {
  font-size: 12px;
  font-weight: 700;
  color: var(--palace-gold);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Recommended Section */
.recommended-section {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.recommended-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.recommended-card {
  display: flex;
  gap: 16px;
  border: 1px solid var(--light-gray);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.recommended-card:hover {
  border-color: var(--palace-gold);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.recommended-image {
  position: relative;
  width: 150px;
  height: 150px;
  flex-shrink: 0;
  overflow: hidden;
}

.recommended-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.recommended-card:hover .recommended-image img {
  transform: scale(1.08);
}

.match-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
}

.recommended-content {
  flex: 1;
  padding: 16px;
  display: flex;
  flex-direction: column;
}

.recommended-content h4 {
  font-size: 15px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 0 0 4px 0;
  font-family: var(--font-display);
}

.rec-location {
  font-size: 12px;
  color: #999;
  margin: 0 0 12px 0;
}

.rec-specs {
  display: flex;
  gap: 12px;
  font-size: 12px;
  color: var(--charcoal-smoke);
  font-weight: 700;
  margin-bottom: auto;
}

.rec-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid var(--light-gray);
}

.rec-price {
  font-size: 18px;
  font-weight: 800;
  color: var(--palace-gold);
  font-family: var(--font-display);
}

.btn-explore {
  background: none;
  border: none;
  color: var(--palace-gold);
  font-weight: 700;
  cursor: pointer;
  font-size: 13px;
  transition: all 0.3s;
  text-decoration: none;
}

.btn-explore:hover {
  color: var(--palace-gold-dark);
}

/* Account Info */
.account-info {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.info-item {
  padding: 16px;
  background: var(--white-smoke);
  border-radius: 8px;
  border-left: 4px solid var(--palace-gold);
}

.info-item label {
  font-size: 11px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  display: block;
  margin-bottom: 8px;
  letter-spacing: 0.8px;
}

.info-item p {
  font-size: 15px;
  color: var(--smoky-black);
  font-weight: 700;
  margin: 0;
}

.role-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.buyer-badge {
  background: #e3f2fd;
  color: #1565c0;
}

/* ============================================================================
   BECOME AGENT CARD
   ============================================================================ */

.become-agent-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 32px;
  color: white;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.agent-cta-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.agent-cta-content h3 {
  font-size: 24px;
  font-weight: 800;
  margin: 0 0 8px 0;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
}

.agent-cta-content p {
  font-size: 15px;
  margin: 0;
  opacity: 0.95;
  font-weight: 500;
}

.agent-icon {
  font-size: 64px;
  opacity: 0.2;
}

.agent-benefits {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.benefit {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  backdrop-filter: blur(10px);
}

.benefit-icon {
  font-size: 20px;
  flex-shrink: 0;
}

.benefit-text {
  font-size: 13px;
  font-weight: 600;
}

.btn-become-agent {
  width: 100%;
  padding: 14px 24px;
  background: white;
  color: #667eea;
  border: none;
  border-radius: 8px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 16px;
  font-family: var(--font-display);
  letter-spacing: 0.5px;
}

.btn-become-agent:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* ============================================================================
   MODAL STYLES
   ============================================================================ */

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-box {
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 24px;
  border-bottom: 1px solid var(--light-gray);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 20px;
  font-weight: 800;
  margin: 0;
  color: var(--smoky-black);
  font-family: var(--font-display);
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
  padding: 0;
  transition: color 0.3s;
}

.btn-close:hover {
  color: var(--smoky-black);
}

.modal-body {
  padding: 24px;
}

.modal-intro {
  font-size: 15px;
  color: #666;
  margin: 0 0 24px 0;
  line-height: 1.6;
}

.agent-form {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-input {
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-size: 14px;
  font-family: var(--font-body);
  transition: all 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.error-text {
  font-size: 12px;
  color: #d32f2f;
  margin-top: 2px;
}

.terms-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 24px;
  padding: 12px;
  background: var(--white-smoke);
  border-radius: 6px;
}

.terms-checkbox input {
  margin-top: 4px;
  cursor: pointer;
}

.terms-checkbox label {
  font-size: 14px;
  color: #666;
  cursor: pointer;
}

.info-box {
  background: #f0f7ff;
  border-left: 4px solid #667eea;
  padding: 16px;
  border-radius: 6px;
  margin-bottom: 24px;
}

.info-box p {
  font-size: 14px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0 0 12px 0;
}

.info-box ul {
  margin: 0;
  padding-left: 20px;
  color: #666;
}

.info-box li {
  font-size: 13px;
  margin-bottom: 6px;
  line-height: 1.5;
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid var(--light-gray);
}

.btn-secondary {
  padding: 12px 24px;
  background: white;
  color: var(--smoky-black);
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
  flex: 1;
}

.btn-secondary:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

.btn-submit {
  padding: 12px 24px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
  flex: 1;
}

.btn-submit:hover:not(:disabled) {
  background: #5568d3;
  transform: translateY(-2px);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.role-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.buyer-badge {
  background: #e3f2fd;
  color: #1565c0;
}

/* Side Panel */
.side-panel {
  width: 360px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.panel-tabs {
  display: flex;
  border-bottom: 1px solid var(--light-gray);
  background: var(--white-smoke);
}

.panel-tab {
  flex: 1;
  padding: 14px 16px;
  border: none;
  background: none;
  color: #999;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  border-bottom: 2px solid transparent;
}

.panel-tab.active {
  color: var(--smoky-black);
  border-bottom-color: var(--palace-gold);
  background: white;
}

.panel-tab:hover {
  color: var(--smoky-black);
}

.panel-content {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.panel-header h3 {
  font-size: 15px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 0;
  font-family: var(--font-display);
}

.unread-count {
  background: var(--palace-gold);
  color: var(--smoky-black);
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.message-item {
  cursor: pointer;
  display: flex;
  gap: 12px;
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  transition: all 0.3s;
  cursor: pointer;
}

.message-item:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

.message-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--palace-gold);
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
  flex-shrink: 0;
}

.message-body {
  flex: 1;
  min-width: 0;
}

.sender-name {
  font-size: 13px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 0 0 4px 0;
}

.message-text {
  font-size: 12px;
  color: #999;
  margin: 0 0 4px 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.message-time {
  font-size: 11px;
  color: #ccc;
}

.btn-view-all {
  width: 100%;
  padding: 10px;
  background: var(--white-smoke);
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  color: var(--palace-gold);
  font-weight: 700;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.3s;
}

.btn-view-all:hover {
  background: var(--palace-gold);
  color: var(--smoky-black);
  border-color: var(--palace-gold);
}

.map-content .map-placeholder {
  width: 100%;
  height: 400px;
  background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #999;
}

.map-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.loading-state {
  padding: 60px 20px;
  text-align: center;
  color: #999;
  font-size: 14px;
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
.no-messages { padding: 16px; text-align: center; color: #999; font-size: 13px; }
.unread-dot {
  background: #e6ae0d; color: #100c08;
  border-radius: 999px; padding: 1px 7px;
  font-size: 11px; font-weight: 700; flex-shrink: 0; align-self: center;
}
.btn-view-all { display: block; text-align: center; text-decoration: none; }

/* Error Message */
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

@media (max-width: 1440px) {
  .properties-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  }

  .side-panel {
    width: 320px;
  }
}

@media (max-width: 1024px) {
  .sidebar {
    width: 240px;
  }

  .main-content {
    margin-left: 240px;
  }

  .page-wrapper {
    flex-direction: column;
  }

  .side-panel {
    width: 100%;
  }

  .page-wrapper {
    padding: 24px;
  }

  .welcome-card {
    padding: 24px;
  }

  .welcome-content {
    flex-direction: column;
    gap: 16px;
  }

  .quick-stats {
    grid-template-columns: 1fr;
  }

  .properties-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: -280px;
    width: 280px;
    z-index: 1001;
    transition: left 0.3s ease;
  }

  .main-content {
    margin-left: 0;
  }

  .topbar {
    padding: 16px;
  }

  .topbar-content {
    flex-direction: column;
  }

  .page-title {
    font-size: 24px;
  }

  .topbar-right {
    width: 100%;
  }

  .search-input {
    width: 100%;
  }

  .page-wrapper {
    padding: 16px;
  }

  .properties-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  }

  .recommended-grid {
    grid-template-columns: 1fr;
  }

  .recommended-card {
    gap: 12px;
  }

  .recommended-image {
    width: 120px;
    height: 120px;
  }
}

@media (max-width: 480px) {
  .topbar {
    padding: 12px;
  }

  .page-title {
    font-size: 20px;
  }

  .welcome-text h2 {
    font-size: 24px;
  }

  .welcome-text p {
    font-size: 13px;
  }

  .page-wrapper {
    padding: 12px;
    gap: 12px;
  }

  .quick-stats {
    grid-template-columns: 1fr;
  }

  .stat-icon-box {
    width: 50px;
    height: 50px;
    font-size: 24px;
  }

  .stat-value {
    font-size: 28px;
  }

  .section-header {
    flex-direction: column;
    gap: 12px;
  }

  .properties-grid {
    grid-template-columns: 1fr;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .side-panel {
    border-radius: 0;
  }
}
</style>