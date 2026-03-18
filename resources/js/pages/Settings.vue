<template>
  <div class="settings-layout">

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
          <span>{{ userRole === 'agent' ? 'Overview' : 'Dashboard' }}</span>
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
        <router-link to="/profile" class="nav-item nav-user" @click="sidebarOpen = false">
          <div class="nav-av">
            <img v-if="profilePhotoUrl" :src="profilePhotoUrl" :alt="userName" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" />
            <span v-else>{{ userName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ userName }}</span>
            <span class="nav-user-role">{{ capitalizeRole(userRole) }}</span>
          </div>
        </router-link>
        <router-link to="/settings" class="nav-item router-link-exact-active" @click="sidebarOpen = false">
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
          <span class="topbar-title">Settings</span>
        </div>
        <div class="topbar-right">
          <router-link to="/profile" class="tb-profile">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            My Profile
          </router-link>
        </div>
      </header>

      <main class="content-area">
        <div class="content-inner">

          <!-- ① APPEARANCE -->
          <div class="settings-card">
            <div class="card-header">
              <div class="card-icon ci-purple">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
              </div>
              <div>
                <h2 class="card-title">{{ t('appearance') }}</h2>
                <p class="card-desc">{{ t('appearanceDesc') }}</p>
              </div>
            </div>

            <div class="theme-grid">
              <button v-for="theme in themeOptions" :key="theme.value"
                @click="setTheme(theme.value)"
                :class="['theme-btn', { active: currentTheme === theme.value }]">
                <div class="theme-preview" :class="'tp-' + theme.value">
                  <div class="tp-bar"></div>
                  <div class="tp-body">
                    <div class="tp-line tl-long"></div>
                    <div class="tp-line tl-short"></div>
                    <div class="tp-blocks">
                      <div class="tp-block"></div>
                      <div class="tp-block"></div>
                    </div>
                  </div>
                </div>
                <div class="theme-label-row">
                  <span class="theme-label">{{ t(theme.key) }}</span>
                  <span v-if="currentTheme === theme.value" class="theme-check">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  </span>
                </div>
              </button>
            </div>
          </div>

          <!-- ② LANGUAGE -->
          <div class="settings-card">
            <div class="card-header">
              <div class="card-icon ci-blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              </div>
              <div>
                <h2 class="card-title">{{ t('language') }}</h2>
                <p class="card-desc">{{ t('languageDesc') }}</p>
              </div>
            </div>

            <div class="lang-list">
              <button v-for="lang in langOptions" :key="lang.code"
                @click="setLanguage(lang.code)"
                :class="['lang-btn', { active: currentLang === lang.code }]">
                <span class="lang-flag">{{ lang.flag }}</span>
                <div class="lang-info">
                  <span class="lang-name">{{ lang.name }}</span>
                  <span class="lang-native">{{ lang.native }}</span>
                </div>
                <div v-if="currentLang === lang.code" class="lang-check">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
              </button>
            </div>
          </div>

          <!-- ③ NOTIFICATIONS -->
          <div class="settings-card">
            <div class="card-header">
              <div class="card-icon ci-gold">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
              </div>
              <div>
                <h2 class="card-title">{{ t('notifications') }}</h2>
                <p class="card-desc">{{ t('notificationsDesc') }}</p>
              </div>
            </div>

            <div class="toggle-list">
              <div v-for="notif in notifSettings" :key="notif.key" class="toggle-row">
                <div class="toggle-info">
                  <span class="toggle-label">{{ t(notif.labelKey) }}</span>
                  <span class="toggle-desc">{{ t(notif.descKey) }}</span>
                </div>
                <button @click="notif.enabled = !notif.enabled; saveNotifSettings()"
                  :class="['toggle-switch', { on: notif.enabled }]">
                  <span class="toggle-thumb"></span>
                </button>
              </div>
            </div>
          </div>

          <!-- ④ GOOGLE CALENDAR -->
          <div class="settings-card">
            <div class="card-header">
              <div class="card-icon ci-green">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </div>
              <div>
                <h2 class="card-title">{{ t('googleCalendar') }}</h2>
                <p class="card-desc">{{ t('googleCalendarDesc') }}</p>
              </div>
            </div>

            <div class="gcal-body">
              <div v-if="gcalLoading" class="gcal-loading">
                <div class="gcal-spinner"></div>
                <span>Checking connection…</span>
              </div>

              <div v-else-if="gcalConnected" class="gcal-connected">
                <div class="gcal-status-row">
                  <div class="gcal-badge-connected">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    Connected
                  </div>
                  <p class="gcal-desc">{{ t('googleCalendarConnected') }}</p>
                </div>
                <button @click="disconnectGoogle" class="btn-disconnect" :disabled="gcalDisconnecting">
                  {{ gcalDisconnecting ? 'Disconnecting…' : t('disconnect') }}
                </button>
              </div>

              <div v-else class="gcal-disconnected">
                <p class="gcal-desc">{{ t('googleCalendarInfo') }}</p>
                <button @click="connectGoogle" class="btn-connect">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  {{ t('connectGoogleCalendar') }}
                </button>
              </div>
            </div>
          </div>

        </div><!-- /content-inner -->
      </main>

      <footer class="footer">
        <span>&copy; 2026 RealtyLinkPH. All rights reserved.</span>
      </footer>
    </div><!-- /main-wrapper -->

    <!-- Save toast -->
    <transition name="fade">
      <div v-if="saved" class="toast toast-ok">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
        {{ t('settingsSaved') }}
      </div>
    </transition>
  </div>
