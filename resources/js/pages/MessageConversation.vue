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
          <router-link to="/agent/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/agent/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
        </template>
        <template v-else>
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
        </template>
        <router-link to="/profile" class="nav-item">
          <span class="nav-icon">👤</span>
          <span class="nav-label">Profile</span>
        </router-link>
      </nav>
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg" :style="myPhotoUrl ? `background-image:url(${myPhotoUrl});background-size:cover;background-position:center;` : ''">
            <span v-if="!myPhotoUrl">{{ myName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="user-info">
            <p class="user-name-card">{{ myName }}</p>
            <p class="user-role-card">{{ userRole }}</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Chat Area -->
    <main class="chat-main">
      <!-- Chat Header -->
      <div class="chat-header">
        <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
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
      if (this.userRole === 'agent') return `/agent/buyer-profile/${this.otherUser.id}`;
      if (this.otherUser.role === 'agent') return `/agent-profile/${this.otherUser.id}`;
      return null;
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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap');
* { margin: 0; padding: 0; box-sizing: border-box; }
:root { --gold: #FFD700; --gold-dark: #DAB600; --gold-light: #fdf5d0; --black: #100c08; --smoke: #f5f5f5; --gray: #e0e0e0; --gray-light: #f0f0f0; }

.chat-wrapper { display: flex; height: 100vh; font-family: 'Inter', sans-serif; background: var(--smoke); }

/* ── Sidebar ── */
.sidebar { width: 260px; background: white; border-right: 1px solid var(--gray); display: flex; flex-direction: column; position: fixed; left: 0; top: 0; height: 100vh; overflow-y: auto; box-shadow: 2px 0 8px rgba(0,0,0,.07); z-index: 100; }
.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--gray); }
.sidebar-logo { font-size: 22px; font-weight: 800; font-family: 'Poppins', sans-serif; display: flex; }
.logo-realty { color: var(--black); } .logo-ph { color: var(--gold); margin-left: 2px; }
.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 11px 16px; margin: 0 8px; border-radius: 8px; color: var(--black); text-decoration: none; font-size: 14px; font-weight: 500; transition: all .2s; }
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black); font-weight: 700; }
.nav-icon { font-size: 17px; min-width: 22px; text-align: center; }
.sidebar-footer { padding: 14px; border-top: 1px solid var(--gray); }
.user-card { background: var(--smoke); border-radius: 8px; padding: 10px; display: flex; align-items: center; gap: 10px; }
.user-avatar-lg { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; flex-shrink: 0; overflow: hidden; }
.user-name-card { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; }
.user-role-card { font-size: 11px; color: #999; margin: 2px 0 0 0; text-transform: capitalize; }

/* ── Chat Main ── */
.chat-main { margin-left: 260px; flex: 1; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

/* Header */
.chat-header { background: white; border-bottom: 1px solid var(--gray); padding: 12px 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.06); flex-shrink: 0; }
.btn-back { color: var(--black); text-decoration: none; font-weight: 600; font-size: 14px; white-space: nowrap; display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; transition: all .2s; background: var(--smoke); }
.btn-back:hover { background: var(--gold-light); color: var(--gold-dark); }
.back-arrow { font-size: 16px; }
.chat-peer { display: flex; align-items: center; gap: 12px; flex: 1; }

.peer-avatar-link { text-decoration: none; display: block; position: relative; }
.peer-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; overflow: hidden; cursor: pointer; transition: transform 0.15s; flex-shrink: 0; border: 2px solid transparent; }
.peer-avatar:hover { transform: scale(1.05); border-color: var(--gold); }
.avatar-online-dot { position: absolute; bottom: 1px; right: 1px; width: 13px; height: 13px; border-radius: 50%; background: #22c55e; border: 2.5px solid white; animation: pulse-online 2s infinite; }

.peer-name-link { text-decoration: none; }
.peer-name { font-size: 16px; font-weight: 700; color: var(--black); margin: 0; cursor: pointer; transition: color 0.15s; }
.peer-name-link:hover .peer-name { color: var(--gold); }

.status-text { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 500; }
.status-text.online { color: #22c55e; }
.status-text.offline { color: #9ca3af; }
.pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; animation: pulse-online 2s infinite; }

@keyframes pulse-online { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(0.85); } }

/* Date separator */
.date-separator { display: flex; align-items: center; justify-content: center; padding: 16px 0 8px; }
.date-separator span { background: white; border: 1px solid var(--gray); padding: 4px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #888; box-shadow: 0 1px 3px rgba(0,0,0,.04); }

/* Typing Indicator */
.typing-indicator { display: flex; align-items: center; gap: 10px; padding: 8px 24px 4px; }
.typing-avatar { width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; background: linear-gradient(135deg, #FFD700, #DAB600); color: #100c08; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center; overflow: hidden; }
.typing-bubble { display: flex; align-items: center; gap: 4px; background: white; border: 1px solid #e8e8e8; border-radius: 18px; padding: 10px 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.typing-dot { width: 7px; height: 7px; border-radius: 50%; background: #aaa; animation: typing-bounce 1.2s infinite; }
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing-bounce { 0%, 60%, 100% { transform: translateY(0); opacity: 0.5; } 30% { transform: translateY(-5px); opacity: 1; } }
.typing-label { font-size: 12px; color: #999; font-style: italic; }
.typing-fade-enter-active, .typing-fade-leave-active { transition: all 0.25s ease; }
.typing-fade-enter-from, .typing-fade-leave-to { opacity: 0; transform: translateY(6px); }

.property-context { background: var(--gold-light); border: 1px solid var(--gold); border-radius: 8px; padding: 6px 14px; font-size: 12px; color: #7a5c00; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-decoration: none; transition: background .15s; }
.property-context:hover { background: #fdf0b5; }
.prop-city { font-size: 11px; color: #a07000; }

/* Messages Area */
.messages-area { flex: 1; overflow-y: auto; padding: 16px 24px; display: flex; flex-direction: column; gap: 3px; background: linear-gradient(180deg, #fafafa 0%, #f5f5f5 100%); }
.messages-area::-webkit-scrollbar { width: 6px; }
.messages-area::-webkit-scrollbar-track { background: transparent; }
.messages-area::-webkit-scrollbar-thumb { background: #d0d0d0; border-radius: 3px; }
.messages-area::-webkit-scrollbar-thumb:hover { background: #bbb; }

.loading-msg { text-align: center; color: #999; font-size: 14px; margin-top: 60px; display: flex; flex-direction: column; align-items: center; gap: 12px; }
.loading-spinner { width: 32px; height: 32px; border: 3px solid var(--gray); border-top-color: var(--gold); border-radius: 50%; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.empty-msg { text-align: center; color: #999; margin: auto; padding: 40px; }
.empty-chat-icon { font-size: 56px; margin-bottom: 16px; opacity: 0.5; }
.empty-msg h3 { font-size: 18px; font-weight: 700; color: var(--black); margin: 0 0 8px; }
.empty-msg p { font-size: 14px; color: #888; }

/* Message rows */
.msg-row { display: flex; align-items: flex-end; gap: 8px; animation: msg-appear 0.2s ease-out; }
@keyframes msg-appear { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.msg-row.sent { justify-content: flex-end; }
.msg-row.received { justify-content: flex-start; }

.msg-avatar-wrap { flex-shrink: 0; }
.msg-avatar-spacer { width: 30px; flex-shrink: 0; }
.my-avatar-wrap { order: 1; }

.msg-avatar { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; overflow: hidden; flex-shrink: 0; }

.msg-bubble-wrap { display: flex; flex-direction: column; max-width: 62%; }
.msg-bubble { padding: 10px 14px; border-radius: 18px; position: relative; transition: box-shadow 0.2s; }
.msg-bubble:hover { box-shadow: 0 2px 8px rgba(0,0,0,.08); }

.msg-row.sent .msg-bubble { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black); border-bottom-right-radius: 4px; }
.msg-row.received .msg-bubble { background: white; color: var(--black); border: 1px solid var(--gray-light); border-bottom-left-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
.msg-text { font-size: 14px; line-height: 1.5; word-break: break-word; margin: 0; }

.msg-meta { display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 4px; }
.msg-time { font-size: 11px; opacity: .55; }
.msg-status { font-size: 12px; display: inline-flex; }
.status-sending { color: #bbb; font-size: 8px; }
.status-sent { color: rgba(0,0,0,0.35); font-size: 11px; font-weight: 700; }
.status-seen { color: #2563eb; font-size: 11px; font-weight: 700; }

.seen-label { font-size: 11px; color: #888; text-align: right; padding: 2px 4px 0; font-weight: 500; }

/* Attachments */
.msg-attachment { margin-top: 6px; }
.attachment-image { max-width: 240px; max-height: 200px; border-radius: 10px; cursor: pointer; display: block; transition: transform 0.2s; }
.attachment-image:hover { transform: scale(1.02); }

/* File card for non-image attachments */
.file-card { display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.85); border: 1px solid rgba(0,0,0,0.08); border-radius: 10px; padding: 10px 12px; min-width: 220px; max-width: 300px; }
.sent .file-card { background: rgba(255,255,255,0.25); border-color: rgba(0,0,0,0.06); }
.file-card-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; color: #fff; }
.file-card-info { flex: 1; min-width: 0; }
.file-card-name { display: block; font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--black); }
.sent .file-card-name { color: var(--white); }
.file-card-type { display: block; font-size: 11px; color: #888; margin-top: 2px; }
.sent .file-card-type { color: rgba(255,255,255,0.7); }
.file-card-download { flex-shrink: 0; width: 34px; height: 34px; border-radius: 8px; background: rgba(0,0,0,.06); display: flex; align-items: center; justify-content: center; color: var(--black); transition: all 0.2s; text-decoration: none; }
.file-card-download:hover { background: var(--gold); color: var(--black); }
.sent .file-card-download { background: rgba(255,255,255,0.2); color: var(--white); }
.sent .file-card-download:hover { background: var(--white); color: var(--black); }

/* Input */
.input-area { background: white; border-top: 1px solid var(--gray); padding: 14px 24px; flex-shrink: 0; }
.attachment-preview { display: flex; align-items: center; gap: 8px; background: var(--gold-light); border: 1px solid var(--gold); border-radius: 8px; padding: 8px 14px; font-size: 13px; margin-bottom: 10px; }
.file-icon { font-size: 16px; }
.file-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
.file-size-label { font-size: 11px; color: #888; flex-shrink: 0; }
.btn-remove-file { background: none; border: none; cursor: pointer; color: #999; font-size: 16px; line-height: 1; padding: 2px 4px; border-radius: 4px; transition: all 0.2s; }
.btn-remove-file:hover { background: #fee; color: #d33; }

.input-row { display: flex; align-items: flex-end; gap: 10px; }
.btn-attach { background: none; border: 1px solid var(--gray); border-radius: 10px; padding: 10px 12px; cursor: pointer; transition: all .2s; flex-shrink: 0; color: #777; display: flex; align-items: center; }
.btn-attach:hover { background: var(--smoke); color: var(--gold); border-color: var(--gold); }

.msg-input { flex: 1; padding: 11px 16px; border: 1px solid var(--gray); border-radius: 12px; font-size: 14px; font-family: 'Inter', sans-serif; resize: none; outline: none; transition: all .2s; max-height: 120px; overflow-y: auto; background: var(--smoke); }
.msg-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(230,174,13,.12); background: white; }

.btn-send { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); border: none; border-radius: 12px; padding: 11px 16px; cursor: pointer; color: var(--black); font-weight: 700; flex-shrink: 0; transition: all .2s; display: flex; align-items: center; }
.btn-send:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(230,174,13,.35); }
.btn-send:disabled { opacity: .35; cursor: not-allowed; }

/* Responsive */
@media (max-width: 768px) {
  .sidebar { display: none; }
  .chat-main { margin-left: 0; }
  .chat-header { padding: 10px 16px; }
  .messages-area { padding: 12px 16px; }
  .input-area { padding: 10px 16px; }
  .msg-bubble-wrap { max-width: 80%; }
  .back-text { display: none; }
}
</style>
