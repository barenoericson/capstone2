<template>
  <div class="discover-wrapper">
    <!-- Sidebar Overlay (mobile) -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>

    <!-- ═══════════════════════════════════════ -->
    <!-- SIDEBAR — Dark Navy                     -->
    <!-- ═══════════════════════════════════════ -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">Realty</span><span class="logo-link">Link</span><span class="logo-ph">PH</span>
        </h2>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/dashboard" class="nav-item">
          <span class="nav-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
          </span>
          <span class="nav-label">Dashboard</span>
        </router-link>

        <div class="nav-section">
          <h3 class="nav-section-title">Browse</h3>
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </span>
            <span class="nav-label">Saved</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="nav-section-title">Activity</h3>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </span>
            <span class="nav-label">Messages</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/documents" class="nav-item">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </span>
            <span class="nav-label">Documents</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="nav-section-title">Account</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <span class="nav-label">Profile</span>
          </router-link>
          <router-link to="/settings" class="nav-item">
            <span class="nav-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </span>
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

    <!-- ═══════════════════════════════════════ -->
    <!-- MAIN CONTENT                            -->
    <!-- ═══════════════════════════════════════ -->
    <main class="main-content">
      <!-- Topbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div>
              <h1 class="page-title">Discover Properties</h1>
              <p class="page-sub">Browse listings from verified agents</p>
            </div>
          </div>
          <div class="topbar-right">
            <span class="properties-count" v-if="!loading">{{ totalCount }} properties found</span>
          </div>
        </div>
      </nav>

      <!-- Filter Bar -->
      <div class="filter-bar">
        <div class="search-wrap">
          <svg class="search-icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by title, city, province..."
            class="search-input"
            @input="debouncedSearch"
          />
          <button v-if="searchQuery" @click="clearSearch" class="clear-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
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

        <!-- Empty State -->
        <div v-else-if="filteredProperties.length === 0" class="empty-state">
          <div class="empty-svg-wrap">
            <svg width="120" height="120" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="100" cy="100" r="100" fill="#F1F5F9"/>
              <path d="M60 130V95L100 65L140 95V130C140 133.314 137.314 136 134 136H66C62.686 136 60 133.314 60 130Z" fill="#CBD5E1" stroke="#94A3B8" stroke-width="3"/>
              <path d="M88 136V110H112V136" fill="#E2E8F0" stroke="#94A3B8" stroke-width="3"/>
              <path d="M52 96L100 60L148 96" stroke="#64748B" stroke-width="4" stroke-linecap="round"/>
              <circle cx="140" cy="140" r="24" fill="#0B1C39"/>
              <path d="M133 140L138 145L147 136" stroke="#D89B0F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>No properties found</h3>
          <p>Try adjusting your search filters to find what you're looking for.</p>
          <button @click="resetFilters" class="btn-reset-large">Clear All Filters</button>
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
              <div v-else class="card-image-placeholder">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </div>

              <!-- Dark gradient overlay (for price readability) -->
              <div class="card-img-overlay"></div>

              <!-- Hover Overlay -->
              <div class="card-overlay">
                <button
                  class="btn-save"
                  :class="{ saved: savedIds.includes(property.id) }"
                  @click.prevent="toggleSave(property)"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" :fill="savedIds.includes(property.id) ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                  {{ savedIds.includes(property.id) ? 'Saved' : 'Save' }}
                </button>
                <router-link :to="`/property/${property.id}`" class="btn-view">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  View
                </router-link>
              </div>

              <!-- Price pill -->
              <div class="price-tag">₱{{ formatPrice(property.price) }}</div>

              <!-- Badges -->
              <div class="card-badges">
                <span v-if="property.featured" class="card-badge badge-feat">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  Featured
                </span>
                <span v-if="property.virtual_tour_url" class="card-badge badge-360">360°</span>
              </div>
            </div>

            <!-- Body -->
            <div class="card-body">
              <div class="type-row">
                <span class="type-badge">{{ formatType(property.property_type) }}</span>
                <span class="status-badge" :class="property.status">{{ property.status }}</span>
              </div>
              <h3 class="card-title">{{ property.title }}</h3>
              <p class="card-location">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ property.city }}, {{ property.province }}
              </p>

              <div class="card-specs">
                <span v-if="property.bedrooms" class="spec">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
                  {{ property.bedrooms }}
                </span>
                <span v-if="property.bathrooms" class="spec">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3v2.25"/></svg>
                  {{ property.bathrooms }}
                </span>
                <span v-if="property.area_sqm" class="spec">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                  {{ property.area_sqm }}m²
                </span>
              </div>

              <div class="card-footer">
                <div class="agent-row" v-if="property.agent">
                  <div class="agent-avatar">
                    {{ (property.agent.user?.name || 'A').charAt(0).toUpperCase() }}
                  </div>
                  <span class="agent-name">{{ property.agent.user?.name || 'Agent' }}</span>
                  <span class="verified-tag" v-if="property.agent.is_verified">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  </span>
                </div>
                <router-link :to="`/property/${property.id}`" class="btn-details">
                  Details →
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
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
          </button>

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
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
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
      sidebarOpen: false,
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
            this.showToast('Saved to My Wallet!', 'success');
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
/* ─── CSS Variables (on wrapper — avoids Vue scoped :root bug) ─── */
.discover-wrapper {
  --navy:        #0B1C39;
  --navy-light:  #102445;
  --navy-mid:    #1a3158;
  --gold:        #D89B0F;
  --gold-dark:   #B07A08;
  --gold-light:  #E5B332;
  --gold-faded:  rgba(216, 155, 15, 0.10);
  --white:       #FFFFFF;
  --stone-50:    #FAFAF9;
  --stone-100:   #F5F4F2;
  --stone-200:   #E8E6E1;
  --stone-300:   #D6D3CE;
  --stone-400:   #A8A29E;
  --stone-500:   #78716C;
  --stone-600:   #57534E;
  --stone-800:   #292524;
  --radius:      12px;
  --radius-lg:   16px;
  --radius-pill: 9999px;
  --font-brand:  'Plus Jakarta Sans', 'Outfit', system-ui, sans-serif;
  --font-ui:     'Inter', system-ui, sans-serif;

  display: flex;
  min-height: 100vh;
  background: var(--stone-100);
  font-family: var(--font-ui);
}

