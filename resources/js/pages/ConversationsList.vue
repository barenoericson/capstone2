<template>
  <div class="conversations-wrapper">
    <!-- Sidebar -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>
      <nav class="sidebar-nav">
        <router-link :to="userRole === 'agent' ? '/agent/dashboard' : '/dashboard'" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>
        <router-link to="/conversations" class="nav-item active">
          <span class="nav-icon">💬</span>
          <span class="nav-label">Messages</span>
        </router-link>
        <template v-if="userRole === 'agent'">
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/agent/saved-properties" class="nav-item">
            <span class="nav-icon">💰</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
        </template>
        <template v-else>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">Saved</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
        </template>
        <router-link to="/profile" class="nav-item">
          <span class="nav-icon">👤</span>
          <span class="nav-label">Profile</span>
        </router-link>
      </nav>
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ userName }}</p>
            <p class="user-role-card">{{ userRole }}</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div class="topbar-left">
          <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
          <h1 class="page-title">Messages</h1>
          <span class="conv-count" v-if="!loading">{{ filteredConversations.length }} conversation{{ filteredConversations.length !== 1 ? 's' : '' }}</span>
        </div>
        <div class="topbar-right" v-if="totalUnread > 0">
          <span class="total-unread-badge">{{ totalUnread }} unread</span>
        </div>
      </div>

      <div class="page-body">
        <!-- Search Bar -->
        <div class="search-bar" v-if="conversations.length > 0">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search conversations..."
            class="search-input"
          />
          <button v-if="searchQuery" class="search-clear" @click="searchQuery = ''">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
              <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="state-box loading-state">
          <div class="loading-spinner">
            <div class="spinner-ring"></div>
          </div>
          <p>Loading conversations...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="conversations.length === 0" class="state-box empty">
          <div class="empty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" width="64" height="64">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <h3>No conversations yet</h3>
          <p v-if="userRole === 'buyer'">
            Browse properties and click <strong>Message</strong> to start a chat with an agent.
          </p>
          <p v-else>Buyers will message you through property listings.</p>
        </div>

        <!-- No Search Results -->
        <div v-else-if="filteredConversations.length === 0" class="state-box empty">
          <h3>No results found</h3>
          <p>No conversations match "{{ searchQuery }}"</p>
        </div>

        <!-- Conversation List -->
        <div v-else class="conv-list">
          <div
            v-for="conv in filteredConversations"
            :key="otherId(conv)"
            class="conv-item"
            :class="{ 'has-unread': conv.unread_count > 0 }"
            @click="openConversation(conv)"
          >
            <div class="conv-avatar-wrap">
              <div
                class="conv-avatar"
                :style="otherPhotoUrl(conv) ? `background-image:url(${otherPhotoUrl(conv)});background-size:cover;background-position:center;` : ''"
              >
                <span v-if="!otherPhotoUrl(conv)">{{ otherName(conv).charAt(0).toUpperCase() }}</span>
              </div>
              <span :class="['presence-dot', isOnline(conv) ? 'online' : 'offline']">
                <span v-if="isOnline(conv)" class="pulse-ring"></span>
              </span>
            </div>
            <div class="conv-body">
              <div class="conv-top">
                <span class="conv-name">{{ otherName(conv) }}</span>
                <span class="conv-time">{{ formatTime(conv.last_message?.created_at) }}</span>
              </div>
              <div class="conv-bottom">
                <p class="conv-preview" :class="{ 'unread-preview': conv.unread_count > 0 }">
                  <span v-if="conv.last_message?.sender_id === myId" class="you-prefix">You: </span>
                  {{ conv.last_message?.message_content || '📎 Attachment' }}
                </p>
                <span v-if="conv.unread_count > 0" class="unread-badge">{{ conv.unread_count > 99 ? '99+' : conv.unread_count }}</span>
              </div>
              <div class="conv-status">
                <span v-if="isOnline(conv)" class="status-text online-text">Active now</span>
                <span v-else-if="otherLastSeen(conv)" class="status-text offline-text">{{ formatLastSeen(otherLastSeen(conv)) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'ConversationsList',
  data() {
    return {
      sidebarOpen: false,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      myId: null,
      userName: '',
      userRole: '',
      conversations: [],
      loading: false,
      searchQuery: '',
      onlineUserIds: new Set(),
      presenceChannel: null,
      echoChannel: null,
      heartbeatInterval: null,
    };
  },

  computed: {
    filteredConversations() {
      if (!this.searchQuery.trim()) return this.conversations;
      const q = this.searchQuery.toLowerCase().trim();
      return this.conversations.filter(conv => {
        const name = this.otherName(conv).toLowerCase();
        const msg = (conv.last_message?.message_content || '').toLowerCase();
        return name.includes(q) || msg.includes(q);
      });
    },
    totalUnread() {
      return this.conversations.reduce((sum, c) => sum + (c.unread_count || 0), 0);
    },
  },

  methods: {
    async clearMessageNotifications() {
      try {
        const token = localStorage.getItem('auth_token');
        const apiUrl = localStorage.getItem('api_url') || window.__API_URL__;
        await fetch(`${apiUrl}/api/notifications/mark-type-read`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ type: 'message' }),
        });
      } catch (e) { /* silent */ }
    },

    otherId(conv) {
      return conv.agent?.id ?? conv.buyer?.id ?? 0;
    },

    otherName(conv) {
      return conv.agent?.name ?? conv.buyer?.name ?? 'Unknown';
    },

    otherPhotoUrl(conv) {
      return conv.agent?.profile_photo_url ?? conv.buyer?.profile_photo_url ?? null;
    },

    otherLastSeen(conv) {
      return conv.agent?.last_seen_at ?? conv.buyer?.last_seen_at ?? null;
    },

    isOnline(conv) {
      return this.onlineUserIds.has(this.otherId(conv));
    },

    formatTime(iso) {
      if (!iso) return '';
      const d = new Date(iso);
      const now = new Date();
      const diffMs = now - d;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);

      if (diffMins < 1) return 'Just now';
      if (diffMins < 60) return `${diffMins}m`;
      if (diffHours < 24 && d.getDate() === now.getDate()) return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      if (diffDays === 1) return 'Yesterday';
      if (diffDays < 7) return d.toLocaleDateString([], { weekday: 'short' });
      return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
    },

    formatLastSeen(iso) {
      if (!iso) return '';
      const d = new Date(iso);
      const now = new Date();
      const diffMs = now - d;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);

      if (diffMins < 1) return 'Active just now';
      if (diffMins < 60) return `Active ${diffMins}m ago`;
      if (diffHours < 24) return `Active ${diffHours}h ago`;
      if (diffDays === 1) return 'Active yesterday';
      return `Active ${diffDays}d ago`;
    },

    openConversation(conv) {
      const id = this.otherId(conv);
      this.$router.push(`/conversations/${id}`);
    },

    async loadConversations() {
      this.loading = true;
      try {
        const token = localStorage.getItem('auth_token');
        const endpoint = this.userRole === 'agent'
          ? `${this.apiUrl}/api/agent/conversations`
          : `${this.apiUrl}/api/buyer/conversations`;

        const res = await fetch(endpoint, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) this.conversations = data.data;
      } catch (e) {
        console.error('Load conversations error:', e);
      } finally {
        this.loading = false;
      }
    },

    async sendHeartbeat() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(`${this.apiUrl}/api/user/heartbeat`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
        });
      } catch (e) { /* silent */ }
    },

    subscribeToEcho() {
      if (!window.Echo || !this.myId) return;
      try {
        window.Echo.connector.pusher.config.auth.headers.Authorization =
          'Bearer ' + localStorage.getItem('auth_token');
      } catch (e) { /* ignore */ }

      // Listen for new messages to update the list in real-time
      this.echoChannel = window.Echo.private(`chat.${this.myId}`)
        .listen('.MessageSent', () => {
          this.loadConversations();
        })
        .listen('.MessageRead', () => {
          this.loadConversations();
        });

      // Presence for online dots
      this.presenceChannel = window.Echo.join('presence-users')
        .here((users) => { this.onlineUserIds = new Set(users.map(u => u.id)); })
        .joining((user) => { this.onlineUserIds.add(user.id); this.$forceUpdate(); })
        .leaving((user) => { this.onlineUserIds.delete(user.id); this.$forceUpdate(); });
    },

    leaveEcho() {
      if (this.echoChannel) window.Echo.leave(`chat.${this.myId}`);
      if (this.presenceChannel) window.Echo.leave('presence-users');
    },
  },

  async mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) { this.$router.push('/'); return; }

    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.myId = user.id;
    this.userName = user.name || '';
    this.userRole = user.role || 'buyer';

    await this.loadConversations();
    this.subscribeToEcho();
    this.clearMessageNotifications();

    // Heartbeat every 30 seconds
    this.sendHeartbeat();
    this.heartbeatInterval = setInterval(() => this.sendHeartbeat(), 30000);
  },

  beforeUnmount() {
    this.leaveEcho();
    if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800&display=swap');
* { margin: 0; padding: 0; box-sizing: border-box; }
:root { --gold: #FFD700; --gold-dark: #DAB600; --black: #100c08; --smoke: #f5f5f5; --gray: #e0e0e0; }

.conversations-wrapper { display: flex; min-height: 100vh; background: var(--smoke); font-family: 'Inter', sans-serif; }

/* ── Sidebar ── */
.sidebar {
  width: 260px; background: white; border-right: 1px solid var(--gray);
  display: flex; flex-direction: column; position: fixed; left: 0; top: 0; height: 100vh;
  overflow-y: auto; box-shadow: 2px 0 8px rgba(0,0,0,.07); z-index: 100;
}
.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--gray); }
.sidebar-logo { font-size: 22px; font-weight: 800; font-family: 'Poppins', sans-serif; display: flex; }
.logo-realty { color: var(--black); }
.logo-ph { color: var(--gold); margin-left: 2px; }
.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-item {
  display: flex; align-items: center; gap: 10px; padding: 11px 16px; margin: 0 8px;
  border-radius: 8px; color: var(--black); text-decoration: none; font-size: 14px; font-weight: 500; transition: all .2s;
}
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black); font-weight: 700; }
.nav-icon { font-size: 17px; min-width: 22px; text-align: center; }
.sidebar-footer { padding: 14px; border-top: 1px solid var(--gray); }
.user-card { background: var(--smoke); border-radius: 8px; padding: 10px; display: flex; align-items: center; gap: 10px; }
.user-avatar-lg {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black);
  display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px;
}
.user-name-card { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; }
.user-role-card { font-size: 11px; color: #999; margin: 2px 0 0; text-transform: capitalize; }

/* ── Main Content ── */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: white; border-bottom: 1px solid var(--gray); padding: 18px 32px;
  box-shadow: 0 2px 6px rgba(0,0,0,.06); display: flex; align-items: center; justify-content: space-between;
}
.topbar-left { display: flex; align-items: baseline; gap: 12px; }
.page-title { font-size: 26px; font-weight: 800; font-family: 'Poppins', sans-serif; color: var(--black); }
.conv-count { font-size: 13px; color: #999; font-weight: 500; }
.total-unread-badge {
  background: var(--gold); color: var(--black); border-radius: 999px;
  padding: 4px 14px; font-size: 13px; font-weight: 700;
}

.page-body { flex: 1; padding: 24px 32px; max-width: 720px; }

/* ── Search ── */
.search-bar {
  display: flex; align-items: center; gap: 10px; background: white;
  border-radius: 12px; padding: 10px 16px; margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,.06); border: 1px solid var(--gray);
  transition: border-color .2s;
}
.search-bar:focus-within { border-color: var(--gold); }
.search-icon { width: 20px; height: 20px; color: #999; flex-shrink: 0; }
.search-input {
  flex: 1; border: none; outline: none; font-size: 14px; font-family: 'Inter', sans-serif;
  background: transparent; color: var(--black);
}
.search-input::placeholder { color: #bbb; }
.search-clear {
  background: none; border: none; cursor: pointer; color: #999; padding: 2px;
  display: flex; align-items: center; transition: color .2s;
}
.search-clear:hover { color: var(--black); }

/* ── Loading ── */
.state-box { text-align: center; padding: 60px 20px; color: #999; }
.loading-state { display: flex; flex-direction: column; align-items: center; gap: 16px; }
.loading-spinner { width: 40px; height: 40px; position: relative; }
.spinner-ring {
  width: 100%; height: 100%; border-radius: 50%;
  border: 3px solid var(--gray); border-top-color: var(--gold);
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.state-box.empty { display: flex; flex-direction: column; align-items: center; gap: 8px; }
.empty-icon { margin-bottom: 8px; }
.state-box.empty h3 { font-size: 18px; color: var(--black); }
.state-box.empty p { font-size: 14px; color: #666; }

/* ── Conversation List ── */
.conv-list { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.07); overflow: hidden; }
.conv-item {
  display: flex; align-items: center; gap: 14px; padding: 16px 20px;
  cursor: pointer; transition: all .2s; border-bottom: 1px solid #f0f0f0;
  position: relative;
}
.conv-item:last-child { border-bottom: none; }
.conv-item:hover { background: #fafaf5; }
.conv-item.has-unread { background: #fffdf5; }
.conv-item.has-unread:hover { background: #fdf8e8; }

/* ── Avatar ── */
.conv-avatar-wrap { position: relative; flex-shrink: 0; }
.conv-avatar {
  width: 52px; height: 52px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 20px;
}
.presence-dot {
  width: 14px; height: 14px; border-radius: 50%; border: 2.5px solid white;
  position: absolute; bottom: 0; right: 0;
}
.presence-dot.online { background: #22c55e; }
.presence-dot.offline { background: #d1d5db; }

.pulse-ring {
  position: absolute; inset: -3px; border-radius: 50%;
  border: 2px solid #22c55e;
  animation: pulse-ring 1.5s ease-out infinite;
}
@keyframes pulse-ring {
  0% { transform: scale(1); opacity: 1; }
  100% { transform: scale(1.8); opacity: 0; }
}

/* ── Conversation Body ── */
.conv-body { flex: 1; min-width: 0; }
.conv-top { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; }
.conv-name { font-weight: 700; font-size: 15px; color: var(--black); }
.conv-time { font-size: 12px; color: #999; white-space: nowrap; flex-shrink: 0; }

.conv-bottom { display: flex; align-items: center; gap: 8px; margin-top: 3px; }
.conv-preview {
  font-size: 13px; color: #888; flex: 1; min-width: 0;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.conv-preview.unread-preview { color: var(--black); font-weight: 600; }
.you-prefix { color: #999; font-weight: 400; }

.conv-status { margin-top: 2px; }
.status-text { font-size: 11px; }
.online-text { color: #22c55e; font-weight: 600; }
.offline-text { color: #aaa; }

.unread-badge {
  background: var(--gold); color: var(--black);
  border-radius: 999px; padding: 2px 8px; font-size: 11px; font-weight: 700;
  flex-shrink: 0; min-width: 22px; text-align: center;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .sidebar { width: 0; display: none; }
  .main-content { margin-left: 0; }
  .page-body { padding: 16px; }
  .topbar { padding: 14px 16px; }
  .page-title { font-size: 20px; }
}
</style>
