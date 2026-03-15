<template>
  <div class="sp-layout">

    <!-- ═══════════════════ SIDEBAR ═══════════════════ -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link to="/dashboard" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/dashboard" exact class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </span>
          <span>Dashboard</span>
        </router-link>

        <div class="nav-group-label">Browse</div>
        <router-link to="/properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span>
          <span>Properties</span>
        </router-link>
        <router-link to="/saved-properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </span>
          <span>Saved</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Messages</span>
        </router-link>
        <router-link to="/viewings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <span>Viewings</span>
        </router-link>

        <div class="nav-group-label">Tools</div>
        <router-link to="/documents" class="nav-item" @click="sidebarOpen = false">
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
            <img v-if="profilePhotoUrl" :src="profilePhotoUrl" :alt="userName" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" />
            <span v-else>{{ userName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ userName }}</span>
            <span class="nav-user-role">Buyer</span>
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

    <!-- ═══════════════════ MAIN ═══════════════════ -->
    <div class="main-wrapper">

      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="sidebarOpen = !sidebarOpen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <span class="topbar-title">Saved Properties</span>
        </div>
        <div class="topbar-right">
          <span class="count-chip" v-if="!loading">{{ properties.length }} saved</span>
          <router-link to="/properties" class="tb-cta">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Browse More
          </router-link>
        </div>
      </header>

      <div class="content-area">
        <div class="content-inner">

          <!-- Loading -->
          <div v-if="loading" class="state-center">
            <div class="spinner"></div>
            <p class="state-txt">Loading your saved properties...</p>
          </div>

          <!-- Empty -->
          <div v-else-if="properties.length === 0" class="state-center">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--s300)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            <h3 class="state-head">No saved properties yet</h3>
            <p class="state-txt">Browse properties and save the ones you love.</p>
            <router-link to="/properties" class="btn-cta">Discover Properties →</router-link>
          </div>

          <!-- Grid -->
          <div v-else>
            <p class="grid-meta">{{ properties.length }} propert{{ properties.length === 1 ? 'y' : 'ies' }} saved</p>
            <div class="prop-grid">
              <div v-for="property in properties" :key="property.id" class="prop-card">
                <div class="card-img-wrap">
                  <img v-if="getCoverPhoto(property)" :src="getCoverPhoto(property)" :alt="property.title" class="card-img" />
                  <div v-else class="card-img-empty">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="var(--s300)" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>

                  <div class="img-overlay">
                    <router-link :to="`/property/${property.id}`" class="btn-view-overlay">View Details</router-link>
                  </div>

                  <button class="btn-unsave" @click="unsave(property)" title="Remove">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#ef4444" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                  </button>

                  <div class="price-chip">₱{{ formatPrice(property.price) }}</div>
                  <div v-if="property.featured" class="featured-chip">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Featured
                  </div>
                </div>

                <div class="card-body">
                  <div class="type-row">
                    <span class="type-chip">{{ formatType(property.property_type) }}</span>
                    <span class="status-chip" :class="property.status">{{ property.status }}</span>
                  </div>
                  <h3 class="card-title">{{ property.title }}</h3>
                  <p class="card-loc">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ property.city }}, {{ property.province }}
                  </p>
                  <div class="card-specs">
                    <span v-if="property.bedrooms" class="spec-item">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
                      {{ property.bedrooms }} bed
                    </span>
                    <span v-if="property.bathrooms" class="spec-item">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/><line x1="10" y1="5" x2="8" y2="7"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
                      {{ property.bathrooms }} bath
                    </span>
                    <span v-if="property.area_sqm" class="spec-item">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="1"/></svg>
                      {{ property.area_sqm }} sqm
                    </span>
                  </div>
                  <div class="card-foot">
                    <div class="agent-row" v-if="property.agent">
                      <div class="agent-av">{{ (property.agent.user?.name || 'A').charAt(0).toUpperCase() }}</div>
                      <span class="agent-nm">{{ property.agent.user?.name || 'Agent' }}</span>
                    </div>
                    <div class="foot-actions">
                      <router-link v-if="property.agent" :to="`/conversations/${property.agent.user_id || property.agent.user?.id}`" class="btn-msg" title="Message">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                      </router-link>
                      <router-link :to="`/property/${property.id}`" class="btn-details">View →</router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Toast -->
    <transition name="toast">
      <div v-if="toast.show" :class="['toast', toast.type]">{{ toast.message }}</div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'SavedProperties',

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
      const map = { house: 'House', condo: 'Condo', apartment: 'Apartment', commercial: 'Commercial', lot: 'Lot' };
      return map[type] || type;
    },

    async loadSavedProperties() {
      this.loading = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/saved-properties`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) this.properties = data.data?.data || data.data || [];
      } catch (e) {
        this.showToast('Failed to load saved properties', 'error');
      } finally {
        this.loading = false;
      }
    },

    async unsave(property) {
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/properties/${property.id}/save`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          this.properties = this.properties.filter(p => p.id !== property.id);
          this.showToast('Removed from saved properties', 'info');
        }
      } catch (e) {
        this.showToast('Failed to remove property', 'error');
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
    this.userName = user.name || 'Buyer';
    if (user.profile_photo_path) this.profilePhotoUrl = `${this.apiUrl}/storage/${user.profile_photo_path}`;
    this.loadSavedProperties();
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@700;800&family=Inter:wght@400;500;600;700&display=swap');

/* ── TOKENS ── */
.sp-layout {
  --primary:  #0B1C39;
  --primary2: #102445;
  --primary3: #1a3158;
  --accent:   #D89B0F;
  --accent2:  #E5B332;
  --accent3:  #B07A08;
  --bg:       #EDF0F2;
  --white:    #FFFFFF;
  --sw:       242px;
  --th:       56px;
  --fd:       'Outfit','Inter',-apple-system,sans-serif;
  --fb:       'Inter',-apple-system,sans-serif;
  --s50:  #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s800: #292524; --s900: #1C1917;
  display: flex; min-height: 100vh;
  background: var(--bg); font-family: var(--fb);
  -webkit-font-smoothing: antialiased;
}

/* ══ SIDEBAR ══ */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--primary); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; }
.logo-ph { background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
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
.nav-item.router-link-exact-active {
  background: linear-gradient(90deg, rgba(216,155,15,0.14) 0%, rgba(216,155,15,0.03) 100%);
  color: var(--accent2);
}
.nav-item.router-link-exact-active::before {
  content: ''; position: absolute; left: 0; top: 7px; bottom: 7px;
  width: 3px; background: var(--accent); border-radius: 0 3px 3px 0;
}
.nav-item.router-link-exact-active .nav-icon-wrap { color: var(--accent); }

.nav-group-label {
  font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
  color: rgba(255,255,255,0.22); padding: 14px 12px 5px;
}
.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }

