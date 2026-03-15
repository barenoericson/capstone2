<template>
  <div class="profile-layout">

    <!-- ═══════ SIDEBAR ═══════ -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link :to="dashboardRoute" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <router-link :to="dashboardRoute" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
          <span>Dashboard</span>
        </router-link>

        <!-- Buyer nav -->
        <template v-if="userRole === 'buyer'">
          <router-link to="/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
            <span>Properties</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></span>
            <span>Saved</span>
          </router-link>
          <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
            <span>Messages</span>
          </router-link>
          <router-link to="/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span>Viewings</span>
          </router-link>
          <router-link to="/documents" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
            <span>Documents</span>
          </router-link>
        </template>

        <!-- Agent nav -->
        <template v-else-if="userRole === 'agent'">
          <router-link to="/agent/properties" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
            <span>My Properties</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span>Viewings</span>
          </router-link>
          <router-link to="/agent/calendar" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span>Calendar</span>
          </router-link>
          <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
            <span>Messages</span>
          </router-link>
          <router-link to="/agent/documents" class="nav-item" @click="sidebarOpen = false">
            <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
            <span>Documents</span>
          </router-link>
        </template>
      </nav>

      <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <router-link to="/profile" class="nav-item nav-user router-link-exact-active" @click="sidebarOpen = false">
          <div class="nav-av">
            <img v-if="profilePhotoUrl" :src="profilePhotoUrl" :alt="userName" />
            <span v-else>{{ userName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ userName }}</span>
            <span class="nav-user-role">{{ capitalizeRole(userRole) }}</span>
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

    <!-- ═══════ MAIN ═══════ -->
    <div class="main-wrapper">

      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="sidebarOpen = !sidebarOpen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <span class="topbar-title">My Profile</span>
        </div>
        <div class="topbar-right">
          <router-link to="/settings" class="tb-settings">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
            Settings
          </router-link>
        </div>
      </header>

      <main class="content-area">
        <div class="content-inner">

          <!-- ① PROFILE HERO -->
          <div class="profile-hero">
            <div class="hero-deco" aria-hidden="true">
              <div class="deco-ring dr1"></div>
              <div class="deco-ring dr2"></div>
              <div class="deco-ring dr3"></div>
            </div>
            <div class="hero-body">
              <div class="hero-av-wrap">
                <div class="hero-av" @click="triggerPhotoUpload" title="Change photo">
                  <img v-if="profilePhotoUrl" :src="profilePhotoUrl" :alt="userName" />
                  <span v-else class="av-init">{{ userName.charAt(0).toUpperCase() }}</span>
                  <div class="av-overlay">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span>Change</span>
                  </div>
                </div>
                <input ref="photoInput" type="file" accept="image/*" @change="handlePhotoUpload" class="hidden-input" />
                <div class="av-ring"></div>
              </div>
              <div class="hero-info">
                <div class="hi-top">
                  <h1 class="hero-name">{{ userName }}</h1>
                  <span class="role-pill" :class="'rp-' + userRole">{{ capitalizeRole(userRole) }}</span>
                </div>
                <p class="hero-email">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  {{ userEmail }}
                </p>
                <p class="hero-since">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  Member since {{ memberSince }}
                </p>
                <div class="hero-progress">
                  <div class="hp-labels">
                    <span>Profile completion</span>
                    <span class="hp-pct">{{ profileCompletion }}%</span>
                  </div>
                  <div class="hp-track"><div class="hp-fill" :style="{ width: profileCompletion + '%' }"></div></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Journey Tracker (buyer only) — migrated from Dashboard -->
          <div v-if="userRole === 'buyer'" class="journey-section">
            <div class="journey-top">
              <div>
                <h3 class="journey-title">Your Home Buying Journey</h3>
                <p class="journey-sub">{{ journeySteps.filter(s => s.done).length }} of {{ journeySteps.length }} steps completed — click any step to navigate</p>
              </div>
              <div class="journey-pct-badge">{{ journeyProgress }}%</div>
            </div>
            <div class="journey-track">
              <div v-for="(step, i) in journeySteps" :key="step.key"
                class="journey-step"
                :class="{ done: step.done, active: i === currentJourneyStep && !step.done, last: i === journeySteps.length - 1 }"
                :title="getStepTooltip(step)"
                @click="navigateToStep(step)">
                <div class="step-node">
                  <svg v-if="step.done" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  <span v-else>{{ i + 1 }}</span>
                </div>
                <span class="step-lbl">{{ step.label }}</span>
              </div>
            </div>
          </div>

          <!-- ② TABS -->
          <div class="tab-nav">
            <button
              v-for="tab in profileTabs.filter(t => !t.buyerOnly || userRole === 'buyer')"
              :key="tab.id"
              class="tab-btn" :class="{ active: activeTab === tab.id }"
              @click="activeTab = tab.id">
              <span class="tab-ic" v-html="tab.svg"></span>
              {{ tab.label }}
              <span v-if="tab.id === 'application' && agentApplication && agentApplication.status === 'pending'" class="tab-badge-dot"></span>
            </button>
          </div>

          <!-- ③ PERSONAL INFO -->
          <section v-if="activeTab === 'personal'" class="section-card">
            <div class="sc-head">
              <div class="sc-title-row">
                <div class="sc-icon si-blue">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                  <h3 class="sc-title">Personal Information</h3>
                  <p class="sc-sub">Manage your name, email, and contact details</p>
                </div>
              </div>
              <div class="sc-actions">
                <button v-if="!isEditingPersonal" @click="isEditingPersonal = true" class="btn-edit">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Edit
                </button>
                <div v-else class="edit-btns">
                  <button @click="cancelEditPersonal" class="btn-cancel">Cancel</button>
                  <button @click="savePersonalInfo" class="btn-save" :disabled="savingPersonal">
                    {{ savingPersonal ? 'Saving…' : 'Save Changes' }}
                  </button>
                </div>
              </div>
            </div>

            <div v-if="!isEditingPersonal" class="info-grid">
              <div class="info-item">
                <span class="ii-label">Full Name</span>
                <span class="ii-value">{{ formData.name || '—' }}</span>
              </div>
              <div class="info-item">
                <span class="ii-label">Email Address</span>
                <span class="ii-value">{{ formData.email || '—' }}</span>
              </div>
              <div class="info-item">
                <span class="ii-label">Phone Number</span>
                <span class="ii-value">{{ formData.phone || 'Not provided' }}</span>
              </div>
              <div class="info-item">
                <span class="ii-label">Account Type</span>
                <span class="ii-value">{{ capitalizeRole(userRole) }}</span>
              </div>
              <div class="info-item ii-full">
                <span class="ii-label">About / Bio</span>
                <span class="ii-value ii-bio">{{ formData.bio || 'Not provided — click Edit to add a bio' }}</span>
              </div>
            </div>

            <form v-else @submit.prevent="savePersonalInfo" class="form-grid">
              <div class="form-group">
                <label class="form-label">Full Name</label>
                <input v-model="formData.name" type="text" class="form-input" placeholder="Enter your full name" />
                <span v-if="errors.name" class="err-text">{{ errors.name }}</span>
              </div>
              <div class="form-group">
                <label class="form-label">Email Address</label>
                <input v-model="formData.email" type="email" class="form-input" placeholder="Enter your email" />
                <span v-if="errors.email" class="err-text">{{ errors.email }}</span>
              </div>
              <div class="form-group fg-full">
                <label class="form-label">Phone Number</label>
                <input v-model="formData.phone" type="tel" class="form-input" placeholder="+63 9XX XXX XXXX" />
                <span v-if="errors.phone" class="err-text">{{ errors.phone }}</span>
              </div>
              <div class="form-group fg-full">
                <label class="form-label">About / Bio</label>
                <textarea v-model="formData.bio" class="form-input form-textarea" rows="3"
                  placeholder="Tell buyers and agents a bit about yourself — your property goals, preferred location, budget range, etc."></textarea>
              </div>
            </form>
          </section>

          <!-- ④ SECURITY -->
          <section v-if="activeTab === 'security'" class="section-card">
            <div class="sc-head">
              <div class="sc-title-row">
                <div class="sc-icon si-gold">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                  <h3 class="sc-title">Security Settings</h3>
                  <p class="sc-sub">Manage your password and account security</p>
                </div>
              </div>
              <div class="sc-actions">
                <button v-if="!showChangePassword" @click="showChangePassword = true" class="btn-edit">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Change Password
                </button>
              </div>
            </div>

            <div v-if="!showChangePassword" class="info-grid">
              <div class="info-item">
                <span class="ii-label">Password</span>
                <span class="ii-value pass-dots">●●●●●●●●</span>
              </div>
              <div class="info-item">
                <span class="ii-label">Last Changed</span>
                <span class="ii-value">{{ lastPasswordChange }}</span>
              </div>
            </div>

            <form v-else @submit.prevent="changePassword" class="form-grid">
              <div class="form-group fg-full">
                <label class="form-label">Current Password</label>
                <input v-model="passwordForm.currentPassword" type="password" class="form-input" placeholder="Enter current password" />
                <span v-if="errors.currentPassword" class="err-text">{{ errors.currentPassword }}</span>
              </div>
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input v-model="passwordForm.newPassword" type="password" class="form-input" placeholder="Min 8 characters" />
                <span v-if="errors.password" class="err-text">{{ errors.password }}</span>
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input v-model="passwordForm.confirmPassword" type="password" class="form-input" placeholder="Repeat new password" />
                <span v-if="errors.passwordConfirmation" class="err-text">{{ errors.passwordConfirmation }}</span>
              </div>
              <div class="form-actions fg-full">
                <button type="button" @click="showChangePassword = false" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-save" :disabled="changingPassword">
                  {{ changingPassword ? 'Updating…' : 'Update Password' }}
                </button>
              </div>
            </form>

            <div class="sub-section">
              <h4 class="sub-title">Recent Login Activity</h4>
              <div class="login-list">
                <div v-for="login in loginHistory" :key="login.id" class="login-item">
                  <div class="li-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                  </div>
                  <div class="li-info">
                    <p class="li-device">{{ login.device }}</p>
                    <p class="li-loc">{{ login.location }}</p>
                  </div>
                  <span class="li-time">{{ login.time }}</span>
                </div>
              </div>
            </div>
          </section>

          <!-- ⑤ ACCOUNT -->
          <section v-if="activeTab === 'account'" class="section-card">
            <div class="sc-head">
              <div class="sc-title-row">
                <div class="sc-icon si-green">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                  <h3 class="sc-title">Account Management</h3>
                  <p class="sc-sub">View status and manage your account</p>
                </div>
              </div>
            </div>

            <div class="info-grid">
              <div class="info-item">
                <span class="ii-label">Account Status</span>
                <span class="status-active">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="12"/></svg>
                  Active
                </span>
              </div>
              <div class="info-item">
                <span class="ii-label">Member Since</span>
                <span class="ii-value">{{ memberSince }}</span>
              </div>
              <div class="info-item">
                <span class="ii-label">Account Type</span>
                <span class="ii-value">{{ capitalizeRole(userRole) }}</span>
              </div>
            </div>

            <div class="danger-zone">
              <div class="dz-head">
                <div class="dz-warn-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                  <h4 class="dz-title">Danger Zone</h4>
                  <p class="dz-desc">These actions are permanent and cannot be undone.</p>
                </div>
              </div>
              <button @click="openDeleteModal" class="btn-danger">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Delete Account
              </button>
            </div>
          </section>

          <!-- ⑥ AGENT APPLICATION (buyer only) -->
          <section v-if="activeTab === 'application' && userRole === 'buyer'" class="section-card">
            <div class="sc-head">
              <div class="sc-title-row">
                <div class="sc-icon si-gold">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                  <h3 class="sc-title">Agent Application</h3>
                  <p class="sc-sub">Track your application to become a RealtyLinkPH agent</p>
                </div>
              </div>
            </div>

            <!-- Loading -->
            <div v-if="loadingApplication" class="app-loading">
              <div class="app-spinner"></div>
              <span>Loading application status…</span>
            </div>

            <!-- No application yet -->
            <div v-else-if="!agentApplication" class="app-empty">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              <p>You haven't applied to become an agent yet.</p>
              <router-link to="/become-agent" class="btn-apply-now">Apply Now →</router-link>
            </div>

            <!-- Application exists -->
            <div v-else class="app-detail">
              <!-- Status banner -->
              <div class="app-status-banner" :class="appStatusClass(agentApplication.status)">
                <div class="app-status-icon">
                  <svg v-if="agentApplication.status === 'approved'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                  <svg v-else-if="agentApplication.status === 'rejected'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  <svg v-else width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <strong>{{ appStatusLabel(agentApplication.status) }}</strong>
                  <p v-if="agentApplication.status === 'pending'">Your application is being reviewed. You'll be notified once a decision is made.</p>
                  <p v-else-if="agentApplication.status === 'approved'">Congratulations! You are now a verified RealtyLinkPH agent.</p>
                  <p v-else-if="agentApplication.status === 'rejected'">Your application was not approved. See details below.</p>
                </div>
              </div>

              <!-- Application details -->
              <div class="info-grid app-info-grid">
                <div class="info-item">
                  <span class="ii-label">Applicant Name</span>
                  <span class="ii-value">{{ agentApplication.applicant_name }}</span>
                </div>
                <div class="info-item">
                  <span class="ii-label">License Number</span>
                  <span class="ii-value">{{ agentApplication.license_number }}</span>
                </div>
                <div class="info-item">
                  <span class="ii-label">Company</span>
                  <span class="ii-value">{{ agentApplication.company_name }}</span>
                </div>
                <div class="info-item">
                  <span class="ii-label">Submitted</span>
                  <span class="ii-value">{{ formatAppDate(agentApplication.applied_at) }}</span>
                </div>
                <div v-if="agentApplication.verified_at" class="info-item">
                  <span class="ii-label">Approved On</span>
                  <span class="ii-value">{{ formatAppDate(agentApplication.verified_at) }}</span>
                </div>
                <div v-if="agentApplication.rejected_at" class="info-item">
                  <span class="ii-label">Rejected On</span>
                  <span class="ii-value">{{ formatAppDate(agentApplication.rejected_at) }}</span>
                </div>
              </div>

              <!-- AI Decision -->
              <div v-if="agentApplication.ai_decision" class="app-ai-section">
                <div class="app-ai-label">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  RealtyLinkPH Buddy Decision:
                  <span class="ai-verdict" :class="'ai-' + agentApplication.ai_decision">
                    {{ agentApplication.ai_decision === 'approved' ? '✅ Auto-Approved' : agentApplication.ai_decision === 'rejected' ? '❌ Rejected' : '🔍 Sent for Review' }}
                  </span>
                </div>
                <p v-if="agentApplication.rejection_reason" class="app-rejection-reason">{{ agentApplication.rejection_reason }}</p>
              </div>

              <!-- Cooldown + reapply -->
              <div v-if="agentApplication.status === 'rejected'" class="app-reapply-section">
                <div v-if="canReapplyAt && new Date(canReapplyAt) > new Date()" class="cooldown-box">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  You can reapply after <strong>{{ formatAppDate(canReapplyAt) }}</strong>
                </div>
                <router-link v-else to="/become-agent" class="btn-apply-now">Reapply Now →</router-link>
              </div>
            </div>
          </section>

        </div><!-- /content-inner -->
      </main>

      <footer class="footer">
        <span>&copy; 2026 RealtyLinkPH. All rights reserved.</span>
      </footer>
    </div><!-- /main-wrapper -->

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
      <div class="modal-box">
        <div class="modal-head">
          <h3>Delete Account</h3>
          <button class="modal-close" @click="showDeleteModal = false">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="modal-alert">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <p><strong>Warning:</strong> This permanently removes all your data and cannot be undone.</p>
          </div>
          <ul class="warn-list">
            <li>All profile information will be deleted</li>
            <li>All properties and listings will be removed</li>
            <li>All messages and communications will be deleted</li>
            <li>This action cannot be reversed</li>
          </ul>
          <div class="form-group fg-full">
            <label class="form-label">Type your email to confirm</label>
            <input v-model="deleteConfirmEmail" type="text" class="form-input" :placeholder="userEmail" />
            <span v-if="errors.deleteConfirm" class="err-text">{{ errors.deleteConfirm }}</span>
          </div>
        </div>
        <div class="modal-foot">
          <button @click="showDeleteModal = false" class="btn-cancel">Cancel</button>
          <button @click="confirmDeleteAccount"
            :disabled="deleteConfirmEmail !== userEmail || deletingAccount"
            class="btn-danger">
            {{ deletingAccount ? 'Deleting…' : 'Delete My Account' }}
          </button>
        </div>
      </div>
    </div>

    <transition name="fade"><div v-if="successMessage" class="toast toast-ok">{{ successMessage }}</div></transition>
    <transition name="fade"><div v-if="errorMessage" class="toast toast-err">{{ errorMessage }}</div></transition>
  </div>
