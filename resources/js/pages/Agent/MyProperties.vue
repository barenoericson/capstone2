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
          <router-link to="/agent/properties" class="nav-item active">
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
          <router-link to="/agent/calendar" class="nav-item">
            <span class="nav-icon">📆</span>
            <span class="nav-label">My Calendar</span>
          </router-link>
          <router-link to="/agent/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Messages</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">Settings</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profile</span>
          </router-link>
          <router-link to="/settings" class="nav-item">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">Settings</span>
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
            <h1 class="page-title">My Properties</h1>
          </div>
          <div class="topbar-right">
            <button class="btn-icon">🔔</button>
            <router-link to="/agent/properties/create" class="btn-primary">
              ➕ Add New Property
            </router-link>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <div class="page-wrapper">
        <!-- Filters Section -->
        <div class="filters-section">
          <div class="filter-group">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search properties..."
              class="search-input"
            />
          </div>

          <div class="filter-group">
            <select v-model="statusFilter" class="filter-select">
              <option value="">All Status</option>
              <option value="available">Available</option>
              <option value="sold">Sold</option>
            </select>
          </div>

          <div class="filter-group">
            <select v-model="sortBy" class="filter-select">
              <option value="newest">Newest First</option>
              <option value="oldest">Oldest First</option>
              <option value="price-high">Price: High to Low</option>
              <option value="price-low">Price: Low to High</option>
              <option value="views">Most Viewed</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <p>⏳ Loading properties...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredProperties.length === 0" class="empty-state">
          <div class="empty-content">
            <h3>📭 No Properties Found</h3>
            <p>{{ searchQuery ? 'No properties match your search.' : 'You haven\'t created any properties yet.' }}</p>
            <router-link to="/agent/properties/create" class="btn-primary">
              Create Your First Property
            </router-link>
          </div>
        </div>

        <!-- Properties Grid -->
        <div v-else class="properties-grid">
          <div v-for="property in filteredProperties" :key="property.id" class="property-card">
            <!-- Property Image -->
            <div class="card-image-container">
              <img 
                v-if="getCoverPhoto(property)" 
                :src="getCoverPhoto(property)" 
                :alt="property.title"
                class="card-image"
              />
              <div v-else class="card-image-placeholder">📷</div>
              
              <span :class="['status-badge', 'status-' + property.status]">
                {{ formatStatus(property.status) }}
              </span>

              <span v-if="property.photos && property.photos.length > 0" class="photo-count">
                📸 {{ property.photos.length }}
              </span>
            </div>

            <!-- Card Content -->
            <div class="card-content">
              <h4 class="property-title">{{ property.title }}</h4>
              <p class="property-location">📍 {{ property.city }}</p>

              <div class="property-specs-row">
                <span class="spec">🛏️ {{ property.bedrooms }}</span>
                <span class="spec">🚿 {{ property.bathrooms }}</span>
                <span class="spec">👁️ {{ property.view_count || 0 }}</span>
              </div>

              <div class="property-price">
                ₱{{ formatPrice(property.price) }}
              </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer">
              <div class="card-actions">
                <router-link
                  :to="`/agent/properties/${property.id}/edit`"
                  class="btn-card btn-edit"
                  title="Edit"
                >
                  ✏️ Edit
                </router-link>
                <button
                  @click="deleteProperty(property.id)"
                  class="btn-card btn-delete"
                  title="Delete"
                >
                  🗑️ Delete
                </button>
                <button
                  @click="changeStatus(property.id, property.status)"
                  class="btn-card btn-status"
                  title="Change Status"
                >
                  ⚙️ Status
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Stats -->
        <div v-if="properties.length > 0" class="summary-stats">
          <div class="stats-grid">
            <div class="stat">
              <span class="stat-label">Total Properties</span>
              <span class="stat-value">{{ properties.length }}</span>
            </div>
            <div class="stat">
              <span class="stat-label">Active Listings</span>
              <span class="stat-value">{{ properties.filter(p => p.status === 'available').length }}</span>
            </div>
            <div class="stat">
              <span class="stat-label">Total Views</span>
              <span class="stat-value">{{ properties.reduce((sum, p) => sum + (p.view_count || 0), 0) }}</span>
            </div>
            <div class="stat">
              <span class="stat-label">Total Photos</span>
              <span class="stat-value">{{ properties.reduce((sum, p) => sum + (p.photos ? p.photos.length : 0), 0) }}</span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Status Modal -->
    <div v-if="showStatusModal" class="modal-overlay" @click.self="showStatusModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Change Property Status</h3>
          <button @click="showStatusModal = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <p>Select new status for this property:</p>
          <div class="status-options">
            <button
              v-for="status in ['available', 'sold']"
              :key="status"
              @click="confirmStatusChange(status)"
              :class="['status-option', 'status-' + status]"
            >
              {{ formatStatus(status) }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <transition name="fade">
      <div v-if="successMessage" class="success-message">
        ✓ {{ successMessage }}
      </div>
    </transition>

    <transition name="fade">
      <div v-if="errorMessage" class="error-message">
        ✗ {{ errorMessage }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'MyProperties',
  data() {
    return {
      // User
      userName: '',
      userRole: '',
      showUserMenu: false,

      // Properties
      properties: [],
      loading: false,

      // Filters
      searchQuery: '',
      statusFilter: '',
      sortBy: 'newest',

      // Modal
      showStatusModal: false,
      selectedPropertyId: null,

      // Messages
      successMessage: '',
      errorMessage: '',
      
      // ✅ FIXED: Dynamic API URL
      apiUrl: window.__API_URL__,
    };
  },

  computed: {
    filteredProperties() {
      let filtered = this.properties;

      // Filter by search query
      if (this.searchQuery) {
        filtered = filtered.filter(p =>
          p.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          p.city.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          p.address.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      }

      // Filter by status
      if (this.statusFilter) {
        filtered = filtered.filter(p => p.status === this.statusFilter);
      }

      // Sort
      switch (this.sortBy) {
        case 'oldest':
          filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
          break;
        case 'price-high':
          filtered.sort((a, b) => b.price - a.price);
          break;
        case 'price-low':
          filtered.sort((a, b) => a.price - b.price);
          break;
        case 'views':
          filtered.sort((a, b) => (b.view_count || 0) - (a.view_count || 0));
          break;
        case 'newest':
        default:
          filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      }

      return filtered;
    },
  },

  methods: {
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },

    capitalizeRole(role) {
      return role.charAt(0).toUpperCase() + role.slice(1);
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH').format(price);
    },

    formatStatus(status) {
      const statusMap = {
        available: 'Available',
        sold: 'Sold',
      };
      return statusMap[status] || status;
    },

    getCoverPhoto(property) {
      if (!property.photos || property.photos.length === 0) return null;
      const cover = property.photos.find(p => p.is_cover);
      return cover ? cover.photo_url : property.photos[0].photo_url;
    },

    // ✅ FIXED: Use dynamic apiUrl from localStorage
    async loadProperties() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');

        // ✅ FIXED: Use dynamic API URL
        const response = await fetch(`${this.apiUrl}/api/agent/properties`, {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        // ✅ FIXED: Check HTTP status before parsing JSON
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: Failed to load properties`);
        }

        const data = await response.json();

        if (data.success) {
          this.properties = data.data.data || data.data || [];
          console.log('✅ Properties loaded:', this.properties);
        } else {
          this.showError(data.message || 'Failed to load properties');
        }
      } catch (error) {
        console.error('❌ Load error:', error);
        this.showError('Failed to load properties: ' + error.message);
      } finally {
        this.loading = false;
      }
    },

    async deleteProperty(id) {
      if (!confirm('Are you sure you want to delete this property?')) return;

      try {
        const token = localStorage.getItem('auth_token');

        const response = await fetch(`${this.apiUrl}/api/agent/properties/${id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
        });

        let data;
        try {
          data = await response.json();
        } catch {
          throw new Error(`Server error (HTTP ${response.status})`);
        }

        if (data.success) {
          this.showSuccess('Property deleted successfully');
          this.loadProperties();
        } else {
          this.showError(data.message || 'Failed to delete property');
        }
      } catch (error) {
        console.error('Delete error:', error);
        this.showError('Failed to delete property: ' + error.message);
      }
    },

    changeStatus(propertyId, currentStatus) {
      this.selectedPropertyId = propertyId;
      this.showStatusModal = true;
    },

    async confirmStatusChange(newStatus) {
      if (!this.selectedPropertyId) return;

      try {
        const token = localStorage.getItem('auth_token');

        const response = await fetch(`${this.apiUrl}/api/agent/properties/${this.selectedPropertyId}/status`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
          body: JSON.stringify({ status: newStatus }),
        });

        let data;
        try {
          data = await response.json();
        } catch {
          throw new Error(`Server error (HTTP ${response.status})`);
        }

        if (data.success) {
          this.showSuccess('Property status updated');
          this.loadProperties();
          this.showStatusModal = false;
        } else {
          this.showError(data.message || 'Failed to update status');
        }
      } catch (error) {
        console.error('Update error:', error);
        this.showError('Failed to update property status: ' + error.message);
      }
    },

    showSuccess(message) {
      this.successMessage = message;
      setTimeout(() => {
        this.successMessage = '';
      }, 3000);
    },

    showError(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 3000);
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

    // ✅ FIXED: Use dynamic apiUrl from localStorage
    async logout() {
      try {
        const token = localStorage.getItem('auth_token');
        
        // ✅ FIXED: Use dynamic API URL
        await fetch(`${this.apiUrl}/api/auth/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/');
      } catch (error) {
        console.error('Logout error:', error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/');
      }
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/');
      return;
    }

    // ✅ FIXED: Set dynamic API URL from localStorage
    this.apiUrl = localStorage.getItem('api_url') || window.__API_URL__;

    this.loadUserData();
    this.loadProperties();
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
  --palace-gold: #FFD700;
  --palace-gold-dark: #DAB600;
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
  color: #999;
  transition: color 0.3s;
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

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

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
  color: var(--smoky-black);
}

.btn-icon:hover {
  transform: scale(1.1);
  color: var(--palace-gold);
}

.btn-primary {
  padding: 12px 24px;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.page-wrapper {
  flex: 1;
  padding: 32px;
  overflow-y: auto;
}

/* Filters Section */
.filters-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 32px;
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.filter-group {
  display: flex;
  flex-direction: column;
}

.search-input,
.filter-select {
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-size: 14px;
  font-family: var(--font-body);
  transition: all 0.3s;
}

.search-input:focus,
.filter-select:focus {
  outline: none;
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

/* Properties Grid */
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 40px;
}

.property-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
  border: 1px solid var(--light-gray);
}

.property-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
  border-color: var(--palace-gold);
}

.card-image-container {
  position: relative;
  width: 100%;
  height: 200px;
  background: #f0f0f0;
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.property-card:hover .card-image {
  transform: scale(1.05);
}

.card-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 64px;
  color: #ddd;
}

.status-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.status-available {
  background: #d4edda;
  color: #155724;
}

.status-sold {
  background: #f8d7da;
  color: #721c24;
}


.photo-count {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 700;
}

.card-content {
  padding: 16px;
  flex: 1;
}

.property-title {
  font-size: 16px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 0 0 8px 0;
}

.property-location {
  font-size: 13px;
  color: #999;
  margin: 0 0 12px 0;
}

.property-specs-row {
  display: flex;
  gap: 12px;
  margin: 12px 0;
  flex-wrap: wrap;
}

.spec {
  font-size: 12px;
  color: #666;
  font-weight: 600;
  padding: 4px 8px;
  background: var(--white-smoke);
  border-radius: 4px;
}

.property-price {
  font-size: 18px;
  font-weight: 800;
  color: var(--palace-gold);
  margin: 12px 0 0 0;
}

.card-footer {
  padding: 12px 16px;
  border-top: 1px solid var(--light-gray);
  background: var(--white-smoke);
}

.card-actions {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.btn-card {
  padding: 8px 12px;
  border: none;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-edit {
  background: #f0f8ff;
  border: 1px solid var(--palace-gold);
  color: var(--palace-gold);
}

.btn-edit:hover {
  background: var(--palace-gold);
  color: var(--smoky-black);
}

.btn-delete {
  background: #ffebee;
  border: 1px solid #f44336;
  color: #f44336;
}

.btn-delete:hover {
  background: #f44336;
  color: white;
}

.btn-status {
  background: #fff3e0;
  border: 1px solid #ff9800;
  color: #ff9800;
}

.btn-status:hover {
  background: #ff9800;
  color: white;
}

/* Loading & Empty States */
.loading-state,
.empty-state {
  padding: 60px 20px;
  text-align: center;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.empty-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}

.empty-content h3 {
  font-size: 20px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
}

.empty-content p {
  font-size: 14px;
  color: #666;
  margin: 0;
  max-width: 400px;
}

/* Summary Stats */
.summary-stats {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  text-align: center;
  padding: 16px;
  background: var(--white-smoke);
  border-radius: 8px;
  border-left: 3px solid var(--palace-gold);
}

.stat-label {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.stat-value {
  font-size: 28px;
  font-weight: 800;
  color: var(--palace-gold);
  font-family: var(--font-display);
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-box {
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 90%;
}

.modal-header {
  padding: 24px;
  border-bottom: 1px solid var(--light-gray);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 18px;
  font-weight: 700;
  margin: 0;
  color: var(--smoky-black);
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
  padding: 0;
  transition: color 0.3s;
}

.btn-close:hover {
  color: var(--smoky-black);
}

.modal-body {
  padding: 24px;
}

.modal-body p {
  font-size: 14px;
  color: #666;
  margin-bottom: 20px;
}

.status-options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.status-option {
  padding: 12px;
  border: 1px solid var(--light-gray);
  background: white;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}

.status-option:hover {
  border-color: var(--palace-gold);
  background: var(--white-smoke);
}

.status-option.status-available {
  border-color: #155724;
  color: #155724;
}

.status-option.status-sold {
  border-color: #721c24;
  color: #721c24;
}


/* Messages */
.success-message {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #4caf50;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3000;
  animation: slideIn 0.3s ease;
}

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

@media (max-width: 1024px) {
  .properties-grid {
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
  }
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

  .topbar-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .page-title {
    font-size: 24px;
  }

  .page-wrapper {
    padding: 16px;
  }

  .filters-section {
    grid-template-columns: 1fr;
  }

  .properties-grid {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 20px;
  }

  .btn-primary {
    padding: 10px 16px;
    font-size: 12px;
  }

  .card-actions {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .modal-box {
    width: 95%;
  }

  .status-options {
    grid-template-columns: 1fr;
  }
}
</style>