<template>
  <div class="conv-layout">

    <!-- ═══════════════════ SIDEBAR ═══════════════════ -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link :to="userRole === 'agent' ? '/agent/dashboard' : '/dashboard'" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <router-link :to="userRole === 'agent' ? '/agent/dashboard' : '/dashboard'" exact class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </span>
          <span>Dashboard</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Messages</span>
          <span v-if="totalUnread > 0" class="nav-badge">{{ totalUnread > 99 ? '99+' : totalUnread }}</span>
        </router-link>

        <template v-if="userRole === 'agent'">
          <div class="nav-group-label">Manage</div>
          <router-link to="/agent/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            <span>My Properties</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span>Viewings</span>
          </router-link>
          <router-link to="/agent/saved-properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            </span>
            <span>My Wallet</span>
          </router-link>
        </template>

        <template v-else>
          <div class="nav-group-label">Browse</div>
          <router-link to="/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            <span>Properties</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </span>
            <span>Saved</span>
          </router-link>
          <router-link to="/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span>Viewings</span>
          </router-link>
          <div class="nav-group-label">Tools</div>
          <router-link to="/documents" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </span>
            <span>Documents</span>
          </router-link>
        </template>
      </nav>

      <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <router-link to="/profile" class="nav-item nav-user" @click="sidebarOpen = false">
          <div class="nav-av">
            <span>{{ userName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ userName }}</span>
            <span class="nav-user-role">{{ userRole }}</span>
          </div>
        </router-link>
        <router-link to="/settings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
          </span>
          <span>Settings</span>
        </router-link>
        <button class="nav-item nav-logout" @click="logout">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>

    <!-- ═══════════════════ MAIN ═══════════════════ -->
    <div class="main-wrapper">

      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="sidebarOpen = !sidebarOpen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <span class="topbar-title">Messages</span>
        </div>
        <div class="topbar-right">
          <span v-if="totalUnread > 0" class="unread-chip">{{ totalUnread }} unread</span>
        </div>
      </header>

      <div class="content-area">
        <div class="content-inner">

          <!-- Search -->
          <div class="search-wrap" v-if="conversations.length > 0">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="search-ico"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input v-model="searchQuery" type="text" placeholder="Search by name or message..." class="search-inp" />
            <button v-if="searchQuery" class="search-clear" @click="searchQuery = ''">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="state-center">
            <div class="spinner"></div>
            <p class="state-txt">Loading conversations...</p>
          </div>

          <!-- Empty -->
          <div v-else-if="conversations.length === 0" class="state-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--s300)" stroke-width="1.2" width="60" height="60">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <h3 class="state-head">No conversations yet</h3>
            <p class="state-txt" v-if="userRole === 'buyer'">Browse properties and click <strong>Message</strong> to start a chat with an agent.</p>
            <p class="state-txt" v-else>Buyers will message you through property listings.</p>
            <router-link v-if="userRole === 'buyer'" to="/properties" class="btn-cta">Browse Properties</router-link>
          </div>

          <!-- No results -->
          <div v-else-if="filteredConversations.length === 0" class="state-center">
            <h3 class="state-head">No results for "{{ searchQuery }}"</h3>
            <p class="state-txt">Try a different name or keyword.</p>
          </div>

          <!-- Conversation list -->
          <div v-else class="conv-list">
            <p class="list-meta">{{ filteredConversations.length }} conversation{{ filteredConversations.length !== 1 ? 's' : '' }}</p>
            <div class="conv-rows">
              <div
                v-for="conv in filteredConversations"
                :key="otherId(conv)"
                class="conv-item"
                :class="{ 'has-unread': conv.unread_count > 0 }"
                @click="openConversation(conv)"
              >
                <div class="av-wrap">
                  <div
                    class="av"
                    :style="otherPhotoUrl(conv) ? `background-image:url(${otherPhotoUrl(conv)});background-size:cover;background-position:center;` : ''"
                  >
                    <span v-if="!otherPhotoUrl(conv)">{{ otherName(conv).charAt(0).toUpperCase() }}</span>
                  </div>
                  <span :class="['dot', isOnline(conv) ? 'online' : 'offline']">
                    <span v-if="isOnline(conv)" class="pulse"></span>
                  </span>
                </div>

                <div class="conv-body">
                  <div class="conv-top">
                    <span class="conv-name">{{ otherName(conv) }}</span>
                    <span class="conv-time">{{ formatTime(conv.last_message?.created_at) }}</span>
                  </div>
                  <div class="conv-mid">
                    <p class="conv-preview" :class="{ bold: conv.unread_count > 0 }">
                      <span v-if="conv.last_message?.sender_id === myId" class="you-tag">You: </span>
                      {{ conv.last_message?.message_content || 'Attachment' }}
                    </p>
                    <span v-if="conv.unread_count > 0" class="badge">{{ conv.unread_count > 99 ? '99+' : conv.unread_count }}</span>
                  </div>
                  <div class="conv-status" v-if="isOnline(conv) || otherLastSeen(conv)">
                    <span v-if="isOnline(conv)" class="status-on">Active now</span>
                    <span v-else-if="otherLastSeen(conv)" class="status-off">{{ formatLastSeen(otherLastSeen(conv)) }}</span>
                  </div>
                </div>

                <svg class="conv-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
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

    otherId(conv)      { return conv.agent?.id ?? conv.buyer?.id ?? 0; },
    otherName(conv)    { return conv.agent?.name ?? conv.buyer?.name ?? 'Unknown'; },
    otherPhotoUrl(conv){ return conv.agent?.profile_photo_url ?? conv.buyer?.profile_photo_url ?? null; },
    otherLastSeen(conv){ return conv.agent?.last_seen_at ?? conv.buyer?.last_seen_at ?? null; },
    isOnline(conv)     { return this.onlineUserIds.has(this.otherId(conv)); },

    formatTime(iso) {
      if (!iso) return '';
      const d = new Date(iso), now = new Date();
      const diffMs = now - d, diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000), diffDays = Math.floor(diffMs / 86400000);
      if (diffMins < 1) return 'Just now';
      if (diffMins < 60) return `${diffMins}m`;
      if (diffHours < 24 && d.getDate() === now.getDate()) return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      if (diffDays === 1) return 'Yesterday';
      if (diffDays < 7) return d.toLocaleDateString([], { weekday: 'short' });
      return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
    },

    formatLastSeen(iso) {
      if (!iso) return '';
      const d = new Date(iso), now = new Date();
      const diffMins = Math.floor((now - d) / 60000);
      const diffHours = Math.floor((now - d) / 3600000);
      const diffDays = Math.floor((now - d) / 86400000);
      if (diffMins < 1) return 'Active just now';
      if (diffMins < 60) return `Active ${diffMins}m ago`;
      if (diffHours < 24) return `Active ${diffHours}h ago`;
      if (diffDays === 1) return 'Active yesterday';
      return `Active ${diffDays}d ago`;
    },

    openConversation(conv) { this.$router.push(`/conversations/${this.otherId(conv)}`); },

    async loadConversations() {
      this.loading = true;
      try {
        const token = localStorage.getItem('auth_token');
        const endpoint = this.userRole === 'agent'
          ? `${this.apiUrl}/api/agent/conversations`
          : `${this.apiUrl}/api/buyer/conversations`;
        const res = await fetch(endpoint, { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } });
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
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json', 'Content-Type': 'application/json' },
        });
      } catch (e) { /* silent */ }
    },

    subscribeToEcho() {
      if (!window.Echo || !this.myId) return;
      try {
        window.Echo.connector.pusher.config.auth.headers.Authorization = 'Bearer ' + localStorage.getItem('auth_token');
      } catch (e) { /* ignore */ }
      this.echoChannel = window.Echo.private(`chat.${this.myId}`)
        .listen('.MessageSent', () => { this.loadConversations(); })
        .listen('.MessageRead', () => { this.loadConversations(); });
      this.presenceChannel = window.Echo.join('presence-users')
        .here((users) => { this.onlineUserIds = new Set(users.map(u => u.id)); })
        .joining((user) => { this.onlineUserIds.add(user.id); this.$forceUpdate(); })
        .leaving((user) => { this.onlineUserIds.delete(user.id); this.$forceUpdate(); });
    },

    leaveEcho() {
      if (this.echoChannel) window.Echo.leave(`chat.${this.myId}`);
      if (this.presenceChannel) window.Echo.leave('presence-users');
    },

    logout() { localStorage.clear(); this.$router.push('/'); },
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
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@700;800&family=Inter:wght@400;500;600;700&display=swap');