/* ═══════════════════════════════════════
   SIDEBAR — Dark Navy
   ═══════════════════════════════════════ */
.sidebar {
  width: 256px;
  background: var(--navy);
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0; top: 0;
  height: 100vh;
  overflow-y: auto;
  z-index: 100;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
  transition: left 0.3s ease;
}

.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 99;
  backdrop-filter: blur(2px);
}

.sidebar-header {
  padding: 28px 20px 22px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.sidebar-logo {
  font-family: var(--font-brand);
  font-size: 20px;
  margin: 0;
  letter-spacing: -0.3px;
}

.logo-realty { font-weight: 400; color: rgba(255,255,255,0.85); }
.logo-link   { font-weight: 800; color: var(--white); }
.logo-ph     { font-weight: 800; color: var(--gold); }

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  overflow-y: auto;
}

.nav-section { margin-bottom: 4px; }

.nav-section-title {
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: rgba(255, 255, 255, 0.28);
  padding: 14px 8px 6px;
  margin: 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 10px 12px;
  margin-bottom: 2px;
  color: rgba(255, 255, 255, 0.55);
  text-decoration: none;
  border-radius: var(--radius);
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.06);
  color: rgba(255, 255, 255, 0.9);
}

.nav-item.router-link-active,
.nav-item.active {
  background: rgba(216, 155, 15, 0.14);
  color: var(--gold);
  font-weight: 600;
}

