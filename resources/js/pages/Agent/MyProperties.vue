<template>
  <div class="dashboard-wrapper">
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link to="/agent/dashboard" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/agent/dashboard" exact class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </span>
          <span>Overview</span>
        </router-link>

        <div class="nav-group-label">Properties</div>
        <router-link to="/agent/properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span>
          <span>My Properties</span>
        </router-link>

        

        <div class="nav-group-label">Schedule</div>
        <router-link to="/agent/viewings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <span>Viewings</span>
        </router-link>
        <router-link to="/agent/calendar" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
          </span>
          <span>Calendar</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Messages</span>
        </router-link>
        <router-link to="/agent/documents" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </span>
          <span>Documents</span>
        </router-link>
      </nav>

      <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <router-link to="/profile" class="nav-item nav-user" @click="sidebarOpen = false">
          <div class="nav-av">
            <img v-if="profilePhotoUrl" :src="profilePhotoUrl" :alt="userName" />
            <span v-else>{{ userName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ userName }}</span>
            <span class="nav-user-role">Agent</span>
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

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Top Navbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <h1 class="page-title">My Properties</h1>
          </div>
          <div class="topbar-right">
            <button class="btn-icon" title="Notifications">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </button>
            <router-link to="/agent/properties/create" class="btn-primary">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add New Property
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
            <select v-model="listingTypeFilter" class="filter-select">
              <option value="">All Listings</option>
              <option value="sale">For Sale</option>
              <option value="rent">For Rent</option>
            </select>
          </div>

          <div class="filter-group">
            <select v-model="statusFilter" class="filter-select">
              <option value="">All Status</option>
              <option value="available">Available</option>
              <option value="sold">Sold</option>
              <option value="rented">Rented</option>
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
          <div class="spinner"></div>
          <p>Loading properties...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredProperties.length === 0" class="empty-state">
          <div class="empty-content">
            <div class="empty-icon-wrap"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
            <h3>No Properties Found</h3>
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
              <div v-else class="card-image-placeholder"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
              
              <span :class="['listing-type-badge', 'lt-' + (property.listing_type || 'sale')]">
                {{ (property.listing_type || 'sale') === 'rent' ? 'For Rent' : 'For Sale' }}
              </span>
              <span :class="['status-badge', 'status-' + property.status]">
                {{ formatStatus(property.status) }}
              </span>

              <span v-if="property.photos && property.photos.length > 0" class="photo-count">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                {{ property.photos.length }}
              </span>
            </div>

            <!-- Card Content -->
            <div class="card-content">
              <h4 class="property-title">{{ property.title }}</h4>
              <p class="property-location"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ property.city }}</p>

              <div class="property-specs-row">
                <span class="spec"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg> {{ property.bedrooms }}</span>
                <span class="spec"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v7"/></svg> {{ property.bathrooms }}</span>
                <span class="spec"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> {{ property.view_count || 0 }}</span>
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
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
                </router-link>
                <button
                  @click="deleteProperty(property.id)"
                  class="btn-card btn-delete"
                  title="Delete"
                >
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Delete
                </button>
                <button
                  @click="changeStatus(property.id, property.status)"
                  class="btn-card btn-status"
                  title="Change Status"
                >
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg> Status
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
              v-for="status in ['available', 'sold', 'rented']"
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
      sidebarOpen: false,
      // User
      userName: '',
      userRole: '',
      showUserMenu: false,
      profilePhotoUrl: null,

      // Properties
      properties: [],
      loading: false,

      // Filters
      searchQuery: '',
      listingTypeFilter: '',
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

      // Filter by listing type
      if (this.listingTypeFilter) {
        filtered = filtered.filter(p => (p.listing_type || 'sale') === this.listingTypeFilter);
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

    loadProfilePhoto() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__ || 'http://localhost:8000'}/storage/${user.profile_photo_path}`;
        }
      } catch (e) { /* non-critical */ }
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
        rented: 'Rented',
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
    this.loadProfilePhoto();
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

.dashboard-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--bg);
  font-family: var(--fb);
  --navy:      #0B1C39;
  --navy2:     #102445;
  --navy3:     #1a3158;
  --gold:      #D89B0F;
  --gold2:     #E5B332;
  --gold3:     #B07A08;
  --gold-glow: rgba(216,155,15,0.12);
  --s50:  #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s800: #292524; --s900: #1C1917;
  --white: #FFFFFF;
  --bg:   #F2F0EB;
  --sw:   242px;
  --th:   56px;
  --r:    12px;
  --fd:   'Outfit','Inter',-apple-system,sans-serif;
  --fb:   'Inter',-apple-system,sans-serif;
}

/* ── SIDEBAR ── */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--navy); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; display: block; }
.logo-ph { background: linear-gradient(135deg, var(--gold), var(--gold2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
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
.nav-item.router-link-active:not(.router-link-exact-active) { color: rgba(255,255,255,0.65); }
.nav-item.router-link-exact-active {
  background: linear-gradient(90deg, rgba(216,155,15,0.14) 0%, rgba(216,155,15,0.03) 100%);
  color: var(--gold2);
}
.nav-item.router-link-exact-active::before {
  content: ''; position: absolute; left: 0; top: 7px; bottom: 7px;
  width: 3px; background: var(--gold); border-radius: 0 3px 3px 0;
}
.nav-item.router-link-exact-active .nav-icon-wrap { color: var(--gold); }
.nav-group-label {
  font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
  color: rgba(255,255,255,0.22); padding: 14px 12px 5px; margin: 0;
}
.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }
.nav-badge {
  margin-left: auto; background: rgba(216,155,15,0.18); color: var(--gold2);
  border-radius: 99px; padding: 2px 7px; font-size: 10.5px; font-weight: 700;
}
.sidebar-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 10px 8px; }
.sidebar-bottom { flex-shrink: 0; padding: 0 10px 14px; }
.nav-user { gap: 10px; padding: 8px 10px; }
.nav-user:hover, .nav-user.router-link-exact-active { background: rgba(216,155,15,0.10); color: var(--gold2); }
.nav-av {
  width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; color: #fff;
}
.nav-av img { width: 100%; height: 100%; object-fit: cover; }
.nav-user-info { flex: 1; min-width: 0; }
.nav-user-name { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nav-user-role  { display: block; font-size: 10.5px; color: rgba(255,255,255,0.38); margin-top: 1px; }
.nav-logout { color: rgba(255,255,255,0.30); }
.nav-logout:hover { background: rgba(239,68,68,0.10); color: #f87171; }
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 90; backdrop-filter: blur(2px); }

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.main-content {
  flex: 1;
  margin-left: var(--sw);
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.topbar {
  background: white;
  border-bottom: 2px solid var(--s200);
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
  font-family: var(--fd);
  letter-spacing: -0.5px;
  color: var(--navy);
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
  color: var(--navy);
}

.btn-icon:hover {
  transform: scale(1.1);
  color: var(--gold);
}

.btn-primary {
  padding: 12px 24px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: var(--navy);
  border: none;
  border-radius: 50px;
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
  border: 1px solid var(--s200);
  border-radius: 6px;
  font-size: 14px;
  font-family: var(--fb);
  transition: all 0.3s;
}

.search-input:focus,
.filter-select:focus {
  outline: none;
  border-color: var(--gold);
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
  border: 1px solid var(--s200);
}

.property-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
  border-color: var(--gold);
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

.listing-type-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}
.lt-sale { background: #dcfce7; color: #15803d; }
.lt-rent { background: #ede9fe; color: #7c3aed; }

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

.status-rented {
  background: #dbeafe;
  color: #1e40af;
}

.photo-count {
  display: inline-flex; align-items: center; gap: 4px;
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
  color: var(--navy);
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
  background: var(--s100);
  border-radius: 4px;
}

.property-price {
  font-size: 18px;
  font-weight: 800;
  color: var(--gold);
  margin: 12px 0 0 0;
}

.card-footer {
  padding: 12px 16px;
  border-top: 1px solid var(--s200);
  background: var(--s100);
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
  border: 1px solid var(--gold);
  color: var(--gold);
}

.btn-edit:hover {
  background: var(--gold);
  color: var(--navy);
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

.spinner { width: 32px; height: 32px; border: 3px solid #e0e0e0; border-top-color: var(--gold); border-radius: 50%; animation: spin .7s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-icon-wrap { margin-bottom: 8px; color: #ccc; }
.empty-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}

.empty-content h3 {
  font-size: 20px;
  font-weight: 700;
  color: var(--navy);
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
  background: var(--s100);
  border-radius: 8px;
  border-left: 3px solid var(--gold);
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
  color: var(--gold);
  font-family: var(--fd);
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
  border-bottom: 1px solid var(--s200);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 18px;
  font-weight: 700;
  margin: 0;
  color: var(--navy);
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
  color: var(--navy);
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
  border: 1px solid var(--s200);
  background: white;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}

.status-option:hover {
  border-color: var(--gold);
  background: var(--s100);
}

.status-option.status-available {
  border-color: #155724;
  color: #155724;
}

.status-option.status-sold {
  border-color: #721c24;
  color: #721c24;
}

.status-option.status-rented {
  border-color: #1e40af;
  color: #1e40af;
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
    left: calc(-1 * var(--sw));
    transition: left 0.3s ease;
    width: var(--sw);
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