/* ── TOKENS ── */
.conv-layout {
  --primary:  #0B1C39;
  --primary2: #102445;
  --primary3: #1a3158;
  --accent:   #D89B0F;
  --accent2:  #E5B332;
  --accent3:  #B07A08;
  --bg:       #EDF0F2;
  --white:    #FFFFFF;
  --sw:       242px;
  --th:       56px;
  --fd:       'Outfit','Inter',-apple-system,sans-serif;
  --fb:       'Inter',-apple-system,sans-serif;
  --s50:  #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s800: #292524; --s900: #1C1917;
  display: flex; min-height: 100vh;
  background: var(--bg); font-family: var(--fb);
  -webkit-font-smoothing: antialiased;
}

/* ══ SIDEBAR ══ */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--primary); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; }
.logo-ph { background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.sidebar-nav { flex: 1; overflow-y: auto; padding: 10px 10px 4px; scrollbar-width: none; }
.sidebar-nav::-webkit-scrollbar { display: none; }

.nav-item {
  display: flex; align-items: center; gap: 10px; padding: 9px 12px 9px 10px;
  border-radius: 9px; color: rgba(255,255,255,0.45); text-decoration: none;
  font-size: 13px; font-weight: 500; transition: all 0.18s ease; margin-bottom: 2px;
  cursor: pointer; border: none; background: none; width: 100%;
  text-align: left; font-family: var(--fb); position: relative;
}
.nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.78); }
.nav-item.router-link-exact-active {
  background: linear-gradient(90deg, rgba(216,155,15,0.14) 0%, rgba(216,155,15,0.03) 100%);
  color: var(--accent2);
}
.nav-item.router-link-exact-active::before {
  content: ''; position: absolute; left: 0; top: 7px; bottom: 7px;
  width: 3px; background: var(--accent); border-radius: 0 3px 3px 0;
}
.nav-item.router-link-exact-active .nav-icon-wrap { color: var(--accent); }

