<template>
  <div class="messages-wrapper">
    <!-- Sidebar Navigation -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/agent/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>

        <router-link to="/agent/messages" class="nav-item active">
          <span class="nav-icon">💬</span>
          <span class="nav-label">Messages</span>
          <span v-if="totalUnread > 0" class="badge">{{ totalUnread }}</span>
        </router-link>

        <router-link to="/agent/properties" class="nav-item">
          <span class="nav-icon">🏠</span>
          <span class="nav-label">Properties</span>
        </router-link>

        <router-link to="/agent/saved-properties" class="nav-item">
          <span class="nav-icon">❤️</span>
          <span class="nav-label">My Wallet</span>
        </router-link>

        <router-link to="/agent/viewings" class="nav-item">
          <span class="nav-icon">📅</span>
          <span class="nav-label">Viewings</span>
        </router-link>

        <router-link to="/agent/calendar" class="nav-item">
          <span class="nav-icon">📆</span>
          <span class="nav-label">My Calendar</span>
        </router-link>

        <router-link to="/agent/documents" class="nav-item">
          <span class="nav-icon">📄</span>
          <span class="nav-label">Documents</span>
        </router-link>

        <router-link to="/profile" class="nav-item">
          <span class="nav-icon">👤</span>
          <span class="nav-label">Profile</span>
        </router-link>
      </nav>

      <button @click="logout" class="btn-logout">
        🚪 Logout
      </button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="conversations-container">
        <div class="conversations-header">
          <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
          <h1>💬 Messages</h1>
          <div class="search-box">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="🔍 Search conversations..."
              class="search-input"
            />
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <p>⏳ Loading conversations...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredConversations.length === 0" class="empty-state">
          <p>📭 No conversations yet</p>
          <small>Buyers will message you when interested in your properties</small>
        </div>

        <!-- Conversations List -->
        <div v-else class="conversations-list">
          <div
            v-for="conv in filteredConversations"
            :key="conv.buyer.id"
            @click="openConversation(conv.buyer.id)"
            :class="['conversation-item', { active: selectedBuyerId === conv.buyer.id, unread: conv.unread_count > 0 }]"
          >
            <!-- Buyer Avatar -->
            <div class="conv-avatar">
              {{ conv.buyer.name.charAt(0).toUpperCase() }}
            </div>

            <!-- Conversation Info -->
            <div class="conv-info">
              <div class="conv-header">
                <h3 class="conv-name">{{ conv.buyer.name }}</h3>
                <span class="conv-time">{{ formatTime(conv.created_at) }}</span>
              </div>

              <p class="conv-preview">{{ truncateMessage(conv.last_message.message_content) }}</p>

              <div class="conv-footer">
                <span v-if="conv.last_message.sender_id === currentUserId" class="sender-indicator">You:</span>
                <span v-if="conv.unread_count > 0" class="unread-badge">{{ conv.unread_count }} unread</span>
              </div>
            </div>

            <!-- Unread Indicator -->
            <div v-if="conv.unread_count > 0" class="unread-dot"></div>
          </div>
        </div>
      </div>

      <!-- Conversation Detail (if selected) -->
      <div v-if="selectedBuyerId" class="conversation-detail">
        <div class="detail-header">
          <button @click="selectedBuyerId = null" class="btn-back">← Back</button>
          <div class="detail-info">
            <h2>{{ selectedBuyer?.name }}</h2>
            <p class="status" :class="selectedBuyerOnline ? 'online' : 'offline'">
              {{ selectedBuyerOnline ? '🟢 Online' : '🔴 Offline' }}
            </p>
          </div>
        </div>

        <Message
          :property-id="0"
          :agent-id="currentUserId"
          :buyer-id="selectedBuyerId"
          @close="selectedBuyerId = null"
        />
      </div>
    </main>
  </div>
</template>

<script>
import Message from '../../components/Message.vue';

export default {
  name: 'AgentMessagesPage',
  components: {
    Message,
  },
  data() {
    return {
      sidebarOpen: false,
      conversations: [],
      loading: true,
      currentUserId: null,
      selectedBuyerId: null,
      searchQuery: '',
      totalUnread: 0,
      selectedBuyerOnline: false,
    };
  },

  computed: {
    selectedBuyer() {
      return this.conversations.find(c => c.buyer.id === this.selectedBuyerId)?.buyer;
    },

    filteredConversations() {
      if (!this.searchQuery) {
        return this.conversations;
      }

      return this.conversations.filter(conv =>
        conv.buyer.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },

  methods: {
    async loadConversations() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.currentUserId = user.id;

        const response = await fetch(window.__API_URL__ + '/api/agent/conversations', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();

        if (data.success) {
          this.conversations = data.data;
          this.calculateTotalUnread();
        }
      } catch (error) {
        console.error('Error loading conversations:', error);
      } finally {
        this.loading = false;
      }
    },

    calculateTotalUnread() {
      this.totalUnread = this.conversations.reduce((sum, conv) => sum + conv.unread_count, 0);
    },

    openConversation(buyerId) {
      this.selectedBuyerId = buyerId;
      this.checkOnlineStatus();
    },

    checkOnlineStatus() {
      // In a real app, this would check with the backend
      // For now, simulate online status
      this.selectedBuyerOnline = Math.random() > 0.3;
    },

    formatTime(timestamp) {
      const date = new Date(timestamp);
      const today = new Date();
      const isToday = date.toDateString() === today.toDateString();

      if (isToday) {
        return date.toLocaleTimeString('en-US', {
          hour: '2-digit',
          minute: '2-digit',
        });
      } else {
        return date.toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
        });
      }
    },

    truncateMessage(message, length = 60) {
      return message.length > length ? message.substring(0, length) + '...' : message;
    },

    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push('/');
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/');
      return;
    }

    this.loadConversations();

    // Poll for new messages every 3 seconds
    setInterval(() => {
      this.loadConversations();
    }, 3000);
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
  --palace-gold: #FFD700;
  --palace-gold-dark: #DAB600;
  --light-gray: #e0e0e0;
  --font-display: 'Poppins', sans-serif;
  --font-body: 'Inter', sans-serif;
}

