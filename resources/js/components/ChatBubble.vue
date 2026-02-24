<template>
  <div class="chatbot-wrapper">
    <!-- Floating Bubble Button -->
    <button v-if="!isOpen" class="chat-bubble-btn" @click="openChat">
      <img src="/images/buddy-avatar.jpg" alt="RealtyLinkPH Buddy" class="bubble-avatar" />
      <span v-if="hasUnread" class="bubble-badge"></span>
    </button>

    <!-- Chat Window -->
    <transition name="chat-slide">
      <div v-if="isOpen" class="chat-window">
        <!-- Header -->
        <div class="chat-header">
          <div class="chat-header-left">
            <img src="/images/buddy-avatar.jpg" alt="Buddy" class="header-avatar" />
            <div class="header-info">
              <h3 class="header-name">
                <span class="hdr-realty">RealtyLink</span><span class="hdr-ph">PH</span> Buddy
              </h3>
              <span class="header-status">
                <span class="status-dot"></span> Online
              </span>
            </div>
          </div>
          <div class="chat-header-actions">
            <button class="hdr-btn" @click="confirmClear" title="Clear chat">
              🗑️
            </button>
            <button class="hdr-btn hdr-close" @click="closeChat" title="Close">
              ✕
            </button>
          </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" ref="messagesContainer">
          <!-- Welcome message if no history -->
          <div v-if="messages.length === 0 && !loading" class="welcome-message">
            <img src="/images/buddy-avatar.jpg" alt="Buddy" class="welcome-avatar" />
            <h4>Hi! I'm RealtyLinkPH Buddy</h4>
            <p>Your AI assistant for everything about RealtyLinkPH and Philippine real estate. Ask me anything!</p>
            <div class="quick-prompts">
              <button class="quick-prompt" @click="sendQuickPrompt('What is RealtyLinkPH?')">What is RealtyLinkPH?</button>
              <button class="quick-prompt" @click="sendQuickPrompt('How do I become an agent?')">How to become an agent?</button>
              <button class="quick-prompt" @click="sendQuickPrompt('Best investment tips in PH real estate?')">PH real estate tips</button>
            </div>
          </div>

          <!-- Loading history -->
          <div v-if="loading" class="loading-history">
            <div class="typing-dots">
              <span></span><span></span><span></span>
            </div>
            <p>Loading conversation...</p>
          </div>

          <!-- Message list -->
          <template v-for="(msg, i) in messages" :key="msg.id || i">
            <!-- Buddy message -->
            <div v-if="msg.role === 'assistant'" class="msg-row msg-buddy">
              <img src="/images/buddy-avatar.jpg" alt="Buddy" class="msg-avatar" />
              <div class="msg-bubble buddy-bubble">
                <div class="msg-text" v-html="formatMessage(msg.content)"></div>
              </div>
            </div>

            <!-- User message -->
            <div v-else class="msg-row msg-user">
              <div class="msg-bubble user-bubble">
                <div class="msg-text">{{ msg.content }}</div>
              </div>
            </div>
          </template>

          <!-- Typing indicator -->
          <div v-if="isTyping" class="msg-row msg-buddy">
            <img src="/images/buddy-avatar.jpg" alt="Buddy" class="msg-avatar" />
            <div class="msg-bubble buddy-bubble typing-bubble">
              <div class="typing-dots">
                <span></span><span></span><span></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Input Bar -->
        <div class="chat-input-bar">
          <input
            v-model="userInput"
            type="text"
            class="chat-input"
            placeholder="Ask RealtyLinkPH Buddy..."
            @keydown.enter="sendMessage"
            :disabled="isTyping"
            maxlength="1000"
            ref="chatInput"
          />
          <button class="send-btn" @click="sendMessage" :disabled="!userInput.trim() || isTyping">
            ➤
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'ChatBubble',

  data() {
    return {
      isOpen: false,
      messages: [],
      userInput: '',
      isTyping: false,
      loading: false,
      hasUnread: false,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      token: localStorage.getItem('auth_token') || '',
    };
  },

  methods: {
    openChat() {
      this.isOpen = true;
      this.hasUnread = false;
      if (this.messages.length === 0) {
        this.loadHistory();
      }
      this.$nextTick(() => {
        this.scrollToBottom();
        if (this.$refs.chatInput) this.$refs.chatInput.focus();
      });
    },

    closeChat() {
      this.isOpen = false;
    },

    async loadHistory() {
      this.loading = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/chatbot/history`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
            Accept: 'application/json',
          },
        });
        const data = await res.json();
        if (data.success) {
          this.messages = data.messages;
          this.$nextTick(() => this.scrollToBottom());
        }
      } catch (e) {
        // Silently fail — user can still chat
      } finally {
        this.loading = false;
      }
    },

    async sendMessage() {
      const text = this.userInput.trim();
      if (!text || this.isTyping) return;

      // Add user message to UI immediately
      this.messages.push({
        id: Date.now(),
        role: 'user',
        content: text,
      });
      this.userInput = '';
      this.isTyping = true;
      this.$nextTick(() => this.scrollToBottom());

      try {
        const res = await fetch(`${this.apiUrl}/api/chatbot/message`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${this.token}`,
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },
          body: JSON.stringify({ message: text }),
        });

        const data = await res.json();

        if (data.success || data.reply) {
          this.messages.push({
            id: data.message_id || Date.now() + 1,
            role: 'assistant',
            content: data.reply,
          });
        } else {
          this.messages.push({
            id: Date.now() + 1,
            role: 'assistant',
            content: 'Sorry, I had trouble processing that. Please try again.',
          });
        }
      } catch (e) {
        this.messages.push({
          id: Date.now() + 1,
          role: 'assistant',
          content: 'I seem to be offline right now. Please check your connection and try again.',
        });
      } finally {
        this.isTyping = false;
        this.$nextTick(() => {
          this.scrollToBottom();
          if (this.$refs.chatInput) this.$refs.chatInput.focus();
        });
      }
    },

    sendQuickPrompt(text) {
      this.userInput = text;
      this.sendMessage();
    },

    async confirmClear() {
      if (!confirm('Clear all chat history with RealtyLinkPH Buddy?')) return;

      try {
        await fetch(`${this.apiUrl}/api/chatbot/history`, {
          method: 'DELETE',
          headers: {
            Authorization: `Bearer ${this.token}`,
            Accept: 'application/json',
          },
        });
        this.messages = [];
      } catch (e) {
        // Silently fail
      }
    },

    scrollToBottom() {
      const container = this.$refs.messagesContainer;
      if (container) {
        container.scrollTop = container.scrollHeight;
      }
    },

    formatMessage(text) {
      if (!text) return '';
      // Convert **bold** to <strong>
      let html = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
      // Convert *italic* to <em>
      html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
      // Convert newlines to <br>
      html = html.replace(/\n/g, '<br>');
      return html;
    },
  },
};
</script>

