<template>
  <div class="lb-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link :to="userRole === 'agent' ? '/agent/dashboard' : '/dashboard'" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <!-- Buyer nav -->
        <template v-if="userRole === 'buyer'">
          <router-link to="/dashboard" exact class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
            <span>Dashboard</span>
          </router-link>
          <div class="nav-group-label">Browse</div>
          <router-link to="/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
            <span>Properties</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></span>
            <span>Saved</span>
          </router-link>
          <div class="nav-group-label">Communicate</div>
          <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
            <span>Messages</span>
          </router-link>
          <router-link to="/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span>Viewings</span>
          </router-link>
          <div class="nav-group-label">Community</div>
          <router-link to="/leaderboard" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg></span>
            <span>Leaderboard</span>
          </router-link>
        </template>

        <!-- Agent nav -->
        <template v-else>
          <router-link to="/agent/dashboard" exact class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
            <span>Overview</span>
          </router-link>
          <div class="nav-group-label">Properties</div>
          <router-link to="/agent/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
            <span>My Properties</span>
          </router-link>
          <div class="nav-group-label">Schedule</div>
          <router-link to="/agent/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span>Viewings</span>
          </router-link>
          <router-link to="/agent/calendar" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg></span>
            <span>Calendar</span>
          </router-link>
          <div class="nav-group-label">Community</div>
          <router-link to="/leaderboard" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg></span>
            <span>Leaderboard</span>
          </router-link>
          <div class="nav-group-label">Communicate</div>
          <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
            <span>Messages</span>
          </router-link>
        </template>
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
            <span class="nav-user-role">{{ userRole === 'agent' ? 'Agent' : 'Buyer' }}</span>
          </div>
        </router-link>
        <router-link to="/settings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg></span>
          <span>Settings</span>
        </router-link>
        <button class="nav-item nav-logout" @click="logout">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>

    <!-- MAIN -->
    <div class="main-wrapper">
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="sidebarOpen = !sidebarOpen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <span class="topbar-title">Agent Leaderboard</span>
        </div>
      </header>

      <div class="content-area">
        <div class="content-inner">

          <!-- Header -->
          <div class="lb-header">
            <div class="lb-trophy">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
            </div>
            <div>
              <h2 class="lb-title">Top 10 Agents</h2>
              <p class="lb-subtitle">Ranked by consistency in posting quality listings and buyer reviews</p>
            </div>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="state-center">
            <div class="spinner"></div>
            <p class="state-txt">Loading leaderboard...</p>
          </div>

          <!-- Empty -->
          <div v-else-if="agents.length === 0" class="state-center">
            <p class="state-txt">No agents ranked yet.</p>
          </div>

          <!-- Leaderboard Cards -->
          <div v-else class="lb-list">
            <div
              v-for="(agent, index) in agents"
              :key="agent.id"
              class="lb-card"
              :class="{ 'rank-gold': index === 0, 'rank-silver': index === 1, 'rank-bronze': index === 2 }"
            >
              <div class="lb-rank" :class="{ 'r1': index === 0, 'r2': index === 1, 'r3': index === 2 }">
                {{ index + 1 }}
              </div>

              <div class="lb-avatar">
                <img v-if="agent.profile_photo_url" :src="agent.profile_photo_url" :alt="agent.name" />
                <span v-else>{{ agent.name.charAt(0).toUpperCase() }}</span>
              </div>

              <div class="lb-info">
                <h3 class="lb-name">{{ agent.name }}</h3>
                <p class="lb-company" v-if="agent.company_name">{{ agent.company_name }}</p>
                <div class="lb-stats">
                  <span class="lb-stat">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    {{ agent.properties_count }} listings
                  </span>
                  <span class="lb-stat" v-if="agent.average_rating > 0">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="#D89B0F" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ agent.average_rating }} ({{ agent.total_reviews }})
                  </span>
                  <span class="lb-stat lb-score">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Score: {{ agent.score.toFixed(1) }}
                  </span>
                </div>
              </div>

              <div class="lb-badge" v-if="index < 3">
                <svg v-if="index === 0" width="28" height="28" viewBox="0 0 24 24" fill="#D89B0F" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg v-else-if="index === 1" width="28" height="28" viewBox="0 0 24 24" fill="#94a3b8" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg v-else width="28" height="28" viewBox="0 0 24 24" fill="#cd7f32" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              </div>

              <router-link :to="'/agent-profile/' + agent.user_id" class="lb-view-btn">
                View Profile
              </router-link>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: 'Leaderboard',
  data() {
    return {
      sidebarOpen: false,
      userName: '',
      userRole: '',
      profilePhotoUrl: null,
      loading: true,
      agents: [],
    };
  },
  mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'User';
    this.userRole = user.role || 'buyer';
    if (user.profile_photo_path) {
      this.profilePhotoUrl = `${window.__API_URL__ || 'http://localhost:8000'}/storage/${user.profile_photo_path}`;
    }
    this.loadLeaderboard();
  },
  methods: {
    async loadLeaderboard() {
      this.loading = true;
      try {
        const res = await fetch(`${window.__API_URL__ || 'http://localhost:8000'}/api/top-agents?limit=10`, {
          headers: { Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success && data.data) this.agents = data.data;
      } catch (e) {
        console.error('Failed to load leaderboard:', e);
      } finally {
        this.loading = false;
      }
    },
    logout() {
      localStorage.clear();
      this.$router.push('/');
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@700;800&family=Inter:wght@400;500;600;700&display=swap');

.lb-layout {
  --navy:  #0B1C39;
  --navy2: #102445;
  --gold:  #D89B0F;
  --gold2: #E5B332;
  --gold3: #B07A08;
  --bg:    #EDF0F2;
  --white: #FFFFFF;
  --sw:    242px;
  --fd:    'Outfit','Inter',-apple-system,sans-serif;
  --fb:    'Inter',-apple-system,sans-serif;
  --s50: #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s800: #292524; --s900: #1C1917;
  display: flex; min-height: 100vh;
  background: var(--bg); font-family: var(--fb);
  -webkit-font-smoothing: antialiased;
}

/* SIDEBAR */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--navy); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; }
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
  color: rgba(255,255,255,0.22); padding: 14px 12px 5px;
}
.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }

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

