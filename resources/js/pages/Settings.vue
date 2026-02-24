<template>
  <div class="settings-wrapper" :class="{ 'dark-mode': isDarkMode }">
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
          <span class="nav-label">{{ t('dashboard') }}</span>
        </router-link>

        <div class="nav-section">
          <h3 class="section-title">{{ t('browse') }}</h3>
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">{{ t('discover') }}</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">{{ t('myWallet') }}</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">{{ t('activity') }}</h3>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">{{ t('inbox') }}</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">{{ t('viewings') }}</span>
          </router-link>
          <router-link to="/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">{{ t('documents') }}</span>
          </router-link>
        </div>

        <div class="nav-section">
          <h3 class="section-title">{{ t('settingsLabel') }}</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-label">{{ t('profile') }}</span>
          </router-link>
          <router-link to="/settings" class="nav-item active">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">{{ t('settings') }}</span>
          </router-link>
        </div>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name">{{ userName }}</p>
            <p class="user-role">Buyer</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <h1 class="page-title">{{ t('settings') }}</h1>
          <p class="page-sub">{{ t('settingsDesc') }}</p>
        </div>
      </nav>

      <div class="content-area">

        <!-- Appearance Section -->
        <div class="settings-card">
          <div class="card-header">
            <div class="card-icon">🎨</div>
            <div>
              <h2 class="card-title">{{ t('appearance') }}</h2>
              <p class="card-desc">{{ t('appearanceDesc') }}</p>
            </div>
          </div>

          <div class="option-grid">
            <button
              v-for="theme in themeOptions"
              :key="theme.value"
              @click="setTheme(theme.value)"
              :class="['option-btn', { active: currentTheme === theme.value }]"
            >
              <span class="option-icon">{{ theme.icon }}</span>
              <span class="option-label">{{ t(theme.key) }}</span>
              <span v-if="currentTheme === theme.value" class="option-check">✓</span>
            </button>
          </div>

          <!-- Preview Box -->
          <div class="preview-box" :class="previewClass">
            <div class="preview-card">
              <div class="preview-bar">
                <div class="preview-dot red"></div>
                <div class="preview-dot yellow"></div>
                <div class="preview-dot green"></div>
              </div>
              <div class="preview-body">
                <div class="preview-line long"></div>
                <div class="preview-line short"></div>
                <div class="preview-blocks">
                  <div class="preview-block"></div>
                  <div class="preview-block"></div>
                  <div class="preview-block"></div>
                </div>
              </div>
            </div>
            <p class="preview-label">{{ t('preview') }}: {{ t(currentTheme + 'Mode') }}</p>
          </div>
        </div>

        <!-- Language Section -->
        <div class="settings-card">
          <div class="card-header">
            <div class="card-icon">🌐</div>
            <div>
              <h2 class="card-title">{{ t('language') }}</h2>
              <p class="card-desc">{{ t('languageDesc') }}</p>
            </div>
          </div>

          <div class="lang-options">
            <button
              v-for="lang in langOptions"
              :key="lang.code"
              @click="setLanguage(lang.code)"
              :class="['lang-btn', { active: currentLang === lang.code }]"
            >
              <span class="lang-flag">{{ lang.flag }}</span>
              <div class="lang-info">
                <span class="lang-name">{{ lang.name }}</span>
                <span class="lang-native">{{ lang.native }}</span>
              </div>
              <span v-if="currentLang === lang.code" class="lang-check">✓</span>
            </button>
          </div>
        </div>

        <!-- Notifications Section -->
        <div class="settings-card">
          <div class="card-header">
            <div class="card-icon">🔔</div>
            <div>
              <h2 class="card-title">{{ t('notifications') }}</h2>
              <p class="card-desc">{{ t('notificationsDesc') }}</p>
            </div>
          </div>

          <div class="toggle-list">
            <div class="toggle-row" v-for="notif in notifSettings" :key="notif.key">
              <div class="toggle-info">
                <span class="toggle-label">{{ t(notif.labelKey) }}</span>
                <span class="toggle-desc">{{ t(notif.descKey) }}</span>
              </div>
              <button
                @click="notif.enabled = !notif.enabled; saveNotifSettings()"
                :class="['toggle-switch', { on: notif.enabled }]"
              >
                <span class="toggle-thumb"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Save Banner -->
        <transition name="save-banner">
          <div v-if="saved" class="save-banner">
            ✓ {{ t('settingsSaved') }}
          </div>
        </transition>

      </div>
    </main>
  </div>
