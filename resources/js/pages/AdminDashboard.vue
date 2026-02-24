<template>
  <div class="admin-wrapper">
    <div class="admin-header">
      <h1>🔐 Admin Dashboard</h1>
      <div class="header-stats">
        <div class="stat">
          <span class="stat-label">Pending</span>
          <span class="stat-value">{{ pendingAgents.length }}</span>
        </div>
        <div class="stat">
          <span class="stat-label">Verified</span>
          <span class="stat-value">{{ verifiedAgents.length }}</span>
        </div>
        <div class="stat">
          <span class="stat-label">Users</span>
          <span class="stat-value">{{ allUsers.length }}</span>
        </div>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="admin-nav-tabs">
      <button
        v-for="tab in navTabs"
        :key="tab.id"
        :class="['nav-tab', { active: activeTab === tab.id }]"
        @click="activeTab = tab.id"
      >
        {{ tab.icon }} {{ tab.label }}
      </button>
    </div>

    <!-- AGENT VERIFICATION TAB -->
    <section v-if="activeTab === 'agents'" class="tab-content">
      <div class="verification-header">
        <h2>Agent Verification</h2>
        <div class="tabs-group">
          <button
            v-for="filter in ['pending', 'verified', 'rejected']"
            :key="filter"
            :class="['tab-filter', { active: agentFilter === filter }]"
            @click="agentFilter = filter"
          >
            {{ getAgentFilterLabel(filter) }}
          </button>
        </div>
      </div>

      <!-- PENDING AGENTS -->
      <div v-if="agentFilter === 'pending'" class="agents-section">
        <div v-if="pendingAgents.length === 0" class="empty-message">
          ✅ No pending applications!
        </div>

        <div v-if="loadingAgents" class="loading">⏳ Loading...</div>

        <div v-for="agent in pendingAgents" :key="agent.id" class="agent-card">
          <div class="agent-card-header">
            <div class="avatar">{{ agent.user?.name?.charAt(0) || 'U' }}</div>
            <div class="agent-name-email">
              <h3>{{ agent.user?.name }}</h3>
              <p>{{ agent.user?.email }}</p>
            </div>
            <span class="badge-pending">⏳ Pending</span>
          </div>

          <div class="agent-card-body">
            <div class="doc-field">
              <label>License Number:</label>
              <p>{{ agent.license_number }}</p>
            </div>
            <div class="doc-field">
              <label>PRC ID:</label>
              <p>{{ agent.prc_id }}</p>
            </div>
            <div class="doc-field">
              <label>Company:</label>
              <p>{{ agent.company_name || '—' }}</p>
            </div>
            <div class="doc-field">
              <label>Bio:</label>
              <p>{{ agent.bio }}</p>
            </div>
            <div class="doc-field">
              <label>Applied:</label>
              <p>{{ formatDate(agent.applied_at) }}</p>
            </div>
          </div>

          <div class="agent-card-actions">
            <button @click="approveAgent(agent)" class="btn-approve" :disabled="approvingId === agent.id">
              {{ approvingId === agent.id ? '✓ Approving...' : '✅ Approve' }}
            </button>
            <button @click="openRejectModal(agent)" class="btn-reject">❌ Reject</button>
          </div>
        </div>
      </div>

      <!-- VERIFIED AGENTS -->
      <div v-if="agentFilter === 'verified'" class="agents-section">
        <div v-if="verifiedAgents.length === 0" class="empty-message">No verified agents yet</div>
        <div v-for="agent in verifiedAgents" :key="agent.id" class="agent-card verified">
          <div class="agent-card-header">
            <div class="avatar verified">{{ agent.user?.name?.charAt(0) || 'U' }}</div>
            <div class="agent-name-email">
              <h3>{{ agent.user?.name }}</h3>
              <p>{{ agent.user?.email }}</p>
            </div>
            <span class="badge-verified">✅ Verified</span>
          </div>
          <div class="agent-card-body">
            <p>Verified on {{ formatDate(agent.verified_at) }}</p>
          </div>
        </div>
      </div>

      <!-- REJECTED AGENTS -->
      <div v-if="agentFilter === 'rejected'" class="agents-section">
        <div v-if="rejectedAgents.length === 0" class="empty-message">No rejected applications</div>
        <div v-for="agent in rejectedAgents" :key="agent.id" class="agent-card rejected">
          <div class="agent-card-header">
            <div class="avatar rejected">{{ agent.user?.name?.charAt(0) || 'U' }}</div>
            <div class="agent-name-email">
              <h3>{{ agent.user?.name }}</h3>
              <p>{{ agent.user?.email }}</p>
            </div>
            <span class="badge-rejected">❌ Rejected</span>
          </div>
          <div class="agent-card-body">
            <div class="rejection-info">
              <label>Reason:</label>
              <p>{{ agent.rejection_reason }}</p>
              <small>{{ formatDate(agent.rejected_at) }}</small>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- USERS MANAGEMENT TAB -->
    <section v-if="activeTab === 'users'" class="tab-content">
      <h2>User Management</h2>

      <div v-if="loadingUsers" class="loading">⏳ Loading users...</div>

      <table v-else class="users-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in allUsers" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span class="role-badge" :class="'role-' + user.role">{{ capitalizeRole(user.role) }}</span>
            </td>
            <td>
              <span class="status-badge" :class="'status-' + user.status">{{ capitalizeRole(user.status) }}</span>
            </td>
            <td>{{ formatDate(user.created_at) }}</td>
            <td class="action-cell">
              <button
                v-if="user.status === 'active'"
                @click="suspendUser(user)"
                class="btn-action"
                title="Suspend"
                :disabled="suspendingId === user.id"
              >
                ⛔
              </button>
              <button
                v-else
                @click="reactivateUser(user)"
                class="btn-action"
                title="Reactivate"
                :disabled="reactivatingId === user.id"
              >
                ✅
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- REJECT MODAL -->
    <div v-if="showRejectModal" class="modal-backdrop" @click.self="showRejectModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Reject Application</h3>
          <button @click="showRejectModal = false" class="close-btn">✕</button>
        </div>

        <div class="modal-body">
          <p v-if="selectedAgent" class="modal-agent-name">
            Agent: <strong>{{ selectedAgent.user?.name }}</strong>
          </p>

          <div class="form-field">
            <label>Reason for Rejection:</label>
            <textarea
              v-model="rejectionReason"
              placeholder="Explain why you are rejecting this application..."
              rows="5"
              class="textarea-field"
            ></textarea>
          </div>

          <p class="info-note">ℹ️ The agent will receive this feedback and can reapply after 30 days.</p>
        </div>

        <div class="modal-footer">
          <button @click="showRejectModal = false" class="btn-cancel">Cancel</button>
          <button @click="confirmReject" class="btn-reject btn-large" :disabled="rejectingId">
            {{ rejectingId ? '⏳ Rejecting...' : 'Reject Application' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { adminAPI, agentAPI } from '../src/services/api';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      activeTab: 'agents',
      agentFilter: 'pending',
      showRejectModal: false,
      selectedAgent: null,
      rejectionReason: '',

      // Loading states
      loadingAgents: false,
      loadingUsers: false,
      approvingId: null,
      rejectingId: null,
      suspendingId: null,
      reactivatingId: null,

      // Data
      pendingAgents: [],
      verifiedAgents: [],
      rejectedAgents: [],
      allUsers: [],

      navTabs: [
        { id: 'agents', label: 'Agent Verification', icon: '🧑‍💼' },
        { id: 'users', label: 'Users', icon: '👥' },
      ],
    };
  },

  computed: {
    totalPending() {
      return this.pendingAgents.length;
    },
    totalVerified() {
      return this.verifiedAgents.length;
    },
  },

  methods: {
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      });
    },

    capitalizeRole(text) {
      if (!text) return '';
      return text.charAt(0).toUpperCase() + text.slice(1);
    },

    getAgentFilterLabel(filter) {
      const counts = {
        pending: this.pendingAgents.length,
        verified: this.verifiedAgents.length,
        rejected: this.rejectedAgents.length,
      };
      const labels = {
        pending: `⏳ Pending (${counts.pending})`,
        verified: `✅ Verified (${counts.verified})`,
        rejected: `❌ Rejected (${counts.rejected})`,
      };
      return labels[filter] || filter;
    },

    // ============ API CALLS ============

    async loadPendingAgents() {
      try {
        this.loadingAgents = true;
        const response = await fetch(`${this.getApiUrl()}/admin/agents/pending`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
        });

        if (response.ok) {
          const data = await response.json();
          this.pendingAgents = data.applications || [];
        }
      } catch (error) {
        console.error('Error loading pending agents:', error);
        alert('Failed to load pending agents');
      } finally {
        this.loadingAgents = false;
      }
    },

    async loadVerifiedAgents() {
      try {
        const response = await fetch(`${this.getApiUrl()}/admin/agents/verified`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
        });

        if (response.ok) {
          const data = await response.json();
          this.verifiedAgents = data.agents || [];
        }
      } catch (error) {
        console.error('Error loading verified agents:', error);
      }
    },

    async loadRejectedAgents() {
      try {
        const response = await fetch(`${this.getApiUrl()}/admin/agents/rejected`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
        });

        if (response.ok) {
          const data = await response.json();
          this.rejectedAgents = data.applications || [];
        }
      } catch (error) {
        console.error('Error loading rejected agents:', error);
      }
    },

    async loadAllUsers() {
      try {
        this.loadingUsers = true;
        const response = await fetch(`${this.getApiUrl()}/admin/users`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
        });

        if (response.ok) {
          const data = await response.json();
          this.allUsers = data.users || [];
        }
      } catch (error) {
        console.error('Error loading users:', error);
        alert('Failed to load users');
      } finally {
        this.loadingUsers = false;
      }
    },

    async approveAgent(agent) {
      if (!confirm(`Approve ${agent.user?.name} as an agent?`)) return;

      try {
        this.approvingId = agent.id;
        const response = await fetch(`${this.getApiUrl()}/admin/agents/${agent.id}/approve`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
          },
        });

        if (response.ok) {
          alert(`✅ ${agent.user?.name} has been approved as an agent!`);
          // Reload data
          await this.loadPendingAgents();
          await this.loadVerifiedAgents();
        } else {
          alert('Failed to approve agent');
        }
      } catch (error) {
        console.error('Error approving agent:', error);
        alert('Error approving agent');
      } finally {
        this.approvingId = null;
      }
    },

    openRejectModal(agent) {
      this.selectedAgent = agent;
      this.rejectionReason = '';
      this.showRejectModal = true;
    },

    async confirmReject() {
      if (!this.rejectionReason.trim()) {
        alert('Please provide a rejection reason');
        return;
      }

      try {
        this.rejectingId = this.selectedAgent.id;
        const response = await fetch(`${this.getApiUrl()}/admin/agents/${this.selectedAgent.id}/reject`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            reason: this.rejectionReason,
          }),
        });

        if (response.ok) {
          alert(`❌ Application rejected. Agent will be notified.`);
          this.showRejectModal = false;
          // Reload data
          await this.loadPendingAgents();
          await this.loadRejectedAgents();
        } else {
          alert('Failed to reject application');
        }
      } catch (error) {
        console.error('Error rejecting application:', error);
        alert('Error rejecting application');
      } finally {
        this.rejectingId = null;
      }
    },

    async suspendUser(user) {
      if (!confirm(`Suspend ${user.name}?`)) return;

      try {
        this.suspendingId = user.id;
        const response = await fetch(`${this.getApiUrl()}/admin/users/${user.id}/suspend`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
        });

        if (response.ok) {
          alert(`⛔ ${user.name} has been suspended`);
          await this.loadAllUsers();
        } else {
          alert('Failed to suspend user');
        }
      } catch (error) {
        console.error('Error suspending user:', error);
      } finally {
        this.suspendingId = null;
      }
    },

    async reactivateUser(user) {
      if (!confirm(`Reactivate ${user.name}?`)) return;

      try {
        this.reactivatingId = user.id;
        const response = await fetch(`${this.getApiUrl()}/admin/users/${user.id}/reactivate`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
        });

        if (response.ok) {
          alert(`✅ ${user.name} has been reactivated`);
          await this.loadAllUsers();
        } else {
          alert('Failed to reactivate user');
        }
      } catch (error) {
        console.error('Error reactivating user:', error);
      } finally {
        this.reactivatingId = null;
      }
    },

    getApiUrl() {
      return process.env.VUE_APP_API_URL || window.__API_URL__ + '/api';
    },

    checkAdminAccess() {
      const user = JSON.parse(localStorage.getItem('user') || '{}');
      if (user.role !== 'admin') {
        this.$router.push('/dashboard');
      }
    },
  },

  watch: {
    activeTab(newTab) {
      if (newTab === 'agents') {
        this.loadPendingAgents();
        this.loadVerifiedAgents();
        this.loadRejectedAgents();
      } else if (newTab === 'users') {
        this.loadAllUsers();
      }
    },

    agentFilter(newFilter) {
      if (newFilter === 'pending' && this.pendingAgents.length === 0) {
        this.loadPendingAgents();
      }
    },
  },

  mounted() {
    this.checkAdminAccess();
    // Load initial data
    this.loadPendingAgents();
    this.loadVerifiedAgents();
    this.loadRejectedAgents();
    this.loadAllUsers();
  },
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --smoky-black: #100c08;
  --white-smoke: #f5f5f5;
  --palace-gold: #e6ae0d;
  --palace-gold-dark: #d4a000;
  --charcoal-smoke: #2a2420;
  --light-gray: #e0e0e0;
}