.nav-icon {
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.nav-label { flex: 1; }

.sidebar-footer {
  padding: 16px 12px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.user-card {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: var(--radius);
  padding: 10px 12px;
  border: 1px solid rgba(255, 255, 255, 0.06);
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
  flex-shrink: 0;
}

.user-info { flex: 1; min-width: 0; }

.user-name {
  font-size: 13px;
  font-weight: 700;
  color: rgba(255,255,255,0.9);
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-role {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.35);
  margin: 0;
}

/* ═══════════════════════════════════════
   MAIN CONTENT
   ═══════════════════════════════════════ */
.main-content {
  margin-left: 256px;
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* ─── Topbar ─── */
.topbar {
  background: var(--white);
  border-bottom: 1px solid var(--stone-200);
  padding: 0 32px;
  position: sticky;
  top: 0;
  z-index: 50;
  height: 72px;
  display: flex;
  align-items: center;
  box-shadow: 0 1px 0 rgba(0,0,0,0.04);
}

.topbar-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.hamburger-btn {
  display: none;
  background: none;
  border: 1.5px solid var(--stone-200);
  border-radius: 8px;
  padding: 7px;
  cursor: pointer;
  color: var(--stone-600);
  transition: all 0.2s;
  align-items: center;
  justify-content: center;
}

.hamburger-btn:hover {
  background: var(--stone-100);
  border-color: var(--stone-300);
}

.page-title {
  font-family: var(--font-brand);
  font-size: 22px;
  font-weight: 800;
  color: var(--navy);
  margin: 0;
  letter-spacing: -0.3px;
}

.page-sub {
  font-size: 12px;
  color: var(--stone-500);
  margin: 2px 0 0;
}

.properties-count {
  font-size: 13px;
  color: var(--stone-600);
  font-weight: 600;
  background: var(--stone-100);
  padding: 6px 14px;
  border-radius: var(--radius-pill);
  border: 1px solid var(--stone-200);
}

/* ─── Filter Bar ─── */
.filter-bar {
  background: var(--white);
  border-bottom: 1px solid var(--stone-200);
  padding: 12px 32px;
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
}

.search-wrap {
  position: relative;
  flex: 1;
  min-width: 220px;
}

.search-icon-svg {
  position: absolute;
  left: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--stone-400);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 10px 36px 10px 38px;
  border: 1.5px solid var(--stone-200);
  border-radius: 10px;
  font-size: 14px;
  font-family: var(--font-ui);
  background: var(--stone-50);
  color: var(--navy);
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.search-input:focus {
  border-color: var(--gold);
  background: var(--white);
  box-shadow: 0 0 0 3px rgba(216, 155, 15, 0.12);
}

.search-input::placeholder { color: var(--stone-400); }

.clear-btn {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--stone-400);
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s;
}

.clear-btn:hover {
  color: var(--stone-800);
  background: var(--stone-100);
}

.filter-select {
  padding: 10px 14px;
  border: 1.5px solid var(--stone-200);
  border-radius: 10px;
  font-size: 13px;
  font-family: var(--font-ui);
  background: var(--stone-50);
  color: var(--navy);
  cursor: pointer;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.filter-select:focus {
  border-color: var(--gold);
  box-shadow: 0 0 0 3px rgba(216, 155, 15, 0.12);
}

.btn-reset {
  padding: 10px 16px;
  background: none;
  border: 1.5px solid var(--stone-200);
  border-radius: 10px;
  font-size: 13px;
  font-family: var(--font-ui);
  color: var(--stone-500);
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-reset:hover {
  border-color: var(--gold);
  color: var(--gold-dark);
}

/* ─── Content Area ─── */
.content-area {
  padding: 32px;
  flex: 1;
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 80px;
  color: var(--stone-500);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--stone-200);
  border-top-color: var(--gold);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.empty-svg-wrap {
  margin-bottom: 24px;
  opacity: 0.9;
}

.empty-state h3 {
  font-family: var(--font-brand);
  font-size: 22px;
  font-weight: 700;
  color: var(--navy);
  margin: 0 0 8px;
}

.empty-state p {
  color: var(--stone-500);
  font-size: 14px;
  margin-bottom: 28px;
  max-width: 340px;
  line-height: 1.6;
}

.btn-reset-large {
  padding: 13px 28px;
  background: linear-gradient(135deg, var(--navy), var(--navy-light));
  border: none;
  border-radius: var(--radius-pill);
  font-size: 14px;
  font-weight: 700;
  font-family: var(--font-ui);
  color: var(--white);
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 16px rgba(11, 28, 57, 0.2);
}

.btn-reset-large:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(11, 28, 57, 0.3);
}

/* ═══════════════════════════════════════
   PROPERTIES GRID
   ═══════════════════════════════════════ */
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.property-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 1px solid var(--stone-200);
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.property-card:hover {
  box-shadow: 0 16px 40px rgba(0,0,0,0.10);
  border-color: rgba(216, 155, 15, 0.3);
  transform: translateY(-5px);
}

/* Card image */
.card-image-wrap {
  position: relative;
  height: 210px;
  overflow: hidden;
  background: var(--stone-100);
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.property-card:hover .card-image { transform: scale(1.06); }

.card-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--stone-400);
  background: var(--stone-100);
}

/* Dark gradient overlay for price pill readability */
.card-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(11, 28, 57, 0.65) 0%,
    rgba(11, 28, 57, 0.08) 50%,
    transparent 100%
  );
  pointer-events: none;
  z-index: 1;
}

/* Hover action overlay */
.card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(11, 28, 57, 0.52);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  opacity: 0;
  transition: opacity 0.3s;
  z-index: 3;
  backdrop-filter: blur(2px);
}

.property-card:hover .card-overlay { opacity: 1; }

