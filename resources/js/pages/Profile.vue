<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar Navigation (Same as Dashboard) -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>

      <nav class="sidebar-nav">
        <!-- Dashboard Link -->
        <router-link to="/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>

        <!-- Buyer Menu Items -->
        <div v-if="userRole === 'buyer'" class="nav-section">
          <h3 class="section-title">Browse</h3>
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
        </div>

        <!-- Viewing & Messages -->
        <div class="nav-section">
          <h3 class="section-title">Activity</h3>
          <a href="#" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Inbox</span>
          </a>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
        </div>

        <!-- Agent Menu Items -->
        <div v-if="userRole === 'agent'" class="nav-section">
          <h3 class="section-title">Management</h3>
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
        </div>

        <!-- Admin Menu Items -->
        <div v-if="userRole === 'admin'" class="nav-section">
          <h3 class="section-title">Admin</h3>
          <router-link to="/admin/users" class="nav-item">
            <span class="nav-icon">👥</span>
            <span class="nav-label">Users</span>
          </router-link>
          <router-link to="/admin/agents" class="nav-item">
            <span class="nav-icon">🧑‍💼</span>
            <span class="nav-label">Verify Agents</span>
          </router-link>
        </div>

        <!-- Settings -->
        <div class="nav-section">
          <h3 class="section-title">Settings</h3>
          <router-link to="/profile" class="nav-item active">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profile</span>
          </router-link>
          <a href="#" class="nav-item">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">Settings</span>
          </a>
        </div>
      </nav>

      <!-- Sidebar Footer -->
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ userName }}</p>
            <p class="user-role-card">{{ capitalizeRole(userRole) }}</p>
          </div>
          <button @click="toggleUserMenu" class="btn-options">⋮</button>

          <!-- User Menu Dropdown -->
          <div v-if="showUserMenu" class="user-dropdown">
            <router-link to="/profile" class="dropdown-item">👤 My Profile</router-link>
            <a href="#" @click.prevent="logout" class="dropdown-item logout">🚪 Logout</a>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Top Navbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">My Profile</h1>
          </div>
          <div class="topbar-right">
            <button class="btn-icon">🔔</button>
            <input type="text" placeholder="Search..." class="search-input" />
          </div>
        </div>
      </nav>

      <!-- Page Content Wrapper -->
      <div class="page-wrapper">
        <!-- Main Panel -->
        <div class="main-panel">
          <!-- Profile Header with Photo -->
          <div class="profile-header">
            <div class="profile-photo-section">
              <div class="profile-photo-container">
                <img v-if="profilePhotoUrl" :src="profilePhotoUrl" :alt="userName" class="profile-photo" />
                <div v-else class="profile-photo-placeholder">
                  {{ userName.charAt(0).toUpperCase() }}
                </div>
                <button @click="triggerPhotoUpload" class="btn-upload-photo" title="Change photo">
                  📷
                </button>
                <input
                  ref="photoInput"
                  type="file"
                  accept="image/*"
                  @change="handlePhotoUpload"
                  class="hidden-input"
                />
              </div>

              <div class="profile-basic-info">
                <h2>{{ userName }}</h2>
                <p class="user-email">{{ userEmail }}</p>
                <p class="user-role-badge">
                  <span class="role-badge" :class="'role-' + userRole">
                    {{ capitalizeRole(userRole) }}
                  </span>
                </p>
                <p class="member-since">Member since {{ memberSince }}</p>
              </div>
            </div>
          </div>

          <!-- Tabs Navigation -->
          <div class="profile-tabs">
            <button
              v-for="tab in visibleTabs"
              :key="tab.id"
              :class="['tab-btn', { active: activeTab === tab.id }]"
              @click="activeTab = tab.id"
            >
              {{ tab.icon }} {{ tab.label }}
            </button>
          </div>

          <!-- Personal Information Tab -->
          <section v-if="activeTab === 'personal'" class="tab-content-section">
            <div class="section-header">
              <h3>Personal Information</h3>
              <button v-if="!isEditingPersonal" @click="isEditingPersonal = true" class="btn-edit">
                ✏️ Edit
              </button>
              <div v-else class="edit-actions">
                <button @click="cancelEditPersonal" class="btn-cancel">Cancel</button>
                <button @click="savePersonalInfo" class="btn-save" :disabled="savingPersonal">
                  {{ savingPersonal ? '⏳ Saving...' : '💾 Save' }}
                </button>
              </div>
            </div>

            <form v-if="isEditingPersonal" @submit.prevent="savePersonalInfo" class="form-grid">
              <div class="form-group">
                <label>Name</label>
                <input v-model="formData.name" type="text" class="form-input" placeholder="Full name" />
                <span v-if="errors.name" class="error-text">{{ errors.name }}</span>
              </div>

              <div class="form-group">
                <label>Email Address</label>
                <input v-model="formData.email" type="email" class="form-input" placeholder="Email address" />
                <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
              </div>

              <div class="form-group full-width">
                <label>Phone Number</label>
                <input
                  v-model="formData.phone"
                  type="tel"
                  class="form-input"
                  placeholder="+63 123 456 7890"
                />
                <span v-if="errors.phone" class="error-text">{{ errors.phone }}</span>
              </div>
            </form>

            <div v-else class="info-grid">
              <div class="info-item">
                <label>Name</label>
                <p>{{ formData.name }}</p>
              </div>
              <div class="info-item">
                <label>Email Address</label>
                <p>{{ formData.email }}</p>
              </div>
              <div class="info-item full-width">
                <label>Phone Number</label>
                <p>{{ formData.phone || '—' }}</p>
              </div>
            </div>
          </section>

          <!-- Security Tab -->
          <section v-if="activeTab === 'security'" class="tab-content-section">
            <div class="section-header">
              <h3>Security Settings</h3>
            </div>

            <!-- Change Password -->
            <div class="security-card">
              <div class="security-header">
                <h4>🔐 Change Password</h4>
                <button
                  v-if="!showChangePassword"
                  @click="showChangePassword = true"
                  class="btn-edit"
                >
                  ✏️ Change
                </button>
              </div>

              <form v-if="showChangePassword" @submit.prevent="changePassword" class="form-grid">
                <div class="form-group full-width">
                  <label>Current Password</label>
                  <input
                    v-model="passwordForm.currentPassword"
                    type="password"
                    class="form-input"
                    placeholder="Enter current password"
                  />
                  <span v-if="errors.currentPassword" class="error-text">{{ errors.currentPassword }}</span>
                </div>

                <div class="form-group full-width">
                  <label>New Password</label>
                  <input
                    v-model="passwordForm.newPassword"
                    type="password"
                    class="form-input"
                    placeholder="Enter new password (min 8 characters)"
                  />
                  <span v-if="errors.password" class="error-text">{{ errors.password }}</span>
                  <small>Must be at least 8 characters</small>
                </div>

                <div class="form-group full-width">
                  <label>Confirm New Password</label>
                  <input
                    v-model="passwordForm.confirmPassword"
                    type="password"
                    class="form-input"
                    placeholder="Confirm new password"
                  />
                  <span v-if="errors.passwordConfirmation" class="error-text">{{ errors.passwordConfirmation }}</span>
                </div>

                <div class="form-actions">
                  <button @click="showChangePassword = false" class="btn-cancel">Cancel</button>
                  <button type="submit" class="btn-save" :disabled="changingPassword">
                    {{ changingPassword ? '⏳ Updating...' : '💾 Update Password' }}
                  </button>
                </div>
              </form>

              <p v-else class="security-info">
                Last changed: <strong>{{ lastPasswordChange }}</strong>
              </p>
            </div>

            <!-- Login Activity -->
            <div class="security-card">
              <h4>📍 Recent Login Activity</h4>
              <div class="login-history">
                <div v-for="login in loginHistory" :key="login.id" class="login-item">
                  <div class="login-info">
                    <p class="device">{{ login.device }}</p>
                    <p class="location">{{ login.location }}</p>
                  </div>
                  <div class="login-time">{{ login.time }}</div>
                </div>
              </div>
            </div>
          </section>

          <!-- Account Tab -->
          <section v-if="activeTab === 'account'" class="tab-content-section">
            <div class="section-header">
              <h3>Account Management</h3>
            </div>

            <!-- Account Status -->
            <div class="account-card">
              <h4>Account Status</h4>
              <div class="status-info">
                <p class="status-label">Current Status:</p>
                <p class="status-value">
                  <span class="status-badge active">Active</span>
                </p>
              </div>
              <p class="account-details">Your account is in good standing. Account created on {{ memberSince }}</p>
            </div>

            <!-- Danger Zone -->
            <div class="account-card danger-zone">
              <h4>⚠️ Danger Zone</h4>
              <p class="danger-description">These actions are permanent and cannot be undone.</p>

              <button @click="openDeleteModal" class="btn-danger full-width" style="margin-bottom: 12px">
                🗑️ Delete Account
              </button>
            </div>
          </section>
        </div>
      </div>
    </main>

    <!-- Delete Account Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>🚨 Delete Account</h3>
          <button @click="showDeleteModal = false" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <p class="warning-text">
            ⚠️ <strong>Warning:</strong> Deleting your account will permanently remove all your data and cannot be
            undone.
          </p>

          <p>Before deleting, please note:</p>
          <ul class="warning-list">
            <li>All your profile information will be deleted</li>
            <li>All your properties/listings will be removed</li>
            <li>All messages and communications will be deleted</li>
            <li>This action cannot be reversed</li>
          </ul>

          <div class="form-group">
            <label>Type your email to confirm:</label>
            <input
              v-model="deleteConfirmEmail"
              type="text"
              class="form-input"
              :placeholder="userEmail"
            />
            <span v-if="errors.deleteConfirm" class="error-text">{{ errors.deleteConfirm }}</span>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="showDeleteModal = false" class="btn-secondary">Cancel</button>
          <button
            @click="confirmDeleteAccount"
            :disabled="deleteConfirmEmail !== userEmail || deletingAccount"
            class="btn-danger"
          >
            {{ deletingAccount ? '⏳ Deleting...' : '🗑️ Delete My Account' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <transition name="fade">
      <div v-if="successMessage" class="success-message">
        ✓ {{ successMessage }}
      </div>
    </transition>

    <!-- Error Message -->
    <transition name="fade">
      <div v-if="errorMessage" class="error-message">
        ✗ {{ errorMessage }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'Profile',
  data() {
    return {
      // User Data
      userName: '',
      userEmail: '',
      userRole: '',
      memberSince: '',
      profilePhotoUrl: null,

      // UI State
      activeTab: 'personal',
      showUserMenu: false,
      isEditingPersonal: false,
      showChangePassword: false,
      showDeleteModal: false,

      // Loading States
      savingPersonal: false,
      changingPassword: false,
      deletingAccount: false,

      // Messages
      successMessage: '',
      errorMessage: '',

      // Form Data
      formData: {
        name: '',
        email: '',
        phone: '',
      },

      passwordForm: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
      },

      // Errors
      errors: {},

      // Security Data
      lastPasswordChange: 'January 15, 2025',
      deleteConfirmEmail: '',

      loginHistory: [
        {
          id: 1,
          device: 'Chrome on MacOS',
          location: 'Cebu City, Philippines',
          time: 'Today at 2:30 PM',
        },
        {
          id: 2,
          device: 'Safari on iPhone',
          location: 'Cebu City, Philippines',
          time: 'Yesterday at 10:15 AM',
        },
        {
          id: 3,
          device: 'Chrome on Windows',
          location: 'Manila, Philippines',
          time: '3 days ago at 8:45 PM',
        },
      ],

      profileTabs: [
        { id: 'personal', label: 'Personal Info', icon: '👤' },
        { id: 'security', label: 'Security', icon: '🔐' },
        { id: 'account', label: 'Account', icon: '📋' },
      ],
    };
  },

  computed: {
    visibleTabs() {
      return this.profileTabs.filter((tab) => !tab.visible || tab.visible === this.userRole);
    },
  },

  methods: {
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },

    capitalizeRole(role) {
      return role.charAt(0).toUpperCase() + role.slice(1);
    },

    triggerPhotoUpload() {
      this.$refs.photoInput.click();
    },

    
async handlePhotoUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  // Validate file
  if (!file.type.startsWith('image/')) {
    this.showError('Please select a valid image file');
    return;
  }

  if (file.size > 2 * 1024 * 1024) {
    // 2MB limit
    this.showError('Image must be less than 2MB');
    return;
  }

  try {
    // Create FormData
    const formData = new FormData();
    formData.append('photo', file);

    // Upload to API
    const token = localStorage.getItem('auth_token');
    const response = await fetch(window.__API_URL__ + '/api/user/upload-profile-photo', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      // Create preview
      const reader = new FileReader();
      reader.onload = (e) => {
        this.profilePhotoUrl = e.target.result;
      };
      reader.readAsDataURL(file);
      
      // ✅ FIXED: Update localStorage with photo path
      const user = JSON.parse(localStorage.getItem('user') || '{}');
      user.profile_photo_path = data.data.photo_path;
      localStorage.setItem('user', JSON.stringify(user));
      
      this.showSuccess('Profile photo updated successfully!');
    } else {
      this.showError(data.message || 'Failed to upload photo');
    }
  } catch (error) {
    console.error('Upload error:', error);
    this.showError('Failed to upload photo');
  }
},

    async savePersonalInfo() {
      // Validate
      this.errors = {};
      if (!this.formData.name.trim()) {
        this.errors.name = 'Name is required';
      }
      if (!this.formData.email.trim()) {
        this.errors.email = 'Email is required';
      }

      if (Object.keys(this.errors).length > 0) return;

      try {
        this.savingPersonal = true;

        const token = localStorage.getItem('auth_token');
        const response = await fetch(window.__API_URL__ + '/api/user/profile', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify({
            name: this.formData.name,
            email: this.formData.email,
            phone: this.formData.phone,
          }),
        });

        const data = await response.json();

        if (data.success) {
          this.isEditingPersonal = false;
          // Update localStorage
          const user = JSON.parse(localStorage.getItem('user') || '{}');
          user.name = this.formData.name;
          user.email = this.formData.email;
          localStorage.setItem('user', JSON.stringify(user));
          this.userName = this.formData.name;
          this.userEmail = this.formData.email;
          this.showSuccess('Personal information updated successfully!');
        } else {
          if (data.errors) {
            this.errors = data.errors;
          } else {
            this.showError(data.message || 'Failed to save personal information');
          }
        }
      } catch (error) {
        console.error('Save error:', error);
        this.showError('Failed to save personal information');
      } finally {
        this.savingPersonal = false;
      }
    },

    cancelEditPersonal() {
      this.isEditingPersonal = false;
      this.loadUserData();
    },

    async changePassword() {
      // Validate
      this.errors = {};
      if (!this.passwordForm.currentPassword) {
        this.errors.currentPassword = 'Current password is required';
      }
      if (!this.passwordForm.newPassword) {
        this.errors.password = 'New password is required';
      }
      if (this.passwordForm.newPassword.length < 8) {
        this.errors.password = 'Password must be at least 8 characters';
      }
      if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
        this.errors.passwordConfirmation = 'Passwords do not match';
      }

      if (Object.keys(this.errors).length > 0) return;

      try {
        this.changingPassword = true;

        const token = localStorage.getItem('auth_token');
        const response = await fetch(window.__API_URL__ + '/api/user/change-password', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify({
            current_password: this.passwordForm.currentPassword,
            password: this.passwordForm.newPassword,
            password_confirmation: this.passwordForm.confirmPassword,
          }),
        });

        const data = await response.json();

        if (data.success) {
          this.passwordForm = {
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
          };
          this.showChangePassword = false;
          this.lastPasswordChange = 'Just now';
          this.showSuccess('Password changed successfully!');
        } else {
          this.showError(data.message || 'Failed to change password');
        }
      } catch (error) {
        console.error('Password change error:', error);
        this.showError('Failed to change password');
      } finally {
        this.changingPassword = false;
      }
    },

    openDeleteModal() {
      this.showDeleteModal = true;
    },

    async confirmDeleteAccount() {
      if (this.deleteConfirmEmail !== this.userEmail) {
        this.errors.deleteConfirm = 'Email does not match';
        return;
      }

      try {
        this.deletingAccount = true;
        // TODO: Implement delete account API endpoint
        // For now, just show a message
        this.showError('Account deletion API not yet implemented');
      } catch (error) {
        this.showError('Failed to delete account');
      } finally {
        this.deletingAccount = false;
      }
    },

    showSuccess(message) {
      this.successMessage = message;
      setTimeout(() => {
        this.successMessage = '';
      }, 3000);
    },

    showError(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 3000);
    },

    loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName = user.name || 'User';
        this.userEmail = user.email || '';
        this.userRole = user.role || 'buyer';

        this.formData.name = this.userName;
        this.formData.email = this.userEmail;
        this.formData.phone = user.phone || '';

        const createdDate = new Date();
        this.memberSince = createdDate.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        });

        // Load profile photo if exists
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__}/storage/${user.profile_photo_path}`;
        }
      } catch (error) {
        console.error('Error loading user data:', error);
      }
    },

    async logout() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(window.__API_URL__ + '/api/auth/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/');
      } catch (error) {
        console.error('Logout error:', error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/');
      }
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/');
      return;
    }
    this.loadUserData();
  },

  beforeUnmount() {
    this.showUserMenu = false;
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --smoky-black: #100c08;
  --white-smoke: #f5f5f5;
  --palace-gold: #FFD700;
  --palace-gold-dark: #DAB600;
  --charcoal-smoke: #2a2420;
  --light-gray: #e0e0e0;
  --border-gray: #ddd;
  --dark-navbar: #1a1a1a;

  --font-display: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.dashboard-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
}

/* ============================================================================
   SIDEBAR
   ============================================================================ */

.sidebar {
  width: 280px;
  background: white;
  border-right: 1px solid var(--light-gray);
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.08);
  z-index: 1000;
}

.sidebar-header {
  padding: 24px 16px;
  border-bottom: 1px solid var(--light-gray);
}

.sidebar-logo {
  font-size: 24px;
  font-weight: 800;
  margin: 0;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
  display: flex;
  align-items: center;
}

.logo-realty {
  color: var(--smoky-black);
}

.logo-ph {
  color: var(--palace-gold);
  margin-left: 2px;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.nav-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #999;
  padding: 8px 16px;
  margin: 0;
  letter-spacing: 1.2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  margin: 0 8px;
  color: var(--smoky-black);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 14px;
  border: none;
  background: none;
  cursor: pointer;
  width: calc(100% - 16px);
  text-align: left;
}

.nav-item:hover {
  background: var(--white-smoke);
  color: var(--palace-gold);
}

.nav-item.active {
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  font-weight: 600;
}

.nav-icon {
  font-size: 18px;
  min-width: 24px;
  text-align: center;
}

.nav-label {
  flex: 1;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid var(--light-gray);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.user-card {
  background: var(--white-smoke);
  border-radius: 8px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
}

.user-avatar-lg {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  flex-shrink: 0;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name-card {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-role-card {
  font-size: 11px;
  color: #999;
  margin: 4px 0 0 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-options {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 4px 8px;
  transition: color 0.3s;
  color: #999;
}

.btn-options:hover {
  color: var(--palace-gold);
}

.user-dropdown {
  position: absolute;
  bottom: 100%;
  right: 0;
  background: white;
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 180px;
  margin-bottom: 8px;
  overflow: hidden;
  z-index: 1001;
}

.dropdown-item {
  display: block;
  padding: 12px 16px;
  color: var(--smoky-black);
  text-decoration: none;
  font-size: 14px;
  transition: all 0.3s;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.dropdown-item:hover {
  background: var(--white-smoke);
  color: var(--palace-gold);
}

.dropdown-item.logout {
  color: #d32f2f;
  border-top: 1px solid var(--light-gray);
}

.dropdown-item.logout:hover {
  background: #ffebee;
}

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.main-content {
  flex: 1;
  margin-left: 280px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.topbar {
  background: linear-gradient(135deg,rgb(253, 252, 252) 0%,rgb(255, 255, 255) 100%);
  border-bottom: 3px solid var(--palace-gold);
  padding: 16px 32px;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.topbar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 30px;
  flex: 1;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  margin: 0;
  font-family: var(--font-display);
  white-space: nowrap;
  letter-spacing: -0.5px;
  color: var(--smoky-black);
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.btn-icon {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  padding: 8px;
  transition: transform 0.3s;
  position: relative;
}

.btn-icon:hover {
  transform: scale(1.1);
}

.search-input {
  padding: 10px 16px;
  border: 1px solid rgba(5, 5, 5, 0.2);
  border-radius: 8px;
  font-size: 14px;
  width: 220px;
  transition: all 0.3s;
  background: rgba(255, 255, 255, 0.05);
  color: black;
}

.search-input::placeholder {
  color: rgba(8, 8, 8, 0.5);
}

.search-input:focus {
  outline: none;
  border-color: var(--palace-gold);
  background: rgba(255, 255, 255, 0.1);
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
}

/* Page Wrapper */
.page-wrapper {
  flex: 1;
  padding: 32px;
  overflow-y: auto;
}

.main-panel {
  max-width: 1000px;
  margin: 0 auto;
  width: 100%;
}

/* ============================================================================
   PROFILE HEADER
   ============================================================================ */

.profile-header {
  background: white;
  border-radius: 12px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.profile-photo-section {
  display: flex;
  align-items: center;
  gap: 32px;
}

.profile-photo-container {
  position: relative;
  flex-shrink: 0;
}

.profile-photo {
  width: 160px;
  height: 160px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid var(--palace-gold);
}

.profile-photo-placeholder {
  width: 160px;
  height: 160px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 64px;
  font-weight: 800;
  border: 4px solid var(--palace-gold);
}

.btn-upload-photo {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--palace-gold);
  border: none;
  font-size: 24px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-upload-photo:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 16px rgba(255, 215, 0, 0.4);
}

.hidden-input {
  display: none;
}

.profile-basic-info h2 {
  font-size: 32px;
  font-weight: 800;
  margin: 0 0 8px 0;
  color: var(--smoky-black);
  font-family: var(--font-display);
  letter-spacing: -0.5px;
}

.user-email {
  font-size: 16px;
  color: #999;
  margin: 0 0 12px 0;
}

.user-role-badge {
  margin: 0 0 12px 0;
}

.role-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 700;
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
  background: #fff3e0;
  color: #e65100;
}

.member-since {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* ============================================================================
   PROFILE TABS
   ============================================================================ */

.profile-tabs {
  display: flex;
  gap: 12px;
  margin-bottom: 32px;
  border-bottom: 2px solid var(--light-gray);
  background: white;
  padding: 0 32px;
  border-radius: 12px 12px 0 0;
  overflow-x: auto;
}

.tab-btn {
  padding: 16px 24px;
  border: none;
  background: none;
  color: #999;
  cursor: pointer;
  font-weight: 700;
  font-size: 14px;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
  transition: all 0.3s;
  white-space: nowrap;
  font-family: var(--font-display);
}

.tab-btn:hover {
  color: var(--smoky-black);
}

.tab-btn.active {
  color: var(--palace-gold);
  border-bottom-color: var(--palace-gold);
}

/* ============================================================================
   TAB CONTENT
   ============================================================================ */

.tab-content-section {
  background: white;
  border-radius: 0 12px 12px 12px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  margin-bottom: 32px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--light-gray);
}

.section-header h3 {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
  color: var(--smoky-black);
  font-family: var(--font-display);
}

.edit-actions {
  display: flex;
  gap: 12px;
}

.btn-edit {
  padding: 8px 16px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  border: none;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
}

.btn-edit:hover {
  background: var(--palace-gold-dark);
  transform: translateY(-2px);
}

.btn-cancel {
  padding: 8px 16px;
  background: var(--white-smoke);
  color: var(--smoky-black);
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
}

.btn-cancel:hover {
  background: var(--light-gray);
}

.btn-save {
  padding: 8px 16px;
  background: #4caf50;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
}

.btn-save:hover:not(:disabled) {
  background: #45a049;
  transform: translateY(-2px);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Forms */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-group small {
  font-size: 12px;
  color: #999;
  margin-top: 4px;
}

.form-input {
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-size: 14px;
  font-family: var(--font-body);
  transition: all 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.error-text {
  font-size: 12px;
  color: #d32f2f;
  margin-top: 4px;
}

/* Info Grid (Read Mode) */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.info-item {
  padding: 16px;
  background: var(--white-smoke);
  border-radius: 6px;
}

.info-item.full-width {
  grid-column: 1 / -1;
}

.info-item label {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  margin-bottom: 8px;
  display: block;
  letter-spacing: 0.5px;
}

.info-item p {
  font-size: 15px;
  color: var(--smoky-black);
  margin: 0;
  font-weight: 500;
}

/* ============================================================================
   SECURITY SECTION
   ============================================================================ */

.security-card {
  background: var(--white-smoke);
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  border: 1px solid var(--light-gray);
}

.security-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.security-header h4 {
  font-size: 16px;
  font-weight: 700;
  margin: 0;
  color: var(--smoky-black);
}

.security-info {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.btn-danger {
  padding: 12px 24px;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
}

.btn-danger:hover:not(:disabled) {
  background: #da190b;
  transform: translateY(-2px);
}

.btn-danger:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  padding: 12px 24px;
  background: white;
  color: var(--smoky-black);
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
}

.btn-secondary:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

.full-width {
  width: 100%;
}

.login-history {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.login-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: white;
  border-radius: 6px;
  border: 1px solid var(--light-gray);
}

.login-info p {
  margin: 0;
  font-size: 14px;
}

.device {
  font-weight: 700;
  color: var(--smoky-black);
}

.location {
  color: #999;
  font-size: 13px;
}

.login-time {
  font-size: 12px;
  color: #999;
  white-space: nowrap;
}

/* ============================================================================
   ACCOUNT SECTION
   ============================================================================ */

.account-card {
  background: var(--white-smoke);
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
}

.account-card h4 {
  font-size: 16px;
  font-weight: 700;
  margin: 0 0 16px 0;
  color: var(--smoky-black);
}

.status-info {
  display: flex;
  gap: 16px;
  align-items: center;
  margin-bottom: 12px;
}

.status-label {
  font-size: 14px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
}

.status-value {
  margin: 0;
}

.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.status-badge.active {
  background: #d4edda;
  color: #155724;
}

.account-details {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.danger-zone {
  border: 2px solid #f44336;
  background: #ffebee;
}

.danger-description {
  font-size: 14px;
  color: #666;
  margin: 0 0 16px 0;
}

/* ============================================================================
   MODAL
   ============================================================================ */

.modal-overlay {
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
  max-height: 90vh;
  overflow-y: auto;
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
  font-weight: 700;
  margin: 0;
  color: var(--smoky-black);
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
  padding: 0;
  transition: color 0.3s;
}

.btn-close:hover {
  color: var(--smoky-black);
}

.modal-body {
  padding: 24px;
}

.warning-text {
  color: #d32f2f;
  margin-bottom: 16px;
  font-size: 14px;
}

.warning-list {
  margin: 16px 0 24px 0;
  padding-left: 20px;
  color: #666;
}

.warning-list li {
  margin-bottom: 8px;
  font-size: 14px;
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid var(--light-gray);
}

.modal-footer .btn-secondary,
.modal-footer .btn-danger {
  flex: 1;
}

/* ============================================================================
   MESSAGES
   ============================================================================ */

.success-message {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #4caf50;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3000;
  animation: slideIn 0.3s ease;
}

.error-message {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #f44336;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3000;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    transform: translateX(400px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */

@media (max-width: 1024px) {
  .sidebar {
    width: 240px;
  }

  .main-content {
    margin-left: 240px;
  }

  .profile-photo-section {
    flex-direction: column;
    text-align: center;
  }

  .page-wrapper {
    padding: 24px;
  }
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: -280px;
    transition: left 0.3s ease;
    width: 280px;
    z-index: 1001;
  }

  .main-content {
    margin-left: 0;
  }

  .topbar {
    padding: 16px;
  }

  .topbar-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .page-title {
    font-size: 24px;
  }

  .topbar-right {
    width: 100%;
  }

  .search-input {
    width: 100%;
  }

  .page-wrapper {
    padding: 16px;
  }

  .profile-header {
    padding: 24px;
  }

  .profile-photo-section {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .profile-tabs {
    padding: 0 24px;
    margin-bottom: 24px;
  }

  .tab-content-section {
    padding: 24px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .edit-actions {
    width: 100%;
  }

  .edit-actions button {
    flex: 1;
  }

  .modal-box {
    width: 95%;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 20px;
  }

  .profile-header {
    padding: 16px;
  }

  .profile-photo-container {
    margin: 0 auto;
  }

  .profile-basic-info h2 {
    font-size: 24px;
  }

  .profile-tabs {
    padding: 0 16px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .tab-btn {
    padding: 12px 16px;
    font-size: 13px;
  }

  .tab-content-section {
    padding: 16px;
  }

  .section-header {
    margin-bottom: 16px;
  }

  .form-grid {
    gap: 16px;
  }

  .modal-header {
    padding: 16px;
  }

  .modal-body {
    padding: 16px;
  }

  .modal-footer {
    flex-direction: column;
  }

  .modal-footer button {
    width: 100%;
  }
}
</style>