</template>

<script>
const translations = {
  en: {
    dashboard: 'Dashboard', browse: 'Browse', discover: 'Discover',
    myWallet: 'My Wallet', activity: 'Activity', inbox: 'Inbox',
    viewings: 'Viewings', documents: 'Documents', settingsLabel: 'Settings',
    profile: 'Profile', settings: 'Settings',
    settingsDesc: 'Customize your RealtyLinkPH experience',
    appearance: 'Appearance', appearanceDesc: 'Choose how RealtyLinkPH looks for you',
    lightTheme: 'Light', darkTheme: 'Dark', systemTheme: 'System Default',
    preview: 'Preview', lightMode: 'Light Mode', darkMode: 'Dark Mode', systemMode: 'System Mode',
    language: 'Language', languageDesc: 'Choose your preferred language',
    notifications: 'Notifications', notificationsDesc: 'Manage how you get notified',
    newMessages: 'New Messages', newMessagesDesc: 'Get notified when you receive messages',
    viewingUpdates: 'Viewing Updates', viewingUpdatesDesc: 'Alerts for viewing approvals and schedules',
    propertyAlerts: 'Property Alerts', propertyAlertsDesc: 'New listings matching your interests',
    settingsSaved: 'Settings saved successfully!',
  },
  tl: {
    dashboard: 'Dashboard', browse: 'Mag-browse', discover: 'Tuklasin',
    myWallet: 'Ang Aking Wallet', activity: 'Aktibidad', inbox: 'Inbox',
    viewings: 'Mga Bisita', documents: 'Mga Dokumento', settingsLabel: 'Mga Setting',
    profile: 'Profile', settings: 'Mga Setting',
    settingsDesc: 'I-customize ang iyong karanasan sa RealtyLinkPH',
    appearance: 'Hitsura', appearanceDesc: 'Piliin kung paano magmumukhang ang RealtyLinkPH para sa iyo',
    lightTheme: 'Maliwanag', darkTheme: 'Madilim', systemTheme: 'Default ng Sistema',
    preview: 'Preview', lightMode: 'Light Mode', darkMode: 'Dark Mode', systemMode: 'System Mode',
    language: 'Wika', languageDesc: 'Piliin ang iyong gustong wika',
    notifications: 'Mga Abiso', notificationsDesc: 'Pamahalaan kung paano ka maabisuhan',
    newMessages: 'Mga Bagong Mensahe', newMessagesDesc: 'Maabisuhan kapag may mensahe',
    viewingUpdates: 'Mga Update sa Bisita', viewingUpdatesDesc: 'Mga alerto para sa mga pagbisita',
    propertyAlerts: 'Mga Alerto sa Ari-arian', propertyAlertsDesc: 'Mga bagong listing na akma sa iyong interes',
    settingsSaved: 'Matagumpay na na-save ang mga setting!',
  },
};