.btn-save, .btn-view {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 9px 18px;
  border: none;
  border-radius: var(--radius);
  font-weight: 700;
  font-size: 13px;
  font-family: var(--font-ui);
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-save {
  background: rgba(255,255,255,0.95);
  color: var(--navy);
}

.btn-save:hover { background: var(--white); }
.btn-save.saved { background: #fee2e2; color: #b91c1c; }

.btn-view {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy);
}

.btn-view:hover {
  background: linear-gradient(135deg, var(--gold-light), var(--gold));
  transform: translateY(-1px);
}

/* Price pill */
.price-tag {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: linear-gradient(135deg, var(--navy), var(--navy-light));
  color: var(--gold);
  padding: 6px 14px;
  border-radius: 8px;
  font-weight: 800;
  font-size: 14px;
  font-family: var(--font-brand);
  letter-spacing: -0.3px;
  z-index: 2;
  box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  border: 1px solid rgba(216, 155, 15, 0.2);
}

/* Badges */
.card-badges {
  position: absolute;
  top: 10px;
  left: 10px;
  display: flex;
  gap: 5px;
  z-index: 2;
}

.card-badge {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: var(--radius-pill);
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.badge-feat {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy);
  box-shadow: 0 2px 8px rgba(216, 155, 15, 0.35);
}

.badge-360 {
  background: rgba(11, 28, 57, 0.85);
  color: var(--white);
  border: 1px solid rgba(255,255,255,0.2);
  backdrop-filter: blur(4px);
}

/* Card body */
.card-body { padding: 18px; }

.type-row {
  display: flex;
  gap: 8px;
  margin-bottom: 9px;
  align-items: center;
}

.type-badge {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--gold-dark);
  background: var(--gold-faded);
  padding: 4px 10px;
  border-radius: var(--radius-pill);
}

.status-badge {
  font-size: 10px;
  font-weight: 700;
  text-transform: capitalize;
  padding: 4px 10px;
  border-radius: var(--radius-pill);
}

.status-badge.active  { color: #15803d; background: #dcfce7; }
.status-badge.sold    { color: #b91c1c; background: #fee2e2; }
.status-badge.pending { color: #b45309; background: #fef3c7; }

.card-title {
  font-family: var(--font-brand);
  font-size: 16px;
  font-weight: 700;
  color: var(--navy);
  margin: 0 0 5px;
  line-height: 1.3;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  letter-spacing: -0.2px;
}

.card-location {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: var(--stone-500);
  margin: 0 0 12px;
}

.card-location svg {
  color: var(--stone-400);
  flex-shrink: 0;
}

.card-specs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}

.spec {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: var(--stone-600);
  font-weight: 600;
  background: var(--stone-100);
  padding: 4px 10px;
  border-radius: var(--radius-pill);
}

.spec svg { color: var(--stone-400); }

.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 12px;
  border-top: 1px solid var(--stone-100);
}

.agent-row {
  display: flex;
  align-items: center;
  gap: 7px;
}

.agent-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy);
  font-weight: 800;
  font-size: 11px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.agent-name {
  font-size: 12px;
  color: var(--stone-600);
  font-weight: 600;
}

.verified-tag {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #10B981;
  color: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.btn-details {
  font-size: 13px;
  font-weight: 700;
  color: var(--navy);
  text-decoration: none;
  transition: color 0.2s;
  letter-spacing: -0.1px;
}

.btn-details:hover { color: var(--gold-dark); }

/* ═══════════════════════════════════════
   PAGINATION
   ═══════════════════════════════════════ */
.pagination {
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  padding: 8px 0;
}

.page-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1.5px solid var(--stone-200);
  border-radius: 10px;
  background: var(--white);
  color: var(--navy);
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  font-family: var(--font-ui);
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  border-color: var(--gold);
  color: var(--gold-dark);
}

.page-btn.active {
  background: linear-gradient(135deg, var(--navy), var(--navy-light));
  border-color: var(--navy);
  color: var(--white);
  font-weight: 700;
}

.page-btn:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

/* ─── Toast ─── */
.toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  padding: 14px 24px;
  border-radius: var(--radius);
  font-size: 14px;
  font-weight: 600;
  font-family: var(--font-ui);
  color: var(--white);
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  z-index: 9999;
}

.toast.success { background: #059669; }
.toast.error   { background: #DC2626; }
.toast.info    { background: var(--navy); }
.toast-enter-active, .toast-leave-active { transition: all 0.3s; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }

/* ═══════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════ */
@media (max-width: 1024px) {
  .sidebar { width: 220px; }
  .main-content { margin-left: 220px; }
  .properties-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
  .content-area { padding: 24px; }
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: -280px;
    width: 280px;
    z-index: 1001;
  }

  .sidebar.open { left: 0; }

  .main-content { margin-left: 0; }

  .hamburger-btn { display: flex; }

  .topbar { padding: 0 16px; }

  .filter-bar {
    padding: 12px 16px;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .search-wrap {
    min-width: unset;
    width: 100%;
  }

  .content-area { padding: 16px; }

  .properties-grid { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
  .page-title { font-size: 18px; }
}
</style>