</template>

<script>
const translations = {
  en: {
    dashboard: 'Dashboard',
    settingsDesc: 'Customize your RealtyLinkPH experience',
    appearance: 'Appearance', appearanceDesc: 'Choose how RealtyLinkPH looks for you',
    lightTheme: 'Light', darkTheme: 'Dark', systemTheme: 'System',
    language: 'Language', languageDesc: 'Choose your preferred language',
    notifications: 'Notifications', notificationsDesc: 'Manage how you get notified',
    newMessages: 'New Messages', newMessagesDesc: 'Get notified when you receive messages',
    viewingUpdates: 'Viewing Updates', viewingUpdatesDesc: 'Alerts for viewing approvals and schedules',
    propertyAlerts: 'Property Alerts', propertyAlertsDesc: 'New listings matching your interests',
    settingsSaved: 'Settings saved!',
    googleCalendar: 'Google Calendar', googleCalendarDesc: 'Sync your approved viewings with Google Calendar',
    connected: 'Connected', disconnect: 'Disconnect', connectGoogleCalendar: 'Connect Google Calendar',
    googleCalendarConnected: 'Your approved viewings will automatically be added to your Google Calendar.',
    googleCalendarInfo: 'Connect your Google Calendar to automatically create events when viewings are approved.',
  },
  tl: {
    dashboard: 'Dashboard',
    settingsDesc: 'I-customize ang iyong karanasan sa RealtyLinkPH',
    appearance: 'Hitsura', appearanceDesc: 'Piliin kung paano magmumukhang ang RealtyLinkPH',
    lightTheme: 'Maliwanag', darkTheme: 'Madilim', systemTheme: 'System',
    language: 'Wika', languageDesc: 'Piliin ang iyong gustong wika',
    notifications: 'Mga Abiso', notificationsDesc: 'Pamahalaan kung paano ka maabisuhan',
    newMessages: 'Mga Bagong Mensahe', newMessagesDesc: 'Maabisuhan kapag may mensahe',
    viewingUpdates: 'Mga Update sa Bisita', viewingUpdatesDesc: 'Mga alerto para sa mga pagbisita',
    propertyAlerts: 'Mga Alerto sa Ari-arian', propertyAlertsDesc: 'Mga bagong listing na akma sa iyong interes',
    settingsSaved: 'Naligtas ang mga setting!',
    googleCalendar: 'Google Calendar', googleCalendarDesc: 'I-sync ang iyong mga viewing sa Google Calendar',
    connected: 'Nakakonekta', disconnect: 'I-disconnect', connectGoogleCalendar: 'I-connect ang Google Calendar',
    googleCalendarConnected: 'Awtomatikong maidadagdag ang mga viewing sa iyong Google Calendar.',
    googleCalendarInfo: 'I-connect ang iyong Google Calendar para awtomatikong gumawa ng events.',
  },
};

