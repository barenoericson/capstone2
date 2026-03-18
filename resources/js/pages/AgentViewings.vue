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
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
          <h1 class="page-title">Viewing Requests</h1>
        </div>
        <div class="topbar-right">
          <button class="btn-calendar" @click="showCalendarManager = true">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Manage Calendar
          </button>
          <div class="filter-tabs">
            <button
              v-for="tab in tabs"
              :key="tab.value"
              class="f-tab"
              :class="{ active: activeTab === tab.value }"
              @click="setTab(tab.value)"
            >
              {{ tab.label }}
              <span v-if="tab.value === 'requested' && pendingCount > 0" class="tab-badge">{{ pendingCount }}</span>
              <span v-if="tab.value === 'negotiating' && negotiatingCount > 0" class="tab-badge tab-badge-blue">{{ negotiatingCount }}</span>
            </button>
          </div>
        </div>
      </header>

      <div class="content-area">
        <div class="content-inner">
          <!-- Loading -->
          <div v-if="loading" class="state-center">
            <div class="spinner"></div>
            <p class="state-txt">Loading viewing requests...</p>
          </div>

          <!-- Empty state -->
          <div v-else-if="filteredViewings.length === 0" class="state-center">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--s300)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <h3 class="state-head">No {{ activeTab === 'all' ? 'requested' : activeTab }} viewing requests</h3>
            <p class="state-txt">When buyers request viewings for your properties, they'll appear here.</p>
          </div>

          <!-- Viewings Grid -->
          <div v-else>
            <p class="grid-meta">{{ filteredViewings.length }} viewing{{ filteredViewings.length !== 1 ? 's' : '' }}</p>
            <div class="vw-grid">
              <div v-for="v in filteredViewings" :key="v.id" class="vw-card" :class="'st-' + v.status">

                <div class="card-thumb">
                  <img v-if="v.property && v.property.cover_photo" :src="v.property.cover_photo" :alt="v.property.title" class="thumb-img" />
                  <div v-else class="thumb-empty">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--s300)" stroke-width="1.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  </div>
                  <span class="status-pill" :class="'pill-' + v.status">{{ statusLabel(v.status) }}</span>
                </div>

                <div class="card-body">
                  <h3 class="prop-name">{{ v.property ? v.property.title : 'Property' }}</h3>
                  <p class="prop-city" v-if="v.property">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ v.property.city }}
                  </p>

                  <div class="meta-row">
                    <span class="meta-item">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                      {{ formatDate(v.viewing_date) }}
                    </span>
                    <span class="meta-item">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                      {{ formatTime(v.viewing_time) }}
                    </span>
                  </div>

                  <div v-if="v.buyer" class="buyer-row">
                    <div class="buyer-av">{{ (v.buyer.name || 'B').charAt(0).toUpperCase() }}</div>
                    <span class="buyer-nm">{{ v.buyer.name }}</span>
                  </div>

                  <div v-if="v.buyer_notes" class="info-box buyer-box">
                    <span class="box-label">Buyer note</span>
                    {{ v.buyer_notes }}
                  </div>
                  <div v-if="v.status === 'rejected' && v.rejection_reason" class="info-box reject-box">
                    <span class="box-label">Rejection reason</span>
                    {{ v.rejection_reason }}
                  </div>
                  <div v-if="v.status === 'approved'" class="info-box approve-box">
                    Viewing confirmed! Buyer will be notified.
                  </div>

                  <!-- Negotiation info -->
                  <div v-if="v.latest_proposal" class="info-box nego-box">
                    <span class="box-label">Proposed by {{ v.latest_proposal.proposed_by_role === 'buyer' ? 'Buyer' : 'You' }}</span>
                    {{ formatDate(v.latest_proposal.proposed_date) }} at {{ formatTime(v.latest_proposal.proposed_time) }}
                    <div v-if="v.latest_proposal.note" class="proposal-note">"{{ v.latest_proposal.note }}"</div>
                  </div>

                  <div class="card-actions">
                    <!-- Approve / Edit Schedule / Reject for pending (requested) -->
                    <template v-if="v.status === 'requested'">
                      <button @click="approveViewing(v)" class="btn-act btn-accept" :disabled="actionLoading === v.id">
                        {{ actionLoading === v.id ? '...' : 'Approve' }}
                      </button>
                      <button @click="openCounterModal(v)" class="btn-act btn-edit">Edit Schedule</button>
                      <button @click="openRejectModal(v)" class="btn-act btn-reject-sm">Reject</button>
                    </template>

                    <!-- Negotiating: buyer proposed -->
                    <template v-if="v.status === 'negotiating' && v.latest_proposal && v.latest_proposal.proposed_by_role === 'buyer'">
                      <button @click="acceptProposal(v)" class="btn-act btn-accept" :disabled="actionLoading === v.id">
                        {{ actionLoading === v.id ? '...' : 'Accept' }}
                      </button>
                      <button @click="openCounterModal(v)" class="btn-act btn-edit">Edit Schedule</button>
                      <button @click="openRejectModal(v)" class="btn-act btn-reject-sm">Reject</button>
                    </template>

                    <!-- Negotiating: agent proposed -->
                    <template v-if="v.status === 'negotiating' && v.latest_proposal && v.latest_proposal.proposed_by_role === 'agent'">
                      <span class="waiting-lbl">Waiting for buyer response...</span>
                    </template>

                    <!-- Mark completed for approved -->
                    <template v-if="v.status === 'approved'">
                      <button @click="markCompleted(v)" class="btn-act btn-complete" :disabled="actionLoading === v.id">
                        {{ actionLoading === v.id ? '...' : 'Mark Completed' }}
                      </button>
                    </template>
                  </div>
                </div>
              </div>
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

    loadProfilePhoto() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__ || 'http://localhost:8000'}/storage/${user.profile_photo_path}`;
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
.main-content { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* ---- Topbar ---- */
.topbar {
  background: var(--white); position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 28px; height: var(--th); border-bottom: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.topbar-left { display: flex; align-items: center; gap: 10px; }
.hamburger-btn { display: none; width: 34px; height: 34px; border-radius: 7px; border: 1px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s600); align-items: center; justify-content: center; }
.page-title { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--navy); letter-spacing: -0.3px; margin: 0; }
.topbar-right { display: flex; align-items: center; gap: 12px; }
.btn-calendar { padding: 8px 16px; border-radius: 50px; border: 1.5px solid var(--gold); background: transparent; color: var(--navy); font-size: 0.82rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: var(--fb); }
.btn-calendar:hover { background: rgba(216,155,15,0.08); }

/* Filter tabs */
.filter-tabs { display: flex; gap: 4px; }
.f-tab {
  padding: 6px 14px; border-radius: 50px; border: 1.5px solid var(--s200);
  background: transparent; font-size: 12px; font-weight: 600; color: var(--s500);
  cursor: pointer; transition: all 0.15s; font-family: var(--fb); white-space: nowrap; position: relative;
}
.f-tab:hover { border-color: rgba(216,155,15,0.4); color: var(--navy); }
.f-tab.active { background: var(--navy); border-color: var(--navy); color: var(--white); font-weight: 700; }
.tab-badge { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; background: #dc2626; color: #fff; font-size: 10px; font-weight: 700; margin-left: 4px; }
.tab-badge-blue { background: #2563eb; }

/* ---- Content ---- */
.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 1200px; margin: 0 auto; padding: 28px 30px 48px; }

/* States */
.state-center { text-align: center; padding: 80px 20px; display: flex; flex-direction: column; align-items: center; gap: 14px; }
.state-head { font-family: var(--fd); font-size: 20px; font-weight: 700; color: var(--navy); margin: 0; }
.state-txt  { font-size: 13.5px; color: var(--s400); margin: 0; }
.spinner { width: 36px; height: 36px; border-radius: 50%; border: 3px solid var(--s200); border-top-color: var(--gold); animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.grid-meta { font-size: 12.5px; color: var(--s400); margin: 0 0 18px; }

/* ---- Viewings Grid ---- */
.vw-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }

.vw-card {
  background: var(--white); border-radius: 14px; overflow: hidden;
  border: 1px solid var(--s200); border-left: 4px solid var(--s200);
  transition: transform 0.2s, box-shadow 0.2s;
}
.vw-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(40,53,62,0.1); }

.vw-card.st-approved    { border-left-color: #16a34a; }
.vw-card.st-rejected    { border-left-color: #dc2626; }
.vw-card.st-requested   { border-left-color: var(--gold); }
.vw-card.st-completed   { border-left-color: var(--s300); }
.vw-card.st-negotiating { border-left-color: #2563eb; }

.card-thumb { position: relative; height: 162px; background: var(--s100); }
.thumb-img  { width: 100%; height: 100%; object-fit: cover; }
.thumb-empty { display: flex; align-items: center; justify-content: center; height: 100%; }

.status-pill { position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.pill-requested   { background: rgba(216,155,15,0.12); color: var(--gold); border: 1px solid rgba(216,155,15,0.25); }
.pill-approved    { background: #dcfce7; color: #166534; }
.pill-rejected    { background: #fee2e2; color: #991b1b; }
.pill-completed   { background: var(--s100); color: var(--s500); }
.pill-negotiating { background: #dbeafe; color: #1e40af; }

.card-body { padding: 15px 17px; }
.prop-name { font-family: var(--fd); font-size: 14.5px; font-weight: 700; color: var(--navy); margin: 0 0 4px; }
.prop-city { font-size: 12px; color: var(--s400); margin: 0 0 11px; display: flex; align-items: center; gap: 4px; }

.meta-row  { display: flex; gap: 14px; margin-bottom: 10px; flex-wrap: wrap; }
.meta-item { display: flex; align-items: center; gap: 5px; font-size: 12.5px; color: var(--s500); font-weight: 500; }

.buyer-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
.buyer-av  {
  width: 24px; height: 24px; border-radius: 50%;
  background: linear-gradient(135deg, var(--navy), #1a3158);
  color: var(--gold2); font-weight: 700; font-size: 10px;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.buyer-nm { font-size: 12.5px; color: var(--s500); font-weight: 600; }

.info-box { padding: 10px 12px; border-radius: 8px; font-size: 12.5px; margin-bottom: 10px; line-height: 1.55; }
.box-label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; opacity: 0.65; }
.buyer-box   { background: var(--s50); border: 1px solid var(--s200); color: var(--s600); }
.reject-box  { background: #fff5f5; border: 1px solid #fecaca; color: #991b1b; }
.approve-box { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.nego-box    { background: rgba(40,53,62,0.04); border: 1px solid rgba(40,53,62,0.1); color: var(--navy); }
.proposal-note { margin-top: 5px; font-style: italic; opacity: 0.75; }

.card-actions { display: flex; gap: 7px; margin-top: 13px; flex-wrap: wrap; }
.btn-act { padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; transition: all 0.15s; font-family: var(--fb); }
.btn-accept    { background: #dcfce7; color: #166534; }
.btn-accept:hover:not(:disabled) { background: #16a34a; color: #fff; }
.btn-edit      { background: rgba(40,53,62,0.08); color: var(--navy); }
.btn-edit:hover { background: var(--navy); color: #fff; }
.btn-reject-sm { background: #fee2e2; color: #991b1b; }
.btn-reject-sm:hover { background: #dc2626; color: #fff; }
.btn-complete  { background: var(--s100); color: var(--navy); }
.btn-complete:hover:not(:disabled) { background: var(--navy); color: #fff; }
.waiting-lbl { font-size: 12px; color: var(--s300); font-style: italic; }
button:disabled { opacity: 0.5; cursor: not-allowed; }

/* ---- Modal ---- */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 2000; padding: 20px; }
.modal-box { background: var(--white); border-radius: 16px; width: 90%; max-width: 480px; box-shadow: 0 12px 40px rgba(40,53,62,0.18); overflow: hidden; }
.modal-wide { max-width: 600px; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--s100); }
.modal-header h3 { font-family: var(--fd); font-size: 1rem; font-weight: 700; color: var(--navy); margin: 0; }
.btn-close { background: none; border: none; cursor: pointer; font-size: 18px; color: var(--s300); padding: 2px; display: flex; align-items: center; transition: color 0.15s; }
.btn-close:hover { color: var(--s500); }
.modal-body { padding: 20px 24px; max-height: 70vh; overflow-y: auto; }
.modal-subtitle { font-size: 0.88rem; color: var(--s400); margin-bottom: 16px; line-height: 1.6; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 14px 24px; border-top: 1px solid var(--s100); }

.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.form-group label { font-size: 11.5px; font-weight: 700; color: var(--s500); text-transform: uppercase; letter-spacing: 0.5px; }
.required { color: #dc2626; }
.form-textarea { width: 100%; border: 1px solid var(--s200); border-radius: 8px; padding: 9px 12px; font-size: 14px; resize: vertical; outline: none; transition: border-color 0.2s; font-family: var(--fb); box-sizing: border-box; }
.form-textarea:focus { border-color: rgba(216,155,15,0.5); box-shadow: 0 0 0 3px rgba(216,155,15,0.08); }
.form-input { width: 100%; border: 1px solid var(--s200); border-radius: 8px; padding: 9px 12px; font-size: 14px; outline: none; transition: border-color 0.2s; font-family: var(--fb); box-sizing: border-box; }
.form-input:focus { border-color: rgba(216,155,15,0.5); box-shadow: 0 0 0 3px rgba(216,155,15,0.08); }
.form-error { font-size: 0.78rem; color: #dc2626; }
.form-error { font-size: 0.78rem; color: #dc2626; }

/* Block form */
/* Buttons */
.btn-primary   { padding: 9px 20px; border-radius: 8px; border: none; background: var(--navy); color: var(--white); font-weight: 700; font-size: 13px; cursor: pointer; transition: background 0.15s; font-family: var(--fb); }
.btn-primary:hover { background: #102445; }
.btn-secondary { padding: 9px 18px; border-radius: 8px; border: 1.5px solid var(--s200); background: var(--white); color: var(--s500); font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.15s; font-family: var(--fb); }
.btn-secondary:hover { background: var(--s100); }
.btn-danger    { padding: 9px 20px; border-radius: 8px; border: none; background: #dc2626; color: #fff; font-weight: 700; font-size: 13px; cursor: pointer; font-family: var(--fb); }
.btn-danger:hover:not(:disabled) { background: #b91c1c; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 9999; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
.toast-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fff5f5; color: #991b1b; border: 1px solid #fecaca; }
.toast-info    { background: rgba(40,53,62,0.05); color: var(--navy); border: 1px solid rgba(40,53,62,0.1); }

/* Block form */
.block-form { margin-bottom: 20px; }
.form-row { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.form-row .form-group { flex: 1; min-width: 140px; margin-bottom: 0; }
.btn-block { padding: 10px 16px; border-radius: 10px; background: var(--gold); color: var(--navy); border: none; font-weight: 700; font-size: 0.85rem; cursor: pointer; white-space: nowrap; font-family: var(--fb); }
.btn-block:hover:not(:disabled) { background: var(--gold3); }

.blocked-title { font-size: 0.88rem; font-weight: 700; color: var(--navy); margin-bottom: 10px; }
.state-empty { padding: 16px 0; color: var(--s400); font-size: 0.85rem; }
.blocked-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 10px; background: rgba(216,155,15,0.06); border: 1px solid rgba(216,155,15,0.2); margin-bottom: 8px; }
.blocked-info { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.blocked-date { font-size: 0.85rem; font-weight: 700; color: var(--navy); }
.blocked-reason { font-size: 0.8rem; color: var(--s500); }
.btn-unblock { padding: 5px 12px; border-radius: 8px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 0.78rem; font-weight: 700; cursor: pointer; }
.btn-unblock:hover { background: #dc2626; color: #fff; }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .hamburger-btn { display: flex; }
  .main-content { margin-left: 0; }
  .topbar { padding: 0 16px; height: auto; flex-direction: column; align-items: flex-start; padding: 12px 16px; gap: 10px; }
  .topbar-right { width: 100%; flex-wrap: wrap; }
  .filter-tabs { flex-wrap: wrap; gap: 5px; }
  .f-tab { padding: 5px 11px; font-size: 11.5px; }
  .btn-calendar { font-size: 0.78rem; padding: 6px 12px; }
  .content-inner { padding: 16px; }
  .vw-grid { grid-template-columns: 1fr; gap: 14px; }
  .card-actions { flex-wrap: wrap; gap: 6px; }
  .modal-box { width: 95%; }
  .modal-footer { flex-direction: column-reverse; gap: 8px; }
  .toast { left: 16px; right: 16px; bottom: 16px; }
}
@media (max-width: 480px) {
  .content-inner { padding: 12px; }
  .card-thumb { height: 140px; }
}
</style>
