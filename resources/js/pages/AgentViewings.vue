<template>
  <div class="dashboard-wrapper">
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
      <!-- Topbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
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
                <span v-if="tab.value === 'negotiating' && negotiatingCount > 0" class="tab-badge tab-badge-blue">{{ negotiatingCount }}</span>
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

              <!-- Negotiation info -->
              <div v-if="v.latest_proposal" class="negotiation-box">
                <div class="negotiation-header">
                  <span class="negotiation-icon">🔄</span>
                  <strong>{{ v.latest_proposal.proposed_by_role === 'buyer' ? 'Buyer' : 'You' }} proposed a new schedule:</strong>
                </div>
                <div class="negotiation-details">
                  <span>📅 {{ formatDate(v.latest_proposal.proposed_date) }}</span>
                  <span>🕐 {{ formatTime(v.latest_proposal.proposed_time) }}</span>
                </div>
                <div v-if="v.latest_proposal.note" class="negotiation-note">
                  💬 {{ v.latest_proposal.note }}
                </div>
              </div>
            </div>

            <div class="row-actions">
              <span class="status-badge" :class="'badge-' + v.status">{{ statusLabel(v.status) }}</span>

              <!-- Approve / Edit Schedule / Reject for pending (requested) -->
              <template v-if="v.status === 'requested'">
                <button @click="approveViewing(v)" class="btn-approve" :disabled="actionLoading === v.id">
                  {{ actionLoading === v.id ? '...' : '✅ Approve' }}
                </button>
                <button @click="openCounterModal(v)" class="btn-edit-schedule">
                  ✏️ Edit Schedule
                </button>
                <button @click="openRejectModal(v)" class="btn-reject">
                  ❌ Reject
                </button>
              </template>

              <!-- Negotiating: buyer proposed → Accept / Edit / Reject -->
              <template v-if="v.status === 'negotiating' && v.latest_proposal && v.latest_proposal.proposed_by_role === 'buyer'">
                <button @click="acceptProposal(v)" class="btn-approve" :disabled="actionLoading === v.id">
                  {{ actionLoading === v.id ? '...' : '✅ Accept' }}
                </button>
                <button @click="openCounterModal(v)" class="btn-edit-schedule">
                  ✏️ Edit Schedule
                </button>
                <button @click="openRejectModal(v)" class="btn-reject">
                  ❌ Reject
                </button>
              </template>

              <!-- Negotiating: agent proposed → waiting -->
              <template v-if="v.status === 'negotiating' && v.latest_proposal && v.latest_proposal.proposed_by_role === 'agent'">
                <span class="waiting-label">⏳ Waiting for buyer response</span>
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

    <!-- ============================================================ -->
    <!-- COUNTER-PROPOSE MODAL -->
    <!-- ============================================================ -->
    <div v-if="showCounterModal" class="modal-overlay" @click.self="showCounterModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>✏️ Propose New Schedule</h3>
          <button @click="showCounterModal = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <p class="modal-subtitle">
            Propose a different schedule for the viewing of
            <strong>{{ counterTarget?.property?.title }}</strong> with
            <strong>{{ counterTarget?.buyer?.name }}</strong>.
          </p>

          <div class="form-group">
            <label>Proposed Date <span class="required">*</span></label>
            <input type="date" v-model="counterForm.proposed_date" class="form-input" :min="today" />
          </div>

          <div class="form-group">
            <label>Proposed Time <span class="required">*</span></label>
            <input type="time" v-model="counterForm.proposed_time" class="form-input" />
          </div>

          <div class="form-group">
            <label>Note (optional)</label>
            <textarea
              v-model="counterForm.note"
              class="form-textarea"
              placeholder="Add a note explaining the schedule change..."
              rows="3"
            ></textarea>
          </div>

          <span v-if="counterError" class="form-error">{{ counterError }}</span>
        </div>
        <div class="modal-footer">
          <button @click="showCounterModal = false" class="btn-secondary">Cancel</button>
          <button @click="submitCounterPropose" class="btn-primary" :disabled="counterLoading">
            {{ counterLoading ? 'Sending...' : 'Send Proposal' }}
          </button>
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
      sidebarOpen: false,
      loading: true,
      viewings: [],
      activeTab: 'requested',
      showUserMenu: false,
      userName: '',
      myId: null,
      profilePhotoUrl: null,
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
        { value: 'negotiating', label: 'Negotiating' },
        { value: 'approved',  label: 'Approved' },
        { value: 'all',       label: 'All' },
        { value: 'rejected',  label: 'Rejected' },
      ],

      // Counter-propose modal
      showCounterModal: false,
      counterTarget: null,
      counterForm: { proposed_date: '', proposed_time: '', note: '' },
      counterLoading: false,
      counterError: '',
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
    negotiatingCount() {
      return this.viewings.filter(v => v.status === 'negotiating').length;
    },
    today() {
      return new Date().toISOString().split('T')[0];
    },
  },

  async mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Agent';
    this.myId = user.id;
    this.loadProfilePhoto();

    await Promise.all([this.loadViewings(), this.loadBlockedDates()]);
    this.subscribeToNotifications();
    this.clearViewingNotifications();
  },

  beforeUnmount() {
    if (window.Echo && this.myId) {
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
            this.loadViewings();
            this.showToast(`New viewing request from ${data.buyer?.name} for "${data.property?.title}"`, 'info');
          })
          .listen('.ViewingNegotiationProposed', (data) => {
            this.loadViewings();
            this.showToast(`${data.proposal?.proposed_by || 'Buyer'} proposed a new schedule for "${data.property?.title}"`, 'info');
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
      return { requested: 'Pending', negotiating: 'Negotiating', approved: 'Approved', rejected: 'Rejected', completed: 'Completed' }[status] || status;
    },

    openCounterModal(v) {
      this.counterTarget = v;
      this.counterForm = { proposed_date: '', proposed_time: '', note: '' };
      this.counterError = '';
      this.showCounterModal = true;
    },

    async submitCounterPropose() {
      if (!this.counterForm.proposed_date || !this.counterForm.proposed_time) {
        this.counterError = 'Please select a date and time.';
        return;
      }
      try {
        this.counterLoading = true;
        this.counterError = '';
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/viewings/${this.counterTarget.id}/counter-propose`, {
          method: 'PUT',
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.counterForm),
        });
        const data = await res.json();
        if (data.success) {
          this.showCounterModal = false;
          this.loadViewings();
          this.showToast('Schedule proposal sent to buyer!', 'success');
        } else {
          this.counterError = data.message || 'Failed to send proposal.';
        }
      } catch (e) {
        this.counterError = 'Error sending proposal.';
      } finally {
        this.counterLoading = false;
      }
    },

    async acceptProposal(v) {
      try {
        this.actionLoading = v.id;
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/viewings/${v.id}/accept-proposal`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.loadViewings();
          this.showToast('Proposal accepted! Viewing confirmed.', 'success');
        } else {
          this.showToast(data.message || 'Failed to accept proposal.', 'error');
        }
      } catch (e) {
        this.showToast('Error accepting proposal.', 'error');
      } finally {
        this.actionLoading = null;
      }
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

    async loadProfilePhoto() {
      try {
        const token = localStorage.getItem('auth_token');
        const apiUrl = window.__API_URL__ || 'http://localhost:8000';
        const res = await fetch(`${apiUrl}/api/user/profile-photo`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        const data = await res.json();
        if (data.profile_photo_url) this.profilePhotoUrl = data.profile_photo_url;
        else {
          const user = JSON.parse(localStorage.getItem('user') || '{}');
          if (user.profile_photo_path) {
            this.profilePhotoUrl = `${apiUrl}/storage/${user.profile_photo_path}`;
          }
        }
      } catch (e) { /* non-critical */ }
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
.dashboard-wrapper {
  display: flex; min-height: 100vh; font-family: 'Inter', sans-serif; background: var(--bg);
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

/* ---- Main ---- */
.main-content { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; }

/* ---- Topbar ---- */
.topbar { background: #fff; border-bottom: 1px solid #f0f0f0; position: sticky; top: 0; z-index: 50; }
.topbar-content { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 64px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #0B1C39; margin: 0; }
.topbar-right { display: flex; align-items: center; gap: 12px; }
.btn-calendar { padding: 8px 16px; border-radius: 50px; border: 1.5px solid #D89B0F; background: transparent; color: #0B1C39; font-size: 0.82rem; font-weight: 700; cursor: pointer; }
.btn-calendar:hover { background: #fef9e7; }
.filter-tabs { display: flex; gap: 6px; }
.filter-tab { padding: 6px 14px; border-radius: 50px; border: 1px solid #e5e7eb; background: #fff; font-size: 0.8rem; font-weight: 600; color: #666; cursor: pointer; transition: all 0.15s; position: relative; }
.filter-tab.active { background: #D89B0F; border-color: #D89B0F; color: #0B1C39; }
.tab-badge { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; background: #dc2626; color: #fff; font-size: 10px; font-weight: 700; margin-left: 4px; }

/* ---- Page wrapper ---- */
.page-wrapper { padding: 24px; }

/* ---- State ---- */
.state-box { text-align: center; padding: 60px 20px; }
.state-icon { font-size: 52px; margin-bottom: 16px; }
.state-box h3 { font-size: 1.2rem; font-weight: 700; color: #0B1C39; margin-bottom: 8px; }
.state-box p { color: #666; font-size: 0.9rem; }

/* ---- Viewings List ---- */
.viewings-list { display: flex; flex-direction: column; gap: 16px; }
.viewing-row { background: #fff; border-radius: 14px; padding: 16px; display: flex; align-items: flex-start; gap: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; }
.viewing-row.status-approved { border-left: 4px solid #16a34a; }
.viewing-row.status-rejected  { border-left: 4px solid #dc2626; }
.viewing-row.status-requested { border-left: 4px solid #D89B0F; }
.viewing-row.status-completed { border-left: 4px solid #6b7280; }
.viewing-row.status-negotiating { border-left: 4px solid #2563eb; }

.row-thumb { width: 80px; height: 80px; border-radius: 10px; overflow: hidden; flex-shrink: 0; background: #f5f5f5; }
.thumb-img { width: 100%; height: 100%; object-fit: cover; }
.thumb-placeholder { display: flex; align-items: center; justify-content: center; height: 100%; font-size: 28px; color: #ccc; }

.row-info { flex: 1; min-width: 0; }
.prop-title { font-size: 0.95rem; font-weight: 700; color: #0B1C39; margin: 0 0 2px; }
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
.badge-negotiating { background: #dbeafe; color: #1e40af; }

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
.modal-header h3 { font-size: 1rem; font-weight: 700; color: #0B1C39; margin: 0; }
.btn-close { background: none; border: none; cursor: pointer; font-size: 18px; color: #999; }
.modal-body { padding: 20px 24px; max-height: 70vh; overflow-y: auto; }
.modal-subtitle { font-size: 0.88rem; color: #555; margin-bottom: 16px; line-height: 1.6; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f0f0f0; }

.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: #0B1C39; }
.required { color: #dc2626; }
.form-textarea { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 0.88rem; resize: vertical; outline: none; transition: border-color 0.2s; font-family: inherit; }
.form-textarea:focus { border-color: #D89B0F; }
.form-input { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: #D89B0F; }
.form-error { font-size: 0.78rem; color: #dc2626; }

/* Block form */
.block-form { margin-bottom: 20px; }
.form-row { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.form-row .form-group { flex: 1; min-width: 140px; margin-bottom: 0; }
.btn-block { padding: 10px 16px; border-radius: 10px; background: #D89B0F; color: #0B1C39; border: none; font-weight: 700; font-size: 0.85rem; cursor: pointer; white-space: nowrap; }
.btn-block:hover:not(:disabled) { background: #B07A08; }

.blocked-title { font-size: 0.88rem; font-weight: 700; color: #0B1C39; margin-bottom: 10px; }
.state-empty { padding: 16px 0; color: #999; font-size: 0.85rem; }
.blocked-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 10px; background: #fef9e7; border: 1px solid #fde68a; margin-bottom: 8px; }
.blocked-info { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.blocked-date { font-size: 0.85rem; font-weight: 700; color: #0B1C39; }
.blocked-reason { font-size: 0.8rem; color: #555; }
.btn-unblock { padding: 5px 12px; border-radius: 8px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 0.78rem; font-weight: 700; cursor: pointer; }
.btn-unblock:hover { background: #dc2626; color: #fff; }

/* Buttons */
.btn-primary   { padding: 10px 20px; background: #D89B0F; color: #0B1C39; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-primary:hover { background: #B07A08; }
.btn-secondary { padding: 10px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger    { padding: 10px 20px; background: #dc2626; color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger:hover:not(:disabled) { background: #b91c1c; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 1000; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.toast-info    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

/* Negotiation box */
.negotiation-box { margin-top: 8px; padding: 10px 14px; border-radius: 10px; background: #eff6ff; border: 1px solid #bfdbfe; }
.negotiation-header { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #1e40af; margin-bottom: 4px; }
.negotiation-icon { font-size: 14px; }
.negotiation-details { display: flex; gap: 14px; font-size: 0.8rem; color: #1e40af; margin-bottom: 2px; }
.negotiation-note { font-size: 0.8rem; color: #3b82f6; font-style: italic; }

/* Edit schedule button */
.btn-edit-schedule { padding: 8px 14px; border-radius: 8px; background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.8rem; font-weight: 700; cursor: pointer; width: 100%; }
.btn-edit-schedule:hover { background: #2563eb; color: #fff; }

/* Waiting label */
.waiting-label { font-size: 0.8rem; color: #6b7280; font-style: italic; text-align: center; padding: 4px 0; }

/* Tab badge blue */
.tab-badge-blue { background: #2563eb; }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .sidebar { position: fixed; left: calc(-1 * var(--sw)); width: var(--sw); z-index: 1001; transition: left 0.3s ease; }
  .main-content { margin-left: 0 !important; }
  .topbar-content { flex-direction: column; align-items: flex-start; height: auto; padding: 12px 16px; gap: 10px; }
  .topbar-right { flex-wrap: wrap; width: 100%; }
  .filter-tabs { flex-wrap: wrap; gap: 4px; }
  .filter-tab { padding: 5px 10px; font-size: 0.75rem; }
  .btn-calendar { font-size: 0.78rem; padding: 6px 12px; }
  .page-wrapper { padding: 14px; }
  .viewing-row { flex-direction: column; padding: 14px; }
  .row-thumb { width: 100%; height: 160px; border-radius: 10px; }
  .row-actions { flex-direction: row; flex-wrap: wrap; align-items: center; min-width: unset; width: 100%; gap: 6px; }
  .row-actions .btn-approve,
  .row-actions .btn-reject,
  .row-actions .btn-complete,
  .row-actions .btn-edit-schedule { width: auto; flex: 1; min-width: 100px; }
  .meta-row { flex-direction: column; gap: 4px; }
  .modal-box { max-width: 95vw; }
  .modal-wide { max-width: 95vw; }
  .form-row { flex-direction: column; }
  .toast { left: 16px; right: 16px; bottom: 16px; text-align: center; }
}

@media (max-width: 480px) {
  .page-title { font-size: 0.95rem; }
  .topbar-content { padding: 10px 12px; }
  .page-wrapper { padding: 10px; }
  .viewing-row { padding: 12px; gap: 10px; }
  .row-thumb { height: 120px; }
  .prop-title { font-size: 0.88rem; }
  .notes-box { font-size: 0.75rem; padding: 6px 10px; }
  .negotiation-box { padding: 8px 10px; }
  .state-box { padding: 40px 16px; }
  .state-icon { font-size: 40px; }
  .modal-body { padding: 14px 16px; }
  .modal-header { padding: 14px 16px; }
  .modal-footer { padding: 12px 16px; }
}
</style>
