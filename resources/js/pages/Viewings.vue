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

              <!-- Negotiation info -->
              <div v-if="v.status === 'negotiating' && v.latest_proposal" class="notes-box negotiation-notes">
                <strong>📋 Proposed by {{ v.latest_proposal.proposed_by_role === 'agent' ? 'Agent' : 'You' }}:</strong>
                {{ formatDate(v.latest_proposal.proposed_date) }} at {{ formatTime(v.latest_proposal.proposed_time) }}
                <div v-if="v.latest_proposal.note" class="proposal-note">"{{ v.latest_proposal.note }}"</div>
              </div>

              <div class="card-actions">
                <!-- Negotiation actions (when agent proposed) -->
                <template v-if="v.status === 'negotiating' && v.latest_proposal && v.latest_proposal.proposed_by_role === 'agent'">
                  <button @click="acceptProposal(v)" class="btn-action btn-accept" :disabled="actionLoading === v.id">Accept</button>
                  <button @click="openCounterModal(v)" class="btn-action btn-edit">Edit Schedule</button>
                  <button @click="rejectViewing(v)" class="btn-action btn-reject-sm" :disabled="actionLoading === v.id">Reject</button>
                </template>

                <!-- Waiting for agent (buyer proposed) -->
                <span v-else-if="v.status === 'negotiating' && v.latest_proposal && v.latest_proposal.proposed_by_role === 'buyer'" class="waiting-label">
                  ⏳ Waiting for agent response...
                </span>

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

    <!-- Counter-propose modal -->
    <div v-if="showCounterModal" class="modal-overlay" @click.self="showCounterModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>📅 Propose New Schedule</h3>
          <button @click="showCounterModal = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <p class="modal-subtitle" v-if="counterTarget">For: <strong>{{ counterTarget.property?.title }}</strong></p>
          <div class="form-group">
            <label>Proposed Date *</label>
            <input v-model="counterForm.date" type="date" class="form-input" :min="todayStr" />
          </div>
          <div class="form-group">
            <label>Proposed Time *</label>
            <input v-model="counterForm.time" type="time" class="form-input" />
          </div>
          <div class="form-group">
            <label>Note to Agent</label>
            <textarea v-model="counterForm.note" class="form-textarea" placeholder="Explain why you'd prefer this schedule..." maxlength="500"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showCounterModal = false" class="btn-secondary">Cancel</button>
          <button @click="submitCounterPropose" class="btn-primary" :disabled="counterLoading">
            {{ counterLoading ? 'Sending...' : 'Propose Schedule' }}
          </button>
        </div>
      </div>
    </div>

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
        { value: 'all',         label: 'All' },
        { value: 'requested',   label: 'Pending' },
        { value: 'negotiating', label: 'Negotiating' },
        { value: 'approved',    label: 'Approved' },
        { value: 'rejected',    label: 'Rejected' },
      ],
      actionLoading: null,
      showCounterModal: false,
      counterTarget: null,
      counterForm: { date: '', time: '', note: '' },
      counterLoading: false,
    };
  },

  computed: {
    filteredViewings() {
      if (this.activeTab === 'all') return this.viewings;
      return this.viewings.filter(v => v.status === this.activeTab);
    },
    todayStr() {
      return new Date().toISOString().split('T')[0];
    },
  },

  async mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'User';
    this.myId = user.id;

    await this.loadViewings();
    this.subscribeToNotifications();
    this.clearViewingNotifications();
  },

  beforeUnmount() {
    if (this.echoChannel && window.Echo) {
      window.Echo.leave('notifications.' + this.myId);
    }
  },

  methods: {
    async clearViewingNotifications() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(`${this.apiUrl}/api/notifications/mark-type-read`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ type: 'viewing_request' }),
        });
      } catch (e) { /* silent */ }
    },

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
            const idx = this.viewings.findIndex(v => v.id === data.id);
            if (idx !== -1) {
              this.viewings[idx].status = data.status;
              this.viewings[idx].rejection_reason = data.rejection_reason;
              if (data.status === 'approved') {
                this.viewings[idx].viewing_date = data.viewing_date;
                this.viewings[idx].viewing_time = data.viewing_time;
              }
            } else {
              this.loadViewings();
            }

            const msg = data.status === 'approved'
              ? `Your viewing for "${data.property?.title}" was approved!`
              : `Your viewing for "${data.property?.title}" was rejected.`;
            this.showToast(msg, data.status === 'approved' ? 'success' : 'error');
          })
          .listen('.ViewingNegotiationProposed', (data) => {
            this.loadViewings();
            this.showToast(`New schedule proposed for "${data.property?.title}"`, 'info');
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
        requested:   'Pending',
        negotiating: 'Negotiating',
        approved:    'Approved',
        rejected:    'Rejected',
        completed:   'Completed',
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

    async acceptProposal(v) {
      this.actionLoading = v.id;
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/buyer/viewings/${v.id}/accept-proposal`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.showToast('Schedule accepted! Viewing confirmed.', 'success');
          this.loadViewings();
        } else {
          this.showToast(data.message || 'Failed to accept proposal.', 'error');
        }
      } catch (e) {
        this.showToast('Failed to accept proposal.', 'error');
      } finally {
        this.actionLoading = null;
      }
    },

    openCounterModal(v) {
      this.counterTarget = v;
      this.counterForm = {
        date: v.latest_proposal?.proposed_date || '',
        time: v.latest_proposal?.proposed_time || '',
        note: '',
      };
      this.showCounterModal = true;
    },

    async submitCounterPropose() {
      if (!this.counterForm.date || !this.counterForm.time) {
        this.showToast('Please fill in both date and time.', 'error');
        return;
      }
      this.counterLoading = true;
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/buyer/viewings/${this.counterTarget.id}/counter-propose`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({
            proposed_date: this.counterForm.date,
            proposed_time: this.counterForm.time,
            note: this.counterForm.note,
          }),
        });
        const data = await res.json();
        if (data.success) {
          this.showCounterModal = false;
          this.showToast('New schedule proposed!', 'success');
          this.loadViewings();
        } else {
          this.showToast(data.message || 'Failed to propose schedule.', 'error');
        }
      } catch (e) {
        this.showToast('Failed to propose schedule.', 'error');
      } finally {
        this.counterLoading = false;
      }
    },

    async rejectViewing(v) {
      if (!confirm('Are you sure you want to reject this viewing?')) return;
      this.actionLoading = v.id;
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/buyer/viewings/${v.id}/reject`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ rejection_reason: 'Rejected by buyer' }),
        });
        const data = await res.json();
        if (data.success) {
          this.showToast('Viewing rejected.', 'success');
          this.loadViewings();
        } else {
          this.showToast(data.message || 'Failed to reject viewing.', 'error');
        }
      } catch (e) {
        this.showToast('Failed to reject viewing.', 'error');
      } finally {
        this.actionLoading = null;
      }
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, type, message };
      setTimeout(() => { this.toast.show = false; }, 4000);
    },

    logout() {
      localStorage.clear();
      this.$router.push('/');
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
.logo-ph { color: #FFD700; }
.sidebar-nav { flex: 1; padding: 12px 10px; }
.nav-section { margin-top: 16px; }
.section-title { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #bbb; font-weight: 700; padding: 0 10px; margin-bottom: 6px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; color: #555; text-decoration: none; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.15s; }
.nav-item:hover, .nav-item.active { background: #fef9e7; color: #100c08; }
.nav-item.active { font-weight: 700; }
.nav-icon { font-size: 16px; width: 20px; text-align: center; }

.sidebar-footer { padding: 12px; border-top: 1px solid #f5f5f5; }
.user-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 12px; background: #fafafa; position: relative; }
.user-avatar-lg { width: 36px; height: 36px; border-radius: 50%; background: #FFD700; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #100c08; flex-shrink: 0; }
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
.filter-tab.active { background: #FFD700; border-color: #FFD700; color: #100c08; }

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
.viewing-card.status-requested { border-left: 4px solid #FFD700; }
.viewing-card.status-completed   { border-left: 4px solid #6b7280; }
.viewing-card.status-negotiating { border-left: 4px solid #2563eb; }

.card-thumb { position: relative; height: 160px; background: #f5f5f5; }
.thumb-img { width: 100%; height: 100%; object-fit: cover; }
.thumb-placeholder { display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px; color: #ccc; }

.status-badge { position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
.badge-requested { background: #fef9c3; color: #854d0e; }
.badge-approved  { background: #dcfce7; color: #166534; }
.badge-rejected  { background: #fee2e2; color: #991b1b; }
.badge-completed   { background: #f3f4f6; color: #374151; }
.badge-negotiating { background: #dbeafe; color: #1e40af; }

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
.btn-view:hover { background: #FFD700; }

/* ---- Primary button ---- */
.btn-primary { display: inline-block; padding: 10px 20px; background: #FFD700; color: #100c08; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; text-decoration: none; }
.btn-primary:hover { background: #DAB600; }

/* ---- Toast ---- */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 1000; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

/* Negotiation */
.negotiation-notes { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
.proposal-note { margin-top: 6px; font-style: italic; color: #3b82f6; }
.btn-accept { background: #dcfce7; color: #166534; }
.btn-accept:hover { background: #16a34a; color: #fff; }
.btn-edit { background: #dbeafe; color: #1e40af; }
.btn-edit:hover { background: #2563eb; color: #fff; }
.btn-reject-sm { background: #fee2e2; color: #991b1b; }
.btn-reject-sm:hover { background: #dc2626; color: #fff; }
.waiting-label { font-size: 0.8rem; color: #6b7280; font-style: italic; }
.toast-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; z-index: 2000; }
.modal-box { background: #fff; border-radius: 16px; width: 90%; max-width: 460px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid #f0f0f0; }
.modal-header h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #100c08; }
.btn-close { background: none; border: none; font-size: 18px; color: #999; cursor: pointer; }
.modal-body { padding: 20px 24px; }
.modal-subtitle { font-size: 0.85rem; color: #555; margin: 0 0 16px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 14px 24px; border-top: 1px solid #f0f0f0; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: #333; margin-bottom: 6px; }
.form-input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.88rem; box-sizing: border-box; }
.form-input:focus { outline: none; border-color: #FFD700; box-shadow: 0 0 0 3px rgba(230,174,13,0.15); }
.form-textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.88rem; min-height: 70px; resize: vertical; box-sizing: border-box; }
.form-textarea:focus { outline: none; border-color: #FFD700; box-shadow: 0 0 0 3px rgba(230,174,13,0.15); }
.btn-secondary { padding: 9px 18px; border-radius: 8px; border: 1px solid #ddd; background: #fff; color: #555; font-weight: 600; font-size: 0.85rem; cursor: pointer; }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(20px); }
</style>