</template>

<script>
export default {
  name: 'Profile',
  data() {
    return {
      sidebarOpen: false,
      userName: '', userEmail: '', userRole: '', memberSince: '', profilePhotoUrl: null,
      viewings: [], recentConversations: [],
      activeTab: 'personal',
      isEditingPersonal: false, showChangePassword: false, showDeleteModal: false,
      savingPersonal: false, changingPassword: false, deletingAccount: false,
      successMessage: '', errorMessage: '',
      formData: { name: '', email: '', phone: '', bio: '' },
      passwordForm: { currentPassword: '', newPassword: '', confirmPassword: '' },
      errors: {},
      lastPasswordChange: 'January 15, 2025',
      deleteConfirmEmail: '',
      loginHistory: [
        { id: 1, device: 'Chrome on Windows', location: 'Manila, Philippines', time: 'Today at 2:30 PM' },
        { id: 2, device: 'Safari on iPhone',  location: 'Cebu City, Philippines', time: 'Yesterday at 10:15 AM' },
        { id: 3, device: 'Chrome on MacOS',   location: 'Davao, Philippines', time: '3 days ago at 8:45 PM' },
      ],
      profileTabs: [
        { id: 'personal',    label: 'Personal Info', svg: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' },
        { id: 'security',    label: 'Security',      svg: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' },
        { id: 'account',     label: 'Account',       svg: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>' },
        { id: 'application', label: 'Agent Application', svg: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>', buyerOnly: true },
      ],
      agentApplication: null,
      loadingApplication: false,
      canReapplyAt: null,
    };
  },

  computed: {
    dashboardRoute() {
      if (this.userRole === 'agent') return '/agent/dashboard';
      if (this.userRole === 'admin') return '/admin/dashboard';
      return '/dashboard';
    },
    profileCompletion() {
      let pct = 25; // account always exists
      if (this.profilePhotoUrl) pct += 25;
      if (this.formData.bio)   pct += 25;
      if (this.formData.phone) pct += 25;
      return pct;
    },
    journeySteps() {
      return [
        { key: 'account', label: 'Account Created',  done: true },
        { key: 'profile', label: 'Profile Complete',  done: this.profileCompletion >= 100 },
        { key: 'browse',  label: 'Browse Properties', done: this.viewings.length > 0 || this.recentConversations.length > 0 },
        { key: 'viewing', label: 'Schedule Viewing',  done: this.viewings.length > 0 },
        { key: 'agent',   label: 'Contact Agent',     done: this.recentConversations.length > 0 },
        { key: 'sign',    label: 'Sign Documents',    done: false },
      ];
    },
    currentJourneyStep() {
      const i = this.journeySteps.findIndex(s => !s.done);
      return i === -1 ? this.journeySteps.length : i;
    },
    journeyProgress() {
      return Math.round((this.journeySteps.filter(s => s.done).length / this.journeySteps.length) * 100);
    },
  },

  methods: {
    capitalizeRole(role) { return role ? role.charAt(0).toUpperCase() + role.slice(1) : ''; },

    triggerPhotoUpload() { this.$refs.photoInput.click(); },

    async handlePhotoUpload(event) {
      const file = event.target.files[0];
      if (!file) return;
      if (!file.type.startsWith('image/')) { this.showError('Please select a valid image file'); return; }
      if (file.size > 2 * 1024 * 1024) { this.showError('Image must be less than 2MB'); return; }
      try {
        const formData = new FormData();
        formData.append('photo', file);
        const token = localStorage.getItem('auth_token');
        const response = await fetch(window.__API_URL__ + '/api/user/upload-profile-photo', {
          method: 'POST',
          headers: { 'Authorization': `Bearer ${token}` },
          body: formData,
        });
        const data = await response.json();
        if (data.success) {
          const reader = new FileReader();
          reader.onload = (e) => { this.profilePhotoUrl = e.target.result; };
          reader.readAsDataURL(file);
          const user = JSON.parse(localStorage.getItem('user') || '{}');
          user.profile_photo_path = data.data.photo_path;
          localStorage.setItem('user', JSON.stringify(user));
          this.showSuccess('Profile photo updated successfully!');
        } else {
          this.showError(data.message || 'Failed to upload photo');
        }
      } catch { this.showError('Failed to upload photo'); }
    },

    async savePersonalInfo() {
      this.errors = {};
      if (!this.formData.name.trim()) this.errors.name = 'Name is required';
      if (!this.formData.email.trim()) this.errors.email = 'Email is required';
      if (Object.keys(this.errors).length > 0) return;
      try {
        this.savingPersonal = true;
        const token = localStorage.getItem('auth_token');
        const response = await fetch(window.__API_URL__ + '/api/user/profile', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
          body: JSON.stringify({ name: this.formData.name, email: this.formData.email, phone: this.formData.phone, bio: this.formData.bio }),
        });
        const data = await response.json();
        if (data.success) {
          this.isEditingPersonal = false;
          const user = JSON.parse(localStorage.getItem('user') || '{}');
          user.name = this.formData.name; user.email = this.formData.email;
          user.phone = this.formData.phone; user.bio = this.formData.bio;
          localStorage.setItem('user', JSON.stringify(user));
          this.userName = this.formData.name; this.userEmail = this.formData.email;
          this.showSuccess('Personal information updated successfully!');
        } else {
          if (data.errors) this.errors = data.errors;
          else this.showError(data.message || 'Failed to save personal information');
        }
      } catch { this.showError('Failed to save personal information'); }
      finally { this.savingPersonal = false; }
    },

    cancelEditPersonal() { this.isEditingPersonal = false; this.loadUserData(); },

    async changePassword() {
      this.errors = {};
      if (!this.passwordForm.currentPassword) this.errors.currentPassword = 'Current password is required';
      if (!this.passwordForm.newPassword) this.errors.password = 'New password is required';
      if (this.passwordForm.newPassword.length < 8) this.errors.password = 'Password must be at least 8 characters';
      if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) this.errors.passwordConfirmation = 'Passwords do not match';
      if (Object.keys(this.errors).length > 0) return;
      try {
        this.changingPassword = true;
        const token = localStorage.getItem('auth_token');
        const response = await fetch(window.__API_URL__ + '/api/user/change-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
          body: JSON.stringify({ current_password: this.passwordForm.currentPassword, password: this.passwordForm.newPassword, password_confirmation: this.passwordForm.confirmPassword }),
        });
        const data = await response.json();
        if (data.success) {
          this.passwordForm = { currentPassword: '', newPassword: '', confirmPassword: '' };
          this.showChangePassword = false;
          this.lastPasswordChange = 'Just now';
          this.showSuccess('Password changed successfully!');
        } else { this.showError(data.message || 'Failed to change password'); }
      } catch { this.showError('Failed to change password'); }
      finally { this.changingPassword = false; }
    },

    openDeleteModal() { this.showDeleteModal = true; },

    async confirmDeleteAccount() {
      if (this.deleteConfirmEmail !== this.userEmail) { this.errors.deleteConfirm = 'Email does not match'; return; }
      try {
        this.deletingAccount = true;
        this.showError('Account deletion API not yet implemented');
      } catch { this.showError('Failed to delete account'); }
      finally { this.deletingAccount = false; }
    },

    showSuccess(msg) { this.successMessage = msg; setTimeout(() => { this.successMessage = ''; }, 3000); },
    showError(msg)   { this.errorMessage   = msg; setTimeout(() => { this.errorMessage   = ''; }, 3000); },

    async loadAgentApplication() {
      this.loadingApplication = true;
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${window.__API_URL__}/api/user/agent-application-status`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        const data = await res.json();
        if (data.success && data.hasApplication) {
          this.agentApplication = data.application;
          this.canReapplyAt     = data.canReapplyAt || null;
        }
      } catch { /* silent */ } finally {
        this.loadingApplication = false;
      }
    },

    appStatusLabel(status) {
      return { pending: 'Under Review', approved: 'Approved', rejected: 'Rejected' }[status] || status;
    },
    appStatusClass(status) {
      return { pending: 'app-pending', approved: 'app-approved', rejected: 'app-rejected' }[status] || '';
    },
    formatAppDate(d) {
      if (!d) return '—';
      return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    },

    loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName  = user.name  || 'User';
        this.userEmail = user.email || '';
        this.userRole  = user.role  || 'buyer';
        this.formData.name  = this.userName;
        this.formData.email = this.userEmail;
        this.formData.phone = user.phone || '';
        this.formData.bio   = user.bio || user.about || '';
        const d = user.created_at ? new Date(user.created_at) : new Date();
        this.memberSince = d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__}/storage/${user.profile_photo_path}`;
        }
      } catch { /* ignore */ }
    },

    navigateToStep(step) {
      const routes = {
        account: '/profile', profile: '/profile', browse: '/properties',
        viewing: '/viewings', agent: '/conversations', sign: '/documents',
      };
      if (routes[step.key]) this.$router.push(routes[step.key]);
    },
    getStepTooltip(step) {
      if (step.key === 'account') return 'Account created — you\'re in!';
      if (step.key === 'profile') return step.done ? 'Profile complete!' : 'Add a photo, bio, and phone number to complete your profile';
      if (step.key === 'browse')  return step.done ? 'You\'ve browsed or contacted agents — great!' : 'Browse available properties — click to explore';
      if (step.key === 'viewing') return step.done ? `${this.viewings.length} viewing${this.viewings.length > 1 ? 's' : ''} scheduled` : 'No viewings yet — schedule a property visit';
      if (step.key === 'agent')   return step.done ? 'You\'ve contacted an agent' : 'Message an agent to get started';
      if (step.key === 'sign')    return 'Sign documents electronically — click to open Documents';
      return step.label;
    },
    async loadViewings() {
      try {
        const res = await fetch(`${window.__API_URL__}/api/buyer/viewings`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) this.viewings = data.viewings || data.data || [];
      } catch { /* silent */ }
    },
    async loadConversations() {
      try {
        const res = await fetch(`${window.__API_URL__}/api/buyer/conversations`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) this.recentConversations = data.data || [];
      } catch { /* silent */ }
    },
    async logout() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(window.__API_URL__ + '/api/auth/logout', { method: 'POST', headers: { 'Authorization': `Bearer ${token}` } });
      } catch { /* ignore */ }
      localStorage.removeItem('auth_token'); localStorage.removeItem('user');
      this.$router.push('/');
    },
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) { this.$router.push('/'); return; }
    this.loadUserData();
    if (this.userRole === 'buyer') {
      this.loadViewings();
      this.loadConversations();
      this.loadAgentApplication();
    }
  },
};
</script>