<style scoped>
/* ── Floating Bubble Button ────────────────────────── */
.chat-bubble-btn {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 1100;
  width: 64px;
  height: 64px;
  border-radius: 50%;
  border: 3px solid #e6ae0d;
  background: #fff;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  transition: all 0.3s;
  padding: 0;
  overflow: hidden;
}
.chat-bubble-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 28px rgba(230, 174, 13, 0.4);
}
.bubble-avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}
.bubble-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  width: 14px;
  height: 14px;
  background: #dc2626;
  border-radius: 50%;
  border: 2px solid #fff;
}

/* ── Chat Window ───────────────────────────────────── */
.chat-window {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 1100;
  width: 380px;
  height: 540px;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

/* Slide animation */
.chat-slide-enter-active,
.chat-slide-leave-active {
  transition: all 0.3s ease;
}
.chat-slide-enter-from,
.chat-slide-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

/* ── Header ────────────────────────────────────────── */
.chat-header {
  background: #100c08;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}
.chat-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.header-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 2px solid #e6ae0d;
  object-fit: cover;
}
.header-info { display: flex; flex-direction: column; }
.header-name {
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}
.hdr-realty { color: #fff; }
.hdr-ph { color: #e6ae0d; }
.header-status {
  font-size: 11px;
  color: #aaa;
  display: flex;
  align-items: center;
  gap: 4px;
}
.status-dot {
  width: 7px;
  height: 7px;
  background: #22c55e;
  border-radius: 50%;
  display: inline-block;
}
.chat-header-actions {
  display: flex;
  gap: 4px;
}
.hdr-btn {
  background: none;
  border: none;
  color: #aaa;
  font-size: 16px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 8px;
  transition: all 0.2s;
}
.hdr-btn:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }
.hdr-close { font-size: 18px; font-weight: 700; }

/* ── Messages Area ─────────────────────────────────── */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: #fafafa;
}
.chat-messages::-webkit-scrollbar { width: 5px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

/* Welcome */
.welcome-message {
  text-align: center;
  padding: 20px 10px;
}
.welcome-avatar {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  border: 3px solid #e6ae0d;
  margin-bottom: 12px;
  object-fit: cover;
}
.welcome-message h4 {
  font-size: 16px;
  font-weight: 700;
  color: #100c08;
  margin: 0 0 6px;
}
.welcome-message p {
  font-size: 13px;
  color: #666;
  margin: 0 0 16px;
  line-height: 1.5;
}
.quick-prompts {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.quick-prompt {
  background: #fff;
  border: 1.5px solid #e6ae0d;
  border-radius: 12px;
  padding: 10px 14px;
  font-size: 13px;
  font-weight: 600;
  color: #100c08;
  cursor: pointer;
  text-align: left;
  transition: all 0.2s;
}
.quick-prompt:hover {
  background: #fef9e7;
  border-color: #d4a00a;
}

/* Loading */
.loading-history {
  text-align: center;
  padding: 24px;
  color: #999;
}
.loading-history p { font-size: 13px; margin: 8px 0 0; }

/* Message rows */
.msg-row { display: flex; align-items: flex-end; gap: 8px; }
.msg-user { justify-content: flex-end; }
.msg-buddy { justify-content: flex-start; }

.msg-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.msg-bubble {
  max-width: 75%;
  padding: 10px 14px;
  border-radius: 16px;
  font-size: 13.5px;
  line-height: 1.5;
  word-wrap: break-word;
}
.buddy-bubble {
  background: #fff;
  color: #100c08;
  border: 1px solid #e8e8e8;
  border-bottom-left-radius: 4px;
}
.user-bubble {
  background: #e6ae0d;
  color: #100c08;
  border-bottom-right-radius: 4px;
}
.msg-text { white-space: pre-wrap; }
.msg-text :deep(strong) { font-weight: 700; }
.msg-text :deep(em) { font-style: italic; }

/* Typing indicator */
.typing-bubble { padding: 12px 18px; }
.typing-dots {
  display: flex;
  gap: 5px;
  align-items: center;
}
.typing-dots span {
  width: 8px;
  height: 8px;
  background: #b8860b;
  border-radius: 50%;
  animation: dotPulse 1.4s infinite ease-in-out;
}
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes dotPulse {
  0%, 80%, 100% { opacity: 0.3; transform: scale(0.8); }
  40% { opacity: 1; transform: scale(1); }
}

/* ── Input Bar ─────────────────────────────────────── */
.chat-input-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 14px;
  border-top: 1px solid #e5e7eb;
  background: #fff;
  flex-shrink: 0;
}
.chat-input {
  flex: 1;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 10px 14px;
  font-size: 13.5px;
  outline: none;
  font-family: inherit;
  transition: border-color 0.2s;
}
.chat-input:focus { border-color: #e6ae0d; }
.chat-input::placeholder { color: #bbb; }

.send-btn {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: #e6ae0d;
  color: #100c08;
  border: none;
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
}
.send-btn:hover:not(:disabled) {
  background: #d4a00a;
  transform: scale(1.05);
}
.send-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* ── Responsive ────────────────────────────────────── */
@media (max-width: 480px) {
  .chat-window {
    width: calc(100vw - 16px);
    height: calc(100vh - 80px);
    bottom: 8px;
    right: 8px;
    border-radius: 16px;
  }
  .chat-bubble-btn {
    bottom: 16px;
    right: 16px;
    width: 56px;
    height: 56px;
  }
}
</style>
