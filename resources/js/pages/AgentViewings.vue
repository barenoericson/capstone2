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
          <h3 class="section-title">Management</h3>
          <router-link to="/agent/dashboard" class="nav-item">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Overview</span>
          </router-link>
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item active">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
            <span v-if="pendingCount > 0" class="badge-dot">{{ pendingCount }}</span>
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
          <div class="user-avatar-lg">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ userName }}</p>
            <p class="user-role-card">Agent</p>
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
      <!-- Topbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">Viewing Requests</h1>
          </div>
          <div class="topbar-right">
            <button class="btn-calendar" @click="showCalendarManager = true">
              📅 Manage Calendar
            </button>
            <div class="filter-tabs">
              <button
                v-for="tab in tabs"
                :key="tab.value"
                class="filter-tab"
                :class="{ active: activeTab === tab.value }"
                @click="setTab(tab.value)"
              >
                {{ tab.label }}
                <span v-if="tab.value === 'requested' && pendingCount > 0" class="tab-badge">{{ pendingCount }}</span>
              </button>
            </div>
          </div>
        </div>
      </nav>

      <div class="page-wrapper">
        <!-- Loading -->
        <div v-if="loading" class="state-box">
          <div class="state-icon">⏳</div>
          <p>Loading viewing requests...</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="filteredViewings.length === 0" class="state-box">
          <div class="state-icon">📅</div>
          <h3>No {{ activeTab === 'all' ? '' : activeTab }} viewing requests</h3>
          <p>When buyers request viewings for your properties, they'll appear here.</p>
        </div>

        <!-- Viewings List -->
        <div v-else class="viewings-list">
          <div
            v-for="v in filteredViewings"
            :key="v.id"
            class="viewing-row"
            :class="'status-' + v.status"
          >
            <!-- Property info -->
            <div class="row-thumb">
              <img
                v-if="v.property && v.property.cover_photo"
                :src="v.property.cover_photo"
                :alt="v.property.title"
                class="thumb-img"
              />
              <div v-else class="thumb-placeholder">🏠</div>
            </div>

            <div class="row-info">
              <h3 class="prop-title">{{ v.property ? v.property.title : 'Property' }}</h3>
              <p class="prop-city" v-if="v.property">📍 {{ v.property.city }}</p>
              <div class="meta-row">
                <span class="meta-item">📅 {{ formatDate(v.viewing_date) }}</span>
                <span class="meta-item">🕐 {{ formatTime(v.viewing_time) }}</span>
                <span class="meta-item">👤 {{ v.buyer ? v.buyer.name : 'Buyer' }}</span>
              </div>
              <div v-if="v.buyer_notes" class="notes-box">
                <strong>Buyer note:</strong> {{ v.buyer_notes }}
              </div>
              <div v-if="v.status === 'rejected' && v.rejection_reason" class="notes-box rejection-notes">
                <strong>Rejection reason:</strong> {{ v.rejection_reason }}
              </div>
            </div>

            <div class="row-actions">
              <span class="status-badge" :class="'badge-' + v.status">{{ statusLabel(v.status) }}</span>

              <!-- Approve/Reject for pending -->
              <template v-if="v.status === 'requested'">
                <button @click="approveViewing(v)" class="btn-approve" :disabled="actionLoading === v.id">
                  {{ actionLoading === v.id ? '...' : '✅ Approve' }}
                </button>
                <button @click="openRejectModal(v)" class="btn-reject">
                  ❌ Reject
                </button>
              </template>

              <!-- Mark completed for approved -->
              <template v-if="v.status === 'approved'">
                <button @click="markCompleted(v)" class="btn-complete" :disabled="actionLoading === v.id">
                  {{ actionLoading === v.id ? '...' : '✔ Mark Completed' }}
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ============================================================ -->
    <!-- REJECT MODAL -->
    <!-- ============================================================ -->
    <div v-if="showRejectModal" class="modal-overlay" @click.self="showRejectModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>❌ Reject Viewing Request</h3>
          <button @click="showRejectModal = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <p class="modal-subtitle">
            You are rejecting a viewing request from
            <strong>{{ rejectTarget?.buyer?.name }}</strong> for
            <strong>{{ rejectTarget?.property?.title }}</strong> on
            {{ formatDate(rejectTarget?.viewing_date) }}.
          </p>
          <div class="form-group">
            <label>Reason for rejection <span class="required">*</span></label>
            <textarea
              v-model="rejectionReason"
              class="form-textarea"
              placeholder="Explain why you are rejecting this viewing request..."
              rows="4"
            ></textarea>
            <span v-if="rejectError" class="form-error">{{ rejectError }}</span>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showRejectModal = false" class="btn-secondary">Cancel</button>
          <button @click="submitReject" class="btn-danger" :disabled="rejectLoading">
            {{ rejectLoading ? 'Rejecting...' : 'Confirm Rejection' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- CALENDAR MANAGER MODAL -->
    <!-- ============================================================ -->
    <div v-if="showCalendarManager" class="modal-overlay" @click.self="showCalendarManager = false">
      <div class="modal-box modal-wide">
        <div class="modal-header">
          <h3>📅 Manage Blocked Dates</h3>
          <button @click="showCalendarManager = false" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <p class="modal-subtitle">
            Block dates when you are unavailable. Buyers won't be able to request viewings on these days.
          </p>

          <!-- Add new blocked date -->
          <div class="block-form">
            <div class="form-row">
              <div class="form-group">
                <label>Date to block</label>
                <input type="date" v-model="newBlockDate" class="form-input" :min="today" />
              </div>
              <div class="form-group">
                <label>Reason (optional)</label>
                <input type="text" v-model="newBlockReason" class="form-input" placeholder="e.g. Prior appointment" />
              </div>
              <button @click="blockDate" class="btn-block" :disabled="!newBlockDate || blockLoading">
                {{ blockLoading ? '...' : '+ Block Date' }}
              </button>
            </div>
            <span v-if="blockError" class="form-error">{{ blockError }}</span>
          </div>

          <!-- List of blocked dates -->
          <div class="blocked-list">
            <h4 class="blocked-title">Blocked Dates</h4>
            <div v-if="blockedDates.length === 0" class="state-empty">
              No dates blocked yet.
            </div>
            <div
              v-for="bd in blockedDates"
              :key="bd.id"
              class="blocked-row"
            >
              <div class="blocked-info">
                <span class="blocked-date">📅 {{ formatDate(bd.blocked_date) }}</span>
                <span class="blocked-reason" v-if="bd.reason">— {{ bd.reason }}</span>
              </div>
              <button @click="unblockDate(bd)" class="btn-unblock">Remove</button>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="showCalendarManager = false" class="btn-primary">Done</button>
        </div>
      </div>
    </div>

    <!-- Notification toast -->
    <transition name="slide-up">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'AgentViewings',

  data() {
    return {
      loading: true,
      viewings: [],
      activeTab: 'requested',
      showUserMenu: false,
      userName: '',
      myId: null,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      echoChannel: null,
      actionLoading: null,

      // Reject modal
      showRejectModal: false,
      rejectTarget: null,
      rejectionReason: '',
      rejectError: '',
      rejectLoading: false,

      // Calendar manager
      showCalendarManager: false,
      blockedDates: [],
      newBlockDate: '',
      newBlockReason: '',
      blockLoading: false,
      blockError: '',

      toast: { show: false, type: 'success', message: '' },

      tabs: [
        { value: 'requested', label: 'Pending' },
        { value: 'approved',  label: 'Approved' },
        { value: 'all',       label: 'All' },
        { value: 'rejected',  label: 'Rejected' },
      ],
    };
  },

  computed: {
    filteredViewings() {
      if (this.activeTab === 'all') return this.viewings;
      return this.viewings.filter(v => v.status === this.activeTab);
    },
    pendingCount() {
      return this.viewings.filter(v => v.status === 'requested').length;
    },
    today() {
      return new Date().toISOString().split('T')[0];
    },
  },

  async mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Agent';
    this.myId = user.id;

    await Promise.all([this.loadViewings(), this.loadBlockedDates()]);
    this.subscribeToNotifications();
  },

  beforeUnmount() {
    if (window.Echo && this.myId) {
      window.Echo.leave('notifications.' + this.myId);
    }
  },

  methods: {
    async loadViewings() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/viewings`, {
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

    async loadBlockedDates() {
      try {
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/blocked-dates`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.blockedDates = data.blocked_dates;
        }
      } catch (e) {
        console.error('Error loading blocked dates:', e);
      }
    },

    subscribeToNotifications() {
      if (!window.Echo || !this.myId) return;
      try {
        this.echoChannel = window.Echo.private('notifications.' + this.myId)
          .listen('.ViewingRequested', (data) => {
            // A new viewing request arrived — reload list
            this.loadViewings();
            this.showToast(`New viewing request from ${data.buyer?.name} for "${data.property?.title}"`, 'info');
          });
      } catch (e) {
        console.error('Echo subscribe error:', e);
      }
    },

    async approveViewing(v) {
      try {
        this.actionLoading = v.id;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/viewings/${v.id}/approve`, {
          method:  'PUT',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          const idx = this.viewings.findIndex(x => x.id === v.id);
          if (idx !== -1) this.viewings[idx].status = 'approved';
          this.showToast('Viewing approved!', 'success');
        } else {
          this.showToast(data.message || 'Failed to approve.', 'error');
        }
      } catch (e) {
        this.showToast('Error approving viewing.', 'error');
      } finally {
        this.actionLoading = null;
      }
    },

    openRejectModal(v) {
      this.rejectTarget   = v;
      this.rejectionReason = '';
      this.rejectError    = '';
      this.showRejectModal = true;
    },

    async submitReject() {
      if (!this.rejectionReason.trim()) {
        this.rejectError = 'Please provide a reason for rejection.';
        return;
      }

      try {
        this.rejectLoading = true;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/viewings/${this.rejectTarget.id}/reject`, {
          method:  'PUT',
          headers: {
            Authorization:  `Bearer ${token}`,
            Accept:         'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ rejection_reason: this.rejectionReason }),
        });
        const data = await res.json();
        if (data.success) {
          const idx = this.viewings.findIndex(x => x.id === this.rejectTarget.id);
          if (idx !== -1) {
            this.viewings[idx].status           = 'rejected';
            this.viewings[idx].rejection_reason = this.rejectionReason;
          }
          this.showRejectModal = false;
          this.showToast('Viewing rejected.', 'success');
        } else {
          this.rejectError = data.message || 'Failed to reject.';
        }
      } catch (e) {
        this.rejectError = 'Error submitting rejection.';
      } finally {
        this.rejectLoading = false;
      }
    },

    async markCompleted(v) {
      try {
        this.actionLoading = v.id;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/viewings/${v.id}/completed`, {
          method:  'PUT',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          const idx = this.viewings.findIndex(x => x.id === v.id);
          if (idx !== -1) this.viewings[idx].status = 'completed';
          this.showToast('Marked as completed!', 'success');
        }
      } catch (e) {
        this.showToast('Error updating viewing.', 'error');
      } finally {
        this.actionLoading = null;
      }
    },

    async blockDate() {
      if (!this.newBlockDate) return;
      try {
        this.blockLoading = true;
        this.blockError   = '';
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/blocked-dates`, {
          method:  'POST',
          headers: {
            Authorization:  `Bearer ${token}`,
            Accept:         'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ blocked_date: this.newBlockDate, reason: this.newBlockReason }),
        });
        const data = await res.json();
        if (data.success) {
          this.blockedDates.push(data.blocked_date);
          this.blockedDates.sort((a, b) => a.blocked_date.localeCompare(b.blocked_date));
          this.newBlockDate   = '';
          this.newBlockReason = '';
          this.showToast('Date blocked successfully.', 'success');
        } else {
          this.blockError = data.message || 'Failed to block date.';
        }
      } catch (e) {
        this.blockError = 'Error blocking date.';
      } finally {
        this.blockLoading = false;
      }
    },

    async unblockDate(bd) {
      try {
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/blocked-dates/${bd.id}`, {
          method:  'DELETE',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.blockedDates = this.blockedDates.filter(x => x.id !== bd.id);
          this.showToast('Date unblocked.', 'success');
        }
      } catch (e) {
        this.showToast('Error unblocking date.', 'error');
      }
    },

    setTab(tab) {
      this.activeTab = tab;
    },

    statusLabel(status) {
      return { requested: 'Pending', approved: 'Approved', rejected: 'Rejected', completed: 'Completed' }[status] || status;
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
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; color: #555; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: all 0.15s; }
.nav-item:hover, .nav-item.active { background: #fef9e7; color: #100c08; }
.nav-item.active { font-weight: 700; }
.nav-icon { font-size: 16px; width: 20px; text-align: center; }
.badge-dot {
  background: #ff4444; color: white; border-radius: 50%;
  font-size: 11px; font-weight: 700; min-width: 20px; height: 20px;
  display: flex; align-items: center; justify-content: center;
}

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
.topbar-right { display: flex; align-items: center; gap: 12px; }
.btn-calendar { padding: 8px 16px; border-radius: 10px; border: 1.5px solid #e6ae0d; background: transparent; color: #100c08; font-size: 0.82rem; font-weight: 700; cursor: pointer; }
.btn-calendar:hover { background: #fef9e7; }
.filter-tabs { display: flex; gap: 6px; }
.filter-tab { padding: 6px 14px; border-radius: 20px; border: 1px solid #e5e7eb; background: #fff; font-size: 0.8rem; font-weight: 600; color: #666; cursor: pointer; transition: all 0.15s; position: relative; }
.filter-tab.active { background: #e6ae0d; border-color: #e6ae0d; color: #100c08; }
.tab-badge { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; background: #dc2626; color: #fff; font-size: 10px; font-weight: 700; margin-left: 4px; }

/* ---- Page wrapper ---- */
.page-wrapper { padding: 24px; }

/* ---- State ---- */
.state-box { text-align: center; padding: 60px 20px; }
.state-icon { font-size: 52px; margin-bottom: 16px; }
.state-box h3 { font-size: 1.2rem; font-weight: 700; color: #100c08; margin-bottom: 8px; }
.state-box p { color: #666; font-size: 0.9rem; }

/* ---- Viewings List ---- */
.viewings-list { display: flex; flex-direction: column; gap: 16px; }
.viewing-row { background: #fff; border-radius: 14px; padding: 16px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
.viewing-row.status-approved { border-left: 4px solid #16a34a; }
.viewing-row.status-rejected  { border-left: 4px solid #dc2626; }
.viewing-row.status-requested { border-left: 4px solid #e6ae0d; }
.viewing-row.status-completed { border-left: 4px solid #6b7280; }

.row-thumb { width: 80px; height: 80px; border-radius: 10px; overflow: hidden; flex-shrink: 0; background: #f5f5f5; }
.thumb-img { width: 100%; height: 100%; object-fit: cover; }
.thumb-placeholder { display: flex; align-items: center; justify-content: center; height: 100%; font-size: 28px; color: #ccc; }

.row-info { flex: 1; min-width: 0; }
.prop-title { font-size: 0.95rem; font-weight: 700; color: #100c08; margin: 0 0 2px; }
.prop-city  { font-size: 0.8rem; color: #666; margin: 0 0 8px; }
.meta-row   { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 8px; }
.meta-item  { font-size: 0.8rem; color: #555; }
.notes-box { padding: 8px 12px; border-radius: 8px; font-size: 0.8rem; margin-top: 6px; background: #f8f9fa; border: 1px solid #e5e7eb; color: #555; line-height: 1.5; }
.rejection-notes { background: #fee2e2; border-color: #fecaca; color: #991b1b; }

.row-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; min-width: 140px; }
.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; white-space: nowrap; }
.badge-requested { background: #fef9c3; color: #854d0e; }
.badge-approved  { background: #dcfce7; color: #166534; }
.badge-rejected  { background: #fee2e2; color: #991b1b; }
.badge-completed { background: #f3f4f6; color: #374151; }

.btn-approve  { padding: 8px 14px; border-radius: 8px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; font-size: 0.8rem; font-weight: 700; cursor: pointer; width: 100%; }
.btn-approve:hover:not(:disabled) { background: #16a34a; color: #fff; }
.btn-reject   { padding: 8px 14px; border-radius: 8px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; font-size: 0.8rem; font-weight: 700; cursor: pointer; width: 100%; }
.btn-reject:hover { background: #dc2626; color: #fff; }
.btn-complete { padding: 8px 14px; border-radius: 8px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; font-size: 0.8rem; font-weight: 700; cursor: pointer; width: 100%; }
.btn-complete:hover:not(:disabled) { background: #374151; color: #fff; }
button:disabled { opacity: 0.5; cursor: not-allowed; }

/* ---- Modal ---- */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 20px; }
.modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 480px; overflow: hidden; }
.modal-wide { max-width: 600px; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #f0f0f0; }
.modal-header h3 { font-size: 1rem; font-weight: 700; color: #100c08; margin: 0; }
.btn-close { background: none; border: none; cursor: pointer; font-size: 18px; color: #999; }
.modal-body { padding: 20px 24px; max-height: 70vh; overflow-y: auto; }
.modal-subtitle { font-size: 0.88rem; color: #555; margin-bottom: 16px; line-height: 1.6; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f0f0f0; }

.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: #100c08; }
.required { color: #dc2626; }
.form-textarea { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 0.88rem; resize: vertical; outline: none; transition: border-color 0.2s; font-family: inherit; }
.form-textarea:focus { border-color: #e6ae0d; }
.form-input { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: #e6ae0d; }
.form-error { font-size: 0.78rem; color: #dc2626; }

/* Block form */
.block-form { margin-bottom: 20px; }
.form-row { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.form-row .form-group { flex: 1; min-width: 140px; margin-bottom: 0; }
.btn-block { padding: 10px 16px; border-radius: 10px; background: #e6ae0d; color: #100c08; border: none; font-weight: 700; font-size: 0.85rem; cursor: pointer; white-space: nowrap; }
.btn-block:hover:not(:disabled) { background: #d4a000; }

.blocked-title { font-size: 0.88rem; font-weight: 700; color: #100c08; margin-bottom: 10px; }
.state-empty { padding: 16px 0; color: #999; font-size: 0.85rem; }
.blocked-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 10px; background: #fef9e7; border: 1px solid #fde68a; margin-bottom: 8px; }
.blocked-info { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.blocked-date { font-size: 0.85rem; font-weight: 700; color: #100c08; }
.blocked-reason { font-size: 0.8rem; color: #555; }
.btn-unblock { padding: 5px 12px; border-radius: 8px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 0.78rem; font-weight: 700; cursor: pointer; }
.btn-unblock:hover { background: #dc2626; color: #fff; }

/* Buttons */
.btn-primary   { padding: 10px 20px; background: #e6ae0d; color: #100c08; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-primary:hover { background: #d4a000; }
.btn-secondary { padding: 10px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger    { padding: 10px 20px; background: #dc2626; color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger:hover:not(:disabled) { background: #b91c1c; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 1000; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.toast-info    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }
</style>