<style scoped>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

/* ── TOKENS (defined on wrapper so scoped vars cascade correctly) ── */
.profile-layout {
  --primary:  #0B1C39; --primary2: #102445; --primary3: #1a3158;
  --navy: #0B1C39; --navy2: #102445; --navy3: #1a3158;
  --accent:   #D89B0F; --accent2:  #E5B332; --accent3:  #B07A08;
  --s50: #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s900: #1C1917;
  --white: #FFFFFF; --bg: #EDF0F2;
  --sw: 242px; --th: 56px;
  --fd: 'Outfit','Inter',-apple-system,sans-serif;
  --fb: 'Inter',-apple-system,sans-serif;
  min-height: 100vh; display: flex; font-family: var(--fb);
  -webkit-font-smoothing: antialiased; background: var(--bg);
}

/* ══ SIDEBAR ══ */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--primary); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; }
.logo-ph { background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.sidebar-nav { flex: 1; overflow-y: auto; padding: 10px 10px 4px; scrollbar-width: none; }
.sidebar-nav::-webkit-scrollbar { display: none; }

.nav-item {
  display: flex; align-items: center; gap: 10px; padding: 9px 12px 9px 10px;
  border-radius: 9px; color: rgba(255,255,255,0.45); text-decoration: none;
  font-size: 13px; font-weight: 500; transition: all 0.18s; margin-bottom: 2px;
  cursor: pointer; border: none; background: none; width: 100%; text-align: left;
  font-family: var(--fb); position: relative;
}
.nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.78); }
.nav-item.router-link-exact-active { background: rgba(216,155,15,0.12); color: var(--accent2); }
.nav-item.router-link-exact-active::before {
  content: ''; position: absolute; left: 0; top: 7px; bottom: 7px;
  width: 3px; background: var(--accent); border-radius: 0 3px 3px 0;
}
.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }
.sidebar-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 10px 8px; }
.sidebar-bottom { flex-shrink: 0; padding: 0 10px 14px; }
.nav-user { gap: 10px; padding: 8px 10px; }
.nav-av {
  width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
  background: linear-gradient(135deg, var(--accent), var(--accent3));
  display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; color: #fff;
}
.nav-av img { width: 100%; height: 100%; object-fit: cover; }
.nav-user-info { flex: 1; min-width: 0; }
.nav-user-name { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nav-user-role  { display: block; font-size: 10.5px; color: rgba(255,255,255,0.38); margin-top: 1px; }
.nav-logout { color: rgba(255,255,255,0.30); }
.nav-logout:hover { background: rgba(239,68,68,0.10); color: #f87171; }
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 90; }

/* ══ MAIN ══ */
.main-wrapper { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* TOPBAR */
.topbar {
  background: var(--white); position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 28px; height: var(--th); border-bottom: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.topbar-left { display: flex; align-items: center; gap: 10px; }
.hamburger { display: none; width: 34px; height: 34px; border-radius: 7px; border: 1px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s600); align-items: center; justify-content: center; }
.topbar-title { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--primary); letter-spacing: -0.3px; }
.topbar-right { display: flex; align-items: center; gap: 8px; }
.tb-settings {
  display: flex; align-items: center; gap: 6px; padding: 7px 15px;
  border: 1.5px solid var(--s200); border-radius: 50px; font-size: 12.5px;
  font-weight: 600; color: var(--s600); text-decoration: none; transition: all .2s;
}
.tb-settings:hover { border-color: var(--accent); color: var(--accent3); }

/* CONTENT */
.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 860px; margin: 0 auto; padding: 28px 30px 48px; }