.sidebar-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 10px 8px; }
.sidebar-bottom { flex-shrink: 0; padding: 0 10px 14px; }

.nav-user { gap: 10px; padding: 8px 10px; }
.nav-user:hover, .nav-user.router-link-exact-active { background: rgba(216,155,15,0.10); color: var(--accent2); }
.nav-av {
  width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
  background: linear-gradient(135deg, var(--accent), var(--accent3));
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 11px; color: #fff;
}
.nav-user-info { flex: 1; min-width: 0; }
.nav-user-name { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nav-user-role  { display: block; font-size: 10.5px; color: rgba(255,255,255,0.38); margin-top: 1px; }
.nav-logout { color: rgba(255,255,255,0.30); }
.nav-logout:hover { background: rgba(239,68,68,0.10); color: #f87171; }

.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 90; backdrop-filter: blur(2px); }

/* ══ MAIN ══ */
.main-wrapper { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* ── Topbar ── */
.topbar {
  background: var(--white); position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 28px; height: var(--th); border-bottom: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.topbar-left  { display: flex; align-items: center; gap: 10px; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.hamburger { display: none; width: 34px; height: 34px; border-radius: 7px; border: 1px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s600); align-items: center; justify-content: center; }
.topbar-title { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--primary); letter-spacing: -0.3px; }

.count-chip {
  background: rgba(216,155,15,0.08); border: 1px solid rgba(216,155,15,0.2);
  color: var(--s700); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
}
.tb-cta {
  display: flex; align-items: center; gap: 6px; padding: 7px 15px;
  background: linear-gradient(135deg, var(--accent), var(--accent3));
  color: var(--white); border-radius: 50px; font-size: 12.5px; font-weight: 600;
  text-decoration: none; transition: all .22s;
  box-shadow: 0 2px 8px rgba(216,155,15,0.30);
}
.tb-cta:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(216,155,15,0.40); }

/* ── Content ── */
.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 1200px; margin: 0 auto; padding: 28px 30px 48px; }

/* States */
.state-center { text-align: center; padding: 80px 20px; display: flex; flex-direction: column; align-items: center; gap: 14px; }
.state-head { font-family: var(--fd); font-size: 20px; font-weight: 700; color: var(--primary); margin: 0; }
.state-txt  { font-size: 13.5px; color: var(--s400); margin: 0; }
.spinner { width: 36px; height: 36px; border-radius: 50%; border: 3px solid var(--s200); border-top-color: var(--accent); animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.btn-cta {
  display: inline-block; padding: 10px 24px; background: var(--primary); color: var(--white);
  border-radius: 9px; font-weight: 700; font-size: 13.5px; text-decoration: none; transition: all 0.2s;
}
.btn-cta:hover { background: var(--primary2); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(40,53,62,0.22); }

.grid-meta { font-size: 12.5px; color: var(--s400); margin: 0 0 18px; }

/* ── Property Grid ── */
.prop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 20px; }