export default {
  name: 'Settings',

  data() {
    return {
      userName: '',
      currentTheme: localStorage.getItem('theme_preference') || 'light',
      currentLang: localStorage.getItem('lang_preference') || 'en',
      saved: false,

      themeOptions: [
        { value: 'light', key: 'lightTheme', icon: '☀️' },
        { value: 'dark', key: 'darkTheme', icon: '🌙' },
        { value: 'system', key: 'systemTheme', icon: '💻' },
      ],

      langOptions: [
        { code: 'en', name: 'English', native: 'English', flag: '🇺🇸' },
        { code: 'tl', name: 'Filipino', native: 'Tagalog', flag: '🇵🇭' },
      ],

      notifSettings: [
        {
          key: 'messages',
          labelKey: 'newMessages',
          descKey: 'newMessagesDesc',
          enabled: true,
        },
        {
          key: 'viewings',
          labelKey: 'viewingUpdates',
          descKey: 'viewingUpdatesDesc',
          enabled: true,
        },
        {
          key: 'properties',
          labelKey: 'propertyAlerts',
          descKey: 'propertyAlertsDesc',
          enabled: false,
        },
      ],
    };
  },

  computed: {
    isDarkMode() {
      if (this.currentTheme === 'dark') return true;
      if (this.currentTheme === 'system') {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      }
      return false;
    },

    previewClass() {
      if (this.currentTheme === 'dark') return 'preview-dark';
      if (this.currentTheme === 'system') {
        const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        return systemDark ? 'preview-dark' : 'preview-light';
      }
      return 'preview-light';
    },
  },

  methods: {
    t(key) {
      return translations[this.currentLang]?.[key] || translations['en'][key] || key;
    },

    setTheme(theme) {
      this.currentTheme = theme;
      localStorage.setItem('theme_preference', theme);
      this.applyTheme();
      this.showSaved();
    },

    applyTheme() {
      const html = document.documentElement;
      html.classList.remove('theme-light', 'theme-dark', 'theme-system');

      if (this.currentTheme === 'dark') {
        html.classList.add('theme-dark');
      } else if (this.currentTheme === 'system') {
        html.classList.add('theme-system');
      } else {
        html.classList.add('theme-light');
      }
    },

    setLanguage(code) {
      this.currentLang = code;
      localStorage.setItem('lang_preference', code);
      this.showSaved();
    },

    saveNotifSettings() {
      const prefs = {};
      this.notifSettings.forEach(n => { prefs[n.key] = n.enabled; });
      localStorage.setItem('notif_preferences', JSON.stringify(prefs));
      this.showSaved();
    },

    showSaved() {
      this.saved = true;
      setTimeout(() => { this.saved = false; }, 2500);
    },

    loadSavedNotifSettings() {
      try {
        const prefs = JSON.parse(localStorage.getItem('notif_preferences') || '{}');
        this.notifSettings.forEach(n => {
          if (prefs[n.key] !== undefined) n.enabled = prefs[n.key];
        });
      } catch (e) { /* ignore */ }
    },
  },

  mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Buyer';
    this.loadSavedNotifSettings();
    this.applyTheme();
  },
};
</script>

<style scoped>
:root {
  --gold: #e6ae0d;
  --gold-dark: #d4a000;
  --black: #100c08;
  --smoke: #f5f5f5;
  --light-gray: #e0e0e0;
}

.settings-wrapper {
  display: flex; min-height: 100vh; background: var(--smoke);
  font-family: 'Inter', sans-serif;
  transition: background 0.3s;
}

