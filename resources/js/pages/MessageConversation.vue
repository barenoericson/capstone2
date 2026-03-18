<template>
  <div class="chat-wrapper">
    <!-- Sidebar -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>
      <nav class="sidebar-nav">
        <router-link :to="userRole === 'agent' ? '/agent/dashboard' : '/dashboard'" exact class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
          <span>{{ userRole === 'agent' ? 'Overview' : 'Dashboard' }}</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
          <span>Messages</span>
        </router-link>

        <template v-if="userRole === 'agent'">
          <router-link to="/agent/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span>Viewings</span>
          </router-link>

          <div class="nav-group-label">Manage</div>
          <router-link to="/agent/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
            <span>My Properties</span>
          </router-link>
          <router-link to="/agent/saved-properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg></span>
            <span>My Wallet</span>
          </router-link>

          <div class="nav-group-label">Tools</div>
          <router-link to="/agent/documents" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
            <span>Documents</span>
          </router-link>
        </template>

        <template v-else>
          <router-link to="/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span>Viewings</span>
          </router-link>

          <div class="nav-group-label">Browse</div>
          <router-link to="/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
            <span>Properties</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></span>
            <span>Saved</span>
          </router-link>

          <div class="nav-group-label">Tools</div>
          <router-link to="/documents" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
            <span>Documents</span>
          </router-link>
        </template>
      </nav>

      <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <router-link to="/profile" class="nav-item nav-user" @click="sidebarOpen = false">
          <div class="nav-av" :style="myPhotoUrl ? `background-image:url(${myPhotoUrl});background-size:cover;background-position:center;` : ''">
            <span v-if="!myPhotoUrl">{{ myName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ myName }}</span>
            <span class="nav-user-role">{{ userRole }}</span>
          </div>
        </router-link>
        <router-link to="/settings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg></span>
          <span>Settings</span>
        </router-link>
        <button class="nav-item nav-logout" @click="logout">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Chat Area -->
    <main class="chat-main">
      <!-- Chat Header -->
      <div class="chat-header">
        <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <router-link to="/conversations" class="btn-back">
          <span class="back-arrow">←</span>
          <span class="back-text">Back</span>
        </router-link>
        <div v-if="otherUser" class="chat-peer">
          <component :is="profileRoute ? 'router-link' : 'div'" :to="profileRoute" class="peer-avatar-link">
            <div class="peer-avatar" :style="otherUser.profile_photo_url ? `background-image:url(${otherUser.profile_photo_url});background-size:cover;background-position:center;` : ''">
              <span v-if="!otherUser.profile_photo_url">{{ otherUser.name.charAt(0).toUpperCase() }}</span>
            </div>
            <span v-if="isOtherOnline" class="avatar-online-dot"></span>
          </component>
          <div class="peer-info">
            <component :is="profileRoute ? 'router-link' : 'span'" :to="profileRoute" class="peer-name-link">
              <h3 class="peer-name">{{ otherUser.name }}</h3>
            </component>
            <span :class="['status-text', isOtherOnline ? 'online' : 'offline']">
              <span v-if="isOtherOnline" class="pulse-dot"></span>
              {{ isOtherOnline ? 'Active now' : lastSeenText }}
            </span>
          </div>
        </div>
        <router-link v-if="contextProperty" :to="`/property/${contextProperty.id}`" class="property-context">
          🏠 <strong>{{ contextProperty.title }}</strong>
          <span v-if="contextProperty.city" class="prop-city"> · {{ contextProperty.city }}</span>
        </router-link>
      </div>

      <!-- Messages -->
      <div class="messages-area" ref="messagesArea">
        <div v-if="loadingMessages" class="loading-msg">
          <div class="loading-spinner"></div>
          <p>Loading messages...</p>
        </div>
        <div v-else-if="messages.length === 0" class="empty-msg">
          <div class="empty-chat-icon">💬</div>
          <h3>Start the conversation</h3>
          <p>Send a message to {{ otherUser?.name || 'start chatting' }}</p>
        </div>
        <div v-else>
          <template v-for="(msg, index) in messages" :key="msg.id">
            <!-- Date separator -->
            <div v-if="showDateSeparator(index)" class="date-separator">
              <span>{{ formatDateLabel(msg.created_at) }}</span>
            </div>

            <div :class="['msg-row', msg.sender_id === myId ? 'sent' : 'received']">
              <!-- Avatar beside received messages -->
              <div v-if="msg.sender_id !== myId && showAvatar(index)" class="msg-avatar-wrap">
                <div class="msg-avatar" :style="otherUser && otherUser.profile_photo_url ? `background-image:url(${otherUser.profile_photo_url});background-size:cover;background-position:center;` : ''">
                  <span v-if="!(otherUser && otherUser.profile_photo_url)">{{ otherUser ? otherUser.name.charAt(0).toUpperCase() : '?' }}</span>
                </div>
              </div>
              <div v-else-if="msg.sender_id !== myId" class="msg-avatar-spacer"></div>

              <div class="msg-bubble-wrap">
                <div class="msg-bubble">
                  <p v-if="msg.message_content" class="msg-text">{{ msg.message_content }}</p>
                  <div v-if="msg.attachment_path" class="msg-attachment">
                    <img v-if="isImage(msg.attachment_type)" :src="attachmentUrl(msg.attachment_path)" :alt="msg.attachment_name" class="attachment-image" @click="openAttachment(msg.attachment_path)" />
                    <div v-else class="file-card">
                      <div class="file-card-icon" :style="{ background: getFileIcon(msg.attachment_type).color }">
                        {{ getFileIcon(msg.attachment_type).icon }}
                      </div>
                      <div class="file-card-info">
                        <span class="file-card-name">{{ msg.attachment_name }}</span>
                        <span class="file-card-type">{{ getFileIcon(msg.attachment_type).label }}</span>
                      </div>
                      <a :href="attachmentUrl(msg.attachment_path)" :download="msg.attachment_name" class="file-card-download" title="Download">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                      </a>
                    </div>
                  </div>
                  <div class="msg-meta">
                    <span class="msg-time">{{ formatTime(msg.created_at) }}</span>
                    <span v-if="msg.sender_id === myId" class="msg-status">
                      <span v-if="msg._pending" class="status-sending">●</span>
                      <span v-else-if="isMessageSeen(msg)" class="status-seen" title="Seen">✓✓</span>
                      <span v-else class="status-sent" title="Sent">✓✓</span>
                    </span>
                  </div>
                </div>
                <div v-if="msg.sender_id === myId && isLastSeenMessage(index)" class="seen-label">
                  Seen {{ seenAtFormatted }}
                </div>
              </div>

              <!-- Avatar beside sent messages -->
              <div v-if="msg.sender_id === myId && showAvatar(index)" class="msg-avatar-wrap my-avatar-wrap">
                <div class="msg-avatar" :style="myPhotoUrl ? `background-image:url(${myPhotoUrl});background-size:cover;background-position:center;` : ''">
                  <span v-if="!myPhotoUrl">{{ myName.charAt(0).toUpperCase() }}</span>
                </div>
              </div>
              <div v-else-if="msg.sender_id === myId" class="msg-avatar-spacer my-avatar-wrap"></div>
            </div>
          </template>
        </div>
      </div>

      <!-- Typing Indicator -->
      <transition name="typing-fade">
        <div v-if="otherTyping && otherUser" class="typing-indicator">
          <div class="typing-avatar" :style="otherUser.profile_photo_url ? `background-image:url(${otherUser.profile_photo_url});background-size:cover;background-position:center;` : ''">
            <span v-if="!otherUser.profile_photo_url">{{ otherUser.name.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="typing-bubble">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
          </div>
          <span class="typing-label">{{ otherUser.name }} is typing...</span>
        </div>
      </transition>

      <!-- Input Area -->
      <div class="input-area">
        <div v-if="pendingFile" class="attachment-preview">
          <span class="file-icon">{{ getFileIcon(pendingFile.type).icon }}</span>
          <span class="file-name">{{ pendingFile.name }}</span>
          <span class="file-size-label">{{ formatFileSize(pendingFile.size) }}</span>
          <button @click="pendingFile = null" class="btn-remove-file">✕</button>
        </div>
        <div class="input-row">
          <button class="btn-attach" @click="$refs.fileInput.click()" title="Attach file">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
          </button>
          <input ref="fileInput" type="file" style="display:none" @change="onFileSelected" />
          <textarea v-model="newMessage" class="msg-input" placeholder="Type a message..." rows="1" @keydown.enter.exact.prevent="sendMessage" @input="handleTyping" ref="messageInput"></textarea>
          <button class="btn-send" @click="sendMessage" :disabled="sending || (!newMessage.trim() && !pendingFile)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'MessageConversation',
  data() {
    return {
      sidebarOpen: false,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      myId: null,
      myName: '',
      myPhotoUrl: null,
      userRole: '',
      otherUser: null,
      contextProperty: null,
      messages: [],
      loadingMessages: false,
      newMessage: '',
      pendingFile: null,
      sending: false,
      onlineUserIds: [],
      echoChannel: null,
      presenceChannel: null,
      conversationChannel: null,
      otherTyping: false,
      typingTimer: null,
      heartbeatInterval: null,
      otherHasSeenMyMessages: false,
      seenAt: null,
    };
  },

  computed: {
    isOtherOnline() {
      return this.otherUser && this.onlineUserIds.includes(this.otherUser.id);
    },
    lastSeenText() {
      if (!this.otherUser) return 'Offline';
      const lastSeen = this.otherUser.last_seen_at;
      if (!lastSeen) return 'Offline';
      return 'Active ' + this.timeAgo(lastSeen);
    },
    seenAtFormatted() {
      if (!this.seenAt) return '';
      const d = new Date(this.seenAt);
      const now = new Date();
      if (now - d < 60000) return 'just now';
      return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    },
    profileRoute() {
      if (!this.otherUser) return null;
      // Agent viewing anyone → buyer profile view
      if (this.userRole === 'agent') return `/agent/buyer-profile/${this.otherUser.id}`;
      // Buyer viewing agent → agent public profile
      if (this.otherUser.role === 'agent') return `/agent-profile/${this.otherUser.id}`;
      // Buyer viewing another buyer → agent public profile (shows basic info)
      return `/agent-profile/${this.otherUser.id}`;
    },
  },

  methods: {
    timeAgo(isoDate) {
      const d = new Date(isoDate);
      const now = new Date();
      const mins = Math.floor((now - d) / 60000);
      if (mins < 1) return 'just now';
      if (mins < 60) return `${mins}m ago`;
      const hours = Math.floor(mins / 60);
      if (hours < 24) return `${hours}h ago`;
      const days = Math.floor(hours / 24);
      if (days === 1) return 'yesterday';
      return `${days}d ago`;
    },

    formatTime(iso) {
      if (!iso) return '';
      return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    },

    formatDateLabel(iso) {
      if (!iso) return '';
      const d = new Date(iso);
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const msgDate = new Date(d.getFullYear(), d.getMonth(), d.getDate());
      const diffDays = Math.floor((today - msgDate) / 86400000);
      if (diffDays === 0) return 'Today';
      if (diffDays === 1) return 'Yesterday';
      return d.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });
    },

    showDateSeparator(index) {
      if (index === 0) return true;
      const curr = new Date(this.messages[index].created_at).toDateString();
      const prev = new Date(this.messages[index - 1].created_at).toDateString();
      return curr !== prev;
    },

    showAvatar(index) {
      const msg = this.messages[index];
      const next = this.messages[index + 1];
      if (!next) return true;
      return next.sender_id !== msg.sender_id;
    },

    isMessageSeen(msg) {
      if (!this.otherHasSeenMyMessages || !this.seenAt) return false;
      return new Date(msg.created_at) <= new Date(this.seenAt);
    },

    isLastSeenMessage(index) {
      if (!this.otherHasSeenMyMessages || !this.seenAt) return false;
      const msg = this.messages[index];
      if (msg.sender_id !== this.myId) return false;
      const seenTime = new Date(this.seenAt);
      if (new Date(msg.created_at) > seenTime) return false;
      for (let i = index + 1; i < this.messages.length; i++) {
        if (this.messages[i].sender_id === this.myId && new Date(this.messages[i].created_at) <= seenTime) return false;
      }
      return true;
    },

    isImage(mimeType) { return mimeType && mimeType.startsWith('image/'); },
    attachmentUrl(path) { return `${this.apiUrl}/storage/${path}`; },
    openAttachment(path) { window.open(this.attachmentUrl(path), '_blank'); },
    getFileIcon(mimeType) {
      if (!mimeType) return { icon: '📎', label: 'File', color: '#888' };
      if (mimeType === 'application/pdf') return { icon: '📕', label: 'PDF Document', color: '#e74c3c' };
      if (mimeType.includes('word') || mimeType.includes('document')) return { icon: '📘', label: 'Word Document', color: '#2b579a' };
      if (mimeType.includes('sheet') || mimeType.includes('excel')) return { icon: '📗', label: 'Excel Spreadsheet', color: '#217346' };
      if (mimeType.includes('presentation') || mimeType.includes('powerpoint')) return { icon: '📙', label: 'Presentation', color: '#d24726' };
      if (mimeType.startsWith('video/')) return { icon: '🎬', label: 'Video', color: '#6c5ce7' };
      if (mimeType.startsWith('audio/')) return { icon: '🎵', label: 'Audio', color: '#00b894' };
      if (mimeType === 'application/zip' || mimeType.includes('compressed')) return { icon: '🗜️', label: 'Archive', color: '#fdcb6e' };
      return { icon: '📎', label: 'File', color: '#888' };
    },
    formatFileSize(bytes) {
      if (!bytes) return '';
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / 1048576).toFixed(1) + ' MB';
    },

    scrollToBottom() {
      this.$nextTick(() => {
        const area = this.$refs.messagesArea;
        if (area) area.scrollTop = area.scrollHeight;
      });
    },

    async loadMessages() {
      this.loadingMessages = true;
      try {
        const token = localStorage.getItem('auth_token');
        const userId = this.$route.params.userId;
        const propertyId = this.$route.query.property_id;
        let url = `${this.apiUrl}/api/conversations/${userId}/messages`;
        if (propertyId) url += `?property_id=${propertyId}`;

        const res = await fetch(url, { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) {
          this.messages = data.data.messages || [];
          this.otherUser = data.data.otherUser || null;
          if (!this.contextProperty && this.messages.length > 0) {
            const msgWithProp = this.messages.find(m => m.property);
            if (msgWithProp) this.contextProperty = msgWithProp.property;
          }
          this.checkInitialSeenStatus();
          this.scrollToBottom();
          await this.markAsRead();
        }
      } catch (e) {
        console.error('Load messages error:', e);
      } finally {
        this.loadingMessages = false;
      }
    },

    checkInitialSeenStatus() {
      for (let i = this.messages.length - 1; i >= 0; i--) {
        const msg = this.messages[i];
        if (msg.sender_id === this.myId) {
          if (msg.is_read && msg.read_at) {
            this.otherHasSeenMyMessages = true;
            this.seenAt = msg.read_at;
          }
          break;
        }
      }
    },

    async markAsRead() {
      try {
        const token = localStorage.getItem('auth_token');
        const userId = this.$route.params.userId;
        await fetch(`${this.apiUrl}/api/conversations/${userId}/mark-read`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
      } catch (e) { /* silent */ }
    },

    async loadContextProperty() {
      const propertyId = this.$route.query.property_id;
      if (!propertyId) return;
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/properties/${propertyId}`, { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) this.contextProperty = data.data;
      } catch (e) { /* silent */ }
    },

    async sendMessage() {
      if ((!this.newMessage.trim() && !this.pendingFile) || this.sending) return;
      this.sending = true;
      try {
        const token = localStorage.getItem('auth_token');
        const recipientId = this.$route.params.userId;
        const propertyId = this.$route.query.property_id;

        const form = new FormData();
        form.append('recipient_id', recipientId);
        form.append('message_content', this.newMessage.trim());
        if (propertyId) form.append('property_id', propertyId);
        if (this.pendingFile) form.append('attachment', this.pendingFile);

        const optimistic = {
          id: Date.now(),
          sender_id: this.myId,
          recipient_id: parseInt(recipientId),
          message_content: this.newMessage.trim(),
          attachment_path: null,
          attachment_name: this.pendingFile?.name || null,
          attachment_type: this.pendingFile?.type || null,
          created_at: new Date().toISOString(),
          _pending: true,
        };
        this.messages.push(optimistic);
        this.newMessage = '';
        this.pendingFile = null;
        this.otherHasSeenMyMessages = false;
        this.seenAt = null;
        this.scrollToBottom();

        const res = await fetch(`${this.apiUrl}/api/messages`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
          body: form,
        });
        const data = await res.json();

        const idx = this.messages.findIndex(m => m.id === optimistic.id);
        if (data.success && data.data && idx !== -1) {
          // Replace optimistic with real message from server
          this.messages.splice(idx, 1, data.data);
        } else if (idx !== -1) {
          // API error — remove failed optimistic message and show error
          this.messages.splice(idx, 1);
          const errMsg = data.message || 'Failed to send message';
          console.error('Send failed:', errMsg, data.errors || '');
          alert(errMsg);
        }
        this.scrollToBottom();
        this.$refs.messageInput?.focus();
      } catch (e) {
        console.error('Send error:', e);
        // Network error — keep the optimistic message visible
      } finally {
        this.sending = false;
      }
    },

    onFileSelected(e) {
      const file = e.target.files[0];
      if (file) this.pendingFile = file;
      e.target.value = '';
    },

    subscribeToEcho() {
      if (!window.Echo || !this.myId) return;
      try {
        window.Echo.connector.pusher.config.auth.headers.Authorization = 'Bearer ' + localStorage.getItem('auth_token');
      } catch (e) { /* connector not ready yet */ }

      // Private channel: receive messages + seen notifications
      this.echoChannel = window.Echo.private(`chat.${this.myId}`)
        .listen('.MessageSent', (payload) => {
          const otherUserId = parseInt(this.$route.params.userId);
          if (payload.sender_id === otherUserId || payload.recipient_id === otherUserId) {
            const alreadyExists = this.messages.some(m => m.id === payload.id);
            if (!alreadyExists) {
              this.messages.push(payload);
              this.scrollToBottom();
              this.markAsRead();
              this.otherTyping = false;
              clearTimeout(this.typingTimer);
            }
          }
        })
        .listen('.MessageRead', (payload) => {
          const otherUserId = parseInt(this.$route.params.userId);
          if (payload.reader_id === otherUserId) {
            this.otherHasSeenMyMessages = true;
            this.seenAt = payload.read_at;
          }
        });

      // Conversation presence channel: online + typing
      const otherUserId = parseInt(this.$route.params.userId);
      const ids = [this.myId, otherUserId].sort((a, b) => a - b);
      const convChannelName = `presence.conversation.${ids[0]}.${ids[1]}`;

      this.conversationChannel = window.Echo.join(convChannelName)
        .here((users) => { this.onlineUserIds = users.map(u => u.id); })
        .joining((user) => {
          if (!this.onlineUserIds.includes(user.id)) this.onlineUserIds = [...this.onlineUserIds, user.id];
        })
        .leaving((user) => {
          this.onlineUserIds = this.onlineUserIds.filter(id => id !== user.id);
          if (user.id === otherUserId) { this.otherTyping = false; clearTimeout(this.typingTimer); }
        })
        .listenForWhisper('typing', (data) => {
          if (data.userId === otherUserId) {
            this.otherTyping = true;
            clearTimeout(this.typingTimer);
            this.typingTimer = setTimeout(() => { this.otherTyping = false; }, 3000);
          }
        });
    },

    handleTyping() {
      if (!this.conversationChannel) return;
      const now = Date.now();
      if (!this._lastWhisperAt || now - this._lastWhisperAt > 2000) {
        this._lastWhisperAt = now;
        this.conversationChannel.whisper('typing', { userId: this.myId });
      }
    },

    startHeartbeat() {
      const sendHeartbeat = async () => {
        try {
          const token = localStorage.getItem('auth_token');
          await fetch(`${this.apiUrl}/api/user/heartbeat`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
          });
        } catch (e) { /* silent */ }
      };
      sendHeartbeat();
      this.heartbeatInterval = setInterval(sendHeartbeat, 30000);
    },

    leaveEcho() {
      try {
        if (this.echoChannel) window.Echo.leave(`chat.${this.myId}`);
        if (this.conversationChannel) {
          const otherUserId = parseInt(this.$route.params.userId);
          const ids = [this.myId, otherUserId].sort((a, b) => a - b);
          window.Echo.leave(`presence.conversation.${ids[0]}.${ids[1]}`);
        }
        if (this.presenceChannel) window.Echo.leave('presence-users');
      } catch (e) { /* ignore */ }
      clearTimeout(this.typingTimer);
      this._lastWhisperAt = null;
    },

    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push('/');
    },
  },

  async mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) { this.$router.push('/'); return; }

    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.myId = user.id;
    this.myName = user.name || 'Me';
    this.userRole = user.role || 'buyer';

    try {
      const res = await fetch(`${this.apiUrl}/api/user/profile`, { headers: { Authorization: `Bearer ${token}` } });
      const data = await res.json();
      const photoPath = data?.data?.profile_photo_path ?? data?.profile_photo_path ?? data?.user?.profile_photo_path ?? null;
      if (photoPath) this.myPhotoUrl = `${this.apiUrl}/storage/${photoPath}`;
    } catch (e) { /* no photo */ }

    await Promise.all([this.loadMessages(), this.loadContextProperty()]);
    this.subscribeToEcho();
    this.startHeartbeat();
  },

  beforeUnmount() {
    this.leaveEcho();
    if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap');
* { margin: 0; padding: 0; box-sizing: border-box; }

.chat-wrapper {
  --navy: #0B1C39; --navy2: #102445; --navy3: #1a3158;
  --gold: #D89B0F; --gold2: #E5B332; --gold3: #B07A08;
  --s50: #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s800: #292524; --s900: #1C1917;
  --white: #FFFFFF; --bg: #F0F2F5;
  --fd: 'Outfit','Inter',-apple-system,sans-serif;
  --fb: 'Inter',-apple-system,sans-serif;
  display: flex; height: 100vh; font-family: var(--fb); background: var(--bg);
  -webkit-font-smoothing: antialiased;
}

/* ── Sidebar ── */
.sidebar {
  width: 252px; background: var(--navy); display: flex; flex-direction: column;
  position: fixed; left: 0; top: 0; height: 100vh; overflow-y: auto; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.sidebar-logo { font-size: 18px; font-weight: 800; font-family: var(--fd); display: flex; }
.logo-realty { color: #fff; }
.logo-ph { background: linear-gradient(135deg, var(--gold), var(--gold2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-left: 1px; }
.sidebar-nav { flex: 1; padding: 10px 10px 4px; overflow-y: auto; scrollbar-width: none; }
.sidebar-nav::-webkit-scrollbar { display: none; }

.nav-group-label {
  font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
  color: rgba(255,255,255,0.22); padding: 14px 12px 5px;
}

.nav-item {
  display: flex; align-items: center; gap: 10px; padding: 9px 12px 9px 10px;
  margin-bottom: 2px; border-radius: 9px; color: rgba(255,255,255,0.45);
  text-decoration: none; font-size: 13px; font-weight: 500; transition: all .18s;
  position: relative; cursor: pointer; border: none; background: none;
  width: 100%; text-align: left; font-family: var(--fb);
}
.nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.78); }
.nav-item.router-link-exact-active {
  background: linear-gradient(90deg, rgba(216,155,15,0.14) 0%, rgba(216,155,15,0.03) 100%);
  color: var(--gold2);
}
.nav-item.router-link-exact-active::before {
  content: ''; position: absolute; left: 0; top: 7px; bottom: 7px;
  width: 3px; background: var(--gold); border-radius: 0 3px 3px 0;
}
.nav-item.router-link-exact-active .nav-icon-wrap { color: var(--gold); }

.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }

.sidebar-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 10px 8px; }
.sidebar-bottom { flex-shrink: 0; padding: 0 10px 14px; }

.nav-user { gap: 10px; padding: 8px 10px; }
.nav-user:hover, .nav-user.router-link-exact-active { background: rgba(216,155,15,0.10); color: var(--gold2); }
.nav-av {
  width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 11px; color: #fff;
}
.nav-user-info { flex: 1; min-width: 0; }
.nav-user-name { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nav-user-role { display: block; font-size: 10.5px; color: rgba(255,255,255,0.38); margin-top: 1px; text-transform: capitalize; }
.nav-logout { color: rgba(255,255,255,0.30); }
.nav-logout:hover { background: rgba(239,68,68,0.10); color: #f87171; }

/* ── Chat Main ── */
.chat-main { margin-left: 252px; flex: 1; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

/* Header */
.chat-header {
  background: var(--white); border-bottom: 1px solid var(--s200);
  padding: 10px 24px; display: flex; align-items: center; gap: 14px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04); flex-shrink: 0;
}
.hamburger-btn {
  display: none; width: 34px; height: 34px; border-radius: 8px;
  border: 1px solid var(--s200); background: var(--white); cursor: pointer;
  color: var(--s600); font-size: 16px; align-items: center; justify-content: center;
}
.btn-back {
  color: var(--navy); text-decoration: none; font-weight: 600; font-size: 13px;
  white-space: nowrap; display: flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 50px; transition: all .2s;
  background: var(--s50); border: 1px solid var(--s200);
}
.btn-back:hover { border-color: var(--gold); color: var(--gold3); background: rgba(216,155,15,0.05); }
.back-arrow { font-size: 15px; }
.chat-peer { display: flex; align-items: center; gap: 12px; flex: 1; }

.peer-avatar-link { text-decoration: none; display: block; position: relative; }
.peer-avatar {
  width: 42px; height: 42px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: #fff; display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 17px; overflow: hidden; cursor: pointer;
  transition: transform 0.2s; flex-shrink: 0; border: 2px solid transparent;
}
.peer-avatar:hover { transform: scale(1.05); border-color: var(--gold); }
.avatar-online-dot {
  position: absolute; bottom: 1px; right: 1px; width: 12px; height: 12px;
  border-radius: 50%; background: #22c55e; border: 2.5px solid white;
  animation: pulse-online 2s infinite;
}

.peer-name-link { text-decoration: none; }
.peer-name { font-family: var(--fd); font-size: 15px; font-weight: 700; color: var(--navy); margin: 0; cursor: pointer; transition: color 0.15s; }
.peer-name-link:hover .peer-name { color: var(--gold); }

.status-text { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 500; }
.status-text.online { color: #22c55e; }
.status-text.offline { color: var(--s400); }
.pulse-dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: pulse-online 2s infinite; }

@keyframes pulse-online { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(0.85); } }

/* Property context */
.property-context {
  background: rgba(216,155,15,0.08); border: 1px solid rgba(216,155,15,0.25);
  border-radius: 50px; padding: 6px 14px; font-size: 12px; color: var(--gold3);
  max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  text-decoration: none; transition: all .2s; margin-left: auto;
}
.property-context:hover { background: rgba(216,155,15,0.14); border-color: var(--gold); }
.prop-city { font-size: 11px; color: var(--s500); }

/* Date separator */
.date-separator { display: flex; align-items: center; justify-content: center; padding: 16px 0 8px; }
.date-separator span {
  background: var(--white); border: 1px solid var(--s200); padding: 4px 16px;
  border-radius: 50px; font-size: 11.5px; font-weight: 600; color: var(--s500);
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}

/* Typing Indicator */
.typing-indicator { display: flex; align-items: center; gap: 10px; padding: 8px 24px 4px; }
.typing-avatar {
  width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, var(--gold), var(--gold3)); color: #fff;
  font-size: 12px; font-weight: 700; display: flex; align-items: center;
  justify-content: center; background-size: cover; background-position: center; overflow: hidden;
}
.typing-bubble {
  display: flex; align-items: center; gap: 4px; background: var(--white);
  border: 1px solid var(--s200); border-radius: 18px; padding: 10px 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.typing-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--s400); animation: typing-bounce 1.2s infinite; }
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing-bounce { 0%, 60%, 100% { transform: translateY(0); opacity: 0.5; } 30% { transform: translateY(-5px); opacity: 1; } }
.typing-label { font-size: 12px; color: var(--s400); font-style: italic; }
.typing-fade-enter-active, .typing-fade-leave-active { transition: all 0.25s ease; }
.typing-fade-enter-from, .typing-fade-leave-to { opacity: 0; transform: translateY(6px); }

/* Messages Area */
.messages-area {
  flex: 1; overflow-y: auto; padding: 16px 24px; display: flex;
  flex-direction: column; gap: 3px; background: var(--bg);
}
.messages-area::-webkit-scrollbar { width: 5px; }
.messages-area::-webkit-scrollbar-track { background: transparent; }
.messages-area::-webkit-scrollbar-thumb { background: var(--s300); border-radius: 3px; }
.messages-area::-webkit-scrollbar-thumb:hover { background: var(--s400); }

.loading-msg { text-align: center; color: var(--s500); font-size: 14px; margin-top: 60px; display: flex; flex-direction: column; align-items: center; gap: 12px; }
.loading-spinner { width: 32px; height: 32px; border: 3px solid var(--s200); border-top-color: var(--gold); border-radius: 50%; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.empty-msg { text-align: center; color: var(--s500); margin: auto; padding: 40px; }
.empty-chat-icon { font-size: 56px; margin-bottom: 16px; opacity: 0.4; }
.empty-msg h3 { font-size: 18px; font-weight: 700; color: var(--navy); margin: 0 0 8px; font-family: var(--fd); }
.empty-msg p { font-size: 14px; color: var(--s400); }

/* Message rows */
.msg-row { display: flex; align-items: flex-end; gap: 8px; animation: msg-appear 0.2s ease-out; }
@keyframes msg-appear { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.msg-row.sent { justify-content: flex-end; }
.msg-row.received { justify-content: flex-start; }

.msg-avatar-wrap { flex-shrink: 0; }
.msg-avatar-spacer { width: 30px; flex-shrink: 0; }
.my-avatar-wrap { order: 1; }

.msg-avatar {
  width: 30px; height: 30px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: #fff; display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 12px; overflow: hidden; flex-shrink: 0;
}

.msg-bubble-wrap { display: flex; flex-direction: column; max-width: 62%; }
.msg-bubble { padding: 10px 14px; border-radius: 18px; position: relative; transition: box-shadow 0.2s; }
.msg-bubble:hover { box-shadow: 0 2px 8px rgba(0,0,0,.06); }

.msg-row.sent .msg-bubble {
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  color: #fff; border-bottom-right-radius: 4px;
}
.msg-row.received .msg-bubble {
  background: var(--white); color: var(--s900);
  border: 1px solid var(--s200); border-bottom-left-radius: 4px;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.msg-text { font-size: 14px; line-height: 1.5; word-break: break-word; margin: 0; }

.msg-meta { display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 4px; }
.msg-time { font-size: 11px; opacity: .55; }
.msg-row.sent .msg-time { color: rgba(255,255,255,0.6); }
.msg-status { font-size: 12px; display: inline-flex; }
.status-sending { color: rgba(255,255,255,0.4); font-size: 8px; }
.status-sent { color: rgba(255,255,255,0.5); font-size: 11px; font-weight: 700; }
.status-seen { color: var(--gold2); font-size: 11px; font-weight: 700; }

.seen-label { font-size: 11px; color: var(--s400); text-align: right; padding: 2px 4px 0; font-weight: 500; }

/* Attachments */
.msg-attachment { margin-top: 6px; }
.attachment-image { max-width: 240px; max-height: 200px; border-radius: 10px; cursor: pointer; display: block; transition: transform 0.2s; }
.attachment-image:hover { transform: scale(1.02); }

/* File card */
.file-card {
  display: flex; align-items: center; gap: 10px;
  background: rgba(255,255,255,0.85); border: 1px solid rgba(0,0,0,0.08);
  border-radius: 10px; padding: 10px 12px; min-width: 220px; max-width: 300px;
}
.sent .file-card { background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.15); }
.file-card-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; color: #fff; }
.file-card-info { flex: 1; min-width: 0; }
.file-card-name { display: block; font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--s900); }
.sent .file-card-name { color: #fff; }
.file-card-type { display: block; font-size: 11px; color: var(--s400); margin-top: 2px; }
.sent .file-card-type { color: rgba(255,255,255,0.6); }
.file-card-download {
  flex-shrink: 0; width: 34px; height: 34px; border-radius: 8px;
  background: rgba(0,0,0,.06); display: flex; align-items: center;
  justify-content: center; color: var(--s700); transition: all 0.2s; text-decoration: none;
}
.file-card-download:hover { background: var(--gold); color: #fff; }
.sent .file-card-download { background: rgba(255,255,255,0.15); color: #fff; }
.sent .file-card-download:hover { background: var(--gold); }

/* Input */
.input-area {
  background: var(--white); border-top: 1px solid var(--s200);
  padding: 14px 24px; flex-shrink: 0;
}
.attachment-preview {
  display: flex; align-items: center; gap: 8px;
  background: rgba(216,155,15,0.08); border: 1px solid rgba(216,155,15,0.25);
  border-radius: 10px; padding: 8px 14px; font-size: 13px; margin-bottom: 10px;
}
.file-icon { font-size: 16px; }
.file-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; color: var(--s700); }
.file-size-label { font-size: 11px; color: var(--s400); flex-shrink: 0; }
.btn-remove-file { background: none; border: none; cursor: pointer; color: var(--s400); font-size: 16px; line-height: 1; padding: 2px 4px; border-radius: 4px; transition: all 0.2s; }
.btn-remove-file:hover { background: #fee2e2; color: #dc2626; }

.input-row { display: flex; align-items: flex-end; gap: 10px; }
.btn-attach {
  background: none; border: 1.5px solid var(--s200); border-radius: 12px;
  padding: 10px 12px; cursor: pointer; transition: all .2s; flex-shrink: 0;
  color: var(--s500); display: flex; align-items: center;
}
.btn-attach:hover { background: rgba(216,155,15,0.05); color: var(--gold); border-color: var(--gold); }

.msg-input {
  flex: 1; padding: 11px 16px; border: 1.5px solid var(--s200); border-radius: 14px;
  font-size: 14px; font-family: var(--fb); resize: none; outline: none;
  transition: all .2s; max-height: 120px; overflow-y: auto; background: var(--s50);
  color: var(--s900);
}
.msg-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(216,155,15,0.10); background: var(--white); }
.msg-input::placeholder { color: var(--s400); }

.btn-send {
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  border: none; border-radius: 12px; padding: 11px 16px; cursor: pointer;
  color: #fff; font-weight: 700; flex-shrink: 0; transition: all .2s;
  display: flex; align-items: center;
  box-shadow: 0 2px 8px rgba(11,28,57,0.20);
}
.btn-send:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(11,28,57,0.30); }
.btn-send:disabled { opacity: .35; cursor: not-allowed; }

/* ── Sidebar overlay (mobile) ── */
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 90; backdrop-filter: blur(2px); }

/* Responsive */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .chat-main { margin-left: 0; }
  .hamburger-btn { display: flex; }
  .chat-header { padding: 10px 16px; }
  .messages-area { padding: 12px 16px; }
  .input-area { padding: 10px 16px; }
  .msg-bubble-wrap { max-width: 80%; }
  .back-text { display: none; }
}
</style>
