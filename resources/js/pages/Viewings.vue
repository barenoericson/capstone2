<template>
  <div class="dashboard-wrapper">
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
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">Saved</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">Activity</h3>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Inbox</span>
          </router-link>
          <router-link to="/viewings" class="nav-item active">
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
        </div>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ userName }}</p>
            <p class="user-role-card">Buyer</p>
          </div>
          <button @click="showUserMenu = !showUserMenu" class="btn-options">⋮</button>
          <div v-if="showUserMenu" class="user-dropdown">
            <router-link to="/profile" class="dropdown-item">👤 My Profile</router-link>
            <a href="#" @click.prevent="logout" class="dropdown-item logout">🚪 Logout</a>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">My Viewings</h1>
          </div>
          <div class="topbar-right">
            <!-- Filter tabs -->
            <div class="filter-tabs">
              <button
                v-for="tab in tabs"
                :key="tab.value"
                class="filter-tab"
                :class="{ active: activeTab === tab.value }"
                @click="setTab(tab.value)"
              >{{ tab.label }}</button>
            </div>
          </div>
        </div>
      </nav>

      <div class="page-wrapper">
        <!-- Loading -->
        <div v-if="loading" class="state-box">
          <div class="state-icon">⏳</div>
          <p>Loading your viewings...</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="filteredViewings.length === 0" class="state-box">
          <div class="state-icon">📅</div>
          <h3>No {{ activeTab === 'all' ? '' : activeTab }} viewings</h3>
          <p>Browse properties and click "Schedule Viewing" to request a tour.</p>
          <router-link to="/properties" class="btn-primary">Discover Properties</router-link>
        </div>

        <!-- Viewings List -->
        <div v-else class="viewings-grid">
          <div
            v-for="v in filteredViewings"
            :key="v.id"
            class="viewing-card"
            :class="'status-' + v.status"
          >
            <!-- Property thumbnail -->
            <div class="card-thumb">
              <img
                v-if="v.property && v.property.cover_photo"
                :src="v.property.cover_photo"
                :alt="v.property.title"
                class="thumb-img"
              />
              <div v-else class="thumb-placeholder">🏠</div>
              <span class="status-badge" :class="'badge-' + v.status">{{ statusLabel(v.status) }}</span>
            </div>

            <!-- Card body -->
            <div class="card-body">
              <h3 class="prop-title">{{ v.property ? v.property.title : 'Property' }}</h3>
              <p class="prop-city" v-if="v.property">📍 {{ v.property.city }}</p>

              <div class="meta-row">
                <span class="meta-item">📅 {{ formatDate(v.viewing_date) }}</span>
                <span class="meta-item">🕐 {{ formatTime(v.viewing_time) }}</span>
              </div>

              <div v-if="v.agent" class="meta-row">
                <span class="meta-item">👤 Agent: {{ v.agent.name }}</span>
              </div>

              <div v-if="v.buyer_notes" class="notes-box buyer-notes">
                <strong>Your note:</strong> {{ v.buyer_notes }}
              </div>

              <!-- Rejection reason -->
              <div v-if="v.status === 'rejected' && v.rejection_reason" class="notes-box rejection-notes">
                <strong>❌ Rejection reason:</strong> {{ v.rejection_reason }}
              </div>

              <!-- Approved message -->
              <div v-if="v.status === 'approved'" class="notes-box approved-notes">
                ✅ Your viewing is confirmed! Please be on time.
              </div>

              <div class="card-actions">
                <router-link
                  v-if="v.property"
                  :to="'/property/' + v.property.id"
                  class="btn-action btn-view"
                >View Property</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Notification toast -->
    <transition name="slide-down">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'Viewings',

  data() {
    return {
      loading: true,
      viewings: [],
      activeTab: 'all',
      showUserMenu: false,
      userName: '',
      myId: null,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      echoChannel: null,
      toast: { show: false, type: 'success', message: '' },
      tabs: [
        { value: 'all',       label: 'All' },
        { value: 'requested', label: 'Pending' },
        { value: 'approved',  label: 'Approved' },
        { value: 'rejected',  label: 'Rejected' },
      ],
    };
  },

  computed: {
    filteredViewings() {
      if (this.activeTab === 'all') return this.viewings;
      return this.viewings.filter(v => v.status === this.activeTab);
    },
  },

  async mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'User';
    this.myId = user.id;

    await this.loadViewings();
    this.subscribeToNotifications();
  },

  beforeUnmount() {
    if (this.echoChannel && window.Echo) {
      window.Echo.leave('notifications.' + this.myId);
    }
  },

  methods: {
    async loadViewings() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/buyer/viewings`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.viewings = data.viewings;
        }
      } catch (e) {
        console.error('Error loading viewings:', e);
      } finally {
        this.loading = false;
      }
    },

    subscribeToNotifications() {
      if (!window.Echo || !this.myId) return;
      try {
        this.echoChannel = window.Echo.private('notifications.' + this.myId)
          .listen('.ViewingStatusChanged', (data) => {
            // Update the viewing in the list
            const idx = this.viewings.findIndex(v => v.id === data.id);
            if (idx !== -1) {
              this.viewings[idx].status = data.status;
              this.viewings[idx].rejection_reason = data.rejection_reason;
            } else {
              // Reload to be safe
              this.loadViewings();
            }

            const msg = data.status === 'approved'
              ? `Your viewing for "${data.property?.title}" was approved!`
              : `Your viewing for "${data.property?.title}" was rejected.`;
            this.showToast(msg, data.status === 'approved' ? 'success' : 'error');
          });
      } catch (e) {
        console.error('Echo subscribe error:', e);
      }
    },

    setTab(tab) {
      this.activeTab = tab;
    },

    statusLabel(status) {
      const map = {
        requested: 'Pending',
        approved:  'Approved',
        rejected:  'Rejected',
        completed: 'Completed',
      };
      return map[status] || status;
    },

    formatDate(dateStr) {
      if (!dateStr) return 'N/A';
      return new Date(dateStr).toLocaleDateString('en-PH', {
        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
      });
    },

    formatTime(timeStr) {
      if (!timeStr) return 'N/A';
      const [h, m] = timeStr.split(':');
      const hour   = parseInt(h, 10);
      const ampm   = hour >= 12 ? 'PM' : 'AM';
      const h12    = hour % 12 || 12;
      return `${h12}:${m} ${ampm}`;
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, type, message };
      setTimeout(() => { this.toast.show = false; }, 4000);
    },

    logout() {
      localStorage.clear();
      this.$router.push('/login');
    },
  },
};
</script>

<style scoped>
/* ---- Layout ---- */
.dashboard-wrapper { display: flex; min-height: 100vh; font-family: 'Inter', sans-serif; background: #f8f9fa; }

/* ---- Sidebar ---- */
.sidebar { width: 240px; min-height: 100vh; background: #fff; border-right: 1px solid #f0f0f0; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100; }
.sidebar-header { padding: 20px; border-bottom: 1px solid #f5f5f5; }
.sidebar-logo { font-size: 1.2rem; font-weight: 800; }
.logo-realty { color: #100c08; }
.logo-ph { color: #e6ae0d; }
.sidebar-nav { flex: 1; padding: 12px 10px; }
.nav-section { margin-top: 16px; }
.section-title { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #bbb; font-weight: 700; padding: 0 10px; margin-bottom: 6px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; color: #555; text-decoration: none; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.15s; }
.nav-item:hover, .nav-item.active { background: #fef9e7; color: #100c08; }
.nav-item.active { font-weight: 700; }
.nav-icon { font-size: 16px; width: 20px; text-align: center; }

.sidebar-footer { padding: 12px; border-top: 1px solid #f5f5f5; }
.user-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 12px; background: #fafafa; position: relative; }
.user-avatar-lg { width: 36px; height: 36px; border-radius: 50%; background: #e6ae0d; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #100c08; flex-shrink: 0; }
.user-info { flex: 1; min-width: 0; }
.user-name-card { font-size: 0.82rem; font-weight: 700; color: #100c08; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-role-card { font-size: 0.72rem; color: #999; margin: 0; }
.btn-options { background: none; border: none; cursor: pointer; font-size: 18px; color: #999; padding: 0 4px; }
.user-dropdown { position: absolute; bottom: 56px; left: 0; right: 0; background: #fff; border: 1px solid #f0f0f0; border-radius: 10px; padding: 6px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); z-index: 10; }
.dropdown-item { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; color: #333; text-decoration: none; font-size: 0.82rem; cursor: pointer; }
.dropdown-item:hover { background: #fef9e7; }
.dropdown-item.logout { color: #dc2626; }

/* ---- Main ---- */
.main-content { margin-left: 240px; flex: 1; display: flex; flex-direction: column; }

/* ---- Topbar ---- */
.topbar { background: #fff; border-bottom: 1px solid #f0f0f0; position: sticky; top: 0; z-index: 50; }
.topbar-content { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 64px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #100c08; margin: 0; }
.filter-tabs { display: flex; gap: 6px; }
.filter-tab { padding: 6px 14px; border-radius: 20px; border: 1px solid #e5e7eb; background: #fff; font-size: 0.8rem; font-weight: 600; color: #666; cursor: pointer; transition: all 0.15s; }
.filter-tab.active { background: #e6ae0d; border-color: #e6ae0d; color: #100c08; }

/* ---- Page content ---- */
.page-wrapper { padding: 24px; }

/* ---- State boxes ---- */
.state-box { text-align: center; padding: 60px 20px; }
.state-icon { font-size: 52px; margin-bottom: 16px; }
.state-box h3 { font-size: 1.2rem; font-weight: 700; color: #100c08; margin-bottom: 8px; }
.state-box p { color: #666; font-size: 0.9rem; margin-bottom: 20px; }

/* ---- Viewings Grid ---- */
.viewings-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
.viewing-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f0f0f0; transition: transform 0.15s; }
.viewing-card:hover { transform: translateY(-2px); }

/* Status color accent */
.viewing-card.status-approved { border-left: 4px solid #16a34a; }
.viewing-card.status-rejected  { border-left: 4px solid #dc2626; }
.viewing-card.status-requested { border-left: 4px solid #e6ae0d; }
.viewing-card.status-completed { border-left: 4px solid #6b7280; }

.card-thumb { position: relative; height: 160px; background: #f5f5f5; }
.thumb-img { width: 100%; height: 100%; object-fit: cover; }
.thumb-placeholder { display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px; color: #ccc; }

.status-badge { position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
.badge-requested { background: #fef9c3; color: #854d0e; }
.badge-approved  { background: #dcfce7; color: #166534; }
.badge-rejected  { background: #fee2e2; color: #991b1b; }
.badge-completed { background: #f3f4f6; color: #374151; }

.card-body { padding: 16px; }
.prop-title { font-size: 0.95rem; font-weight: 700; color: #100c08; margin: 0 0 4px; }
.prop-city  { font-size: 0.8rem; color: #666; margin: 0 0 10px; }
.meta-row   { display: flex; gap: 16px; margin-bottom: 8px; flex-wrap: wrap; }
.meta-item  { font-size: 0.8rem; color: #555; }

.notes-box { padding: 10px 12px; border-radius: 8px; font-size: 0.8rem; margin-bottom: 10px; line-height: 1.5; }
.buyer-notes     { background: #f8f9fa; border: 1px solid #e5e7eb; color: #555; }
.rejection-notes { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }
.approved-notes  { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; }

.card-actions { display: flex; gap: 8px; margin-top: 12px; }
.btn-action { padding: 8px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; text-decoration: none; border: none; }
.btn-view   { background: #f5f5f5; color: #100c08; }
.btn-view:hover { background: #e6ae0d; }

/* ---- Primary button ---- */
.btn-primary { display: inline-block; padding: 10px 20px; background: #e6ae0d; color: #100c08; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; text-decoration: none; }
.btn-primary:hover { background: #d4a000; }

/* ---- Toast ---- */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 1000; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(20px); }
</style>
