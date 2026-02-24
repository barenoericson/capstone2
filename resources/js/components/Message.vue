<template>
  <div class="message-container">
    <!-- Header -->
    <div class="chat-header">
      <div class="header-left">
        <button @click="closeMessaging" class="btn-back">← Back</button>
        
        <div class="agent-profile">
          <div class="agent-avatar">
            {{ otherUser && otherUser.name ? otherUser.name.charAt(0).toUpperCase() : '?' }}
          </div>
          <div class="agent-info">
            <h3 class="agent-name">{{ otherUser && otherUser.name ? otherUser.name : 'Loading...' }}</h3>
            <div class="agent-meta">
              <span class="status-badge" :class="{ online: isOnline }">
                <span class="status-dot"></span>
                {{ isOnline ? 'Online' : 'Offline' }}
              </span>
            </div>
          </div>
        </div>
      </div>
      <button @click="closeMessaging" class="btn-close">✕</button>
    </div>

    <!-- Messages Area -->
    <div class="messages-area" ref="messagesContainer">
      <div v-if="messages.length === 0" class="empty-state">
        <p>💬 No messages yet. Start the conversation!</p>
      </div>

      <div v-for="message in messages" :key="message.id" :class="['message', message.is_sent_by_me ? 'sent' : 'received']">
        <div class="message-content">
          <p class="message-text">{{ message.message_content }}</p>
          <span class="message-time">{{ formatTime(message.created_at) }}</span>
          <span v-if="message.is_sent_by_me && message.is_read" class="read-receipt">✓✓</span>
        </div>
      </div>

      <div v-if="otherUserTyping" class="typing-indicator">
        <span>Agent is typing</span>
        <span class="dots"><span></span><span></span><span></span></span>
      </div>
    </div>

    <!-- Input Area -->
    <div class="input-wrapper">
      <textarea
        v-model="newMessage"
        @keydown.enter.ctrl="sendMessage"
        @input="handleTyping"
        placeholder="Type your message... (Ctrl+Enter to send)"
        class="message-input"
        rows="2"
      ></textarea>
      <button @click="sendMessage" class="btn-send" :disabled="!newMessage.trim() || sending">
        {{ sending ? '⏳' : '📤' }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-overlay">⏳ Loading...</div>
    
    <!-- Error -->
    <div v-if="error" class="error-overlay">
      ❌ {{ error }}
      <button @click="loadMessages" class="btn-retry">Retry</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Message',
  props: {
    propertyId: {
      type: [String, Number],
      required: true,
    },
    agentId: {
      type: [String, Number],
      required: true,
    },
  },
  data() {
    return {
      messages: [],
      newMessage: '',
      loading: false,
      sending: false,
      error: null,
      
      otherUser: {
        id: null,
        name: 'Agent',
        email: '',
      },
      
      currentUser: {
        id: null,
        name: '',
        role: '',
      },
      
      isOnline: true,
      otherUserTyping: false,
      pollInterval: null,
      typingTimeout: null,
    };
  },

  methods: {
    async loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.currentUser = {
          id: user.id,
          name: user.name,
          role: user.role,
        };
        console.log('✅ Current user loaded:', this.currentUser);
      } catch (error) {
        console.error('❌ Error loading user:', error);
      }
    },

    async loadMessages() {
      try {
        this.loading = true;
        this.error = null;
        const token = localStorage.getItem('auth_token');

        if (!token) {
          this.error = 'Authentication failed';
          return;
        }

        // ✅ FIX: Make sure we have all required data
        console.log('=== LOAD MESSAGES ===');
        console.log('Current User:', this.currentUser.id);
        console.log('Agent ID:', this.agentId);
        console.log('Property ID:', this.propertyId);

        const url = `${window.__API_URL__}/api/conversations/${this.agentId}/messages?property_id=${this.propertyId}`;
        console.log('URL:', url);

        const response = await fetch(url, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
        });

        console.log('Status:', response.status);
        const data = await response.json();
        console.log('Response:', data);

        if (!response.ok || !data.success) {
          throw new Error(data.message || `HTTP ${response.status}`);
        }

        // ✅ FIX: Map messages correctly
        this.messages = data.data.messages.map(msg => ({
          ...msg,
          is_sent_by_me: msg.sender_id === this.currentUser.id,
        }));

        // ✅ FIX: Get other user correctly
        if (data.data.otherUser) {
          this.otherUser = data.data.otherUser;
          console.log('✅ Other user:', this.otherUser);
        }

        console.log('✅ Messages loaded:', this.messages.length);

        this.$nextTick(() => {
          this.scrollToBottom();
          this.markMessagesAsRead();
        });
      } catch (error) {
        console.error('❌ Load error:', error);
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    async sendMessage() {
      if (!this.newMessage.trim() || this.sending) return;

      try {
        this.sending = true;
        const token = localStorage.getItem('auth_token');

        const response = await fetch(window.__API_URL__ + '/api/messages', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify({
            property_id: this.propertyId,
            recipient_id: this.agentId,
            message_content: this.newMessage,
          }),
        });

        const data = await response.json();

        if (data.success) {
          this.messages.push({
            ...data.data,
            is_sent_by_me: true,
          });

          this.newMessage = '';

          this.$nextTick(() => {
            this.scrollToBottom();
          });

          console.log('✅ Message sent');
        } else {
          this.error = data.message || 'Failed to send message';
        }
      } catch (error) {
        console.error('❌ Send error:', error);
        this.error = error.message;
      } finally {
        this.sending = false;
      }
    },

    handleTyping() {
      if (this.typingTimeout) clearTimeout(this.typingTimeout);
      this.typingTimeout = setTimeout(() => {}, 3000);
    },

    async markMessagesAsRead() {
      try {
        const unreadMessages = this.messages.filter(msg => !msg.is_read && !msg.is_sent_by_me);
        if (unreadMessages.length === 0) return;

        const token = localStorage.getItem('auth_token');
        for (const msg of unreadMessages) {
          await fetch(`${window.__API_URL__}/api/messages/${msg.id}/read`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}` },
          });
        }
      } catch (error) {
        console.error('⚠️ Error marking as read:', error);
      }
    },

    scrollToBottom() {
      const container = this.$refs.messagesContainer;
      if (container) {
        setTimeout(() => {
          container.scrollTop = container.scrollHeight;
        }, 0);
      }
    },

    formatTime(timestamp) {
      return new Date(timestamp).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
      });
    },

    closeMessaging() {
      this.$emit('close');
    },

    startPolling() {
      this.pollInterval = setInterval(() => {
        this.loadMessages();
      }, 2000);
    },

    stopPolling() {
      if (this.pollInterval) clearInterval(this.pollInterval);
    },
  },

  mounted() {
    console.log('📱 Message mounted with props:', { propertyId: this.propertyId, agentId: this.agentId });
    this.loadUserData();
    setTimeout(() => {
      this.loadMessages();
      this.startPolling();
    }, 100);
  },

  beforeUnmount() {
    this.stopPolling();
    if (this.typingTimeout) clearTimeout(this.typingTimeout);
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --primary: #667eea;
  --primary-light: #748ffc;
  --white: #fff;
  --gray-light: #f3f4f6;
  --gray-border: #e5e7eb;
  --gray-text: #6b7280;
  --dark: #111;
  --success: #10b981;
}

.message-container {
  display: flex;
  flex-direction: column;
  height: 600px;
  background: var(--white);
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  position: relative;
  overflow: hidden;
  font-family: 'Inter', sans-serif;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid var(--gray-border);
  background: var(--white);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}

.btn-back {
  background: none;
  border: none;
  font-size: 14px;
  cursor: pointer;
  color: var(--gray-text);
  padding: 6px;
  border-radius: 6px;
  transition: all 0.2s;
}

.btn-back:hover {
  background: var(--gray-light);
}

.agent-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}

.agent-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  flex-shrink: 0;
}

.agent-info {
  flex: 1;
  min-width: 0;
}

.agent-name {
  font-size: 13px;
  font-weight: 700;
  color: var(--dark);
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.agent-meta {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  margin-top: 2px;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 600;
  color: var(--gray-text);
}

.status-badge.online {
  color: var(--success);
}

.status-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: currentColor;
}

.btn-close {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: var(--gray-text);
  padding: 0;
  transition: color 0.2s;
}

.btn-close:hover {
  color: var(--dark);
}

.messages-area {
  flex: 1;
  overflow-y: auto;
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: var(--gray-light);
}

.empty-state {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: var(--gray-text);
  font-size: 13px;
  text-align: center;
}

.message {
  display: flex;
  margin-bottom: 4px;
}

.message.sent {
  justify-content: flex-end;
}

.message.received {
  justify-content: flex-start;
}

.message-content {
  max-width: 70%;
  padding: 10px 14px;
  border-radius: 10px;
  word-wrap: break-word;
}

.message.sent .message-content {
  background: var(--primary);
  color: var(--white);
  border-radius: 10px 2px 10px 10px;
}

.message.received .message-content {
  background: var(--white);
  color: var(--dark);
  border: 1px solid var(--gray-border);
  border-radius: 2px 10px 10px 10px;
}

.message-text {
  font-size: 13px;
  line-height: 1.4;
  margin: 0 0 4px 0;
}

.message-time {
  font-size: 10px;
  opacity: 0.7;
}

.read-receipt {
  font-size: 10px;
  color: var(--success);
  font-weight: 700;
  margin-left: 4px;
}

.typing-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--gray-text);
  padding: 10px 14px;
  background: var(--white);
  border-radius: 10px;
  width: fit-content;
  border: 1px solid var(--gray-border);
}

.dots {
  display: flex;
  gap: 3px;
}

.dots span {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--gray-text);
  animation: bounce 1.4s infinite;
}

.dots span:nth-child(2) {
  animation-delay: 0.2s;
}

.dots span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes bounce {
  0%, 80%, 100% {
    transform: translateY(0);
    opacity: 0.5;
  }
  40% {
    transform: translateY(-6px);
    opacity: 1;
  }
}

.input-wrapper {
  display: flex;
  gap: 10px;
  padding: 12px 16px;
  border-top: 1px solid var(--gray-border);
  background: var(--white);
}

.message-input {
  flex: 1;
  padding: 10px;
  border: 1px solid var(--gray-border);
  border-radius: 8px;
  font-family: inherit;
  font-size: 13px;
  resize: none;
  transition: all 0.2s;
}

.message-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.btn-send {
  padding: 10px 16px;
  background: var(--primary);
  color: var(--white);
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
  font-size: 14px;
}

.btn-send:hover:not(:disabled) {
  background: var(--primary-light);
  transform: translateY(-1px);
}

.btn-send:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  color: var(--gray-text);
  border-radius: 12px;
}

.error-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  font-size: 13px;
  color: #dc2626;
  border-radius: 12px;
  padding: 20px;
}

.btn-retry {
  padding: 8px 16px;
  background: #dc2626;
  color: var(--white);
  border: none;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  font-size: 12px;
}

.btn-retry:hover {
  background: #b91c1c;
}

.messages-area::-webkit-scrollbar {
  width: 6px;
}

.messages-area::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 3px;
}

.messages-area::-webkit-scrollbar-thumb:hover {
  background: #999;
}

@media (max-width: 768px) {
  .message-container {
    height: 500px;
  }

  .message-content {
    max-width: 80%;
  }
}
</style>