export default {
  name: 'Settings',
  data() {
    return {
      sidebarOpen: false,
      userName: '', userRole: '', profilePhotoUrl: null,
      currentTheme: localStorage.getItem('theme_preference') || 'light',
      currentLang:  localStorage.getItem('lang_preference')  || 'en',
      saved: false,
      themeOptions: [
        { value: 'light',  key: 'lightTheme'  },
        { value: 'dark',   key: 'darkTheme'   },
        { value: 'system', key: 'systemTheme' },
      ],
      langOptions: [
        { code: 'en', name: 'English', native: 'English', flag: '🇺🇸' },
        { code: 'tl', name: 'Filipino', native: 'Tagalog', flag: '🇵🇭' },
      ],
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      gcalConnected: false, gcalLoading: true, gcalDisconnecting: false,
      notifSettings: [
        { key: 'messages',   labelKey: 'newMessages',    descKey: 'newMessagesDesc',    enabled: true  },
        { key: 'viewings',   labelKey: 'viewingUpdates', descKey: 'viewingUpdatesDesc', enabled: true  },
        { key: 'properties', labelKey: 'propertyAlerts', descKey: 'propertyAlertsDesc', enabled: false },
      ],
    };
  },

  computed: {
    dashboardRoute() {
      if (this.userRole === 'agent') return '/agent/dashboard';
      if (this.userRole === 'admin') return '/admin/dashboard';
      return '/dashboard';
    },
    isDarkMode() {
      if (this.currentTheme === 'dark') return true;
      if (this.currentTheme === 'system') return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      return false;
    },
  },

  methods: {
    t(key) { return translations[this.currentLang]?.[key] || translations['en'][key] || key; },
    capitalizeRole(r) { return r ? r.charAt(0).toUpperCase() + r.slice(1) : ''; },

    setTheme(theme) { this.currentTheme = theme; localStorage.setItem('theme_preference', theme); this.applyTheme(); this.showSaved(); },
    applyTheme() {
      const html = document.documentElement;
      html.classList.remove('theme-light', 'theme-dark');
      if (this.currentTheme === 'dark') { html.classList.add('theme-dark'); }
      else if (this.currentTheme === 'system') { html.classList.add(window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'theme-dark' : 'theme-light'); }
      else { html.classList.add('theme-light'); }
    },
    setLanguage(code) { this.currentLang = code; localStorage.setItem('lang_preference', code); this.showSaved(); },
    saveNotifSettings() {
      const prefs = {};
      this.notifSettings.forEach(n => { prefs[n.key] = n.enabled; });
      localStorage.setItem('notif_preferences', JSON.stringify(prefs));
      this.showSaved();
    },
    showSaved() { this.saved = true; setTimeout(() => { this.saved = false; }, 2500); },
    loadSavedNotifSettings() {
      try {
        const prefs = JSON.parse(localStorage.getItem('notif_preferences') || '{}');
        this.notifSettings.forEach(n => { if (prefs[n.key] !== undefined) n.enabled = prefs[n.key]; });
      } catch { /* ignore */ }
    },

    async checkGoogleCalendarStatus() {
      try {
        this.gcalLoading = true;
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/google/status`, { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } });
        const data = await res.json();
        this.gcalConnected = data.connected || false;
      } catch { this.gcalConnected = false; }
      finally { this.gcalLoading = false; }
    },
    async connectGoogle() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/google/redirect`, { headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.auth_url) window.location.href = data.auth_url;
      } catch { /* ignore */ }
    },
    async disconnectGoogle() {
      try {
        this.gcalDisconnecting = true;
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/google/disconnect`, { method: 'DELETE', headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) { this.gcalConnected = false; this.showSaved(); }
      } catch { /* ignore */ }
      finally { this.gcalDisconnecting = false; }
    },
    handleGoogleCallback() {
      const params = new URLSearchParams(window.location.search);
      if (params.get('google') === 'connected') {
        this.gcalConnected = true; this.saved = true;
        const url = new URL(window.location); url.searchParams.delete('google');
        window.history.replaceState({}, '', url);
        setTimeout(() => { this.saved = false; }, 3000);
      }
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
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'User';
    this.userRole = user.role || 'buyer';
    if (user.profile_photo_path) this.profilePhotoUrl = `${this.apiUrl}/storage/${user.profile_photo_path}`;
    this.loadSavedNotifSettings();
    this.applyTheme();
    this.checkGoogleCalendarStatus();
    this.handleGoogleCallback();
  },
};
</script>

<style scoped>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

/* ── TOKENS ── */
.settings-layout {
  --primary:  #0B1C39; --primary2: #102445; --primary3: #1a3158;
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
.topbar-right { display: flex; align-items: center; }
.tb-profile {
  display: flex; align-items: center; gap: 6px; padding: 7px 15px;
  border: 1.5px solid var(--s200); border-radius: 50px; font-size: 12.5px;
  font-weight: 600; color: var(--s600); text-decoration: none; transition: all .2s;
}
.tb-profile:hover { border-color: var(--accent); color: var(--accent3); }

/* CONTENT */
.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 720px; margin: 0 auto; padding: 28px 30px 48px; display: flex; flex-direction: column; gap: 20px; }

/* ── SETTINGS CARD ── */
.settings-card {
  background: var(--white); border-radius: 16px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden;
}
.card-header {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 22px 26px; border-bottom: 1px solid var(--s100);
}
.card-icon {
  width: 42px; height: 42px; border-radius: 11px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.ci-purple { background: rgba(168,85,247,0.09); color: #9333ea; }
.ci-blue   { background: rgba(59,130,246,0.09);  color: #3b82f6; }
.ci-gold   { background: rgba(216,155,15,0.10);  color: var(--accent); }
.ci-green  { background: rgba(22,163,74,0.09);   color: #16a34a; }
.card-title { font-family: var(--fd); font-size: 15px; font-weight: 700; color: var(--primary); margin-bottom: 2px; }
.card-desc  { font-size: 12.5px; color: var(--s400); }

/* ── THEME GRID ── */
.theme-grid { display: flex; gap: 12px; padding: 22px 26px; }
.theme-btn {
  flex: 1; border: 2px solid var(--s200); border-radius: 12px;
  background: var(--s50); cursor: pointer; overflow: hidden; transition: all .2s;
  padding: 0 0 12px; display: flex; flex-direction: column;
}
.theme-btn:hover { border-color: rgba(216,155,15,0.35); }
.theme-btn.active { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(216,155,15,0.12); }

.theme-preview { height: 72px; display: flex; flex-direction: column; overflow: hidden; }
.tp-light .theme-preview { background: #f8f8f8; }
.tp-dark .theme-preview  { background: #1a1a2e; }
.tp-system .theme-preview { background: linear-gradient(135deg, #f8f8f8 50%, #1a1a2e 50%); }

.tp-bar { height: 16px; background: rgba(255,255,255,0.80); display: flex; align-items: center; padding: 0 8px; gap: 4px; }
.tp-dark .tp-bar { background: rgba(255,255,255,0.08); }
.tp-body { flex: 1; padding: 8px; display: flex; flex-direction: column; gap: 4px; }
.tp-line { height: 6px; border-radius: 3px; background: rgba(0,0,0,0.12); }
.tl-long { width: 75%; }
.tl-short { width: 45%; }
.tp-dark .tp-line { background: rgba(255,255,255,0.15); }
.tp-blocks { display: flex; gap: 5px; margin-top: 2px; }
.tp-block { flex: 1; height: 18px; border-radius: 4px; background: rgba(0,0,0,0.08); }
.tp-dark .tp-block { background: rgba(255,255,255,0.10); }

.theme-label-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 13px 0; }
.theme-label { font-size: 13px; font-weight: 600; color: var(--s700); }
.theme-check {
  width: 20px; height: 20px; border-radius: 50%; background: var(--accent);
  display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0;
}

/* ── LANGUAGE ── */
.lang-list { display: flex; flex-direction: column; gap: 0; padding: 10px 18px 18px; }
.lang-btn {
  display: flex; align-items: center; gap: 14px; padding: 14px 16px;
  border: 1.5px solid transparent; border-radius: 11px;
  background: none; cursor: pointer; transition: all .18s; width: 100%;
  margin-bottom: 6px;
}
.lang-btn:hover { background: var(--s50); border-color: var(--s200); }
.lang-btn.active { background: rgba(216,155,15,0.06); border-color: rgba(216,155,15,0.25); }
.lang-flag { font-size: 26px; flex-shrink: 0; }
.lang-info { flex: 1; text-align: left; }
.lang-name { display: block; font-size: 14px; font-weight: 700; color: var(--s900); }
.lang-native { display: block; font-size: 12px; color: var(--s400); margin-top: 1px; }
.lang-check {
  width: 22px; height: 22px; border-radius: 50%; background: var(--accent);
  display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0;
}

/* ── TOGGLES ── */
.toggle-list { display: flex; flex-direction: column; padding: 8px 26px; }
.toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 17px 0; gap: 20px; border-bottom: 1px solid var(--s100);
}
.toggle-row:last-child { border-bottom: none; }
.toggle-info { flex: 1; }
.toggle-label { display: block; font-size: 13.5px; font-weight: 600; color: var(--s900); margin-bottom: 2px; }
.toggle-desc  { display: block; font-size: 12px; color: var(--s400); }

.toggle-switch {
  width: 46px; height: 25px; border-radius: 12.5px;
  background: var(--s300); border: none; cursor: pointer;
  position: relative; transition: background .3s; flex-shrink: 0; padding: 0;
}
.toggle-switch.on { background: var(--accent); }
.toggle-thumb {
  position: absolute; width: 19px; height: 19px; border-radius: 50%;
  background: white; top: 3px; left: 3px;
  transition: left .25s; box-shadow: 0 1px 4px rgba(0,0,0,0.20);
}
.toggle-switch.on .toggle-thumb { left: 24px; }

/* ── GOOGLE CAL ── */
.gcal-body { padding: 20px 26px; }
.gcal-loading {
  display: flex; align-items: center; gap: 10px;
  color: var(--s400); font-size: 13.5px;
}
.gcal-spinner {
  width: 18px; height: 18px; border-radius: 50%;
  border: 2px solid var(--s200); border-top-color: var(--accent);
  animation: spin .7s linear infinite; flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }
.gcal-connected, .gcal-disconnected { display: flex; flex-direction: column; gap: 14px; }
.gcal-status-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.gcal-badge-connected {
  display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px;
  border-radius: 99px; background: #dcfce7; color: #166534;
  font-size: 12px; font-weight: 700;
}
.gcal-desc { font-size: 13px; color: var(--s500); }

.btn-connect {
  display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px;
  border-radius: 10px; border: 1.5px solid rgba(216,155,15,0.35);
  background: rgba(216,155,15,0.06); color: var(--accent3);
  font-size: 13px; font-weight: 700; cursor: pointer; transition: all .22s; align-self: flex-start;
}
.btn-connect:hover { background: var(--accent); border-color: var(--accent); color: #fff; }
.btn-disconnect {
  display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px;
  border-radius: 9px; border: 1.5px solid #fecaca; background: #fef2f2;
  color: #991b1b; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .2s; align-self: flex-start;
}
.btn-disconnect:hover:not(:disabled) { background: #dc2626; border-color: #dc2626; color: #fff; }
.btn-disconnect:disabled { opacity: 0.5; cursor: not-allowed; }

/* FOOTER */
.footer { padding: 16px 30px; background: var(--white); border-top: 1px solid var(--s100); font-size: 12px; color: var(--s400); }

/* TOAST */
.toast {
  position: fixed; bottom: 24px; right: 24px; padding: 12px 20px;
  border-radius: 11px; font-size: 13.5px; font-weight: 600; z-index: 9999;
  box-shadow: 0 6px 20px rgba(0,0,0,0.14); display: flex; align-items: center; gap: 8px;
}
.toast-ok { background: #15803d; color: #fff; }
.fade-enter-active, .fade-leave-active { transition: opacity .25s, transform .25s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(10px); }

/* ══ RESPONSIVE ══ */
@media (max-width: 768px) {
  .sidebar { left: -100%; transition: left .3s ease; }
  .sidebar.open { left: 0; }
  .sidebar-overlay { display: block; }
  .main-wrapper { margin-left: 0; }
  .hamburger { display: flex; }
  .content-inner { padding: 14px; gap: 14px; }
  .theme-grid { flex-direction: column; padding: 16px; }
  .card-header { padding: 18px 18px; }
  .toggle-list { padding: 6px 18px; }
  .gcal-body { padding: 16px 18px; }
  .lang-list { padding: 8px 14px 14px; }
}
</style>
