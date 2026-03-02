<template>
  <div class="saved-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-section">
          <h3 class="section-title">Management</h3>
          <router-link to="/agent/dashboard" class="nav-item">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Overview</span>
          </router-link>
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/saved-properties" class="nav-item active">
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
          <div v-if="profilePhotoUrl" class="user-avatar">
            <img :src="profilePhotoUrl" :alt="userName" class="avatar-photo" />
          </div>
          <div v-else class="user-avatar">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name">{{ userName }}</p>
            <p class="user-role">Agent</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">My Wallet</h1>
            <p class="page-sub">Properties you've saved for reference</p>
          </div>
          <div class="topbar-right">
            <span class="count-badge" v-if="!loading">
              {{ properties.length }} saved
            </span>
            <router-link to="/properties" class="btn-browse">
              🏠 Browse Properties
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
          <div class="empty-icon">🏡</div>
          <h3>No Saved Properties Yet</h3>
          <p>Browse listings and tap the save button to add properties to your wallet.</p>
          <router-link to="/properties" class="btn-go-browse">
            Discover Properties →
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
:root {
  --gold: #FFD700;
  --gold-dark: #DAB600;
  --black: #100c08;
  --smoke: #f5f5f5;
  --light-gray: #e0e0e0;
}

.saved-wrapper {
  display: flex; min-height: 100vh; background: var(--smoke);
  font-family: 'Inter', sans-serif;
}

/* Sidebar */
.sidebar {
  width: 280px; background: white; border-right: 1px solid var(--light-gray);
  display: flex; flex-direction: column; position: fixed; left: 0; top: 0;
  height: 100vh; overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0,0,0,0.08); z-index: 100;
}
.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--light-gray); }
.sidebar-logo { font-size: 24px; font-weight: 800; margin: 0; font-family: 'Poppins', sans-serif; }
.logo-realty { color: var(--black); }
.logo-ph { color: var(--gold); }
.sidebar-nav { flex: 1; padding: 20px 0; overflow-y: auto; }
.nav-section { margin-bottom: 24px; }
.section-title {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.2px; color: #999; padding: 8px 16px; margin: 0;
}
.nav-item {
  display: flex; align-items: center; gap: 12px; padding: 12px 16px;
  margin: 0 8px; color: var(--black); text-decoration: none; border-radius: 8px;
  font-size: 14px; font-weight: 500; transition: all 0.3s;
  width: calc(100% - 16px);
}
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-weight: 600;
}
.nav-icon { font-size: 18px; min-width: 24px; text-align: center; }
.nav-label { flex: 1; }
.sidebar-footer { padding: 16px; border-top: 1px solid var(--light-gray); }
.user-card { display: flex; align-items: center; gap: 12px; background: var(--smoke); border-radius: 8px; padding: 12px; }
.user-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 16px; flex-shrink: 0; overflow: hidden;
}
.avatar-photo { width: 100%; height: 100%; object-fit: cover; }
.user-info { flex: 1; min-width: 0; }
.user-name { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-role { font-size: 11px; color: #999; margin: 4px 0 0 0; text-transform: uppercase; letter-spacing: 0.5px; }

/* Main */
.main-content { margin-left: 280px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: white; border-bottom: 2px solid var(--light-gray);
  padding: 20px 32px; position: sticky; top: 0; z-index: 50;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.topbar-content { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.topbar-left { flex: 1; }
.page-title { font-size: 28px; font-weight: 800; color: var(--black); margin: 0; font-family: 'Poppins', sans-serif; }
.page-sub { font-size: 13px; color: #999; margin: 4px 0 0; }
.topbar-right { display: flex; align-items: center; gap: 12px; }
.count-badge {
  font-size: 13px; font-weight: 700; color: var(--black);
  background: #fdf5d0; border: 1px solid var(--gold); padding: 6px 14px; border-radius: 20px;
}
.btn-browse {
  padding: 10px 20px; background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--black);
  border-radius: 8px; font-weight: 700; font-size: 14px; text-decoration: none;
  transition: all 0.3s;
}
.btn-browse:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(230,174,13,0.3); }

/* Content */
.content-area { padding: 32px; flex: 1; }

.loading-state { text-align: center; padding: 80px; color: #999; }
.spinner {
  width: 40px; height: 40px; border: 4px solid var(--light-gray);
  border-top-color: var(--gold); border-radius: 50%;
  animation: spin 0.8s linear infinite; margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }

.empty-state { text-align: center; padding: 100px 20px; }
.empty-icon { font-size: 72px; margin-bottom: 20px; }
.empty-state h3 { font-size: 24px; font-weight: 700; color: var(--black); margin: 0 0 12px; }
.empty-state p { color: #999; font-size: 15px; margin-bottom: 28px; }
.btn-go-browse {
  display: inline-block; padding: 13px 28px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); border-radius: 10px; font-weight: 700; font-size: 15px;
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
  border: 1px solid var(--light-gray); transition: all 0.3s;
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
  padding: 11px 22px; background: white; color: var(--black);
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
  background: var(--gold); color: var(--black);
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
  font-size: 16px; font-weight: 700; color: var(--black); margin: 0 0 6px;
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
.action-btns { display: flex; gap: 8px; align-items: center; }
.btn-msg {
  width: 32px; height: 32px; border-radius: 8px;
  background: var(--smoke); display: flex; align-items: center; justify-content: center;
  text-decoration: none; font-size: 14px; transition: all 0.2s;
}
.btn-msg:hover { background: #fdf5d0; }
.btn-details {
  font-size: 13px; font-weight: 700; color: var(--gold);
  text-decoration: none; transition: color 0.2s;
}
.btn-details:hover { color: var(--gold-dark); }

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

/* Responsive */
@media (max-width: 768px) {
  .sidebar { position: fixed; left: -280px; transition: left 0.3s; z-index: 1001; }
  .main-content { margin-left: 0; }
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