/* Dark mode overrides */
.settings-wrapper.dark-mode { background: #1a1714; }
.settings-wrapper.dark-mode .main-content { background: #1a1714; }
.settings-wrapper.dark-mode .topbar { background: #231f1b; border-color: #333; }
.settings-wrapper.dark-mode .page-title { color: #f5f5f5; }
.settings-wrapper.dark-mode .page-sub { color: #888; }
.settings-wrapper.dark-mode .settings-card { background: #231f1b; border-color: #333; }
.settings-wrapper.dark-mode .card-title { color: #f5f5f5; }
.settings-wrapper.dark-mode .card-desc { color: #888; }
.settings-wrapper.dark-mode .option-btn { background: #2d2925; border-color: #444; color: #ccc; }
.settings-wrapper.dark-mode .option-btn:hover { border-color: var(--gold); }
.settings-wrapper.dark-mode .option-btn.active { border-color: var(--gold); background: #3a3020; }
.settings-wrapper.dark-mode .option-label { color: #e0e0e0; }
.settings-wrapper.dark-mode .lang-btn { background: #2d2925; border-color: #444; }
.settings-wrapper.dark-mode .lang-btn:hover { border-color: var(--gold); }
.settings-wrapper.dark-mode .lang-btn.active { border-color: var(--gold); background: #3a3020; }
.settings-wrapper.dark-mode .lang-name { color: #f5f5f5; }
.settings-wrapper.dark-mode .lang-native { color: #888; }
.settings-wrapper.dark-mode .toggle-row { border-color: #333; }
.settings-wrapper.dark-mode .toggle-label { color: #f5f5f5; }
.settings-wrapper.dark-mode .toggle-desc { color: #888; }

/* Sidebar */
.sidebar {
  width: 260px; background: white; border-right: 1px solid var(--light-gray);
  display: flex; flex-direction: column; position: fixed; left: 0; top: 0;
  height: 100vh; overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0,0,0,0.06); z-index: 100;
}
.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--light-gray); }
.sidebar-logo { font-size: 22px; font-weight: 800; margin: 0; }
.logo-realty { color: var(--black); }
.logo-ph { color: var(--gold); }
.sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
.nav-section { margin-bottom: 20px; }
.section-title {
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.2px; color: #999; padding: 6px 16px; margin: 0;
}
.nav-item {
  display: flex; align-items: center; gap: 12px; padding: 11px 16px; margin: 0 8px;
  color: var(--black); text-decoration: none; border-radius: 8px;
  font-size: 14px; font-weight: 500; transition: all 0.2s;
}
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-weight: 700;
}
.nav-icon { font-size: 18px; min-width: 22px; text-align: center; }
.nav-label { flex: 1; }
.sidebar-footer { padding: 16px; border-top: 1px solid var(--light-gray); }
.user-card { display: flex; align-items: center; gap: 10px; background: var(--smoke); border-radius: 8px; padding: 10px; }
.user-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 15px; flex-shrink: 0;
}
.user-info { flex: 1; min-width: 0; }
.user-name { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-role { font-size: 11px; color: #999; margin: 0; }

/* Main */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; transition: background 0.3s; }

.topbar {
  background: white; border-bottom: 1px solid var(--light-gray);
  padding: 20px 32px; position: sticky; top: 0; z-index: 50; transition: all 0.3s;
}
.topbar-content { display: flex; flex-direction: column; }
.page-title { font-size: 26px; font-weight: 800; color: var(--black); margin: 0; transition: color 0.3s; }
.page-sub { font-size: 13px; color: #999; margin: 4px 0 0; transition: color 0.3s; }

/* Content */
.content-area { padding: 32px; flex: 1; max-width: 760px; display: flex; flex-direction: column; gap: 24px; }

/* Settings Card */
.settings-card {
  background: white; border-radius: 16px; border: 1px solid var(--light-gray);
  padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  transition: all 0.3s;
}

.card-header { display: flex; gap: 16px; align-items: flex-start; margin-bottom: 24px; }
.card-icon {
  width: 48px; height: 48px; border-radius: 12px;
  background: linear-gradient(135deg, #fdf5d0, #fef0a0);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.card-title { font-size: 18px; font-weight: 700; color: var(--black); margin: 0 0 4px; transition: color 0.3s; }
.card-desc { font-size: 13px; color: #888; margin: 0; transition: color 0.3s; }

/* Theme Options */
.option-grid { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
.option-btn {
  display: flex; align-items: center; gap: 10px; padding: 14px 20px;
  border: 2px solid var(--light-gray); border-radius: 12px;
  background: var(--smoke); cursor: pointer; font-size: 14px;
  font-weight: 600; color: #555; transition: all 0.2s; flex: 1; min-width: 120px;
}
.option-btn:hover { border-color: var(--gold); background: #fdf5d0; color: var(--black); }
.option-btn.active {
  border-color: var(--gold); background: linear-gradient(135deg, #fdf5d0, #fef0a0);
  color: var(--black);
}
.option-icon { font-size: 20px; }
.option-label { flex: 1; text-align: left; }
.option-check { color: var(--gold); font-weight: 800; font-size: 16px; }

/* Preview Box */
.preview-box {
  border-radius: 12px; padding: 20px; text-align: center;
  border: 1px solid var(--light-gray); transition: all 0.3s;
}
.preview-light { background: #f8f8f8; }
.preview-dark { background: #1a1a2e; border-color: #333; }
.preview-card {
  max-width: 280px; margin: 0 auto 12px;
  border-radius: 8px; overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}
.preview-bar {
  display: flex; gap: 5px; padding: 8px 10px; align-items: center;
  background: white;
}
.preview-dot { width: 8px; height: 8px; border-radius: 50%; }
.preview-dot.red { background: #ff5f57; }
.preview-dot.yellow { background: #ffbd2e; }
.preview-dot.green { background: #28c840; }
.preview-dark .preview-bar { background: #2d2d3d; }
.preview-body { padding: 12px; background: white; }
.preview-dark .preview-body { background: #252540; }
.preview-line {
  height: 8px; border-radius: 4px; background: #e0e0e0; margin-bottom: 8px;
}
.preview-dark .preview-line { background: #444; }
.preview-line.long { width: 80%; }
.preview-line.short { width: 50%; }
.preview-blocks { display: flex; gap: 6px; margin-top: 10px; }
.preview-block {
  flex: 1; height: 40px; border-radius: 4px; background: #f0f0f0;
}
.preview-dark .preview-block { background: #3a3a50; }
.preview-label { font-size: 12px; color: #999; margin: 0; }
.preview-dark .preview-label { color: #666; }

/* Language Options */
.lang-options { display: flex; flex-direction: column; gap: 12px; }
.lang-btn {
  display: flex; align-items: center; gap: 16px; padding: 16px 20px;
  border: 2px solid var(--light-gray); border-radius: 12px;
  background: var(--smoke); cursor: pointer; transition: all 0.2s; width: 100%;
}
.lang-btn:hover { border-color: var(--gold); background: #fdf5d0; }
.lang-btn.active {
  border-color: var(--gold); background: linear-gradient(135deg, #fdf5d0, #fef0a0);
}
.lang-flag { font-size: 28px; }
.lang-info { flex: 1; text-align: left; }
.lang-name { display: block; font-size: 15px; font-weight: 700; color: var(--black); margin-bottom: 2px; transition: color 0.3s; }
.lang-native { display: block; font-size: 12px; color: #999; transition: color 0.3s; }
.lang-check { color: var(--gold); font-weight: 800; font-size: 18px; }

/* Toggle List */
.toggle-list { display: flex; flex-direction: column; }
.toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 0; border-bottom: 1px solid var(--light-gray); gap: 20px;
}
.toggle-row:last-child { border-bottom: none; }
.toggle-info { flex: 1; }
.toggle-label { display: block; font-size: 14px; font-weight: 600; color: var(--black); margin-bottom: 3px; transition: color 0.3s; }
.toggle-desc { display: block; font-size: 12px; color: #999; transition: color 0.3s; }

.toggle-switch {
  width: 48px; height: 26px; border-radius: 13px;
  background: #ccc; border: none; cursor: pointer;
  position: relative; transition: background 0.3s; flex-shrink: 0;
  padding: 0;
}
.toggle-switch.on { background: var(--gold); }
.toggle-thumb {
  position: absolute; width: 20px; height: 20px; border-radius: 50%;
  background: white; top: 3px; left: 3px;
  transition: left 0.3s; box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}
.toggle-switch.on .toggle-thumb { left: 25px; }

/* Save Banner */
.save-banner {
  position: fixed; bottom: 24px; right: 24px;
  background: #15803d; color: white;
  padding: 14px 24px; border-radius: 12px;
  font-size: 14px; font-weight: 600;
  box-shadow: 0 4px 16px rgba(0,0,0,0.15); z-index: 9999;
}
.save-banner-enter-active, .save-banner-leave-active { transition: all 0.3s; }
.save-banner-enter-from, .save-banner-leave-to { opacity: 0; transform: translateY(20px); }
</style>