.prop-card {
  background: var(--white); border-radius: 14px; overflow: hidden;
  border: 1px solid var(--s200); transition: box-shadow 0.22s, transform 0.22s;
}
.prop-card:hover { box-shadow: 0 12px 36px rgba(40,53,62,0.10); transform: translateY(-4px); border-color: rgba(216,155,15,0.3); }

.card-img-wrap { position: relative; height: 210px; overflow: hidden; background: var(--s100); }
.card-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
.prop-card:hover .card-img { transform: scale(1.05); }
.card-img-empty { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }

.img-overlay {
  position: absolute; inset: 0; background: rgba(40,53,62,0.5);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity 0.25s;
}
.prop-card:hover .img-overlay { opacity: 1; }
.btn-view-overlay {
  padding: 9px 20px; background: var(--white); color: var(--primary);
  border-radius: 8px; font-weight: 700; font-size: 13px; text-decoration: none; transition: background 0.2s;
}
.btn-view-overlay:hover { background: var(--accent2); }

.btn-unsave {
  position: absolute; top: 10px; left: 10px;
  background: var(--white); border: none; border-radius: 50%;
  width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
  cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.14); transition: transform 0.18s;
}
.btn-unsave:hover { transform: scale(1.18); }

.price-chip {
  position: absolute; bottom: 10px; left: 10px;
  background: linear-gradient(135deg, var(--primary), var(--primary3));
  color: var(--accent2); padding: 4px 11px; border-radius: 7px;
  font-weight: 700; font-size: 12.5px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.featured-chip {
  position: absolute; top: 10px; right: 10px;
  background: rgba(255,255,255,0.92); color: var(--accent);
  padding: 3px 9px; border-radius: 20px; font-weight: 700; font-size: 10.5px;
  display: flex; align-items: center; gap: 4px;
}

/* Card body */
.card-body { padding: 15px 17px; }
.type-row { display: flex; gap: 7px; margin-bottom: 9px; align-items: center; }
.type-chip { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--accent); background: rgba(216,155,15,0.08); padding: 3px 8px; border-radius: 4px; }
.status-chip { font-size: 10px; font-weight: 700; text-transform: capitalize; padding: 3px 8px; border-radius: 4px; }
.status-chip.active, .status-chip.available { color: #15803d; background: #dcfce7; }
.status-chip.sold    { color: #b91c1c; background: #fee2e2; }
.status-chip.pending { color: #b45309; background: #fef3c7; }

.card-title { font-family: var(--fd); font-size: 14.5px; font-weight: 700; color: var(--primary); margin: 0 0 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card-loc   { font-size: 12px; color: var(--s400); margin: 0 0 11px; display: flex; align-items: center; gap: 4px; }

.card-specs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 13px; }
.spec-item  { display: flex; align-items: center; gap: 4px; font-size: 11.5px; color: var(--s500); font-weight: 500; padding: 3px 8px; background: var(--s50); border-radius: 5px; }

.card-foot  { display: flex; align-items: center; justify-content: space-between; padding-top: 11px; border-top: 1px solid var(--s100); }
.agent-row  { display: flex; align-items: center; gap: 7px; }
.agent-av   {
  width: 24px; height: 24px; border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), var(--primary3));
  color: var(--accent2); font-weight: 700; font-size: 10px;
  display: flex; align-items: center; justify-content: center;
}
.agent-nm  { font-size: 12px; color: var(--s500); font-weight: 600; }

.foot-actions { display: flex; gap: 8px; align-items: center; }
.btn-msg {
  width: 28px; height: 28px; border-radius: 7px; background: var(--s100);
  display: flex; align-items: center; justify-content: center;
  text-decoration: none; color: var(--s500); transition: all 0.18s;
}
.btn-msg:hover { background: rgba(216,155,15,0.1); color: var(--accent); }
.btn-details { font-size: 12.5px; font-weight: 700; color: var(--accent); text-decoration: none; transition: color 0.18s; }
.btn-details:hover { color: var(--primary); }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 9999; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
.toast.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.toast.error   { background: #fff5f5; color: #991b1b; border: 1px solid #fecaca; }
.toast.info    { background: rgba(40,53,62,0.05); color: var(--primary); border: 1px solid rgba(40,53,62,0.1); }
.toast-enter-active, .toast-leave-active { transition: all 0.3s; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }

/* ── Responsive ── */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .hamburger { display: flex; }
  .main-wrapper { margin-left: 0; }
  .topbar { padding: 0 16px; }
  .topbar-title { font-size: 14px; }
  .count-chip { display: none; }
  .content-inner { padding: 16px; }
  .prop-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 14px; }
  .card-img-wrap { height: 180px; }
  .toast { left: 16px; right: 16px; bottom: 16px; }
}
@media (max-width: 480px) {
  .prop-grid { grid-template-columns: 1fr; }
  .content-inner { padding: 12px; }
}
</style>
