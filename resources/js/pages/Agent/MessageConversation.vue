<template>
  <div class="agent-message-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
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
        </router-link>

        <router-link to="/agent/properties" class="nav-item">
          <span class="nav-icon">🏠</span>
          <span class="nav-label">Properties</span>
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
      <Message
        :property-id="propertyId"
        :agent-id="currentUserId"
        :buyer-id="buyerId"
        @close="goBack"
      />
    </main>
  </div>
</template>

<script>
import Message from '../../components/Message.vue';

export default {
  name: 'AgentMessageConversation',
  components: {
    Message,
  },
  data() {
    return {
      currentUserId: null,
      buyerId: null,
      propertyId: null,
    };
  },

  methods: {
    goBack() {
      this.$router.push('/agent/messages');
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

    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.currentUserId = user.id;
    this.buyerId = this.$route.params.buyerId;
    this.propertyId = this.$route.query.property_id || null;
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

.agent-message-wrapper {
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
  padding: 20px;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */

@media (max-width: 768px) {
  .sidebar {
    width: 80px;
  }

  .main-content {
    margin-left: 80px;
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