.admin-wrapper {
  min-height: 100vh;
  background: var(--white-smoke);
  padding: 32px;
  font-family: 'Open Sans', sans-serif;
}

.admin-header {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 24px;
}

.admin-header h1 {
  font-size: 28px;
  color: var(--smoky-black);
  margin: 0;
}

.header-stats {
  display: flex;
  gap: 24px;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.stat-label {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 600;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: var(--palace-gold);
}

.admin-nav-tabs {
  display: flex;
  gap: 12px;
  margin-bottom: 32px;
  border-bottom: 2px solid var(--light-gray);
}

.nav-tab {
  padding: 12px 24px;
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
  transition: all 0.3s;
}

.nav-tab.active {
  color: var(--palace-gold);
  border-bottom-color: var(--palace-gold);
}

.tab-content {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.verification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.verification-header h2 {
  font-size: 24px;
  color: var(--smoky-black);
}

.tabs-group {
  display: flex;
  gap: 8px;
}

.tab-filter {
  padding: 8px 16px;
  background: white;
  border: 2px solid var(--light-gray);
  border-radius: 6px;
  color: #666;
  cursor: pointer;
  font-weight: 600;
  font-size: 13px;
  transition: all 0.3s;
}

.tab-filter.active {
  background: var(--palace-gold);
  color: var(--smoky-black);
  border-color: var(--palace-gold);
}

.agents-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.empty-message {
  text-align: center;
  padding: 60px;
  color: #999;
  background: white;
  border-radius: 12px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #999;
  background: white;
  border-radius: 12px;
}

.agent-card {
  background: white;
  border: 1px solid var(--light-gray);
  border-radius: 12px;
  padding: 20px;
  transition: all 0.3s;
}

.agent-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.agent-card.verified {
  border-left: 4px solid #4caf50;
  background: #f1f8f4;
}

.agent-card.rejected {
  border-left: 4px solid #f44336;
  background: #ffebee;
}

.agent-card-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: var(--palace-gold);
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 20px;
}

.avatar.verified {
  background: #4caf50;
  color: white;
}

.avatar.rejected {
  background: #f44336;
  color: white;
}

.agent-name-email {
  flex: 1;
}

.agent-name-email h3 {
  font-size: 16px;
  color: var(--smoky-black);
  margin: 0 0 4px 0;
  font-weight: 600;
}

.agent-name-email p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

.badge-pending,
.badge-verified,
.badge-rejected {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  white-space: nowrap;
}

.badge-pending {
  background: #fff3cd;
  color: #856404;
}

.badge-verified {
  background: #d4edda;
  color: #155724;
}

.badge-rejected {
  background: #f8d7da;
  color: #721c24;
}

.agent-card-body {
  margin-bottom: 16px;
  padding: 16px;
  background: var(--white-smoke);
  border-radius: 8px;
}

.doc-field {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 12px;
}

.doc-field label {
  font-weight: 600;
  color: var(--smoky-black);
  min-width: 120px;
}

.doc-field p {
  color: #666;
}

.rejection-info label {
  font-weight: 600;
  color: var(--smoky-black);
  margin-bottom: 8px;
  display: block;
}

.rejection-info p {
  color: #666;
  margin: 0 0 8px 0;
}

.rejection-info small {
  color: #999;
}

.agent-card-actions {
  display: flex;
  gap: 12px;
}

.btn-approve,
.btn-reject {
  flex: 1;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-approve {
  background: #4caf50;
  color: white;
}

.btn-approve:hover:not(:disabled) {
  background: #45a049;
  transform: translateY(-2px);
}

.btn-approve:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-reject {
  background: #f44336;
  color: white;
}

.btn-reject:hover:not(:disabled) {
  background: #da190b;
  transform: translateY(-2px);
}

.btn-reject:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Users Table */
.users-table {
  width: 100%;
  background: white;
  border: 1px solid var(--light-gray);
  border-radius: 12px;
  border-collapse: collapse;
  overflow: hidden;
}

.users-table thead {
  background: var(--white-smoke);
}

.users-table th {
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: var(--smoky-black);
  border-bottom: 2px solid var(--light-gray);
}

.users-table td {
  padding: 16px;
  border-bottom: 1px solid var(--light-gray);
}

.users-table tbody tr:hover {
  background: var(--white-smoke);
}

.role-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.role-buyer {
  background: #e3f2fd;
  color: #1565c0;
}

.role-agent {
  background: #f3e5f5;
  color: #6a1b9a;
}

.role-admin {
  background: #ffe0b2;
  color: #e65100;
}

.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.active {
  background: #d4edda;
  color: #155724;
}

.status-badge.suspended {
  background: #f8d7da;
  color: #721c24;
}

.action-cell {
  text-align: center;
}

.btn-action {
  width: 32px;
  height: 32px;
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  background: white;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s;
}

.btn-action:hover:not(:disabled) {
  border-color: #4caf50;
}

.btn-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Modal */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-box {
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 90%;
}

.modal-header {
  padding: 24px;
  border-bottom: 1px solid var(--light-gray);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 18px;
  color: var(--smoky-black);
  margin: 0;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.modal-body {
  padding: 24px;
}

.modal-agent-name {
  padding: 12px;
  background: var(--white-smoke);
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.form-field {
  margin-bottom: 16px;
}

.form-field label {
  display: block;
  font-weight: 600;
  color: var(--smoky-black);
  margin-bottom: 8px;
}

.textarea-field {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  font-size: 14px;
  resize: vertical;
}

.textarea-field:focus {
  outline: none;
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 3px rgba(230, 174, 13, 0.1);
}

.info-note {
  padding: 12px;
  background: #e3f2fd;
  border-radius: 8px;
  font-size: 13px;
  color: #1565c0;
  margin: 0;
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid var(--light-gray);
}

.btn-cancel {
  flex: 1;
  padding: 12px;
  background: var(--white-smoke);
  color: var(--smoky-black);
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.btn-large {
  flex: 1;
}

@media (max-width: 768px) {
  .admin-wrapper {
    padding: 16px;
  }

  .admin-header {
    flex-direction: column;
  }

  .verification-header {
    flex-direction: column;
    align-items: stretch;
  }

  .tabs-group {
    flex-wrap: wrap;
  }

  .modal-box {
    width: 95%;
  }
}
</style>