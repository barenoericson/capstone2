<template>
  <div class="buyer-message-wrapper">
    <!-- Top Navigation -->
    <nav class="top-nav">
      <div class="nav-container">
        <button @click="goBack" class="btn-back">
          ← Back to Messages
        </button>
        <router-link to="/" class="nav-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </router-link>
        <div style="width: 120px;"></div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <Message
        :property-id="propertyId"
        :agent-id="agentId"
        @close="goBack"
      />
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
import Message from '../../components/Message.vue';

export default {
  name: 'BuyerMessageConversation',
  components: {
    Message,
  },
  data() {
    return {
      currentUserId: null,
      agentId: null,
      propertyId: null,
      errorMessage: '',
    };
  },

  methods: {
    goBack() {
      this.$router.push('/buyer/messages');
    },

    showError(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 3000);
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/');
      return;
    }

    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.currentUserId = user.id;
    this.agentId = this.$route.params.agentId;
    this.propertyId = this.$route.query.property_id || null;

    if (!this.agentId) {
      this.showError('Invalid agent ID');
      this.goBack();
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
  --palace-gold: #FFD700;
  --palace-gold-dark: #DAB600;
  --light-gray: #e0e0e0;
  --font-display: 'Poppins', sans-serif;
  --font-body: 'Inter', sans-serif;
}

.buyer-message-wrapper {
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
  padding: 12px 0;
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

.btn-back {
  padding: 8px 16px;
  background: white;
  border: 1px solid var(--light-gray);
  color: var(--smoky-black);
  border-radius: 6px;
  font-weight: 600;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-back:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

.nav-logo {
  font-size: 20px;
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

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.main-content {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px 32px;
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
    padding: 12px 16px;
  }

  .nav-logo {
    font-size: 18px;
  }
}

@media (max-width: 480px) {
  .main-content {
    padding: 8px 12px;
  }

  .nav-logo {
    font-size: 16px;
  }

  .btn-back {
    padding: 6px 12px;
    font-size: 11px;
  }
}
</style>