.messages-wrapper {
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
  height: 100vh;
  left: 0;
  top: 0;
  z-index: 100;
  overflow-y: auto;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid var(--light-gray);
}

.sidebar-logo {
  font-size: 18px;
  font-weight: 800;
  font-family: var(--font-display);
  margin: 0;
  color: var(--smoky-black);
}

.logo-realty {
  color: var(--smoky-black);
}

.logo-ph {
  color: var(--palace-gold);
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  color: #666;
  text-decoration: none;
  transition: all 0.3s;
  position: relative;
}

.nav-item:hover {
  background: var(--white-smoke);
  color: var(--smoky-black);
}

.nav-item.active {
  background: var(--white-smoke);
  color: var(--palace-gold);
  border-left: 3px solid var(--palace-gold);
  padding-left: 17px;
}

.nav-icon {
  font-size: 18px;
  min-width: 24px;
}

.nav-label {
  flex: 1;
  font-size: 13px;
  font-weight: 600;
}

.badge {
  background: #ff4444;
  color: white;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
}

.btn-logout {
  margin: 20px;
  padding: 10px 16px;
  background: white;
  border: 1px solid var(--light-gray);
  color: var(--smoky-black);
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-logout:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.main-content {
  flex: 1;
  margin-left: 280px;
  display: flex;
}

.conversations-container {
  width: 350px;
  border-right: 1px solid var(--light-gray);
  background: white;
  display: flex;
  flex-direction: column;
}

.conversations-header {
  padding: 20px;
  border-bottom: 1px solid var(--light-gray);
}

.conversations-header h1 {
  font-size: 20px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 16px;
  font-family: var(--font-display);
}

.search-box {
  position: relative;
}

.search-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-size: 13px;
  transition: all 0.3s;
}

.search-input:focus {
  outline: none;
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.loading-state,
.empty-state {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #999;
  text-align: center;
  padding: 40px 20px;
}

.empty-state small {
  font-size: 12px;
  color: #ccc;
}

.conversations-list {
  flex: 1;
  overflow-y: auto;
}

.conversation-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-bottom: 1px solid var(--light-gray);
  cursor: pointer;
  transition: all 0.3s;
  position: relative;
}

.conversation-item:hover {
  background: var(--white-smoke);
}

.conversation-item.active {
  background: rgba(255, 215, 0, 0.1);
}

.conversation-item.unread {
  background: var(--white-smoke);
}

.conv-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 800;
  flex-shrink: 0;
}

.conv-info {
  flex: 1;
  min-width: 0;
}

.conv-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.conv-name {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
}

.conv-time {
  font-size: 11px;
  color: #999;
}

.conv-preview {
  font-size: 12px;
  color: #666;
  margin: 0 0 6px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.conv-footer {
  display: flex;
  gap: 8px;
  font-size: 11px;
}

.sender-indicator {
  color: #999;
  font-weight: 600;
}

.unread-badge {
  color: #ff4444;
  font-weight: 700;
}

.unread-dot {
  position: absolute;
  right: 12px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #ff4444;
}

/* ============================================================================
   CONVERSATION DETAIL
   ============================================================================ */

.conversation-detail {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: white;
}

.detail-header {
  padding: 20px;
  border-bottom: 1px solid var(--light-gray);
  display: flex;
  align-items: center;
  gap: 16px;
}

.btn-back {
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
  color: var(--smoky-black);
  padding: 0;
  font-weight: 700;
}

.detail-info h2 {
  font-size: 16px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0 0 4px 0;
}

.detail-info .status {
  font-size: 12px;
  margin: 0;
}

.status.online {
  color: #4caf50;
  font-weight: 600;
}

.status.offline {
  color: #999;
  font-weight: 600;
}

/* ============================================================================
   SCROLLBAR
   ============================================================================ */

.conversations-list::-webkit-scrollbar,
.sidebar::-webkit-scrollbar {
  width: 6px;
}

.conversations-list::-webkit-scrollbar-track,
.sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.conversations-list::-webkit-scrollbar-thumb,
.sidebar::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 3px;
}

.conversations-list::-webkit-scrollbar-thumb:hover,
.sidebar::-webkit-scrollbar-thumb:hover {
  background: #999;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */

@media (max-width: 1024px) {
  .sidebar {
    width: 240px;
  }

  .main-content {
    margin-left: 240px;
  }

  .conversations-container {
    width: 280px;
  }
}

@media (max-width: 768px) {
  .sidebar {
    width: 80px;
  }

  .main-content {
    margin-left: 80px;
  }

  .conversations-container {
    display: none;
  }

  .nav-label {
    display: none;
  }

  .sidebar-header {
    padding: 12px;
  }

  .sidebar-logo {
    font-size: 14px;
  }

  .nav-item {
    justify-content: center;
    padding: 12px;
  }
}

@media (max-width: 480px) {
  .sidebar {
    width: 60px;
  }

  .main-content {
    margin-left: 60px;
  }

  .nav-icon {
    font-size: 16px;
  }
}
</style>