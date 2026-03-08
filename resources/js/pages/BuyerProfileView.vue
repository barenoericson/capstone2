<template>
  <div class="dashboard-wrapper">
    <!-- Agent Sidebar -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
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
          <router-link to="/agent/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
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
          <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
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
              <div class="cover-pattern"></div>
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
              <div class="profile-head">
                <div>
                  <h1 class="buyer-name">{{ buyer.name }}</h1>
                  <div class="buyer-tags">
                    <span class="buyer-tag">Buyer</span>
                    <span class="buyer-tag outline" v-if="buyer.city || buyer.province">
                      📍 {{ [buyer.city, buyer.province].filter(Boolean).join(', ') }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Info grid -->
              <div class="info-grid">
                <div class="info-item">
                  <span class="info-icon">📧</span>
                  <div>
                    <span class="info-label">Email</span>
                    <p class="info-value">{{ buyer.email }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="buyer.city || buyer.province">
                  <span class="info-icon">📍</span>
                  <div>
                    <span class="info-label">Location</span>
                    <p class="info-value">
                      {{ [buyer.city, buyer.province].filter(Boolean).join(', ') }}
                    </p>
                  </div>
                </div>
                <div class="info-item">
                  <span class="info-icon">📅</span>
                  <div>
                    <span class="info-label">Member Since</span>
                    <p class="info-value">{{ formatDate(buyer.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="action-row">
                <router-link :to="`/conversations/${buyer.id}`" class="btn-chat">
                  💬 Open Chat
                </router-link>
              </div>
            </div>
          </div>

          <!-- Quick note for agent -->
          <div class="note-box">
            <div class="note-icon">👁️</div>
            <div>
              <p class="note-title">Public Profile</p>
              <p class="note-text">You are viewing this buyer's public profile. Only basic information is visible for privacy.</p>
            </div>
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
      sidebarOpen: false,
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
:root {
  --gold: #FFD700;
  --gold-dark: #DAB600;
  --black: #100c08;
  --smoke: #f5f5f5;
  --gray: #e0e0e0;
  --white: #ffffff;
}

.dashboard-wrapper { display: flex; min-height: 100vh; background: var(--smoke); font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

/* =========================================================
   Sidebar
   ========================================================= */
.sidebar {
  width: 260px; min-height: 100vh;
  background: var(--white);
  border-right: 1px solid var(--gray);
  display: flex; flex-direction: column;
  position: fixed; left: 0; top: 0; height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 8px rgba(0,0,0,.07);
  z-index: 100;
}
.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--gray); }
.sidebar-logo { font-size: 22px; font-weight: 800; margin: 0; display: flex; }
.logo-realty { color: var(--black); }
.logo-ph { color: var(--gold); margin-left: 2px; }
.sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
.nav-section { margin-top: 16px; }
.section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #aaa; margin: 0 16px 6px; display: block; }
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 16px; margin: 0 8px; border-radius: 8px;
  color: var(--black); text-decoration: none; font-size: 14px; font-weight: 500;
  transition: all .2s;
}
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-weight: 700;
}
.nav-icon { font-size: 17px; min-width: 22px; text-align: center; }
.sidebar-footer { padding: 14px; border-top: 1px solid var(--gray); }
.user-card { display: flex; align-items: center; gap: 10px; background: var(--smoke); border-radius: 8px; padding: 10px; }
.user-avatar-lg {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 15px; flex-shrink: 0;
}
.user-info { flex: 1; min-width: 0; }
.user-name-card { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-role-card { font-size: 11px; color: #999; margin: 2px 0 0; }

/* =========================================================
   Main Content
   ========================================================= */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: var(--white); border-bottom: 1px solid var(--gray);
  padding: 0 28px; height: 64px;
  position: sticky; top: 0; z-index: 50;
  box-shadow: 0 2px 6px rgba(0,0,0,.06);
}
.topbar-content { display: flex; align-items: center; justify-content: space-between; height: 100%; }
.btn-back {
  background: none; border: none;
  color: var(--black); font-size: 15px; font-weight: 600;
  cursor: pointer; padding: 8px 12px; border-radius: 8px;
}
.btn-back:hover { background: var(--smoke); color: var(--gold); }
.btn-message {
  padding: 9px 20px; border-radius: 8px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-size: 14px; font-weight: 700;
  text-decoration: none; transition: transform .2s, box-shadow .2s;
}
.btn-message:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(230,174,13,.4); }

.content-area { padding: 28px; max-width: 700px; }

/* Loading */
.loading-state { text-align: center; padding: 80px; color: #999; }
.spinner { width: 40px; height: 40px; border: 4px solid var(--gray); border-top-color: var(--gold); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* =========================================================
   Profile Card
   ========================================================= */
.profile-card {
  background: var(--white); border-radius: 20px; overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,.08); margin-bottom: 20px;
}
.profile-cover {
  height: 160px;
  background: linear-gradient(135deg, var(--black) 0%, #2c2520 60%, #3a2f20 100%);
  display: flex; align-items: flex-end; padding: 0 36px;
  position: relative; overflow: hidden;
}
.cover-pattern {
  position: absolute; inset: 0;
  background: repeating-linear-gradient(
    45deg,
    transparent,
    transparent 30px,
    rgba(230,174,13,0.03) 30px,
    rgba(230,174,13,0.03) 60px
  );
}
.buyer-avatar-wrap { margin-bottom: -44px; z-index: 2; position: relative; }
.buyer-avatar {
  width: 110px; height: 110px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  border: 5px solid var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: 42px; font-weight: 700; color: var(--black); overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
.profile-body { padding: 56px 36px 36px; }
.profile-head { margin-bottom: 28px; }
.buyer-name { font-size: 28px; font-weight: 800; color: var(--black); margin: 0 0 10px; }
.buyer-tags { display: flex; gap: 8px; flex-wrap: wrap; }
.buyer-tag {
  padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
  background: rgba(230,174,13,0.1); color: var(--gold-dark);
  border: 1px solid rgba(230,174,13,0.3);
  text-transform: uppercase; letter-spacing: 0.5px;
}
.buyer-tag.outline {
  background: var(--smoke); color: #666;
  border-color: var(--gray); text-transform: none; font-weight: 600;
}

/* Info grid */
.info-grid { display: flex; flex-direction: column; gap: 14px; margin-bottom: 28px; }
.info-item {
  display: flex; gap: 14px; align-items: flex-start;
  padding: 14px 16px; border-radius: 12px;
  background: var(--smoke); border: 1px solid transparent;
  transition: border-color 0.2s;
}
.info-item:hover { border-color: rgba(230,174,13,0.2); }
.info-icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
.info-label {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.8px; color: #999; display: block; margin-bottom: 3px;
}
.info-value { font-size: 14px; color: #444; margin: 0; line-height: 1.5; }

.action-row { display: flex; gap: 12px; }
.btn-chat {
  padding: 12px 28px; border-radius: 10px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); text-decoration: none; font-size: 14px; font-weight: 700;
  transition: transform .2s, box-shadow .2s;
}
.btn-chat:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(230,174,13,.35); }

/* Note box */
.note-box {
  display: flex; gap: 14px; align-items: flex-start;
  background: var(--white); border: 1px solid var(--gray);
  border-radius: 14px; padding: 18px 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.note-icon { font-size: 24px; flex-shrink: 0; }
.note-title { font-size: 14px; font-weight: 700; color: var(--black); margin: 0 0 3px; }
.note-text { font-size: 13px; color: #777; margin: 0; line-height: 1.5; }

.empty-state { text-align: center; padding: 80px 20px; }
.empty-icon { font-size: 56px; margin-bottom: 16px; opacity: 0.5; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: var(--black); margin: 0 0 8px; }
.empty-state p { color: #666; }

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .sidebar { position: fixed; left: -280px; width: 280px; z-index: 1001; transition: left 0.3s ease; }
  .main-content { margin-left: 0; }
  .content-area { padding: 16px; max-width: 100%; }
  .topbar { padding: 0 16px; }
  .profile-cover { height: 120px; padding: 0 20px; }
  .buyer-avatar { width: 80px; height: 80px; font-size: 30px; }
  .buyer-avatar-wrap { margin-bottom: -36px; }
  .profile-body { padding: 44px 20px 24px; }
  .buyer-name { font-size: 22px; }
  .action-row { flex-direction: column; }
  .action-row .btn-chat { width: 100%; text-align: center; }
  .note-box { flex-direction: column; text-align: center; }
}

@media (max-width: 480px) {
  .profile-cover { height: 100px; padding: 0 14px; }
  .buyer-avatar { width: 64px; height: 64px; font-size: 24px; border-width: 3px; }
  .buyer-avatar-wrap { margin-bottom: -28px; }
  .profile-body { padding: 36px 14px 18px; }
  .buyer-name { font-size: 18px; }
  .info-item { padding: 10px 12px; }
  .content-area { padding: 12px; }
}
</style>
