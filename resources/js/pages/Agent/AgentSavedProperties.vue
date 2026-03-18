<template>
  <div class="saved-wrapper">
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
        <router-link to="/agent/saved-properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </span>
          <span>My Wallet</span>
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

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
            <h1 class="page-title">My Wallet</h1>
            <p class="page-sub">Properties you've saved for reference</p>
          </div>
          <div class="topbar-right">
            <span class="count-badge" v-if="!loading">
              {{ properties.length }} saved
            </span>
            <router-link to="/properties" class="btn-browse">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              Browse Properties
            </router-link>
          </div>
        </div>
      </nav>

      <div class="content-area">
        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading your saved properties...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="properties.length === 0" class="empty-state">
          <div class="empty-icon-svg">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <h3>No Saved Properties Yet</h3>
          <p>Browse listings and tap the save button to add properties to your wallet.</p>
          <router-link to="/properties" class="btn-go-browse">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Discover Properties
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </router-link>
        </div>

        <!-- Properties Grid -->
        <div v-else>
          <div class="grid-header">
            <p class="grid-desc">{{ properties.length }} propert{{ properties.length === 1 ? 'y' : 'ies' }} saved</p>
          </div>

          <div class="properties-grid">
            <div
              v-for="property in properties"
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

                <div class="card-overlay">
                  <router-link :to="`/property/${property.id}`" class="btn-view">
                    👁️ View Details
                  </router-link>
                </div>

                <!-- Remove/Unsave button -->
                <button
                  class="btn-unsave"
                  @click="unsave(property)"
                  title="Remove from saved"
                >
                  ❤️
                </button>

                <div class="price-tag">₱{{ formatPrice(property.price) }}</div>
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
                  </div>
                  <div class="action-btns">
                    <router-link
                      v-if="property.agent"
                      :to="`/conversations/${property.agent.user_id || property.agent.user?.id}`"
                      class="btn-msg"
                    >
                      💬
                    </router-link>
                    <router-link :to="`/property/${property.id}`" class="btn-details">
                      View →
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
  name: 'AgentSavedProperties',

  data() {
    return {
      sidebarOpen: false,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      token: localStorage.getItem('auth_token') || '',
      userName: '',
      profilePhotoUrl: null,
      properties: [],
      loading: true,
      toast: { show: false, message: '', type: 'success' },
    };
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

    async loadSavedProperties() {
      this.loading = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/agent/saved-properties`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          this.properties = data.data?.data || data.data || [];
        }
      } catch (e) {
        console.error(e);
        this.showToast('Failed to load saved properties', 'error');
      } finally {
        this.loading = false;
      }
    },

    async unsave(property) {
      try {
        const res = await fetch(`${this.apiUrl}/api/agent/properties/${property.id}/save`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          this.properties = this.properties.filter(p => p.id !== property.id);
          this.showToast('Removed from My Wallet', 'info');
        }
      } catch (e) {
        this.showToast('Failed to remove property', 'error');
      }
    },

    loadProfilePhoto() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${this.apiUrl}/storage/${user.profile_photo_path}`;
        }
      } catch (e) {
        console.error('Error loading profile photo:', e);
      }
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, message, type };
      setTimeout(() => { this.toast.show = false; }, 3000);
    },

    logout() {
      localStorage.clear();
      this.$router.push('/');
    },
  },

  mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Agent';
    this.loadProfilePhoto();
    this.loadSavedProperties();
  },
};
</script>

