<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>

        <div v-if="userRole === 'agent'" class="nav-section">
          <h3 class="section-title">Management</h3>
          <router-link to="/agent/dashboard" class="nav-item">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Overview</span>
          </router-link>
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
          <router-link to="/agent/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item active">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">Settings</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profile</span>
          </router-link>
        </div>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ userName }}</p>
            <p class="user-role-card">{{ capitalizeRole(userRole) }}</p>
          </div>
          <button @click="toggleUserMenu" class="btn-options">⋮</button>

          <div v-if="showUserMenu" class="user-dropdown">
            <router-link to="/profile" class="dropdown-item">👤 My Profile</router-link>
            <a href="#" @click.prevent="logout" class="dropdown-item logout">🚪 Logout</a>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Top Navbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">📅 Viewings & Appointments</h1>
          </div>
          <div class="topbar-right">
            <button class="btn-icon">🔔</button>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <div class="page-wrapper">
        <div class="empty-container">
          <div class="empty-icon">📅</div>
          <h2>Viewings Coming Soon</h2>
          <p>Viewing management features will be available in the next phase.</p>
          <p style="color: #999; font-size: 12px;">
            You'll be able to manage property viewings, set availability, and approve buyer requests.
          </p>
          <router-link to="/agent/dashboard" class="btn-back">
            ← Back to Dashboard
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'ViewingsPage',
  data() {
    return {
      userName: '',
      userRole: '',
      showUserMenu: false,
    };
  },

  methods: {
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },

    capitalizeRole(role) {
      return role.charAt(0).toUpperCase() + role.slice(1);
    },

    loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName = user.name || 'Agent';
        this.userRole = user.role || 'agent';
      } catch (error) {
        console.error('Error loading user data:', error);
      }
    },

    async logout() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(window.__API_URL__ + '/api/auth/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/login');
      } catch (error) {
        console.error('Logout error:', error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/login');
      }
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/login');
      return;
    }
    this.loadUserData();
  },

  beforeUnmount() {
    this.showUserMenu = false;
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

.dashboard-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
}

.sidebar {
  width: 280px;
  background: white;
  border-right: 1px solid var(--light-gray);
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.08);
  z-index: 1000;
}

.sidebar-header {
  padding: 24px 16px;
  border-bottom: 1px solid var(--light-gray);
}

.sidebar-logo {
  font-size: 24px;
  font-weight: 800;
  margin: 0;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
  display: flex;
  align-items: center;
}

.logo-realty {
  color: var(--smoky-black);
}

.logo-ph {
  color: var(--palace-gold);
  margin-left: 2px;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.nav-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #999;
  padding: 8px 16px;
  margin: 0;
  letter-spacing: 1.2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  margin: 0 8px;
  color: var(--smoky-black);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 14px;
  border: none;
  background: none;
  cursor: pointer;
  width: calc(100% - 16px);
  text-align: left;
}

.nav-item:hover {
  background: var(--white-smoke);
  color: var(--palace-gold);
}

.nav-item.active {
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  font-weight: 600;
}

.nav-icon {
  font-size: 18px;
  min-width: 24px;
  text-align: center;
}

.nav-label {
  flex: 1;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid var(--light-gray);
}

.user-card {
  background: var(--white-smoke);
  border-radius: 8px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
}

.user-avatar-lg {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  flex-shrink: 0;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name-card {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-role-card {
  font-size: 11px;
  color: #999;
  margin: 4px 0 0 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-options {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 4px 8px;
  transition: color 0.3s;
  color: #999;
}

.btn-options:hover {
  color: var(--palace-gold);
}

.user-dropdown {
  position: absolute;
  bottom: 100%;
  right: 0;
  background: white;
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 180px;
  margin-bottom: 8px;
  overflow: hidden;
  z-index: 1001;
}

.dropdown-item {
  display: block;
  padding: 12px 16px;
  color: var(--smoky-black);
  text-decoration: none;
  font-size: 14px;
  transition: all 0.3s;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.dropdown-item:hover {
  background: var(--white-smoke);
  color: var(--palace-gold);
}

.dropdown-item.logout {
  color: #d32f2f;
  border-top: 1px solid var(--light-gray);
}

.dropdown-item.logout:hover {
  background: #ffebee;
}

.main-content {
  flex: 1;
  margin-left: 280px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.topbar {
  background: white;
  border-bottom: 2px solid var(--light-gray);
  padding: 16px 32px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.topbar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 30px;
  flex: 1;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  margin: 0;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
  color: var(--smoky-black);
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.btn-icon {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 8px;
  transition: transform 0.3s;
  position: relative;
  color: var(--smoky-black);
}

.btn-icon:hover {
  transform: scale(1.1);
  color: var(--palace-gold);
}

.page-wrapper {
  flex: 1;
  padding: 32px;
  overflow-y: auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-container {
  background: white;
  border-radius: 12px;
  padding: 60px 40px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  max-width: 500px;
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 20px;
}

.empty-container h2 {
  font-size: 24px;
  font-weight: 700;
  color: var(--smoky-black);
  margin-bottom: 12px;
}

.empty-container p {
  color: #666;
  margin-bottom: 8px;
  line-height: 1.6;
}

.btn-back {
  display: inline-block;
  margin-top: 24px;
  padding: 12px 24px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  text-decoration: none;
  border-radius: 8px;
  font-weight: 700;
  transition: all 0.3s;
}

.btn-back:hover {
  background: var(--palace-gold-dark);
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: -280px;
    transition: left 0.3s ease;
    width: 280px;
    z-index: 1001;
  }

  .main-content {
    margin-left: 0;
  }

  .topbar {
    padding: 16px;
  }

  .page-title {
    font-size: 24px;
  }

  .page-wrapper {
    padding: 16px;
  }

  .empty-container {
    padding: 40px 24px;
  }
}
</style>