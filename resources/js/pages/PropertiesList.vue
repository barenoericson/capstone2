<template>
  <div class="discover-wrapper">
    <!-- Sidebar -->
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

        <div class="nav-section">
          <h3 class="section-title">Browse</h3>
          <router-link to="/properties" class="nav-item active">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">Activity</h3>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Inbox</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
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
          <div class="user-avatar">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name">{{ userName }}</p>
            <p class="user-role">Buyer</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">Discover Properties</h1>
            <p class="page-sub">Browse listings from verified agents</p>
          </div>
          <div class="topbar-right">
            <span class="properties-count" v-if="!loading">{{ totalCount }} properties found</span>
          </div>
        </div>
      </nav>

      <!-- Filter Bar -->
      <div class="filter-bar">
        <div class="search-wrap">
          <span class="search-icon">🔍</span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by title, city, province..."
            class="search-input"
            @input="debouncedSearch"
          />
          <button v-if="searchQuery" @click="clearSearch" class="clear-btn">✕</button>
        </div>

        <select v-model="filterType" @change="applyFilters" class="filter-select">
          <option value="">All Types</option>
          <option value="house">House</option>
          <option value="condo">Condo</option>
          <option value="apartment">Apartment</option>
          <option value="commercial">Commercial</option>
          <option value="lot">Lot</option>
        </select>

        <select v-model="filterBedrooms" @change="applyFilters" class="filter-select">
          <option value="">Any Bedrooms</option>
          <option value="1">1+ Bed</option>
          <option value="2">2+ Beds</option>
          <option value="3">3+ Beds</option>
          <option value="4">4+ Beds</option>
        </select>

        <select v-model="filterPrice" @change="applyFilters" class="filter-select">
          <option value="">Any Price</option>
          <option value="1000000">Under ₱1M</option>
          <option value="3000000">Under ₱3M</option>
          <option value="5000000">Under ₱5M</option>
          <option value="10000000">Under ₱10M</option>
        </select>

        <button @click="resetFilters" class="btn-reset" v-if="hasActiveFilters">
          Reset Filters
        </button>
      </div>

      <!-- Content Area -->
      <div class="content-area">
        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading properties...</p>
        </div>

        <!-- Empty -->
        <div v-else-if="filteredProperties.length === 0" class="empty-state">
          <div class="empty-icon">🏡</div>
          <h3>No properties found</h3>
          <p>Try adjusting your search or filters.</p>
          <button @click="resetFilters" class="btn-reset-large">Reset All Filters</button>
        </div>

        <!-- Properties Grid -->
        <div v-else class="properties-grid">
          <div
            v-for="property in filteredProperties"
            :key="property.id"
            class="property-card"
          >
            <!-- Image -->
            <div class="card-image-wrap">
              <img
                v-if="getCoverPhoto(property)"
                :src="getCoverPhoto(property)"
                :alt="property.title"
                class="card-image"
              />
              <div v-else class="card-image-placeholder">📷</div>

              <!-- Hover Overlay -->
              <div class="card-overlay">
                <button
                  class="btn-save"
                  :class="{ saved: savedIds.includes(property.id) }"
                  @click.prevent="toggleSave(property)"
                >
                  {{ savedIds.includes(property.id) ? '❤️ Saved' : '🤍 Save' }}
                </button>
                <router-link :to="`/property/${property.id}`" class="btn-view">
                  👁️ View
                </router-link>
              </div>

              <!-- Price Tag -->
              <div class="price-tag">₱{{ formatPrice(property.price) }}</div>

              <!-- Featured Badge -->
              <div v-if="property.featured" class="featured-badge">⭐ Featured</div>
            </div>

            <!-- Body -->
            <div class="card-body">
              <div class="type-row">
                <span class="type-badge">{{ formatType(property.property_type) }}</span>
                <span class="status-badge" :class="property.status">{{ property.status }}</span>
              </div>
              <h3 class="card-title">{{ property.title }}</h3>
              <p class="card-location">📍 {{ property.city }}, {{ property.province }}</p>

              <div class="card-specs">
                <span v-if="property.bedrooms" class="spec">🛏️ {{ property.bedrooms }} bed</span>
                <span v-if="property.bathrooms" class="spec">🚿 {{ property.bathrooms }} bath</span>
                <span v-if="property.area_sqm" class="spec">📐 {{ property.area_sqm }} sqm</span>
              </div>

              <div class="card-footer">
                <div class="agent-row" v-if="property.agent">
                  <div class="agent-avatar">
                    {{ (property.agent.user?.name || 'A').charAt(0).toUpperCase() }}
                  </div>
                  <span class="agent-name">{{ property.agent.user?.name || 'Agent' }}</span>
                  <span class="verified-tag" v-if="property.agent.is_verified">✓</span>
                </div>
                <router-link :to="`/property/${property.id}`" class="btn-details">
                  View →
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1 && !loading" class="pagination">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="page-btn"
          >← Prev</button>

          <button
            v-for="page in visiblePages"
            :key="page"
            @click="changePage(page)"
            :class="['page-btn', { active: page === currentPage }]"
          >{{ page }}</button>

          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="page-btn"
          >Next →</button>
        </div>
      </div>
    </main>

    <!-- Toast -->
    <transition name="toast">
      <div v-if="toast.show" :class="['toast', toast.type]">{{ toast.message }}</div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'PropertiesList',

  data() {
    return {
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      token: localStorage.getItem('auth_token') || '',
      userName: '',

      // Properties
      allProperties: [],
      loading: true,
      totalCount: 0,
      currentPage: 1,
      totalPages: 1,
      perPage: 12,

      // Filters
      searchQuery: '',
      filterType: '',
      filterBedrooms: '',
      filterPrice: '',

      // Saved
      savedIds: [],

      // Toast
      toast: { show: false, message: '', type: 'success' },
      searchTimer: null,
    };
  },

  computed: {
    filteredProperties() {
      let props = this.allProperties;

      if (this.searchQuery.trim()) {
        const q = this.searchQuery.toLowerCase();
        props = props.filter(p =>
          p.title?.toLowerCase().includes(q) ||
          p.city?.toLowerCase().includes(q) ||
          p.province?.toLowerCase().includes(q)
        );
      }

      if (this.filterType) {
        props = props.filter(p => p.property_type === this.filterType);
      }

      if (this.filterBedrooms) {
        props = props.filter(p => (p.bedrooms || 0) >= parseInt(this.filterBedrooms));
      }

      if (this.filterPrice) {
        props = props.filter(p => (p.price || 0) <= parseInt(this.filterPrice));
      }

      this.totalCount = props.length;
      return props;
    },

    hasActiveFilters() {
      return this.searchQuery || this.filterType || this.filterBedrooms || this.filterPrice;
    },

    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.totalPages, this.currentPage + 2);
      for (let i = start; i <= end; i++) pages.push(i);
      return pages;
    },
  },

  methods: {
    getCoverPhoto(property) {
      if (!property.photos || property.photos.length === 0) return null;
      const cover = property.photos.find(p => p.is_cover);
      return cover ? cover.photo_url : property.photos[0].photo_url;
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH').format(price);
    },

    formatType(type) {
      const map = {
        house: 'House', condo: 'Condo', apartment: 'Apartment',
        commercial: 'Commercial', lot: 'Lot',
      };
      return map[type] || type;
    },

    async loadProperties() {
      this.loading = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/properties?per_page=100`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          this.allProperties = data.data.data || data.data || [];
          this.totalCount = this.allProperties.length;
        }
      } catch (e) {
        console.error(e);
        this.showToast('Failed to load properties', 'error');
      } finally {
        this.loading = false;
      }
    },

    async loadSavedIds() {
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/saved-properties`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          const list = data.data?.data || data.data || [];
          this.savedIds = list.map(p => p.id);
        }
      } catch (e) {
        console.error(e);
      }
    },

    async toggleSave(property) {
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/properties/${property.id}/save`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          if (this.savedIds.includes(property.id)) {
            this.savedIds = this.savedIds.filter(id => id !== property.id);
            this.showToast('Removed from saved properties', 'info');
          } else {
            this.savedIds.push(property.id);
            this.showToast('Saved to My Wallet! ❤️', 'success');
          }
        }
      } catch (e) {
        this.showToast('Failed to save property', 'error');
      }
    },

    debouncedSearch() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => {
        this.currentPage = 1;
      }, 300);
    },

    applyFilters() {
      this.currentPage = 1;
    },

    clearSearch() {
      this.searchQuery = '';
      this.currentPage = 1;
    },

    resetFilters() {
      this.searchQuery = '';
      this.filterType = '';
      this.filterBedrooms = '';
      this.filterPrice = '';
      this.currentPage = 1;
    },

    changePage(page) {
      if (page < 1 || page > this.totalPages) return;
      this.currentPage = page;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, message, type };
      setTimeout(() => { this.toast.show = false; }, 3000);
    },
  },

  mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Buyer';
    this.loadProperties();
    this.loadSavedIds();
  },

  beforeUnmount() {
    clearTimeout(this.searchTimer);
  },
};
</script>

<style scoped>
:root {
  --gold: #e6ae0d;
  --gold-dark: #d4a000;
  --black: #100c08;
  --smoke: #f5f5f5;
  --light-gray: #e0e0e0;
}

.discover-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--smoke);
  font-family: 'Inter', sans-serif;
}

/* Sidebar */
.sidebar {
  width: 260px;
  background: white;
  border-right: 1px solid var(--light-gray);
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0; top: 0;
  height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0,0,0,0.06);
  z-index: 100;
}

.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--light-gray); }
.sidebar-logo { font-size: 22px; font-weight: 800; margin: 0; }
.logo-realty { color: var(--black); }
.logo-ph { color: var(--gold); }

.sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
.nav-section { margin-bottom: 20px; }
.section-title {
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.2px; color: #999; padding: 6px 16px; margin: 0;
}
.nav-item {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 16px; margin: 0 8px;
  color: var(--black); text-decoration: none; border-radius: 8px;
  font-size: 14px; font-weight: 500; transition: all 0.2s;
}
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-weight: 700;
}
.nav-icon { font-size: 18px; min-width: 22px; text-align: center; }
.nav-label { flex: 1; }

.sidebar-footer { padding: 16px; border-top: 1px solid var(--light-gray); }
.user-card { display: flex; align-items: center; gap: 10px; background: var(--smoke); border-radius: 8px; padding: 10px; }
.user-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 15px; flex-shrink: 0;
}
.user-info { flex: 1; min-width: 0; }
.user-name { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-role { font-size: 11px; color: #999; margin: 0; }

/* Main */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: white; border-bottom: 1px solid var(--light-gray);
  padding: 20px 32px; position: sticky; top: 0; z-index: 50;
}
.topbar-content { display: flex; align-items: center; justify-content: space-between; }
.page-title { font-size: 26px; font-weight: 800; color: var(--black); margin: 0; }
.page-sub { font-size: 13px; color: #999; margin: 4px 0 0; }
.properties-count { font-size: 13px; color: #666; font-weight: 600; background: var(--smoke); padding: 6px 14px; border-radius: 20px; }

/* Filter Bar */
.filter-bar {
  background: white; border-bottom: 1px solid var(--light-gray);
  padding: 14px 32px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;
}

.search-wrap {
  position: relative; flex: 1; min-width: 200px;
}
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px; }
.search-input {
  width: 100%; padding: 10px 36px 10px 36px;
  border: 1px solid var(--light-gray); border-radius: 8px;
  font-size: 14px; background: var(--smoke); color: var(--black); outline: none;
  transition: border-color 0.2s;
}
.search-input:focus { border-color: var(--gold); background: white; }
.clear-btn {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #999; cursor: pointer; font-size: 14px;
  padding: 2px 6px;
}
.clear-btn:hover { color: var(--black); }

.filter-select {
  padding: 10px 14px; border: 1px solid var(--light-gray); border-radius: 8px;
  font-size: 13px; background: var(--smoke); color: var(--black);
  cursor: pointer; outline: none; transition: border-color 0.2s;
}
.filter-select:focus { border-color: var(--gold); }

.btn-reset {
  padding: 10px 16px; background: none; border: 1px solid #ccc;
  border-radius: 8px; font-size: 13px; color: #666; cursor: pointer; transition: all 0.2s;
}
.btn-reset:hover { border-color: var(--gold); color: var(--gold); }

/* Content */
.content-area { padding: 32px; flex: 1; }

/* Loading */
.loading-state { text-align: center; padding: 80px; color: #999; }
.spinner {
  width: 40px; height: 40px; border: 4px solid var(--light-gray);
  border-top-color: var(--gold); border-radius: 50%;
  animation: spin 0.8s linear infinite; margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Empty */
.empty-state { text-align: center; padding: 80px 20px; }
.empty-icon { font-size: 64px; margin-bottom: 16px; }
.empty-state h3 { font-size: 22px; font-weight: 700; color: var(--black); margin: 0 0 8px; }
.empty-state p { color: #999; font-size: 14px; margin-bottom: 24px; }
.btn-reset-large {
  padding: 12px 24px; background: var(--gold); border: none; border-radius: 8px;
  font-size: 14px; font-weight: 700; color: var(--black); cursor: pointer;
}

/* Properties Grid */
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.property-card {
  background: white; border-radius: 14px; overflow: hidden;
  border: 1px solid var(--light-gray); transition: all 0.3s ease;
}
.property-card:hover {
  box-shadow: 0 12px 32px rgba(0,0,0,0.1);
  border-color: var(--gold);
  transform: translateY(-6px);
}

.card-image-wrap {
  position: relative; height: 210px; overflow: hidden; background: #f0f0f0;
}
.card-image { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
.property-card:hover .card-image { transform: scale(1.06); }
.card-image-placeholder {
  width: 100%; height: 100%; display: flex; align-items: center;
  justify-content: center; font-size: 56px; color: #ddd;
}

.card-overlay {
  position: absolute; inset: 0; background: rgba(16,12,8,0.5);
  display: flex; align-items: center; justify-content: center;
  gap: 10px; opacity: 0; transition: opacity 0.3s;
}
.property-card:hover .card-overlay { opacity: 1; }

.btn-save, .btn-view {
  padding: 10px 18px; border: none; border-radius: 8px;
  font-weight: 700; font-size: 13px; cursor: pointer;
  text-decoration: none; display: inline-block; transition: all 0.2s;
}
.btn-save {
  background: white; color: var(--black);
}
.btn-save:hover { background: #ffe0e0; }
.btn-save.saved { background: #ffe0e0; color: #c0392b; }

.btn-view {
  background: var(--gold); color: var(--black);
}
.btn-view:hover { background: var(--gold-dark); }

.price-tag {
  position: absolute; bottom: 12px; left: 12px;
  background: var(--gold); color: var(--black);
  padding: 6px 14px; border-radius: 6px;
  font-weight: 800; font-size: 13px;
}
.featured-badge {
  position: absolute; top: 12px; right: 12px;
  background: white; color: var(--gold);
  padding: 5px 10px; border-radius: 6px;
  font-weight: 700; font-size: 11px;
}

.card-body { padding: 18px; }

.type-row { display: flex; gap: 8px; margin-bottom: 10px; }
.type-badge {
  font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
  color: var(--gold); background: #fdf5d0; padding: 4px 10px; border-radius: 4px;
}
.status-badge {
  font-size: 11px; font-weight: 700; text-transform: capitalize;
  padding: 4px 10px; border-radius: 4px;
}
.status-badge.active { color: #15803d; background: #dcfce7; }
.status-badge.sold { color: #b91c1c; background: #fee2e2; }
.status-badge.pending { color: #b45309; background: #fef3c7; }

.card-title {
  font-size: 16px; font-weight: 700; color: var(--black);
  margin: 0 0 6px; line-height: 1.3;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.card-location { font-size: 13px; color: #888; margin: 0 0 12px; }

.card-specs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
.spec {
  font-size: 12px; color: #555; font-weight: 600;
  padding: 4px 8px; background: var(--smoke); border-radius: 4px;
}

.card-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding-top: 12px; border-top: 1px solid var(--light-gray);
}

.agent-row { display: flex; align-items: center; gap: 8px; }
.agent-avatar {
  width: 28px; height: 28px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-weight: 700; font-size: 12px;
  display: flex; align-items: center; justify-content: center;
}
.agent-name { font-size: 12px; color: #555; font-weight: 600; }
.verified-tag {
  font-size: 11px; color: white; background: #15803d;
  border-radius: 50%; width: 16px; height: 16px;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700;
}

.btn-details {
  font-size: 13px; font-weight: 700; color: var(--gold);
  text-decoration: none; transition: color 0.2s;
}
.btn-details:hover { color: var(--gold-dark); }

/* Pagination */
.pagination {
  display: flex; gap: 8px; justify-content: center; align-items: center; flex-wrap: wrap;
}
.page-btn {
  padding: 8px 14px; border: 1px solid var(--light-gray); border-radius: 8px;
  background: white; color: var(--black); cursor: pointer; font-size: 13px;
  font-weight: 600; transition: all 0.2s;
}
.page-btn:hover:not(:disabled) { border-color: var(--gold); color: var(--gold); }
.page-btn.active { background: var(--gold); border-color: var(--gold); color: var(--black); font-weight: 700; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* Toast */
.toast {
  position: fixed; bottom: 24px; right: 24px;
  padding: 14px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;
  color: white; box-shadow: 0 4px 16px rgba(0,0,0,0.15); z-index: 9999;
}
.toast.success { background: #15803d; }
.toast.error { background: #b91c1c; }
.toast.info { background: var(--black); }
.toast-enter-active, .toast-leave-active { transition: all 0.3s; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }
</style>