/* MAIN */
.main-wrapper { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: var(--white); position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 28px; height: 56px; border-bottom: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.topbar-left { display: flex; align-items: center; gap: 10px; }
.hamburger { display: none; width: 34px; height: 34px; border-radius: 7px; border: 1px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s600); align-items: center; justify-content: center; }
.topbar-title { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--navy); letter-spacing: -0.3px; }

.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 800px; margin: 0 auto; padding: 32px 30px 60px; }

/* States */
.state-center { text-align: center; padding: 80px 20px; display: flex; flex-direction: column; align-items: center; gap: 14px; }
.state-txt { font-size: 13.5px; color: var(--s400); margin: 0; }
.spinner { width: 36px; height: 36px; border-radius: 50%; border: 3px solid var(--s200); border-top-color: var(--gold); animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Header */
.lb-header {
  display: flex; align-items: center; gap: 16px; margin-bottom: 28px;
  padding: 20px 24px; background: var(--white); border-radius: 14px;
  border: 1px solid var(--s200);
}
.lb-trophy { flex-shrink: 0; }
.lb-title { font-family: var(--fd); font-size: 20px; font-weight: 800; color: var(--navy); margin: 0 0 4px; letter-spacing: -0.3px; }
.lb-subtitle { font-size: 13px; color: var(--s400); margin: 0; }

/* Leaderboard List */
.lb-list { display: flex; flex-direction: column; gap: 12px; }

.lb-card {
  display: flex; align-items: center; gap: 16px;
  background: var(--white); border-radius: 14px; padding: 16px 20px;
  border: 1px solid var(--s200); transition: all 0.2s;
}
.lb-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(11,28,57,0.08); }
.lb-card.rank-gold   { border-color: rgba(216,155,15,0.3); background: linear-gradient(135deg, #fffdf5 0%, var(--white) 100%); }
.lb-card.rank-silver { border-color: rgba(148,163,184,0.3); background: linear-gradient(135deg, #f8fafc 0%, var(--white) 100%); }
.lb-card.rank-bronze { border-color: rgba(205,127,50,0.25); background: linear-gradient(135deg, #fdf8f0 0%, var(--white) 100%); }

.lb-rank {
  width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--fd); font-weight: 800; font-size: 16px;
  background: var(--s100); color: var(--s500);
}
.lb-rank.r1 { background: linear-gradient(135deg, #D89B0F, #E5B332); color: #fff; box-shadow: 0 3px 12px rgba(216,155,15,0.3); }
.lb-rank.r2 { background: linear-gradient(135deg, #94a3b8, #b0bec5); color: #fff; box-shadow: 0 3px 12px rgba(148,163,184,0.3); }
.lb-rank.r3 { background: linear-gradient(135deg, #cd7f32, #daa06d); color: #fff; box-shadow: 0 3px 12px rgba(205,127,50,0.3); }

.lb-avatar {
  width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0; overflow: hidden;
  background: linear-gradient(135deg, var(--navy), #1a3158);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 18px; color: var(--gold2);
}
.lb-avatar img { width: 100%; height: 100%; object-fit: cover; }

.lb-info { flex: 1; min-width: 0; }
.lb-name { font-family: var(--fd); font-size: 15px; font-weight: 700; color: var(--navy); margin: 0 0 2px; }
.lb-company { font-size: 12px; color: var(--s400); margin: 0 0 6px; }

.lb-stats { display: flex; gap: 14px; flex-wrap: wrap; }
.lb-stat {
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; color: var(--s500); font-weight: 500;
}
.lb-score { color: var(--gold3); font-weight: 700; }

.lb-badge { flex-shrink: 0; }

.lb-view-btn {
  padding: 8px 18px; border-radius: 50px; font-size: 12px; font-weight: 700;
  text-decoration: none; transition: all 0.15s; flex-shrink: 0;
  background: var(--navy); color: var(--white); border: none;
}
.lb-view-btn:hover { background: var(--navy2); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(11,28,57,0.2); }

/* Responsive */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .hamburger { display: flex; }
  .main-wrapper { margin-left: 0; }
  .content-inner { padding: 16px; }
  .lb-card { flex-wrap: wrap; gap: 12px; padding: 14px; }
  .lb-badge { display: none; }
  .lb-view-btn { width: 100%; text-align: center; margin-top: 4px; }
}
</style>
