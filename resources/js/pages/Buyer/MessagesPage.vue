<template>
  <div class="buyer-messages-wrapper">
    <!-- Top Navigation -->
    <nav class="top-nav">
      <div class="nav-container">
        <router-link to="/" class="nav-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </router-link>
        <div class="nav-actions">
          <router-link to="/dashboard" class="btn-secondary">← Back to Dashboard</router-link>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <div class="messages-container">
        <!-- Header -->
        <div class="messages-header">
          <h1>💬 My Messages</h1>
          <div class="search-box">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="🔍 Search agents..."
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
          <small>Start messaging agents about properties you're interested in!</small>
        </div>

        <!-- Conversations List -->
        <div v-else class="conversations-list">
          <div
            v-for="conv in filteredConversations"
            :key="conv.agent.id"
            @click="openConversation(conv.agent.id)"
            :class="['conversation-item', { unread: conv.unread_count > 0 }]"
          >
            <!-- Agent Avatar -->
            <div class="conv-avatar">
              {{ conv.agent.name.charAt(0).toUpperCase() }}
            </div>

            <!-- Conversation Info -->
            <div class="conv-info">
              <div class="conv-header">
                <h3 class="conv-name">{{ conv.agent.name }}</h3>
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
    </main>

    <!-- Error Message -->
    <transition name="fade">
      <div v-if="errorMessage" class="error-message">
        ✗ {{ errorMessage }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'BuyerMessagesPage',
  data() {
    return {
      conversations: [],
      loading: true,
      currentUserId: null,
      searchQuery: '',
      errorMessage: '',
      pollInterval: null,
    };
  },

  computed: {
    filteredConversations() {
      if (!this.searchQuery) {
        return this.conversations;
      }

      return this.conversations.filter(conv =>
        conv.agent.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },

  methods: {
    async loadConversations() {
      try {
        const token = localStorage.getItem('auth_token');
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.currentUserId = user.id;

        const response = await fetch(window.__API_URL__ + '/api/buyer/conversations', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();

        if (data.success) {
          this.conversations = data.data;
          this.loading = false;
        } else {
          this.showError(data.message || 'Failed to load conversations');
        }
      } catch (error) {
        console.error('Error loading conversations:', error);
        this.showError('Failed to load conversations');
        this.loading = false;
      }
    },

    openConversation(agentId) {
      this.$router.push(`/buyer/messages/${agentId}`);
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

    showError(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 3000);
    },

    startPolling() {
      this.pollInterval = setInterval(() => {
        this.loadConversations();
      }, 3000);
    },

    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval);
        this.pollInterval = null;
      }
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/login');
      return;
    }

    this.loadConversations();
    this.startPolling();
  },

  beforeUnmount() {
    this.stopPolling();
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

.buyer-messages-wrapper {
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
}

/* ============================================================================
   TOP NAVIGATION
   ============================================================================ */

.top-nav {
  background: white;
  border-bottom: 1px solid var(--light-gray);
  padding: 16px 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-logo {
  font-size: 24px;
  font-weight: 800;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
  text-decoration: none;
  display: flex;
  align-items: center;
  color: var(--smoky-black);
}

.logo-realty {
  color: var(--smoky-black);
}

.logo-ph {
  color: var(--palace-gold);
  margin-left: 2px;
}

.btn-secondary {
  padding: 10px 20px;
  background: white;
  border: 1px solid var(--light-gray);
  color: var(--smoky-black);
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-secondary:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.main-content {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 32px;
}

.messages-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  overflow: hidden;
}

.messages-header {
  padding: 24px;
  border-bottom: 1px solid var(--light-gray);
}

.messages-header h1 {
  font-size: 28px;
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
  font-family: var(--font-body);
  transition: all 0.3s;
}

.search-input:focus {
  outline: none;
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 3px rgba(230, 174, 13, 0.1);
}

.loading-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #999;
  text-align: center;
  padding: 60px 20px;
  min-height: 300px;
}

.empty-state small {
  font-size: 12px;
  color: #ccc;
}

.conversations-list {
  display: flex;
  flex-direction: column;
}

.conversation-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 24px;
  border-bottom: 1px solid var(--light-gray);
  cursor: pointer;
  transition: all 0.3s;
  position: relative;
}

.conversation-item:hover {
  background: var(--white-smoke);
}

.conversation-item.unread {
  background: rgba(230, 174, 13, 0.05);
}

.conv-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
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
  margin-bottom: 6px;
}

.conv-name {
  font-size: 14px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
}

.conv-time {
  font-size: 12px;
  color: #999;
}

.conv-preview {
  font-size: 13px;
  color: #666;
  margin: 0 0 8px 0;
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
  right: 20px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #ff4444;
}

/* ============================================================================
   MESSAGES
   ============================================================================ */

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
  .nav-container {
    padding: 0 16px;
  }

  .main-content {
    padding: 20px 16px;
  }

  .messages-header {
    padding: 16px;
  }

  .messages-header h1 {
    font-size: 22px;
  }

  .conversation-item {
    padding: 12px 16px;
  }

  .conv-avatar {
    width: 44px;
    height: 44px;
    font-size: 16px;
  }
}

@media (max-width: 480px) {
  .messages-header h1 {
    font-size: 20px;
  }

  .conversation-item {
    padding: 12px;
  }

  .conv-avatar {
    width: 40px;
    height: 40px;
    font-size: 14px;
  }

  .conv-name {
    font-size: 13px;
  }

  .conv-time {
    font-size: 11px;
  }
}
</style>