<style scoped>
.saved-wrapper {
  display: flex; min-height: 100vh; background: var(--bg);
  font-family: 'Inter', sans-serif;
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

/* Main */
.main-content { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: white; border-bottom: 2px solid var(--s200);
  padding: 20px 32px; position: sticky; top: 0; z-index: 50;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.topbar-content { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.topbar-left { flex: 1; }
.page-title { font-size: 28px; font-weight: 800; color: var(--navy); margin: 0; font-family: 'Poppins', sans-serif; }
.page-sub { font-size: 13px; color: #999; margin: 4px 0 0; }
.topbar-right { display: flex; align-items: center; gap: 12px; }
.count-badge {
  font-size: 13px; font-weight: 700; color: var(--navy);
  background: #fdf5d0; border: 1px solid var(--gold); padding: 6px 14px; border-radius: 20px;
}
.btn-browse {
  padding: 10px 20px; background: linear-gradient(135deg, var(--gold), var(--gold3)); color: var(--navy);
  border-radius: 50px; font-weight: 700; font-size: 14px; text-decoration: none;
  transition: all 0.3s;
}
.btn-browse:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(230,174,13,0.3); }

/* Content */
.content-area { padding: 32px; flex: 1; }

.loading-state { text-align: center; padding: 80px; color: #999; }
.spinner {
  width: 40px; height: 40px; border: 4px solid var(--s200);
  border-top-color: var(--gold); border-radius: 50%;
  animation: spin 0.8s linear infinite; margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }

.empty-state { text-align: center; padding: 100px 20px; }
.empty-icon { font-size: 72px; margin-bottom: 20px; }
.empty-icon-svg { margin-bottom: 20px; }
.empty-state h3 { font-size: 24px; font-weight: 700; color: var(--navy); margin: 0 0 12px; }
.empty-state p { color: #999; font-size: 15px; margin-bottom: 28px; }
.btn-go-browse {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 13px 28px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: var(--navy); border-radius: 50px; font-weight: 700; font-size: 15px;
  text-decoration: none; transition: all 0.2s;
}
.btn-go-browse:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(230,174,13,0.3); }

.grid-header { margin-bottom: 20px; }
.grid-desc { font-size: 14px; color: #666; margin: 0; }

/* Properties Grid */
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.property-card {
  background: white; border-radius: 14px; overflow: hidden;
  border: 1px solid var(--s200); transition: all 0.3s;
}
.property-card:hover {
  box-shadow: 0 12px 32px rgba(0,0,0,0.1);
  border-color: var(--gold); transform: translateY(-6px);
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
  position: absolute; inset: 0; background: rgba(16,12,8,0.45);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity 0.3s;
}
.property-card:hover .card-overlay { opacity: 1; }
.btn-view {
  padding: 11px 22px; background: white; color: var(--navy);
  border-radius: 8px; font-weight: 700; font-size: 13px;
  text-decoration: none; transition: all 0.2s;
}
.btn-view:hover { background: var(--gold); }

.btn-unsave {
  position: absolute; top: 12px; left: 12px;
  background: white; border: none; border-radius: 50%;
  width: 36px; height: 36px; font-size: 16px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: transform 0.2s;
}
.btn-unsave:hover { transform: scale(1.15); background: #ffe0e0; }

.price-tag {
  position: absolute; bottom: 12px; left: 12px;
  background: var(--gold); color: var(--navy);
  padding: 6px 14px; border-radius: 6px; font-weight: 800; font-size: 13px;
}
.featured-badge {
  position: absolute; top: 12px; right: 12px;
  background: white; color: var(--gold);
  padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 11px;
}

.card-body { padding: 18px; }
.type-row { display: flex; gap: 8px; margin-bottom: 10px; }
.type-badge {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  color: var(--gold); background: #fdf5d0; padding: 4px 10px; border-radius: 4px;
}
.status-badge {
  font-size: 11px; font-weight: 700; text-transform: capitalize;
  padding: 4px 10px; border-radius: 4px;
}
.status-badge.available { color: #15803d; background: #dcfce7; }
.status-badge.sold { color: #b91c1c; background: #fee2e2; }
.status-badge.pending { color: #b45309; background: #fef3c7; }

.card-title {
  font-size: 16px; font-weight: 700; color: var(--navy); margin: 0 0 6px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.card-location { font-size: 13px; color: #888; margin: 0 0 12px; }
.card-specs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
.spec {
  font-size: 12px; color: #555; font-weight: 600;
  padding: 4px 8px; background: var(--s100); border-radius: 4px;
}

.card-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding-top: 12px; border-top: 1px solid var(--s200);
}
.agent-row { display: flex; align-items: center; gap: 8px; }
.agent-avatar {
  width: 28px; height: 28px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: var(--navy); font-weight: 700; font-size: 12px;
  display: flex; align-items: center; justify-content: center;
}
.agent-name { font-size: 12px; color: #555; font-weight: 600; }
.action-btns { display: flex; gap: 8px; align-items: center; }
.btn-msg {
  width: 32px; height: 32px; border-radius: 8px;
  background: var(--s100); display: flex; align-items: center; justify-content: center;
  text-decoration: none; font-size: 14px; transition: all 0.2s;
}
.btn-msg:hover { background: #fdf5d0; }
.btn-details {
  font-size: 13px; font-weight: 700; color: var(--gold);
  text-decoration: none; transition: color 0.2s;
}
.btn-details:hover { color: var(--gold3); }

/* Toast */
.toast {
  position: fixed; bottom: 24px; right: 24px;
  padding: 14px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;
  color: white; box-shadow: 0 4px 16px rgba(0,0,0,0.15); z-index: 9999;
}
.toast.success { background: #15803d; }
.toast.error { background: #b91c1c; }
.toast.info { background: var(--navy); }
.toast-enter-active, .toast-leave-active { transition: all 0.3s; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }

/* Responsive */
@media (max-width: 768px) {
  .sidebar { position: fixed; left: calc(-1 * var(--sw)); transition: left 0.3s; z-index: 1001; }
  .main-content { margin-left: 0 !important; }
  .topbar { padding: 16px; }
  .topbar-content { flex-direction: column; align-items: flex-start; }
  .page-title { font-size: 24px; }
  .content-area { padding: 16px; }
  .properties-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
}

@media (max-width: 480px) {
  .properties-grid { grid-template-columns: 1fr; }
}
</style>