.nav-group-label {
  font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
  color: rgba(255,255,255,0.22); padding: 14px 12px 5px;
}
.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }
.nav-badge { margin-left: auto; background: rgba(216,155,15,0.18); color: var(--accent2); border-radius: 99px; padding: 2px 7px; font-size: 10.5px; font-weight: 700; }

.sidebar-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 10px 8px; }
.sidebar-bottom { flex-shrink: 0; padding: 0 10px 14px; }

.nav-user { gap: 10px; padding: 8px 10px; }
.nav-user:hover, .nav-user.router-link-exact-active { background: rgba(216,155,15,0.10); color: var(--accent2); }
.nav-av {
  width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
  background: linear-gradient(135deg, var(--accent), var(--accent3));
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 11px; color: #fff;
}
.nav-user-info { flex: 1; min-width: 0; }
.nav-user-name { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nav-user-role  { display: block; font-size: 10.5px; color: rgba(255,255,255,0.38); margin-top: 1px; text-transform: capitalize; }
.nav-logout { color: rgba(255,255,255,0.30); }
.nav-logout:hover { background: rgba(239,68,68,0.10); color: #f87171; }

.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 90; backdrop-filter: blur(2px); }

/* ══ MAIN ══ */
.main-wrapper { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: var(--white); position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 28px; height: var(--th); border-bottom: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.topbar-left  { display: flex; align-items: center; gap: 10px; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.hamburger { display: none; width: 34px; height: 34px; border-radius: 7px; border: 1px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s600); align-items: center; justify-content: center; }
.topbar-title { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--primary); letter-spacing: -0.3px; }

.unread-chip { background: rgba(216,155,15,0.08); border: 1px solid rgba(216,155,15,0.2); color: var(--s700); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 760px; margin: 0 auto; padding: 28px 30px 48px; }

/* Search */
.search-wrap {
  display: flex; align-items: center; gap: 10px;
  background: var(--white); border-radius: 12px; padding: 10px 16px;
  margin-bottom: 20px; border: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: border-color 0.2s;
}
.search-wrap:focus-within { border-color: rgba(216,155,15,0.4); }
.search-ico { color: var(--s300); flex-shrink: 0; }
.search-inp { flex: 1; border: none; outline: none; font-size: 13.5px; font-family: var(--fb); background: transparent; color: var(--s900); }
.search-inp::placeholder { color: var(--s300); }
.search-clear { background: none; border: none; cursor: pointer; color: var(--s300); display: flex; align-items: center; transition: color 0.2s; }
.search-clear:hover { color: var(--s500); }

/* States */
.state-center { text-align: center; padding: 72px 20px; display: flex; flex-direction: column; align-items: center; gap: 12px; }
.state-head { font-family: var(--fd); font-size: 19px; font-weight: 700; color: var(--primary); margin: 0; }
.state-txt  { font-size: 13.5px; color: var(--s400); margin: 0; max-width: 300px; text-align: center; }
.spinner { width: 36px; height: 36px; border-radius: 50%; border: 3px solid var(--s200); border-top-color: var(--accent); animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.btn-cta {
  display: inline-block; padding: 10px 24px; background: var(--primary); color: var(--white);
  border-radius: 9px; font-weight: 700; font-size: 13px; text-decoration: none; transition: all 0.18s;
}
.btn-cta:hover { background: var(--primary2); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(40,53,62,0.22); }

/* Conversation list */
.list-meta { font-size: 12.5px; color: var(--s400); margin: 0 0 14px; }
.conv-rows { background: var(--white); border-radius: 14px; border: 1px solid var(--s200); overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }

.conv-item {
  display: flex; align-items: center; gap: 13px; padding: 14px 18px;
  cursor: pointer; transition: background 0.16s;
  border-bottom: 1px solid var(--s100); position: relative;
}
.conv-item:last-child { border-bottom: none; }
.conv-item:hover { background: var(--s50); }
.conv-item.has-unread { background: rgba(216,155,15,0.03); }
.conv-item.has-unread:hover { background: rgba(216,155,15,0.07); }

.av-wrap { position: relative; flex-shrink: 0; }
.av {
  width: 46px; height: 46px; border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), var(--primary3));
  color: var(--accent2);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 17px;
}
.dot { width: 12px; height: 12px; border-radius: 50%; border: 2.5px solid var(--white); position: absolute; bottom: 1px; right: 1px; }
.dot.online  { background: #22c55e; }
.dot.offline { background: var(--s300); }
.pulse { position: absolute; inset: -3px; border-radius: 50%; border: 2px solid #22c55e; animation: pulse 1.5s ease-out infinite; }
@keyframes pulse { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(1.8); opacity: 0; } }

.conv-body { flex: 1; min-width: 0; }
.conv-top   { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; }
.conv-name  { font-weight: 700; font-size: 14px; color: var(--primary); }
.conv-time  { font-size: 11px; color: var(--s300); white-space: nowrap; flex-shrink: 0; }

.conv-mid     { display: flex; align-items: center; gap: 8px; margin-top: 3px; }
.conv-preview { font-size: 13px; color: var(--s400); flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.conv-preview.bold { color: var(--s900); font-weight: 600; }
.you-tag { color: var(--s300); font-weight: 400; }

.conv-status { margin-top: 2px; }
.status-on  { font-size: 11px; color: #22c55e; font-weight: 600; }
.status-off { font-size: 11px; color: var(--s300); }

.badge { background: var(--accent); color: var(--primary); border-radius: 999px; padding: 2px 7px; font-size: 11px; font-weight: 700; flex-shrink: 0; min-width: 20px; text-align: center; }

.conv-arrow { flex-shrink: 0; opacity: 0; transition: opacity 0.18s; color: var(--s300); }
.conv-item:hover .conv-arrow { opacity: 1; }

/* Responsive */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .hamburger { display: flex; }
  .main-wrapper { margin-left: 0; }
  .topbar { padding: 0 16px; }
  .topbar-title { font-size: 14px; }
  .content-inner { padding: 16px; }
}
@media (max-width: 480px) {
  .conv-item { padding: 12px 14px; }
  .av { width: 40px; height: 40px; font-size: 15px; }
}
</style>
