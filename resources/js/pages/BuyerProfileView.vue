<template>
  <div class="dashboard-wrapper">
    <!-- Agent Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>
      <nav class="sidebar-nav">
        <router-link to="/agent/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Overview</span>
        </router-link>
        <div class="nav-section">
          <h3 class="section-title">Management</h3>
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
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
        </div>
      </nav>
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg">{{ myName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ myName }}</p>
            <p class="user-role-card">Agent</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <button @click="$router.back()" class="btn-back">← Back</button>
          <div class="topbar-right">
            <router-link
              v-if="buyer"
              :to="`/conversations/${buyer.id}`"
              class="btn-message"
            >
              💬 Message Buyer
            </router-link>
          </div>
        </div>
      </nav>

      <div class="content-area">
        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading profile...</p>
        </div>

        <template v-else-if="buyer">
          <!-- Profile Card -->
          <div class="profile-card">
            <div class="profile-cover">
              <div class="buyer-avatar-wrap">
                <div
                  class="buyer-avatar"
                  :style="buyer.profile_photo_url
                    ? `background-image:url(${buyer.profile_photo_url});background-size:cover;background-position:center;`
                    : ''"
                >
                  <span v-if="!buyer.profile_photo_url">{{ buyer.name.charAt(0).toUpperCase() }}</span>
                </div>
              </div>
            </div>

            <div class="profile-body">
              <h1 class="buyer-name">{{ buyer.name }}</h1>
              <p class="buyer-role-tag">🏷️ Buyer</p>

              <div class="info-grid">
                <div class="info-row">
                  <span class="info-label">📧 Email</span>
                  <span class="info-value">{{ buyer.email }}</span>
                </div>
                <div class="info-row" v-if="buyer.city || buyer.province">
                  <span class="info-label">📍 Location</span>
                  <span class="info-value">
                    {{ [buyer.city, buyer.province].filter(Boolean).join(', ') }}
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">📅 Member Since</span>
                  <span class="info-value">{{ formatDate(buyer.created_at) }}</span>
                </div>
              </div>

              <div class="action-row">
                <router-link :to="`/conversations/${buyer.id}`" class="btn-chat">
                  💬 Open Chat
                </router-link>
              </div>
            </div>
          </div>

          <!-- Quick note for agent -->
          <div class="note-box">
            <p>👁️ You are viewing this buyer's public profile. Only basic information is visible.</p>
          </div>
        </template>

        <div v-else-if="!loading" class="empty-state">
          <div class="empty-icon">👤</div>
          <h3>Profile Not Found</h3>
          <p>This buyer's profile could not be loaded.</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'BuyerProfileView',

  data() {
    return {
      apiUrl: '',
      token: '',
      myName: '',
      loading: true,
      buyer: null,
    };
  },

  mounted() {
    this.apiUrl = localStorage.getItem('api_url') || '';
    this.token = localStorage.getItem('auth_token') || '';
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.myName = user.name || 'Agent';
    this.loadProfile();
  },

  methods: {
    async loadProfile() {
      this.loading = true;
      try {
        const userId = this.$route.params.userId;
        const res = await fetch(`${this.apiUrl}/api/users/${userId}/profile`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          this.buyer = data.user;
        }
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },

    formatDate(iso) {
      if (!iso) return '—';
      return new Date(iso).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'long', day: 'numeric',
      });
    },
  },
};
</script>

<style scoped>
.dashboard-wrapper { display: flex; min-height: 100vh; background: #f0f2f5; font-family: 'Inter', sans-serif; }

/* Sidebar */
.sidebar {
  width: 260px; min-height: 100vh;
  background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  display: flex; flex-direction: column;
  position: fixed; left: 0; top: 0; bottom: 0; z-index: 100;
  box-shadow: 4px 0 20px rgba(0,0,0,0.3);
}
.sidebar-header { padding: 24px 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.sidebar-logo { font-size: 22px; font-weight: 800; margin: 0; }
.logo-realty { color: #fff; }
.logo-ph { color: #e94560; }
.sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
.nav-section { margin-top: 20px; }
.section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); margin: 0 8px 8px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; margin-bottom: 4px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
.nav-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-item.active { background: linear-gradient(135deg, #e94560, #c23152); color: #fff; }
.nav-icon { font-size: 18px; }
.sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-card { display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.08); border-radius: 12px; }
.user-avatar-lg { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #e94560, #c23152); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 16px; flex-shrink: 0; }
.user-name-card { font-size: 13px; font-weight: 600; color: #fff; margin: 0; }
.user-role-card { font-size: 11px; color: rgba(255,255,255,0.5); margin: 0; }

/* Main */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
.topbar { background: #fff; border-bottom: 1px solid #e8ecf0; padding: 0 28px; height: 64px; position: sticky; top: 0; z-index: 50; }
.topbar-content { display: flex; align-items: center; justify-content: space-between; height: 100%; }
.btn-back { background: none; border: none; color: #0f3460; font-size: 15px; font-weight: 600; cursor: pointer; padding: 8px 12px; border-radius: 8px; }
.btn-back:hover { background: #f0f2f5; }
.btn-message { padding: 9px 20px; border-radius: 10px; background: linear-gradient(135deg, #0f3460, #163c72); color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; }

.content-area { padding: 28px; max-width: 680px; }

.loading-state { text-align: center; padding: 80px; color: #666; }
.spinner { width: 40px; height: 40px; border: 4px solid #e0e0e0; border-top-color: #0f3460; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Profile Card */
.profile-card { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 20px; }
.profile-cover {
  height: 140px;
  background: linear-gradient(135deg, #0f3460, #1a1a2e);
  display: flex; align-items: flex-end; padding: 0 32px;
}
.buyer-avatar-wrap { margin-bottom: -40px; }
.buyer-avatar {
  width: 100px; height: 100px; border-radius: 50%;
  background: linear-gradient(135deg, #0f3460, #1a1a2e);
  border: 4px solid #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 40px; font-weight: 700; color: #fff; overflow: hidden;
}
.profile-body { padding: 52px 32px 32px; }
.buyer-name { font-size: 26px; font-weight: 800; color: #1a1a2e; margin: 0 0 4px; }
.buyer-role-tag { font-size: 14px; color: #666; margin: 0 0 24px; }

.info-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px; }
.info-row { display: flex; gap: 16px; align-items: baseline; }
.info-label { font-size: 13px; font-weight: 600; color: #999; min-width: 120px; flex-shrink: 0; }
.info-value { font-size: 14px; color: #333; }

.action-row { display: flex; gap: 12px; }
.btn-chat { padding: 11px 24px; border-radius: 10px; background: linear-gradient(135deg, #e94560, #c23152); color: #fff; text-decoration: none; font-size: 14px; font-weight: 700; }

.note-box { background: #e8f4fd; border: 1px solid #bde0f5; border-radius: 12px; padding: 14px 18px; color: #0a5c8a; font-size: 13px; }

.empty-state { text-align: center; padding: 80px 20px; }
.empty-icon { font-size: 56px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a2e; margin: 0 0 8px; }
.empty-state p { color: #666; }
</style>