/* ── PROFILE HERO ── */
.profile-hero {
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy3) 100%);
  border-radius: 20px; overflow: hidden; position: relative; margin-bottom: 24px;
  box-shadow: 0 8px 32px rgba(11,28,57,0.28);
}
.hero-deco { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.deco-ring { position: absolute; border-radius: 50%; border: 1px solid rgba(216,155,15,0.10); }
.dr1 { width: 350px; height: 350px; top: -120px; right: -60px; }
.dr2 { width: 220px; height: 220px; top: -60px; right: -10px; border-style: dashed; }
.dr3 { width: 120px; height: 120px; top: -10px; right: 60px; }
.hero-body { display: flex; align-items: center; gap: 28px; padding: 36px 40px; position: relative; }

/* Avatar */
.hero-av-wrap { position: relative; flex-shrink: 0; }
.hero-av {
  width: 96px; height: 96px; border-radius: 50%; overflow: hidden; cursor: pointer;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center; position: relative;
}
.hero-av img { width: 100%; height: 100%; object-fit: cover; }
.av-init { font-family: var(--fd); font-size: 36px; font-weight: 800; color: #fff; }
.av-overlay {
  position: absolute; inset: 0; border-radius: 50%;
  background: rgba(11,28,57,0.65); display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 3px;
  color: #fff; opacity: 0; transition: opacity .22s;
  font-size: 10px; font-weight: 600;
}
.hero-av:hover .av-overlay { opacity: 1; }
.av-ring {
  position: absolute; inset: -4px; border-radius: 50%;
  border: 2px solid rgba(216,155,15,0.35); pointer-events: none;
}
.hidden-input { display: none; }

/* Hero info */
.hero-info { flex: 1; min-width: 0; }
.hi-top { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; flex-wrap: wrap; }
.hero-name { font-family: var(--fd); font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
.role-pill {
  padding: 4px 12px; border-radius: 99px; font-size: 11px; font-weight: 700;
  letter-spacing: 0.3px; text-transform: uppercase;
}
.rp-buyer { background: rgba(216,155,15,0.18); color: var(--gold2); border: 1px solid rgba(216,155,15,0.3); }
.rp-agent { background: rgba(59,130,246,0.18); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); }
.rp-admin { background: rgba(168,85,247,0.18); color: #d8b4fe; border: 1px solid rgba(168,85,247,0.3); }
.hero-email, .hero-since {
  display: flex; align-items: center; gap: 7px; font-size: 13px;
  color: rgba(255,255,255,0.55); margin-bottom: 5px;
}
.hero-progress { margin-top: 14px; }
.hp-labels { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11.5px; color: rgba(255,255,255,0.45); }
.hp-pct { color: var(--gold2) !important; font-weight: 700; }
.hp-track { height: 5px; background: rgba(255,255,255,0.10); border-radius: 99px; overflow: hidden; }
.hp-fill { height: 100%; background: linear-gradient(90deg, var(--gold), var(--gold2)); border-radius: 99px; transition: width 1.2s cubic-bezier(.4,0,.2,1); }

/* ── TABS ── */
.tab-nav {
  display: flex; gap: 4px; margin-bottom: 20px;
  background: var(--white); border-radius: 12px; padding: 5px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.tab-btn {
  display: flex; align-items: center; gap: 7px; padding: 9px 18px;
  border-radius: 9px; border: none; background: none; cursor: pointer;
  font-size: 13px; font-weight: 600; color: var(--s500); transition: all .18s;
  font-family: var(--fb); flex: 1; justify-content: center;
}
.tab-btn:hover { background: var(--s100); color: var(--s700); }
.tab-btn.active { background: var(--navy); color: #fff; box-shadow: 0 2px 8px rgba(11,28,57,0.20); }
.tab-ic { display: flex; align-items: center; }
.tab-ic svg { transition: stroke .18s; }

/* ── SECTION CARD ── */
.section-card {
  background: var(--white); border-radius: 16px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  overflow: hidden; margin-bottom: 20px;
}
.sc-head {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 22px 26px 20px; border-bottom: 1px solid var(--s100); gap: 12px;
}
.sc-title-row { display: flex; align-items: flex-start; gap: 13px; }
.sc-icon {
  width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.si-blue  { background: rgba(59,130,246,0.09); color: #3b82f6; }
.si-gold  { background: rgba(216,155,15,0.10); color: var(--gold); }
.si-green { background: rgba(22,163,74,0.09);  color: #16a34a; }
.sc-title { font-family: var(--fd); font-size: 15px; font-weight: 700; color: var(--navy); }
.sc-sub   { font-size: 12px; color: var(--s400); margin-top: 2px; }
.sc-actions { flex-shrink: 0; display: flex; align-items: center; }

/* Buttons */
.btn-edit {
  display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
  border: 1.5px solid var(--s200); border-radius: 8px; background: var(--white);
  font-size: 12.5px; font-weight: 600; color: var(--s600); cursor: pointer; transition: all .18s;
}
.btn-edit:hover { border-color: var(--gold); color: var(--gold3); background: rgba(216,155,15,0.05); }
.edit-btns { display: flex; gap: 8px; }
.btn-cancel {
  padding: 7px 14px; border-radius: 8px; border: 1.5px solid var(--s200);
  background: var(--white); font-size: 12.5px; font-weight: 600; color: var(--s500); cursor: pointer; transition: all .18s;
}
.btn-cancel:hover { border-color: var(--s300); color: var(--s700); }
.btn-save {
  padding: 7px 16px; border-radius: 8px; border: none;
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  color: #fff; font-size: 12.5px; font-weight: 700; cursor: pointer; transition: all .18s;
  box-shadow: 0 2px 8px rgba(11,28,57,0.22);
}
.btn-save:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(11,28,57,0.30); }
.btn-save:disabled { opacity: 0.55; cursor: not-allowed; }
.btn-danger {
  display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px;
  border-radius: 9px; border: 1.5px solid #fecaca; background: #fef2f2;
  color: #991b1b; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s;
}
.btn-danger:hover:not(:disabled) { background: #dc2626; border-color: #dc2626; color: #fff; }
.btn-danger:disabled { opacity: 0.4; cursor: not-allowed; }
.form-actions { display: flex; gap: 10px; }

/* Info grid (view mode) */
.info-grid {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 0;
  padding: 4px 0;
}
.info-item {
  display: flex; flex-direction: column; gap: 4px;
  padding: 18px 26px; border-bottom: 1px solid var(--s100);
}
.info-item:nth-last-child(-n+2) { border-bottom: none; }
.ii-full { grid-column: 1 / -1; border-bottom: none !important; }
.ii-label { font-size: 11px; font-weight: 700; color: var(--s400); text-transform: uppercase; letter-spacing: 0.5px; }
.ii-value { font-size: 14px; font-weight: 500; color: var(--s900); }
.ii-bio { color: var(--s600); font-style: italic; line-height: 1.55; white-space: pre-wrap; }
.ii-bio:not([data-empty]) { color: var(--s900); font-style: normal; }
.form-textarea { resize: vertical; min-height: 80px; line-height: 1.55; }
.pass-dots { letter-spacing: 3px; }
.status-active {
  display: inline-flex; align-items: center; gap: 6px; font-size: 13px;
  font-weight: 600; color: #16a34a;
}

/* Form grid (edit mode) */
.form-grid {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
  padding: 22px 26px;
}
.form-group { display: flex; flex-direction: column; gap: 6px; }
.fg-full { grid-column: 1 / -1; }
.form-label { font-size: 12px; font-weight: 700; color: var(--s600); text-transform: uppercase; letter-spacing: 0.4px; }
.form-input {
  padding: 10px 13px; border: 1.5px solid var(--s200); border-radius: 9px;
  font-size: 13.5px; font-family: var(--fb); color: var(--s900); background: var(--s50);
  transition: border-color .18s, box-shadow .18s;
}
.form-input:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(216,155,15,0.12); background: var(--white); }
.form-input::placeholder { color: var(--s300); }
.err-text { font-size: 11.5px; color: #dc2626; font-weight: 500; }

/* Security sub-section */
.sub-section { padding: 20px 26px; border-top: 1px solid var(--s100); }
.sub-title { font-size: 13px; font-weight: 700; color: var(--s700); margin-bottom: 14px; }
.login-list { display: flex; flex-direction: column; gap: 2px; }
.login-item {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 13px; border-radius: 10px; transition: background .15s;
}
.login-item:hover { background: var(--s50); }
.li-icon {
  width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
  background: var(--s100); color: var(--s400);
  display: flex; align-items: center; justify-content: center;
}
.li-info { flex: 1; }
.li-device { font-size: 13px; font-weight: 600; color: var(--s800); margin: 0; }
.li-loc    { font-size: 11.5px; color: var(--s400); margin: 1px 0 0; }
.li-time   { font-size: 11.5px; color: var(--s400); white-space: nowrap; flex-shrink: 0; }

/* Danger zone */
.danger-zone {
  margin: 0 26px 26px; border: 1.5px solid #fecaca; border-radius: 12px;
  padding: 20px 22px; background: #fffafa;
}
.dz-head { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
.dz-warn-icon { width: 36px; height: 36px; border-radius: 9px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.dz-title { font-size: 14px; font-weight: 700; color: #7f1d1d; margin-bottom: 2px; }
.dz-desc  { font-size: 12.5px; color: #b91c1c; }

/* FOOTER */
.footer { padding: 16px 30px; background: var(--white); border-top: 1px solid var(--s100); font-size: 12px; color: var(--s400); }

/* TOAST */
.toast {
  position: fixed; bottom: 24px; right: 24px; padding: 13px 22px;
  border-radius: 11px; font-size: 13.5px; font-weight: 600; z-index: 9999;
  box-shadow: 0 6px 20px rgba(0,0,0,0.14);
}
.toast-ok  { background: #15803d; color: #fff; }
.toast-err { background: #dc2626; color: #fff; }
.fade-enter-active, .fade-leave-active { transition: opacity .25s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* MODAL */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.50);
  display: flex; align-items: center; justify-content: center; z-index: 200;
  backdrop-filter: blur(3px); padding: 20px;
}
.modal-box {
  background: var(--white); border-radius: 18px; width: 100%; max-width: 480px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.20); overflow: hidden;
}
.modal-head {
  display: flex; justify-content: space-between; align-items: center;
  padding: 20px 24px; border-bottom: 1px solid var(--s100);
}
.modal-head h3 { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--navy); }
.modal-close { width: 30px; height: 30px; border-radius: 7px; border: 1.5px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s500); display: flex; align-items: center; justify-content: center; transition: all .18s; }
.modal-close:hover { border-color: var(--s300); color: var(--s800); }
.modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; }
.modal-alert {
  display: flex; align-items: flex-start; gap: 10px; padding: 13px 15px;
  background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; color: #991b1b; font-size: 13px;
}
.warn-list { padding-left: 18px; color: var(--s600); font-size: 13px; line-height: 1.8; }
.modal-foot {
  display: flex; justify-content: flex-end; gap: 10px;
  padding: 16px 24px; border-top: 1px solid var(--s100); background: var(--s50);
}

/* ══ JOURNEY TRACKER ══ */
.journey-section {
  background: var(--white); border-radius: 16px; padding: 22px 26px;
  margin-bottom: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.journey-top {
  display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px;
}
.journey-title { font-family: var(--fd); font-size: 15px; font-weight: 700; color: var(--navy); }
.journey-sub   { font-size: 12px; color: var(--s400); margin-top: 2px; }
.journey-pct-badge {
  padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 700;
  color: var(--gold3); background: rgba(216,155,15,0.10); border: 1px solid rgba(216,155,15,0.20);
  white-space: nowrap; flex-shrink: 0;
}
.journey-track {
  display: flex; align-items: flex-start; gap: 0; overflow-x: auto; scrollbar-width: none;
}
.journey-track::-webkit-scrollbar { display: none; }
.journey-step {
  flex: 1; display: flex; flex-direction: column; align-items: center;
  gap: 8px; position: relative; min-width: 72px; cursor: pointer; transition: transform 0.18s;
}
.journey-step:hover { transform: translateY(-2px); }
.journey-step:hover .step-node { box-shadow: 0 4px 16px rgba(216,155,15,0.30); }
.journey-step:not(.last)::after {
  content: ''; position: absolute; top: 16px; left: 50%; right: -50%;
  height: 2px; background: var(--s200); z-index: 0; transition: background .4s;
}
.journey-step.done:not(.last)::after { background: var(--gold); }
.step-node {
  width: 34px; height: 34px; border-radius: 50%; border: 2px solid var(--s200);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; color: var(--s400); background: var(--white);
  position: relative; z-index: 1; transition: all .3s;
}
.journey-step.done  .step-node { background: var(--gold); border-color: var(--gold); color: #fff; box-shadow: 0 2px 10px rgba(216,155,15,0.35); }
.journey-step.active .step-node { border-color: var(--gold); color: var(--gold); box-shadow: 0 0 0 4px rgba(216,155,15,0.15); }
.step-lbl { font-size: 11px; font-weight: 600; color: var(--s400); text-align: center; white-space: nowrap; }
.journey-step.done  .step-lbl { color: var(--gold3); }
.journey-step.active .step-lbl { color: var(--gold); }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
  .sidebar { left: -100%; transition: left .3s ease; }
  .sidebar.open { left: 0; }
  .sidebar-overlay { display: block; }
  .main-wrapper { margin-left: 0; }
  .hamburger { display: flex; }
  .content-inner { padding: 16px; }
  .hero-body { flex-direction: column; align-items: flex-start; padding: 24px 20px; gap: 18px; }
  .hero-name { font-size: 22px; }
  .info-grid { grid-template-columns: 1fr; }
  .info-item:last-child { border-bottom: none; }
  .info-item:nth-last-child(-n+2) { border-bottom: 1px solid var(--s100); }
  .info-item:last-child { border-bottom: none; }
  .form-grid { grid-template-columns: 1fr; padding: 16px; }
  .sc-head { flex-direction: column; padding: 18px 18px 16px; }
  .tab-btn { font-size: 12px; padding: 8px 10px; }
}

/* ── AGENT APPLICATION TAB ── */
.tab-badge-dot { width: 7px; height: 7px; background: var(--gold); border-radius: 50%; display: inline-block; margin-left: 5px; vertical-align: middle; }

.app-loading { display: flex; align-items: center; gap: 12px; padding: 32px 24px; color: var(--s500); font-size: 14px; }
.app-spinner { width: 22px; height: 22px; border: 3px solid #e5e7eb; border-top-color: var(--gold); border-radius: 50%; animation: spin 0.8s linear infinite; flex-shrink: 0; }
@keyframes spin { to { transform: rotate(360deg); } }

.app-empty { display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 48px 24px; color: var(--s500); font-size: 14px; }
.btn-apply-now { display: inline-block; padding: 10px 26px; border-radius: 50px; background: linear-gradient(135deg, var(--gold), var(--gold3, #B07A08)); color: #fff; font-weight: 700; font-size: 14px; text-decoration: none; transition: opacity 0.2s; }
.btn-apply-now:hover { opacity: 0.88; }

.app-status-banner { display: flex; align-items: flex-start; gap: 14px; padding: 16px 20px; border-radius: 12px; margin-bottom: 20px; }
.app-status-banner strong { display: block; font-size: 15px; font-weight: 700; margin-bottom: 3px; }
.app-status-banner p { font-size: 13px; margin: 0; opacity: 0.85; }
.app-pending  { background: #fef9ec; border: 1px solid #fde68a; color: #92400e; }
.app-approved { background: #d4edda; border: 1px solid #86efac; color: #166534; }
.app-rejected { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
.app-status-icon { flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.6); }

.app-info-grid { margin-bottom: 16px; }

.app-ai-section { background: #f9f8f6; border-radius: 10px; padding: 14px 18px; margin-bottom: 16px; }
.app-ai-label { display: flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 600; color: var(--s600); }
.ai-verdict { margin-left: auto; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 50px; }
.ai-approved { background: #d4edda; color: #166534; }
.ai-rejected  { background: #fee2e2; color: #991b1b; }
.ai-unclear   { background: #fef3c7; color: #92400e; }
.app-rejection-reason { font-size: 13px; color: var(--s500); margin: 10px 0 0; line-height: 1.5; }

.app-reapply-section { margin-top: 8px; }
.cooldown-box { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #92400e; background: #fef9ec; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 16px; }
</style>
