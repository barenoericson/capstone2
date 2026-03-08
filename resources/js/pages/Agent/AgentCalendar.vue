<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
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
          <router-link to="/agent/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/agent/calendar" class="nav-item active">
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
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
            <h1 class="page-title">My Calendar</h1>
          </div>
          <div class="topbar-right">
            <select v-model="selectedPropertyId" @change="loadCalendarData" class="filter-select">
              <option value="all">All Properties</option>
              <option v-for="p in properties" :key="p.id" :value="p.id">
                {{ p.title }} — {{ p.city }}
              </option>
            </select>
          </div>
        </div>
      </nav>

      <div class="page-wrapper">
        <!-- Loading -->
        <div v-if="loading" class="state-box">
          <div class="state-icon">⏳</div>
          <p>Loading calendar...</p>
        </div>

        <!-- Calendar -->
        <div v-else class="calendar-container">
          <!-- Month Navigation -->
          <div class="cal-month-header">
            <button @click="prevMonth" class="cal-nav-btn">← Prev</button>
            <h2 class="cal-month-title">{{ monthName }} {{ currentYear }}</h2>
            <button @click="nextMonth" class="cal-nav-btn">Next →</button>
          </div>

          <!-- Weekday Headers -->
          <div class="cal-weekday-row">
            <div v-for="d in dayNames" :key="d" class="cal-weekday-cell">{{ d }}</div>
          </div>

          <!-- Calendar Grid -->
          <div class="cal-grid">
            <div
              v-for="(day, idx) in calendarDays"
              :key="idx"
              class="cal-day-cell"
              :class="dayCellClass(day)"
              @click="openDayPanel(day)"
            >
              <span v-if="day.date" class="day-number">{{ day.dayNum }}</span>
              <div v-if="day.date && day.indicators.length > 0" class="dot-row">
                <span
                  v-for="(dot, i) in day.indicators"
                  :key="i"
                  class="status-dot"
                  :class="'dot-' + dot"
                  :title="dotLabel(dot)"
                ></span>
              </div>
              <span v-if="day.date && day.viewingCount > 0" class="day-count">{{ day.viewingCount }}</span>
            </div>
          </div>

          <!-- Legend -->
          <div class="cal-legend">
            <span class="legend-item"><span class="legend-dot dot-approved"></span> Approved</span>
            <span class="legend-item"><span class="legend-dot dot-requested"></span> Pending</span>
            <span class="legend-item"><span class="legend-dot dot-negotiating"></span> Negotiating</span>
            <span class="legend-item"><span class="legend-dot dot-blocked"></span> Blocked</span>
          </div>
        </div>
      </div>
    </main>

    <!-- ============================================================ -->
    <!-- RIGHT SIDE PANEL (Day Details) -->
    <!-- ============================================================ -->
    <transition name="slide-right">
      <div v-if="showDayPanel" class="day-panel">
        <div class="panel-header">
          <h3>{{ formatDate(selectedDay) }}</h3>
          <button @click="showDayPanel = false" class="btn-close">✕</button>
        </div>

        <div class="panel-body">
          <!-- Blocked Date Banner -->
          <div v-if="isSelectedDayBlocked" class="blocked-banner">
            <div class="blocked-banner-info">
              <span class="blocked-banner-icon">🚫</span>
              <div>
                <strong>Date Blocked</strong>
                <p v-if="selectedDayBlockedReason" class="blocked-banner-reason">{{ selectedDayBlockedReason }}</p>
              </div>
            </div>
            <button @click="unblockSelectedDay" class="btn-unblock-sm" :disabled="blockLoading">
              {{ blockLoading ? '...' : 'Unblock' }}
            </button>
          </div>

          <!-- Block This Date (if not blocked and not past) -->
          <div v-if="!isSelectedDayBlocked && !isSelectedDayPast" class="block-section">
            <div class="block-form-inline">
              <input
                type="text"
                v-model="blockReason"
                placeholder="Reason (optional)"
                class="form-input-sm"
              />
              <button @click="blockSelectedDay" class="btn-block-sm" :disabled="blockLoading">
                {{ blockLoading ? '...' : '🚫 Block' }}
              </button>
            </div>
          </div>

          <!-- Viewings List -->
          <div v-if="selectedDayViewings.length > 0" class="panel-viewings">
            <h4 class="panel-section-title">Viewings ({{ selectedDayViewings.length }})</h4>
            <div
              v-for="v in selectedDayViewings"
              :key="v.id"
              class="panel-viewing-card"
              :class="'card-' + v.status"
            >
              <div class="pv-header">
                <span class="pv-property">{{ v.property?.title || 'Property' }}</span>
                <span class="status-badge" :class="'badge-' + v.status">{{ statusLabel(v.status) }}</span>
              </div>
              <div class="pv-meta">
                <span v-if="v.property?.city" class="pv-city">{{ v.property.city }}</span>
              </div>
              <div class="pv-details">
                <span>👤 {{ v.buyer?.name || 'Buyer' }}</span>
                <span>🕐 {{ formatTime(v.viewing_time) }}</span>
              </div>

              <div v-if="v.buyer_notes" class="pv-notes">
                <strong>Note:</strong> {{ v.buyer_notes }}
              </div>

              <!-- Negotiation info -->
              <div v-if="v.latest_proposal" class="pv-negotiation">
                <div class="pv-neg-header">
                  🔄 <strong>{{ v.latest_proposal.proposed_by_role === 'buyer' ? 'Buyer' : 'You' }} proposed:</strong>
                </div>
                <div class="pv-neg-details">
                  📅 {{ formatDate(v.latest_proposal.proposed_date) }} at {{ formatTime(v.latest_proposal.proposed_time) }}
                </div>
                <div v-if="v.latest_proposal.note" class="pv-neg-note">
                  💬 {{ v.latest_proposal.note }}
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="pv-actions">
                <!-- Pending (requested) -->
                <template v-if="v.status === 'requested'">
                  <button @click="approveViewing(v)" class="btn-sm btn-approve-sm" :disabled="actionLoading === v.id">
                    {{ actionLoading === v.id ? '...' : '✅ Approve' }}
                  </button>
                  <button @click="openCounterModal(v)" class="btn-sm btn-edit-sm">✏️ Edit</button>
                  <button @click="openRejectModal(v)" class="btn-sm btn-reject-sm">❌ Reject</button>
                </template>

                <!-- Negotiating: buyer proposed -->
                <template v-if="v.status === 'negotiating' && v.latest_proposal?.proposed_by_role === 'buyer'">
                  <button @click="acceptProposal(v)" class="btn-sm btn-approve-sm" :disabled="actionLoading === v.id">
                    {{ actionLoading === v.id ? '...' : '✅ Accept' }}
                  </button>
                  <button @click="openCounterModal(v)" class="btn-sm btn-edit-sm">✏️ Edit</button>
                  <button @click="openRejectModal(v)" class="btn-sm btn-reject-sm">❌ Reject</button>
                </template>

                <!-- Negotiating: agent proposed (waiting) -->
                <template v-if="v.status === 'negotiating' && v.latest_proposal?.proposed_by_role === 'agent'">
                  <span class="waiting-text">⏳ Waiting for buyer</span>
                </template>

                <!-- Approved -->
                <template v-if="v.status === 'approved'">
                  <button @click="markCompleted(v)" class="btn-sm btn-complete-sm" :disabled="actionLoading === v.id">
                    {{ actionLoading === v.id ? '...' : '✔ Complete' }}
                  </button>
                </template>
              </div>
            </div>
          </div>

          <!-- No viewings -->
          <div v-else class="no-viewings">
            <p v-if="!isSelectedDayBlocked">No viewings scheduled for this date.</p>
          </div>
        </div>
      </div>
    </transition>

    <!-- ============================================================ -->
    <!-- REJECT MODAL -->
    <!-- ============================================================ -->
    <div v-if="showRejectModal" class="modal-overlay" @click.self="showRejectModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>❌ Reject Viewing</h3>
          <button @click="showRejectModal = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <p class="modal-subtitle">
            Reject the viewing request for
            <strong>{{ rejectTarget?.property?.title }}</strong> with
            <strong>{{ rejectTarget?.buyer?.name }}</strong>.
          </p>
          <div class="form-group">
            <label>Reason for rejection <span class="required">*</span></label>
            <textarea
              v-model="rejectionReason"
              class="form-textarea"
              placeholder="Provide a reason..."
              rows="3"
            ></textarea>
          </div>
          <span v-if="rejectError" class="form-error">{{ rejectError }}</span>
        </div>
        <div class="modal-footer">
          <button @click="showRejectModal = false" class="btn-secondary">Cancel</button>
          <button @click="submitReject" class="btn-danger" :disabled="rejectLoading">
            {{ rejectLoading ? 'Rejecting...' : 'Reject Viewing' }}
          </button>
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

    <!-- Toast -->
    <transition name="slide-up">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'AgentCalendar',

  data() {
    return {
      sidebarOpen: false,
      userName: '',
      myId: null,
      showUserMenu: false,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      loading: true,

      // Calendar state
      currentMonth: new Date().getMonth(),
      currentYear: new Date().getFullYear(),
      dayNames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

      // Data from API
      viewingsByDate: {},
      blockedDates: [],
      properties: [],

      // Filters
      selectedPropertyId: 'all',

      // Day panel
      showDayPanel: false,
      selectedDay: null,
      blockReason: '',
      blockLoading: false,

      // Reject modal
      showRejectModal: false,
      rejectTarget: null,
      rejectionReason: '',
      rejectError: '',
      rejectLoading: false,

      // Counter-propose modal
      showCounterModal: false,
      counterTarget: null,
      counterForm: { proposed_date: '', proposed_time: '', note: '' },
      counterLoading: false,
      counterError: '',

      actionLoading: null,
      toast: { show: false, type: 'success', message: '' },
    };
  },

  computed: {
    monthStr() {
      return `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}`;
    },

    monthName() {
      return new Date(this.currentYear, this.currentMonth).toLocaleString('en-PH', { month: 'long' });
    },

    today() {
      return new Date().toISOString().split('T')[0];
    },

    todayStr() {
      return this.today;
    },

    blockedDateSet() {
      return new Set(this.blockedDates.map(bd => bd.blocked_date));
    },

    calendarDays() {
      const year = this.currentYear;
      const month = this.currentMonth;
      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const days = [];

      for (let i = 0; i < firstDay; i++) {
        days.push({ date: null, dayNum: null, indicators: [], viewingCount: 0 });
      }

      for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const indicators = this.getDayIndicators(dateStr);
        const viewings = this.viewingsByDate[dateStr] || [];
        days.push({ date: dateStr, dayNum: d, indicators, viewingCount: viewings.length });
      }

      return days;
    },

    selectedDayViewings() {
      if (!this.selectedDay || !this.viewingsByDate[this.selectedDay]) return [];
      return this.viewingsByDate[this.selectedDay];
    },

    isSelectedDayBlocked() {
      return this.blockedDateSet.has(this.selectedDay);
    },

    selectedDayBlockedReason() {
      const bd = this.blockedDates.find(d => d.blocked_date === this.selectedDay);
      return bd?.reason || '';
    },

    selectedDayBlockedId() {
      const bd = this.blockedDates.find(d => d.blocked_date === this.selectedDay);
      return bd?.id || null;
    },

    isSelectedDayPast() {
      return this.selectedDay < this.todayStr;
    },
  },

  async mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Agent';
    this.myId = user.id;
    await this.loadCalendarData();
  },

  methods: {
    getDayIndicators(dateStr) {
      const indicators = [];
      const viewings = this.viewingsByDate[dateStr] || [];
      const statuses = new Set(viewings.map(v => v.status));

      if (statuses.has('approved'))    indicators.push('approved');
      if (statuses.has('requested'))   indicators.push('requested');
      if (statuses.has('negotiating')) indicators.push('negotiating');
      if (this.blockedDateSet.has(dateStr)) indicators.push('blocked');

      return indicators;
    },

    dayCellClass(day) {
      if (!day.date) return 'cal-empty';
      const classes = ['cal-active'];
      if (day.date === this.todayStr) classes.push('cal-today');
      if (day.date === this.selectedDay) classes.push('cal-selected');
      if (day.date < this.todayStr) classes.push('cal-past');
      if (day.indicators.length > 0) classes.push('cal-has-events');
      return classes.join(' ');
    },

    dotLabel(dot) {
      return { approved: 'Approved', requested: 'Pending', negotiating: 'Negotiating', blocked: 'Blocked' }[dot] || dot;
    },

    async loadCalendarData() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');
        const params = new URLSearchParams({
          month: this.monthStr,
          property_id: this.selectedPropertyId,
        });
        const res = await fetch(`${this.apiUrl}/api/agent/calendar-data?${params}`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.viewingsByDate = data.viewings_by_date || {};
          this.blockedDates = data.blocked_dates || [];
          if (data.properties && this.properties.length === 0) {
            this.properties = data.properties;
          }
        }
      } catch (e) {
        console.error('Error loading calendar data:', e);
      } finally {
        this.loading = false;
      }
    },

    prevMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
      this.showDayPanel = false;
      this.loadCalendarData();
    },

    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
      this.showDayPanel = false;
      this.loadCalendarData();
    },

    openDayPanel(day) {
      if (!day.date) return;
      this.selectedDay = day.date;
      this.blockReason = '';
      this.showDayPanel = true;
    },

    // ========================
    // Block / Unblock
    // ========================
    async blockSelectedDay() {
      try {
        this.blockLoading = true;
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/blocked-dates`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ blocked_date: this.selectedDay, reason: this.blockReason }),
        });
        const data = await res.json();
        if (data.success) {
          this.blockReason = '';
          await this.loadCalendarData();
          this.showToast('Date blocked successfully.', 'success');
        } else {
          this.showToast(data.message || 'Failed to block date.', 'error');
        }
      } catch (e) {
        this.showToast('Error blocking date.', 'error');
      } finally {
        this.blockLoading = false;
      }
    },

    async unblockSelectedDay() {
      const id = this.selectedDayBlockedId;
      if (!id) return;
      try {
        this.blockLoading = true;
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/blocked-dates/${id}`, {
          method: 'DELETE',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          await this.loadCalendarData();
          this.showToast('Date unblocked.', 'success');
        }
      } catch (e) {
        this.showToast('Error unblocking date.', 'error');
      } finally {
        this.blockLoading = false;
      }
    },

    // ========================
    // Viewing Actions
    // ========================
    async approveViewing(v) {
      try {
        this.actionLoading = v.id;
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/viewings/${v.id}/approve`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          await this.loadCalendarData();
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
      this.rejectTarget = v;
      this.rejectionReason = '';
      this.rejectError = '';
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
        const res = await fetch(`${this.apiUrl}/api/agent/viewings/${this.rejectTarget.id}/reject`, {
          method: 'PUT',
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ rejection_reason: this.rejectionReason }),
        });
        const data = await res.json();
        if (data.success) {
          this.showRejectModal = false;
          await this.loadCalendarData();
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
        const res = await fetch(`${this.apiUrl}/api/agent/viewings/${v.id}/completed`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          await this.loadCalendarData();
          this.showToast('Marked as completed!', 'success');
        }
      } catch (e) {
        this.showToast('Error updating viewing.', 'error');
      } finally {
        this.actionLoading = null;
      }
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
          await this.loadCalendarData();
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
          await this.loadCalendarData();
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

    // ========================
    // Helpers
    // ========================
    formatDate(dateStr) {
      if (!dateStr) return 'N/A';
      return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-PH', {
        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
      });
    },

    formatTime(timeStr) {
      if (!timeStr) return 'N/A';
      const [h, m] = timeStr.split(':');
      const hour = parseInt(h, 10);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const h12 = hour % 12 || 12;
      return `${h12}:${m} ${ampm}`;
    },

    statusLabel(status) {
      return { requested: 'Pending', negotiating: 'Negotiating', approved: 'Approved', rejected: 'Rejected', completed: 'Completed' }[status] || status;
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
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; color: #555; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: all 0.15s; }
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
.topbar-right { display: flex; align-items: center; gap: 12px; }

.filter-select {
  padding: 8px 14px; border-radius: 10px; border: 1.5px solid #e5e7eb;
  font-size: 0.85rem; font-weight: 500; color: #100c08; background: #fff;
  outline: none; min-width: 200px; cursor: pointer;
}
.filter-select:focus { border-color: #FFD700; }

/* ---- Page wrapper ---- */
.page-wrapper { padding: 24px; }

/* ---- State ---- */
.state-box { text-align: center; padding: 60px 20px; }
.state-icon { font-size: 52px; margin-bottom: 16px; }
.state-box p { color: #666; font-size: 0.9rem; }

/* ============================================================ */
/* CALENDAR */
/* ============================================================ */
.calendar-container {
  background: #fff; border-radius: 16px; padding: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #f0f0f0;
}

.cal-month-header {
  display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
}
.cal-month-title { font-size: 1.2rem; font-weight: 700; color: #100c08; margin: 0; }
.cal-nav-btn {
  padding: 8px 16px; border-radius: 10px; border: 1.5px solid #e5e7eb;
  background: #fff; font-weight: 600; font-size: 0.85rem; cursor: pointer; color: #100c08;
}
.cal-nav-btn:hover { background: #fef9e7; border-color: #FFD700; }

.cal-weekday-row { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 4px; }
.cal-weekday-cell {
  text-align: center; font-size: 0.75rem; font-weight: 700; color: #9ca3af;
  text-transform: uppercase; padding: 8px 0;
}

.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }

.cal-day-cell {
  min-height: 80px; border: 1px solid #f0f0f0; border-radius: 10px; padding: 8px;
  cursor: pointer; transition: all 0.15s; display: flex; flex-direction: column;
  position: relative;
}
.cal-day-cell.cal-active:hover { background: #fef9e7; border-color: #FFD700; }
.cal-empty { cursor: default; background: transparent; border-color: transparent; }
.cal-today { border-color: #FFD700; background: #fffdf5; }
.cal-selected { border-color: #FFD700; background: #fef9e7; box-shadow: 0 0 0 2px rgba(230,174,13,0.3); }
.cal-past { opacity: 0.5; }
.cal-has-events { background: #fafafa; }

.day-number { font-size: 0.85rem; font-weight: 600; color: #100c08; }
.day-count {
  position: absolute; top: 6px; right: 8px; font-size: 0.65rem; font-weight: 700;
  color: #6b7280; background: #f3f4f6; border-radius: 8px; padding: 1px 5px;
}

.dot-row { display: flex; gap: 4px; margin-top: auto; flex-wrap: wrap; }
.status-dot { width: 8px; height: 8px; border-radius: 50%; }
.dot-approved    { background: #16a34a; }
.dot-requested   { background: #FFD700; }
.dot-negotiating { background: #2563eb; }
.dot-blocked     { background: #dc2626; }

.cal-legend { display: flex; gap: 20px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f0f0f0; justify-content: center; flex-wrap: wrap; }
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: #6b7280; font-weight: 500; }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

/* ============================================================ */
/* SIDE PANEL */
/* ============================================================ */
.day-panel {
  position: fixed; top: 0; right: 0; width: 360px; height: 100vh;
  background: #fff; border-left: 1px solid #f0f0f0;
  box-shadow: -4px 0 16px rgba(0,0,0,0.08); z-index: 150;
  display: flex; flex-direction: column;
}
.panel-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 20px 16px; border-bottom: 1px solid #f0f0f0;
}
.panel-header h3 { font-size: 1rem; font-weight: 700; color: #100c08; margin: 0; }
.panel-body { flex: 1; padding: 16px 20px; overflow-y: auto; }

/* Blocked banner */
.blocked-banner {
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
  padding: 12px 14px; border-radius: 12px; background: #fee2e2; border: 1px solid #fecaca;
  margin-bottom: 16px;
}
.blocked-banner-info { display: flex; align-items: center; gap: 10px; }
.blocked-banner-icon { font-size: 20px; }
.blocked-banner-info strong { font-size: 0.85rem; color: #991b1b; }
.blocked-banner-reason { font-size: 0.78rem; color: #b91c1c; margin: 2px 0 0; }
.btn-unblock-sm {
  padding: 6px 12px; border-radius: 8px; background: #fff; color: #dc2626;
  border: 1px solid #fecaca; font-size: 0.78rem; font-weight: 700; cursor: pointer;
  white-space: nowrap;
}
.btn-unblock-sm:hover:not(:disabled) { background: #dc2626; color: #fff; }

/* Block section */
.block-section { margin-bottom: 16px; }
.block-form-inline { display: flex; gap: 8px; }
.form-input-sm {
  flex: 1; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #e5e7eb;
  font-size: 0.82rem; outline: none;
}
.form-input-sm:focus { border-color: #FFD700; }
.btn-block-sm {
  padding: 8px 14px; border-radius: 8px; background: #fee2e2; color: #991b1b;
  border: 1px solid #fecaca; font-size: 0.78rem; font-weight: 700; cursor: pointer;
  white-space: nowrap;
}
.btn-block-sm:hover:not(:disabled) { background: #dc2626; color: #fff; }

/* Panel viewings */
.panel-section-title { font-size: 0.88rem; font-weight: 700; color: #100c08; margin: 0 0 12px; }
.panel-viewing-card {
  background: #fafafa; border-radius: 12px; padding: 14px; margin-bottom: 12px;
  border: 1px solid #f0f0f0;
}
.panel-viewing-card.card-approved    { border-left: 4px solid #16a34a; }
.panel-viewing-card.card-requested   { border-left: 4px solid #FFD700; }
.panel-viewing-card.card-negotiating { border-left: 4px solid #2563eb; }
.panel-viewing-card.card-completed   { border-left: 4px solid #6b7280; }

.pv-header { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 4px; }
.pv-property { font-size: 0.88rem; font-weight: 700; color: #100c08; }
.pv-meta { margin-bottom: 6px; }
.pv-city { font-size: 0.78rem; color: #6b7280; }
.pv-details { display: flex; gap: 14px; font-size: 0.8rem; color: #555; margin-bottom: 6px; }
.pv-notes { font-size: 0.78rem; color: #555; padding: 6px 10px; background: #f3f4f6; border-radius: 8px; margin-bottom: 6px; line-height: 1.5; }

.pv-negotiation { padding: 8px 10px; border-radius: 8px; background: #eff6ff; border: 1px solid #bfdbfe; margin-bottom: 6px; }
.pv-neg-header { font-size: 0.8rem; color: #1e40af; margin-bottom: 2px; }
.pv-neg-details { font-size: 0.78rem; color: #1e40af; }
.pv-neg-note { font-size: 0.78rem; color: #3b82f6; font-style: italic; margin-top: 2px; }

.pv-actions { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }
.btn-sm { padding: 6px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; border: 1px solid transparent; }
.btn-approve-sm { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
.btn-approve-sm:hover:not(:disabled) { background: #16a34a; color: #fff; }
.btn-edit-sm { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
.btn-edit-sm:hover { background: #2563eb; color: #fff; }
.btn-reject-sm { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
.btn-reject-sm:hover { background: #dc2626; color: #fff; }
.btn-complete-sm { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
.btn-complete-sm:hover:not(:disabled) { background: #374151; color: #fff; }
.waiting-text { font-size: 0.78rem; color: #6b7280; font-style: italic; }

.no-viewings { text-align: center; padding: 24px 0; }
.no-viewings p { color: #999; font-size: 0.85rem; }

.status-badge { padding: 3px 8px; border-radius: 20px; font-size: 0.68rem; font-weight: 700; white-space: nowrap; }
.badge-requested { background: #fef9c3; color: #854d0e; }
.badge-approved  { background: #dcfce7; color: #166534; }
.badge-rejected  { background: #fee2e2; color: #991b1b; }
.badge-completed { background: #f3f4f6; color: #374151; }
.badge-negotiating { background: #dbeafe; color: #1e40af; }

/* Slide transition */
.slide-right-enter-active, .slide-right-leave-active { transition: transform 0.3s ease; }
.slide-right-enter-from, .slide-right-leave-to { transform: translateX(100%); }

/* ---- Modal ---- */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 20px; }
.modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 480px; overflow: hidden; }
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
.form-textarea:focus { border-color: #FFD700; }
.form-input { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; }
.form-input:focus { border-color: #FFD700; }
.form-error { font-size: 0.78rem; color: #dc2626; }

/* Buttons */
.btn-primary   { padding: 10px 20px; background: #FFD700; color: #100c08; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-primary:hover { background: #DAB600; }
.btn-secondary { padding: 10px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger    { padding: 10px 20px; background: #dc2626; color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger:hover:not(:disabled) { background: #b91c1c; }
button:disabled { opacity: 0.5; cursor: not-allowed; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 1000; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.toast-info    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }

/* ---- Responsive ---- */
@media (max-width: 768px) {
  .sidebar { display: none; }
  .main-content { margin-left: 0; }
  .cal-day-cell { min-height: 60px; padding: 4px; }
  .day-number { font-size: 0.75rem; }
  .day-count { display: none; }
  .day-panel { width: 100%; }
  .filter-select { min-width: 140px; }
}
</style>
