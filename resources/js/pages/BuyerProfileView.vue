<template>
  <div class="ad-layout">

    <!-- ═══════════════════════════════════
         SIDEBAR  (matches AgentDashboard)
    ═══════════════════════════════════ -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link to="/agent/dashboard" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/agent/dashboard" class="nav-item" @click="sidebarOpen = false">
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
            <img v-if="myPhotoUrl" :src="myPhotoUrl" :alt="myName" />
            <span v-else>{{ myName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ myName }}</span>
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

    <!-- ═══════════════════════════════════
         MAIN
    ═══════════════════════════════════ -->
    <div class="main-wrapper">

      <!-- Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="sidebarOpen = !sidebarOpen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <button @click="$router.back()" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back
          </button>
        </div>
        <div class="topbar-right">
          <router-link v-if="buyer" :to="`/conversations/${buyer.id}`" class="btn-message-buyer">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Message Buyer
          </router-link>
        </div>
      </header>

      <!-- Content -->
      <div class="content-area">

        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading profile...</p>
        </div>

        <template v-else-if="buyer">
          <!-- Profile Hero Card -->
          <div class="profile-card" style="animation: fadeUp .5s ease both">
            <div class="profile-cover">
              <div class="cover-pattern"></div>
              <div class="cover-rings">
                <div class="ring r1"></div>
                <div class="ring r2"></div>
              </div>
            </div>

            <div class="profile-body">
              <div class="avatar-row">
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
                <div class="profile-head">
                  <h1 class="buyer-name">{{ buyer.name }}</h1>
                  <div class="buyer-tags">
                    <span class="buyer-tag role-tag">Buyer</span>
                    <span class="buyer-tag loc-tag" v-if="buyer.city || buyer.province">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                      {{ [buyer.city, buyer.province].filter(Boolean).join(', ') }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Info grid -->
              <div class="info-grid">
                <div class="info-item">
                  <div class="info-icon-wrap ii-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  </div>
                  <div>
                    <span class="info-label">Email</span>
                    <p class="info-value">{{ buyer.email }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="buyer.phone">
                  <div class="info-icon-wrap ii-green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  </div>
                  <div>
                    <span class="info-label">Phone</span>
                    <p class="info-value">{{ buyer.phone }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="buyer.city || buyer.province">
                  <div class="info-icon-wrap ii-red">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  </div>
                  <div>
                    <span class="info-label">Location</span>
                    <p class="info-value">{{ [buyer.city, buyer.province].filter(Boolean).join(', ') }}</p>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon-wrap ii-gold">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  </div>
                  <div>
                    <span class="info-label">Member Since</span>
                    <p class="info-value">{{ formatDate(buyer.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="action-row">
                <router-link :to="`/conversations/${buyer.id}`" class="btn-action btn-primary">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                  Open Chat
                </router-link>
              </div>
            </div>
          </div>

          <!-- Privacy note -->
          <div class="note-box" style="animation: fadeUp .5s ease .1s both">
            <div class="note-icon-wrap">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </div>
            <div>
              <p class="note-title">Public Profile</p>
              <p class="note-text">You are viewing this buyer's public profile. Only basic information is visible for privacy.</p>
            </div>
          </div>
        </template>

        <div v-else-if="!loading" class="empty-state">
          <div class="empty-icon-wrap">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <h3>Profile Not Found</h3>
          <p>This buyer's profile could not be loaded.</p>
        </div>
      </div>
    </div>
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
      myPhotoUrl: null,
      loading: true,
      buyer: null,
    };
  },

  mounted() {
    this.apiUrl = localStorage.getItem('api_url') || '';
    this.token = localStorage.getItem('auth_token') || '';
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.myName = user.name || 'Agent';
    if (user.profile_photo_url) {
      this.myPhotoUrl = user.profile_photo_url;
    } else if (user.profile_photo_path) {
      this.myPhotoUrl = `${this.apiUrl}/storage/${user.profile_photo_path}`;
    }
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

    async logout() {
      try {
        await fetch(this.apiUrl + '/api/auth/logout', {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}` },
        });
      } catch {}
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push('/');
    },
  },
};
</script>

<style scoped>
.ad-layout {
  --navy: #0B1C39;
  --navy2: #102445;
  --navy3: #1a3158;
  --gold: #D89B0F;
  --gold2: #E5B332;
  --gold3: #B07A08;
  --s50: #f8fafc;
  --s100: #f1f5f9;
  --s200: #e2e8f0;
  --s300: #cbd5e1;
  --s400: #94a3b8;
  --s500: #64748b;
  --s600: #475569;
  --s700: #334155;
  --s800: #1e293b;
  --font-display: 'Outfit','Inter',-apple-system,sans-serif;
  --font-body: 'Inter',-apple-system,sans-serif;
  display: flex;
  min-height: 100vh;
  background: var(--s50);
  font-family: var(--font-body);
}

/* ===== Sidebar ===== */
.sidebar {
  width: 250px; min-height: 100vh;
  background: var(--navy);
  display: flex; flex-direction: column;
  position: fixed; left: 0; top: 0; height: 100vh;
  z-index: 100;
}
.sidebar-header { padding: 22px 20px 18px; }
.sidebar-logo {
  font-family: var(--font-display);
  font-size: 20px; font-weight: 800; color: #fff;
  text-decoration: none; letter-spacing: -.3px;
}
.logo-ph { color: var(--gold); }

.sidebar-nav { flex: 1; padding: 4px 0; overflow-y: auto; }
.nav-group-label {
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.5px; color: var(--s400);
  padding: 20px 20px 6px; margin: 0;
}
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 20px; margin: 1px 8px; border-radius: 8px;
  color: var(--s300); font-size: 13.5px; font-weight: 500;
  text-decoration: none; transition: all .18s; cursor: pointer;
  background: none; border: none; width: calc(100% - 16px);
  text-align: left;
}
.nav-item:hover { background: rgba(255,255,255,.07); color: #fff; }
.nav-item.router-link-active { background: rgba(216,155,15,.13); color: var(--gold); font-weight: 600; }

.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }

/* Sidebar bottom */
.sidebar-bottom { padding: 0 0 10px; }
.sidebar-divider { height: 1px; background: rgba(255,255,255,.08); margin: 6px 16px 8px; }

.nav-user { gap: 10px; }
.nav-av {
  width: 32px; height: 32px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700; color: var(--navy);
  flex-shrink: 0; overflow: hidden;
}
.nav-av img { width: 100%; height: 100%; object-fit: cover; }
.nav-user-info { display: flex; flex-direction: column; min-width: 0; }
.nav-user-name { font-size: 13px; font-weight: 600; color: #fff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.nav-user-role { font-size: 11px; color: var(--s400); }

.nav-logout { color: var(--s400); }
.nav-logout:hover { color: #ef4444; background: rgba(239,68,68,.1); }

.sidebar-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45); z-index: 99;
  display: none;
}

/* ===== Main Wrapper ===== */
.main-wrapper { margin-left: 250px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* Topbar */
.topbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 28px; height: 60px;
  background: #fff; border-bottom: 1px solid var(--s200);
  position: sticky; top: 0; z-index: 50;
}
.topbar-left { display: flex; align-items: center; gap: 12px; }
.topbar-right { display: flex; align-items: center; gap: 12px; }

.hamburger {
  display: none;
  background: none; border: none; color: var(--s600);
  padding: 6px; border-radius: 6px; cursor: pointer;
}
.hamburger:hover { background: var(--s100); }

.btn-back {
  display: inline-flex; align-items: center; gap: 6px;
  background: none; border: none; color: var(--s600);
  font-size: 14px; font-weight: 600; cursor: pointer;
  padding: 7px 14px; border-radius: 50px; transition: all .18s;
  font-family: var(--font-body);
}
.btn-back:hover { background: var(--s100); color: var(--navy); }

.btn-message-buyer {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 8px 20px; border-radius: 50px;
  background: linear-gradient(135deg, var(--gold), var(--gold2));
  color: var(--navy); font-size: 13.5px; font-weight: 700;
  text-decoration: none; transition: transform .2s, box-shadow .2s;
  font-family: var(--font-body);
}
.btn-message-buyer:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(216,155,15,.35); }

/* Content */
.content-area { padding: 28px 30px; max-width: 100%; }

/* Loading */
.loading-state { text-align: center; padding: 80px; color: var(--s400); }
.spinner { width: 40px; height: 40px; border: 3px solid var(--s200); border-top-color: var(--gold); border-radius: 50%; animation: spin .7s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ===== Profile Card ===== */
.profile-card {
  background: #fff; border-radius: 16px; overflow: hidden;
  border: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(11,28,57,.06), 0 6px 24px rgba(11,28,57,.04);
  margin-bottom: 20px;
}
.profile-cover {
  height: 140px;
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy2) 50%, var(--navy3) 100%);
  position: relative; overflow: hidden;
}
.cover-pattern {
  position: absolute; inset: 0;
  background: repeating-linear-gradient(45deg, transparent, transparent 30px, rgba(216,155,15,.04) 30px, rgba(216,155,15,.04) 60px);
}
.cover-rings { position: absolute; inset: 0; pointer-events: none; }
.ring {
  position: absolute; border-radius: 50%;
  border: 1.5px solid rgba(216,155,15,.1);
}
.r1 { width: 200px; height: 200px; top: -60px; right: -40px; animation: ringPulse 6s ease-in-out infinite; }
.r2 { width: 140px; height: 140px; bottom: -50px; left: 30%; animation: ringPulse 8s ease-in-out 2s infinite; }
@keyframes ringPulse { 0%,100% { opacity:.5; transform:scale(1); } 50% { opacity:1; transform:scale(1.06); } }

.profile-body { padding: 28px 32px 32px; }

.avatar-row { display: flex; align-items: center; gap: 22px; margin-top: -72px; margin-bottom: 24px; }
.buyer-avatar-wrap { flex-shrink: 0; }
.buyer-avatar {
  width: 100px; height: 100px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  border: 4px solid #fff;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-display);
  font-size: 38px; font-weight: 700; color: var(--navy);
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(11,28,57,.18);
}
.profile-head { padding-top: 32px; }
.buyer-name { font-family: var(--font-display); font-size: 24px; font-weight: 800; color: var(--navy); margin: 0 0 8px; }
.buyer-tags { display: flex; gap: 8px; flex-wrap: wrap; }
.role-tag {
  padding: 3px 12px; border-radius: 50px; font-size: 11px; font-weight: 700;
  background: rgba(216,155,15,.1); color: var(--gold3);
  border: 1px solid rgba(216,155,15,.2); text-transform: uppercase; letter-spacing: .5px;
}
.loc-tag {
  padding: 3px 12px; border-radius: 50px; font-size: 11px; font-weight: 600;
  background: var(--s100); color: var(--s600);
  border: 1px solid var(--s200);
  display: inline-flex; align-items: center; gap: 4px;
}

/* Info grid */
.info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; margin-bottom: 28px; }
.info-item {
  display: flex; gap: 14px; align-items: flex-start;
  padding: 16px; border-radius: 12px;
  background: var(--s50); border: 1px solid var(--s200);
  transition: border-color .2s, box-shadow .2s;
}
.info-item:hover { border-color: rgba(216,155,15,.25); box-shadow: 0 2px 8px rgba(216,155,15,.08); }

.info-icon-wrap {
  width: 38px; height: 38px; border-radius: 10px;
  flex-shrink: 0; display: flex; align-items: center; justify-content: center;
}
.ii-blue { background: rgba(59,130,246,.08); color: #3b82f6; }
.ii-red { background: rgba(239,68,68,.08); color: #ef4444; }
.ii-gold { background: rgba(216,155,15,.08); color: var(--gold); }
.ii-green { background: rgba(22,163,74,.08); color: #16a34a; }

.info-label {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .8px; color: var(--s400); display: block; margin-bottom: 3px;
}
.info-value { font-size: 14px; color: var(--s700); margin: 0; line-height: 1.5; }

.action-row { display: flex; gap: 12px; }
.btn-action {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 11px 28px; border-radius: 50px;
  font-size: 14px; font-weight: 700;
  text-decoration: none; transition: transform .2s, box-shadow .2s;
  font-family: var(--font-body);
}
.btn-primary {
  background: linear-gradient(135deg, var(--gold), var(--gold2));
  color: var(--navy);
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(216,155,15,.35); }

/* Note box */
.note-box {
  display: flex; gap: 14px; align-items: flex-start;
  background: #fff; border: 1px solid var(--s200);
  border-radius: 12px; padding: 18px 22px;
  box-shadow: 0 1px 3px rgba(11,28,57,.04);
}
.note-icon-wrap { color: var(--gold); flex-shrink: 0; margin-top: 1px; }
.note-title { font-family: var(--font-display); font-size: 14px; font-weight: 700; color: var(--navy); margin: 0 0 3px; }
.note-text { font-size: 13px; color: var(--s500); margin: 0; line-height: 1.6; }

/* Empty */
.empty-state { text-align: center; padding: 80px 20px; }
.empty-icon-wrap { color: var(--s300); margin-bottom: 16px; }
.empty-state h3 { font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--navy); margin: 0 0 8px; }
.empty-state p { color: var(--s500); }

@keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .sidebar { left: -280px; width: 280px; transition: left .3s ease; }
  .sidebar.open { left: 0; }
  .sidebar-overlay { display: block; }
  .main-wrapper { margin-left: 0; }
  .hamburger { display: flex; }
  .topbar { padding: 0 16px; }
  .content-area { padding: 16px; }
  .profile-cover { height: 110px; }
  .avatar-row { gap: 16px; margin-top: -56px; }
  .buyer-avatar { width: 80px; height: 80px; font-size: 30px; }
  .profile-body { padding: 20px 20px 24px; }
  .buyer-name { font-size: 20px; }
  .info-grid { grid-template-columns: 1fr; }
  .action-row { flex-direction: column; }
  .btn-action { width: 100%; justify-content: center; }
  .note-box { flex-direction: column; }
}
</style>
