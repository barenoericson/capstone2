<template>
  <div class="dashboard-layout">

    <!-- ═══════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════ -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link to="/dashboard" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/dashboard" exact class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </span>
          <span>Dashboard</span>
        </router-link>

        <div class="nav-group-label">Browse</div>
        <router-link to="/properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span>
          <span>Properties</span>
        </router-link>

        <router-link to="/saved-properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </span>
          <span>Saved</span>
          <span v-if="savedPropertiesCount > 0" class="nav-badge">{{ savedPropertiesCount }}</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Messages</span>
          <span v-if="unreadMessagesCount > 0" class="nav-badge">{{ unreadMessagesCount }}</span>
        </router-link>

        <router-link to="/viewings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <span>Viewings</span>
          <span v-if="viewingRequestsCount > 0" class="nav-badge">{{ viewingRequestsCount }}</span>
        </router-link>

        <div class="nav-group-label">Tools</div>
        <router-link to="/documents" class="nav-item" @click="sidebarOpen = false">
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
            <span class="nav-user-role">{{ capitalizeRole(userRole) }}</span>
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

    <!-- ═══════════════════════════════════
         MAIN WRAPPER
    ═══════════════════════════════════ -->
    <div class="main-wrapper">

      <!-- ── TOPBAR ── -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="sidebarOpen = !sidebarOpen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <span class="topbar-title">Dashboard</span>
        </div>
        <div class="topbar-right">
          <div class="notif-wrap" v-click-outside="() => showNotifBell = false">
            <button class="tb-icon-btn" @click="toggleNotifBell">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
              <span v-if="totalNotifCount > 0" class="tb-badge">{{ totalNotifCount > 99 ? '99+' : totalNotifCount }}</span>
            </button>
            <transition name="drop">
              <div v-if="showNotifBell" class="notif-panel">
                <div class="np-head">
                  <span>Notifications</span>
                  <button v-if="notifications.length > 0" class="np-read-all" @click="markAllNotificationsRead">Mark all read</button>
                </div>
                <div v-if="notifications.length === 0 && recentConversations.length === 0" class="np-empty">
                  <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#D6D3D1" stroke-width="1.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>
                  <p>No notifications yet</p>
                </div>
                <div v-for="n in notifications.slice(0,6)" :key="'n'+n.id"
                  class="np-item" :class="{'np-unread': !n.is_read}" @click="handleNotifClick(n)">
                  <div class="np-dot-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg></div>
                  <div class="np-body"><p class="np-title">{{ n.title }}</p><p class="np-msg">{{ n.message }}</p><p class="np-time">{{ n.relative_time }}</p></div>
                  <span v-if="!n.is_read" class="np-unread-dot"></span>
                </div>
                <template v-if="recentConversations.length > 0">
                  <div class="np-head np-sub-head"><span>Messages</span><router-link to="/conversations" @click="showNotifBell=false" class="np-link">View All</router-link></div>
                  <div v-for="c in recentConversations.slice(0,3)" :key="'c'+convOtherId(c)" class="np-item" @click="goToConversation(c)">
                    <div class="np-av">{{ convOtherName(c).charAt(0).toUpperCase() }}</div>
                    <div class="np-body"><p class="np-title">{{ convOtherName(c) }}</p><p class="np-msg">{{ c.last_message?.message_content || 'Attachment' }}</p></div>
                    <span v-if="c.unread_count > 0" class="np-cnt">{{ c.unread_count }}</span>
                  </div>
                </template>
              </div>
            </transition>
          </div>
          <router-link v-if="userRole === 'buyer'" to="/become-agent" class="tb-cta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Become an Agent</span>
          </router-link>
        </div>
      </header>

      <!-- ═══════════════════════════════════
           CONTENT
      ═══════════════════════════════════ -->
      <main class="content-area">
        <div class="content-inner">

          <!-- ①  GREETING BOX ────────────── -->
          <div class="hero-card">
            <div class="hero-deco" aria-hidden="true">
              <div class="deco-grid-pattern"></div>
              <div class="deco-ring deco-r1"></div>
              <div class="deco-ring deco-r2"></div>
              <div class="deco-ring deco-r3"></div>
              <div class="deco-dot dd1"></div>
              <div class="deco-dot dd2"></div>
              <div class="deco-dot dd3"></div>
            </div>
            <div class="hero-body">
              <div class="hero-left">
                <p class="hero-greeting">{{ greeting }},</p>
                <h1 class="hero-name">{{ firstName }} <span class="hero-wave">👋</span></h1>
                <p class="hero-sub">{{ heroSubtitle }}</p>
                <div class="hero-btns">
                  <router-link to="/properties" class="hero-btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Browse Properties
                  </router-link>
                  <router-link v-if="profileCompletion < 100" to="/profile" class="hero-btn-ghost">
                    Complete Profile &rarr;
                  </router-link>
                  <router-link v-else to="/viewings" class="hero-btn-ghost">
                    My Viewings &rarr;
                  </router-link>
                </div>
              </div>
              <!-- Property Teaser Card — glassmorphism -->
              <div v-if="teaserProperty" class="prop-teaser" @click="$router.push('/property/' + teaserProperty.id)">
                <div class="pt-img-wrap">
                  <img v-if="getCoverPhoto(teaserProperty)" :src="getCoverPhoto(teaserProperty)" :alt="teaserProperty.title" class="pt-img" />
                  <div v-else class="pt-img pt-img-placeholder">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                  </div>
                  <span class="pt-badge"><span class="pt-pulse-dot"></span>NEW</span>
                </div>
                <div class="pt-body">
                  <p class="pt-price">&#8369;{{ formatPrice(teaserProperty.price) }}</p>
                  <p class="pt-title-text">{{ teaserProperty.title }}</p>
                  <p class="pt-loc-text">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ teaserProperty.city }}, {{ teaserProperty.province }}
                  </p>
                </div>
              </div>
            </div>
            <!-- Profile completion bar inside hero -->
            <div v-if="profileCompletion < 100" class="hero-progress">
              <div class="hp-labels">
                <span>Profile completion</span>
                <span class="hp-pct">{{ profileCompletion }}%</span>
              </div>
              <div class="hp-track"><div class="hp-fill" :style="{ width: profileCompletion + '%' }"></div></div>
            </div>
          </div>

          <!-- ③  STATS ──────────────────── -->
          <div class="stats-row">
            <div class="stat-card" @click="$router.push('/properties')">
              <div class="stat-ic ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
              <div class="stat-info">
                <span class="stat-n">{{ displayStats.properties }}</span>
                <span class="stat-l">Available Listings</span>
              </div>
              <svg class="stat-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div class="stat-card" @click="$router.push('/saved-properties')">
              <div class="stat-ic ic-rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
              <div class="stat-info">
                <span class="stat-n">{{ displayStats.saved }}</span>
                <span class="stat-l">Saved Properties</span>
              </div>
              <svg class="stat-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div class="stat-card" :class="{ urgent: viewingRequestsCount > 0 }" @click="$router.push('/viewings')">
              <div class="stat-ic ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
              <div class="stat-info">
                <span class="stat-n">{{ displayStats.viewings }}</span>
                <span class="stat-l">Pending Viewings</span>
              </div>
              <svg class="stat-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div class="stat-card" :class="{ urgent: unreadMessagesCount > 0 }" @click="$router.push('/conversations')">
              <div class="stat-ic ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
              <div class="stat-info">
                <span class="stat-n">{{ displayStats.messages }}</span>
                <span class="stat-l">Unread Messages</span>
              </div>
              <svg class="stat-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
          </div>

          <!-- ④  AGENT BANNER ──────────── -->
          <transition name="slide-up">
            <div v-if="userRole === 'buyer' && !agentBannerDismissed" class="agent-banner">
              <div class="ab-left">
                <div class="ab-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div>
                  <strong>Unlock Agent Tools</strong>
                  <p>List properties, manage clients, and earn commissions on RealtyLinkPH.</p>
                </div>
              </div>
              <div class="ab-right">
                <router-link to="/become-agent" class="ab-cta">Become an Agent →</router-link>
                <button class="ab-x" @click="dismissAgentBanner">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
              </div>
            </div>
          </transition>

          <!-- ⑤  QUICK ACTIONS ─────────── -->
          <div class="sh"><h2>Quick Actions</h2></div>
          <div class="qa-grid">
            <router-link to="/properties" class="qa-card">
              <div class="qa-icon qa-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
              <div class="qa-text">
                <span class="qa-title">Browse Properties</span>
                <span class="qa-sub">{{ properties.length }} listings available</span>
              </div>
              <svg class="qa-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
            </router-link>
            <router-link to="/saved-properties" class="qa-card">
              <div class="qa-icon qa-rose"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
              <div class="qa-text">
                <span class="qa-title">My Saved</span>
                <span class="qa-sub">{{ savedPropertiesCount }} saved propert{{ savedPropertiesCount === 1 ? 'y' : 'ies' }}</span>
              </div>
              <svg class="qa-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
            </router-link>
            <router-link to="/viewings" class="qa-card">
              <div class="qa-icon qa-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
              <div class="qa-text">
                <span class="qa-title">Scheduled Viewings</span>
                <span class="qa-sub">{{ viewingRequestsCount > 0 ? viewingRequestsCount + ' pending' : 'No pending viewings' }}</span>
              </div>
              <svg class="qa-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
            </router-link>
            <router-link to="/conversations" class="qa-card">
              <div class="qa-icon qa-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
              <div class="qa-text">
                <span class="qa-title">Messages</span>
                <span class="qa-sub">{{ unreadMessagesCount > 0 ? unreadMessagesCount + ' unread' : 'No new messages' }}</span>
              </div>
              <svg class="qa-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
            </router-link>
          </div>

          <!-- ⑥  FEATURED PROPERTIES ───── -->
          <div class="sh">
            <h2>Featured Properties</h2>
            <router-link to="/properties" class="sh-link">View all &rarr;</router-link>
          </div>

          <!-- Skeletons -->
          <div v-if="loadingProperties" class="prop-grid">
            <div v-for="i in 6" :key="'sk'+i" class="skel-card">
              <div class="skel-img"></div>
              <div class="skel-body">
                <div class="skel-line sl-sm"></div>
                <div class="skel-line sl-lg"></div>
                <div class="skel-line sl-md"></div>
              </div>
            </div>
          </div>

          <!-- Empty -->
          <div v-else-if="previewProperties.length === 0" class="empty-box">
            <div class="empty-ic">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <h3>No properties yet</h3>
            <p>New listings will appear here soon.</p>
            <router-link to="/properties" class="empty-btn">Browse All</router-link>
          </div>

          <!-- Property Grid -->
          <div v-else class="prop-grid">
            <div v-for="(p, idx) in previewProperties" :key="p.id"
              class="prop-card" :style="{ animationDelay: (idx * 55) + 'ms' }"
              @click="$router.push('/property/' + p.id)">
              <div class="pc-img">
                <img v-if="getCoverPhoto(p)" :src="getCoverPhoto(p)" :alt="p.title" loading="lazy"/>
                <div v-else class="pc-placeholder">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div class="pc-badges">
                  <span v-if="p.featured" class="pc-badge pb-gold">Featured</span>
                  <span v-else-if="isNewListing(p)" class="pc-badge pb-green">New</span>
                  <span v-if="has360Tour(p)" class="pc-badge pb-dark">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>360°
                  </span>
                </div>
                <button class="pc-save" :class="{ saved: savedPropertyIds.includes(p.id) }" @click.stop="toggleSave(p)">
                  <svg width="15" height="15" viewBox="0 0 24 24" :fill="savedPropertyIds.includes(p.id)?'currentColor':'none'" stroke="currentColor" stroke-width="2.2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>
                <span class="pc-price">&#8369;{{ formatPrice(p.price) }}</span>
              </div>
              <div class="pc-body">
                <div class="pc-row1">
                  <span class="pc-type">{{ formatPropertyType(p.property_type) }}</span>
                  <span class="pc-views"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>{{ p.view_count || 0 }}</span>
                </div>
                <h3 class="pc-title">{{ p.title }}</h3>
                <p class="pc-loc"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ p.city }}, {{ p.province }}</p>
                <div class="pc-specs">
                  <span v-if="p.bedrooms"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>{{ p.bedrooms }} bd</span>
                  <span v-if="p.bathrooms"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16a1 1 0 0 1 1 1v3H3v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v7"/></svg>{{ p.bathrooms }} ba</span>
                  <span v-if="p.lot_area"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18"/></svg>{{ p.lot_area }}m²</span>
                </div>
              </div>
            </div>
          </div>

          <!-- ⑦  WIDGETS ROW ─────────────── -->
          <div class="widgets-row">

            <!-- Upcoming Viewings -->
            <div class="widget-card">
              <div class="wc-head">
                <div class="wc-icon wi-gold"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <h3>Upcoming Viewings</h3>
                <router-link to="/viewings" class="wc-link">See all</router-link>
              </div>
              <div v-if="upcomingViewings.length === 0" class="wc-empty">
                <p>No upcoming viewings</p>
                <router-link to="/properties" class="wc-cta">Schedule one →</router-link>
              </div>
              <div v-else class="wc-list">
                <div v-for="v in upcomingViewings" :key="v.id" class="view-item">
                  <div class="vi-date">
                    <span class="vid-day">{{ formatViewingDay(v) }}</span>
                    <span class="vid-mon">{{ formatViewingMonth(v) }}</span>
                  </div>
                  <div class="vi-info">
                    <p class="vi-title">{{ v.property?.title || 'Property #' + v.property_id }}</p>
                    <p class="vi-time">{{ formatViewingTime(v) || 'Time TBD' }}</p>
                  </div>
                  <span class="vi-status" :class="'vs-' + v.status">{{ v.status }}</span>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="widget-card">
              <div class="wc-head">
                <div class="wc-icon wi-blue"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                <h3>Recent Activity</h3>
              </div>
              <div v-if="activityItems.length === 0" class="wc-empty">
                <p>No recent activity</p>
              </div>
              <div v-else class="activity-list">
                <div v-for="item in activityItems" :key="item.id"
                  class="act-item" :class="{ 'act-unread': !item.read }"
                  @click="handleNotifClick(item.notif)">
                  <div class="act-icon" :class="'ai-' + item.type">
                    <svg v-if="item.type === 'viewing_request'" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <svg v-else-if="item.type === 'message'" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <svg v-else-if="item.type === 'document'" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <svg v-else width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>
                  </div>
                  <div class="act-body">
                    <p class="act-title">{{ item.text }}</p>
                    <p class="act-time">{{ item.time }}</p>
                  </div>
                  <div v-if="!item.read" class="act-unread-dot"></div>
                  <svg class="act-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
              </div>
            </div>

            <!-- Promo / Tip card -->
            <div class="widget-card tip-card">
              <div class="tip-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              </div>
              <h3 class="tip-title">Buying Tip</h3>
              <p class="tip-body">Always schedule a property viewing before making an offer. Check for water pressure, natural lighting, and neighborhood safety.</p>
              <router-link to="/properties" class="tip-btn">Explore Properties</router-link>
            </div>
          </div>

          <!-- ⑧  SUGGESTED AGENTS ──────── -->
          <template v-if="suggestedAgents.length > 0">
            <div class="sh">
              <h2>Suggested Agents</h2>
              <router-link to="/properties" class="sh-link">Browse Agents &rarr;</router-link>
            </div>
            <div class="agents-grid">
              <div v-for="agent in suggestedAgents" :key="agent.id" class="agent-card">
                <div class="ag-photo">
                  <img v-if="getAgentPhoto(agent)" :src="getAgentPhoto(agent)" :alt="agent.name"/>
                  <span v-else>{{ agent.name.charAt(0).toUpperCase() }}</span>
                </div>
                <div class="ag-info">
                  <h4 class="ag-name">{{ agent.name }}</h4>
                  <p class="ag-role">Real Estate Agent</p>
                  <p class="ag-listings">{{ agent.listings_count || 0 }} active listings</p>
                </div>
                <router-link :to="'/agent/' + agent.id" class="ag-btn">View Profile</router-link>
              </div>
            </div>
          </template>

        </div><!-- /content-inner -->
      </main>

      <footer class="footer">
        <span>&copy; 2026 RealtyLinkPH. All rights reserved.</span>
        <span class="footer-links">
          <router-link to="/properties">Properties</router-link>
          <router-link to="/settings">Settings</router-link>
        </span>
      </footer>
    </div><!-- /main-wrapper -->

    <transition name="fade"><div v-if="errorMessage" class="toast toast-err">{{ errorMessage }}</div></transition>
    <transition name="fade"><div v-if="successMessage" class="toast toast-ok">{{ successMessage }}</div></transition>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      userName: '', userEmail: '', userRole: '', hasBio: false, userPhone: '', profilePhotoUrl: null,
      properties: [], loadingProperties: false,
      savedPropertiesCount: 0, savedPropertyIds: [],
      viewings: [], viewingRequestsCount: 0,
      suggestedAgents: [],
      recentConversations: [], unreadMessagesCount: 0,
      notifications: [], unreadNotificationsCount: 0,
      echoChannel: null, myId: null,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      showNotifBell: false,
      successMessage: '', errorMessage: '',
      sidebarOpen: false,
      agentBannerDismissed: localStorage.getItem('agentBannerDismissed') === 'true',
      displayStats: { properties: 0, saved: 0, viewings: 0, messages: 0 },
      // legacy compat
      activeCategory: 'all', activeFilter: 'all', sortBy: 'newest',
      showSortDropdown: false, searchLocation: '', searchPrice: '',
      sortOptions: [
        { key: 'newest', label: 'Newest' }, { key: 'price_low', label: 'Price: Low to High' },
        { key: 'price_high', label: 'Price: High to Low' }, { key: 'most_viewed', label: 'Most Viewed' },
      ],
    };
  },

  computed: {
    firstName() { return this.userName.split(' ')[0] || 'there'; },
    greeting() {
      const h = new Date().getHours();
      if (h < 12) return 'Good morning';
      if (h < 17) return 'Good afternoon';
      return 'Good evening';
    },
    todayDate() {
      return new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    },
    heroSubtitle() {
      if (this.viewingRequestsCount > 0)
        return `You have ${this.viewingRequestsCount} pending viewing${this.viewingRequestsCount > 1 ? 's' : ''} — review them below.`;
      if (this.unreadMessagesCount > 0)
        return `${this.unreadMessagesCount} unread message${this.unreadMessagesCount > 1 ? 's' : ''} from agents.`;
      if (this.savedPropertiesCount > 0)
        return `You've saved ${this.savedPropertiesCount} propert${this.savedPropertiesCount > 1 ? 'ies' : 'y'}. Keep exploring!`;
      return 'Find your perfect property in the Philippines.';
    },
    profileCompletion() {
      let s = 25;
      if (this.profilePhotoUrl) s += 25;
      if (this.hasBio) s += 25;
      if (this.userPhone) s += 25;
      return s;
    },
    journeySteps() {
      return [
        { key: 'account',  label: 'Account Created',   done: true },
        { key: 'profile',  label: 'Profile Completed', done: this.profileCompletion >= 100 },
        { key: 'browse',   label: 'Browse Properties', done: this.properties.length > 0 },
        { key: 'viewing',  label: 'Schedule Viewing',  done: this.viewings.length > 0 },
        { key: 'agent',    label: 'Contact Agent',     done: this.recentConversations.length > 0 },
        { key: 'sign',     label: 'Sign Documents',    done: false },
      ];
    },
    currentJourneyStep() {
      const i = this.journeySteps.findIndex(s => !s.done);
      return i === -1 ? this.journeySteps.length : i;
    },
    journeyProgress() {
      return Math.round((this.journeySteps.filter(s => s.done).length / this.journeySteps.length) * 100);
    },
    upcomingViewings() {
      return this.viewings
        .filter(v => ['requested', 'confirmed', 'approved'].includes(v.status))
        .sort((a, b) => new Date(a.preferred_date || a.scheduled_at || 0) - new Date(b.preferred_date || b.scheduled_at || 0))
        .slice(0, 3);
    },
    activityItems() {
      return this.notifications.slice(0, 6).map(n => ({
        id: n.id, type: n.notification_type || 'general',
        text: n.title, time: n.relative_time, read: n.is_read, notif: n,
      }));
    },
    previewProperties() {
      const feat = this.properties.filter(p => p.featured);
      if (feat.length >= 3) return feat.slice(0, 6);
      const rest = this.properties.filter(p => !p.featured).sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      return [...feat, ...rest].slice(0, 6);
    },
    teaserProperty() {
      return this.properties.find(p => p.featured) || this.properties[0] || null;
    },
    totalNotifCount() { return this.unreadNotificationsCount + this.unreadMessagesCount; },
    currentSortLabel() { return this.sortOptions.find(s => s.key === this.sortBy)?.label ?? 'Newest'; },
    featuredProperties() { return this.properties.filter(p => p.featured); },
    displayedProperties() {
      let list = [...this.properties];
      switch (this.sortBy) {
        case 'price_low': list.sort((a,b) => a.price - b.price); break;
        case 'price_high': list.sort((a,b) => b.price - a.price); break;
        case 'most_viewed': list.sort((a,b) => (b.view_count||0) - (a.view_count||0)); break;
        default: list.sort((a,b) => new Date(b.created_at) - new Date(a.created_at));
      }
      return list;
    },
  },

  methods: {
    navigateToStep(step) {
      const routes = {
        account: '/profile', profile: '/profile', browse: '/properties',
        viewing: '/viewings', agent: '/conversations', sign: '/documents',
      };
      if (routes[step.key]) this.$router.push(routes[step.key]);
    },
    getStepTooltip(step) {
      if (step.key === 'viewing')
        return this.viewingRequestsCount > 0
          ? `You have ${this.viewingRequestsCount} pending viewing${this.viewingRequestsCount > 1 ? 's' : ''} — click to review`
          : 'No pending viewings — schedule one';
      if (step.key === 'agent')
        return this.unreadMessagesCount > 0
          ? `${this.unreadMessagesCount} unread message${this.unreadMessagesCount > 1 ? 's' : ''} — click to view`
          : 'No unread messages — message an agent';
      if (step.key === 'profile') return step.done ? 'Profile complete!' : 'Complete your profile to unlock all features';
      if (step.key === 'browse') return `${this.properties.length} properties available — click to browse`;
      if (step.key === 'sign') return 'Sign documents digitally — click to open Documents';
      return step.label;
    },
    capitalizeRole(r) { return r ? r.charAt(0).toUpperCase() + r.slice(1) : ''; },
    formatPrice(p) { return new Intl.NumberFormat('en-PH').format(p); },
    formatPropertyType(t) {
      return { house:'House', condo:'Condo', apartment:'Apartment', townhouse:'Townhouse', commercial:'Commercial', land:'Land' }[t] ?? t;
    },
    getCoverPhoto(prop) {
      if (!prop.photos?.length) return null;
      return (prop.photos.find(p => p.is_cover) ?? prop.photos[0])?.photo_url ?? null;
    },
    isNewListing(p) { return new Date(p.created_at) >= Date.now() - 7 * 86400000; },
    has360Tour(p)   { return !!p.virtual_tour_url; },
    getAgentPhoto(agent) {
      return agent.profile_photo_path ? `${window.__API_URL__}/storage/${agent.profile_photo_path}` : null;
    },
    formatViewingDay(v) {
      const d = v.preferred_date || v.scheduled_at;
      return d ? new Date(d).getDate() : '—';
    },
    formatViewingMonth(v) {
      const d = v.preferred_date || v.scheduled_at;
      return d ? new Date(d).toLocaleString('en-US', { month: 'short' }) : '';
    },
    formatViewingTime(v) {
      const t = v.preferred_time || v.scheduled_time;
      return t ? t.substring(0, 5) : '';
    },
    dismissAgentBanner() {
      this.agentBannerDismissed = true;
      localStorage.setItem('agentBannerDismissed', 'true');
    },
    animateCount(key, target) {
      const dur = 900; const start = performance.now();
      const step = now => {
        const t = Math.min((now - start) / dur, 1);
        this.displayStats[key] = Math.round(target * (1 - Math.pow(1 - t, 3)));
        if (t < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    },
    triggerCountUps() {
      this.animateCount('properties', this.properties.length);
      this.animateCount('saved', this.savedPropertiesCount);
      this.animateCount('viewings', this.viewingRequestsCount);
      this.animateCount('messages', this.unreadMessagesCount);
    },
    async loadProperties() {
      try {
        this.loadingProperties = true;
        const res = await fetch(window.__API_URL__ + '/api/properties', { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` } });
        const data = await res.json();
        if (data.success) this.properties = data.data.data || data.data || [];
        else this.showError(data.message || 'Failed to load properties');
      } catch { this.showError('Failed to load properties'); }
      finally { this.loadingProperties = false; }
    },
    async loadSavedIds() {
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/saved-properties`, { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) {
          const list = data.data?.data || data.data || [];
          this.savedPropertyIds = list.map(p => p.id);
          this.savedPropertiesCount = list.length;
        }
      } catch { /* silent */ }
    },
    async toggleSave(prop) {
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/properties/${prop.id}/save`, { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) {
          if (this.savedPropertyIds.includes(prop.id)) {
            this.savedPropertyIds = this.savedPropertyIds.filter(id => id !== prop.id);
            this.savedPropertiesCount = Math.max(0, this.savedPropertiesCount - 1);
            this.showSuccess('Removed from saved');
          } else {
            this.savedPropertyIds.push(prop.id);
            this.savedPropertiesCount++;
            this.showSuccess('Property saved!');
          }
        }
      } catch { this.showError('Could not save property'); }
    },
    loadProfilePhoto() {
      try {
        const u = JSON.parse(localStorage.getItem('user') || '{}');
        if (u.profile_photo_path) this.profilePhotoUrl = `${window.__API_URL__}/storage/${u.profile_photo_path}`;
      } catch { /* silent */ }
    },
    loadUserData() {
      try {
        const u = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName = u.name || 'User';
        this.userEmail = u.email || '';
        this.userRole = u.role || 'buyer';
        this.hasBio = !!(u.bio || u.about);
        this.userPhone = u.phone || '';
      } catch { /* silent */ }
    },
    async logout() {
      try { await fetch(window.__API_URL__ + '/api/auth/logout', { method: 'POST', headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` } }); } catch { /* silent */ }
      localStorage.removeItem('auth_token'); localStorage.removeItem('user');
      this.$router.push('/');
    },
    showError(msg)   { this.errorMessage   = msg; setTimeout(() => { this.errorMessage   = ''; }, 3200); },
    showSuccess(msg) { this.successMessage = msg; setTimeout(() => { this.successMessage = ''; }, 3200); },
    convOtherName(c) { return c.agent?.name ?? c.buyer?.name ?? 'Unknown'; },
    convOtherId(c)   { return c.agent?.id   ?? c.buyer?.id   ?? 0; },
    toggleNotifBell() {
      this.showNotifBell = !this.showNotifBell;
      if (this.showNotifBell) { this.loadNotifications(); this.markAllNotificationsRead(); }
    },
    async loadNotifications() {
      try {
        const res = await fetch(`${this.apiUrl}/api/notifications`, { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) { this.notifications = data.notifications || []; this.unreadNotificationsCount = this.notifications.filter(n => !n.is_read).length; }
      } catch { /* silent */ }
    },
    async markAllNotificationsRead() {
      try {
        await fetch(`${this.apiUrl}/api/notifications/mark-all-read`, { method: 'PUT', headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
        this.notifications.forEach(n => n.is_read = true); this.unreadNotificationsCount = 0;
      } catch { /* silent */ }
    },
    async handleNotifClick(notif) {
      if (!notif?.id) return;
      if (!notif.is_read) {
        try {
          await fetch(`${this.apiUrl}/api/notifications/${notif.id}/read`, { method: 'PUT', headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
          notif.is_read = true; this.unreadNotificationsCount = Math.max(0, this.unreadNotificationsCount - 1);
        } catch { /* silent */ }
      }
      this.showNotifBell = false;
      const routes = { viewing_request: '/viewings', message: '/conversations', document: '/documents' };
      if (routes[notif.notification_type]) this.$router.push(routes[notif.notification_type]);
    },
    goToConversation(conv) { this.showNotifBell = false; this.$router.push(`/conversations/${this.convOtherId(conv)}`); },
    async loadConversations() {
      try {
        const ep = this.userRole === 'agent' ? `${this.apiUrl}/api/agent/conversations` : `${this.apiUrl}/api/buyer/conversations`;
        const res = await fetch(ep, { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) { this.recentConversations = data.data; this.unreadMessagesCount = data.data.reduce((s, c) => s + (c.unread_count || 0), 0); }
      } catch { /* silent */ }
    },
    async loadViewingCount() {
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/viewings`, { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success) {
          this.viewings = data.viewings || data.data || [];
          this.viewingRequestsCount = this.viewings.filter(v => v.status === 'requested').length;
        }
      } catch { /* silent */ }
    },
    async loadSuggestedAgents() {
      try {
        const res = await fetch(`${this.apiUrl}/api/agents`, { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}`, Accept: 'application/json' } });
        const data = await res.json();
        if (data.success || data.data) this.suggestedAgents = (data.data?.data || data.data || []).slice(0, 4);
      } catch { /* non-critical — section hidden if empty */ }
    },
    subscribeToEcho() {
      if (!window.Echo || !this.myId) return;
      try { window.Echo.connector.pusher.config.auth.headers.Authorization = 'Bearer ' + localStorage.getItem('auth_token'); } catch { /* ignore */ }
      this.echoChannel = window.Echo.private(`chat.${this.myId}`).listen('.MessageSent', () => this.loadConversations());
      try {
        window.Echo.private(`notifications.${this.myId}`)
          .listen('.NotificationSent', d => { this.notifications.unshift(d); this.unreadNotificationsCount++; })
          .listen('.ViewingStatusChanged', () => this.loadViewingCount());
      } catch { /* ignore */ }
    },
    leaveEcho() {
      if (this.echoChannel) { window.Echo.leave(`chat.${this.myId}`); this.echoChannel = null; }
      try { if (window.Echo && this.myId) window.Echo.leave(`notifications.${this.myId}`); } catch { /* ignore */ }
    },
  },

  watch: {
    properties(v)            { this.animateCount('properties', v.length); },
    savedPropertiesCount(v)  { this.animateCount('saved',      v); },
    viewingRequestsCount(v)  { this.animateCount('viewings',   v); },
    unreadMessagesCount(v)   { this.animateCount('messages',   v); },
  },

  async mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) { this.$router.push('/'); return; }
    this.myId = JSON.parse(localStorage.getItem('user') || '{}').id || null;
    this.loadUserData(); this.loadProfilePhoto();
    await Promise.all([
      this.loadProperties(), this.loadSavedIds(),
      this.loadConversations(), this.loadViewingCount(),
      this.loadNotifications(), this.loadSuggestedAgents(),
    ]).catch(() => {});
    this.triggerCountUps();
    this.subscribeToEcho();
    this.photoWatcher = setInterval(() => {
      const u = JSON.parse(localStorage.getItem('user') || '{}');
      if (u.profile_photo_path && u.profile_photo_path !== this.profilePhotoUrl?.split('/storage/')[1]) this.loadProfilePhoto();
    }, 2000);
  },
  beforeUnmount() { this.leaveEcho(); if (this.photoWatcher) clearInterval(this.photoWatcher); },
};
</script>

<style scoped>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

/* ── TOKENS ── defined on wrapper so scoped CSS variables actually work */
.dashboard-layout {
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
  min-height: 100vh; display: flex; font-family: var(--fb); -webkit-font-smoothing: antialiased; background: var(--bg);
}

/* ══════════════════════════════════
   SIDEBAR
══════════════════════════════════ */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--navy); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; }
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

/* ══════════════════════════════════
   MAIN
══════════════════════════════════ */
.main-wrapper { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* ── TOPBAR ── */
.topbar {
  background: var(--white); position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 28px; height: var(--th); border-bottom: 1px solid var(--s200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.topbar-left { display: flex; align-items: center; gap: 10px; }
.hamburger { display: none; width: 34px; height: 34px; border-radius: 7px; border: 1px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s600); align-items: center; justify-content: center; }
.topbar-title { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--navy); letter-spacing: -0.3px; }
.topbar-right { display: flex; align-items: center; gap: 8px; }
.tb-icon-btn { width: 34px; height: 34px; border-radius: 50%; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--s500); position: relative; transition: all 0.18s; }
.tb-icon-btn:hover { background: var(--s100); color: var(--navy); }
.tb-badge {
  position: absolute; top: -2px; right: -2px; background: #ef4444; color: #fff;
  border-radius: 99px; padding: 1px 4.5px; font-size: 9.5px; font-weight: 700;
  min-width: 16px; text-align: center; border: 2px solid var(--white);
  animation: pop .3s cubic-bezier(.34,1.56,.64,1);
}
@keyframes pop { from { transform: scale(0); } to { transform: scale(1); } }
.tb-cta {
  display: flex; align-items: center; gap: 6px; padding: 7px 15px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: var(--white); border-radius: 8px; font-size: 12.5px; font-weight: 600;
  text-decoration: none; transition: all .22s; white-space: nowrap;
  box-shadow: 0 2px 8px rgba(216,155,15,0.30);
}
.tb-cta:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(216,155,15,0.40); }

/* ── NOTIF PANEL ── */
.notif-wrap { position: relative; }
.notif-panel {
  position: absolute; top: calc(100% + 8px); right: 0; width: 330px;
  background: var(--white); border: 1px solid var(--s100); border-radius: 14px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.12); z-index: 200; overflow: hidden; max-height: 420px; overflow-y: auto;
}
.np-head { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; border-bottom: 1px solid var(--s100); font-weight: 700; font-size: 12.5px; color: var(--s900); }
.np-sub-head { border-top: 1px solid var(--s100); margin-top: 4px; }
.np-read-all { background: none; border: none; cursor: pointer; font-size: 11px; color: var(--gold); font-weight: 600; }
.np-link { font-size: 11.5px; color: var(--gold); text-decoration: none; font-weight: 600; }
.np-empty { padding: 26px 16px; text-align: center; color: var(--s400); font-size: 13px; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.np-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; cursor: pointer; transition: background .15s; }
.np-item:hover { background: rgba(216,155,15,0.04); }
.np-unread { background: rgba(216,155,15,0.03); }
.np-dot-icon { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: rgba(216,155,15,0.08); color: var(--gold); display: flex; align-items: center; justify-content: center; }
.np-av { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; background: linear-gradient(135deg, var(--navy), var(--navy3)); color: var(--gold2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; }
.np-body { flex: 1; min-width: 0; }
.np-title { font-size: 12.5px; font-weight: 600; color: var(--s900); margin: 0; }
.np-msg   { font-size: 11.5px; color: var(--s500); margin: 1px 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.np-time  { font-size: 10.5px; color: var(--s400); margin: 2px 0 0; }
.np-unread-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--gold); flex-shrink: 0; }
.np-cnt { background: rgba(216,155,15,0.12); color: var(--gold); border-radius: 99px; padding: 1px 7px; font-size: 10.5px; font-weight: 700; flex-shrink: 0; }

/* ══════════════════════════════════
   CONTENT
══════════════════════════════════ */
.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 1080px; margin: 0 auto; padding: 28px 30px 48px; }

/* ── ① HERO ── */
.hero-card {
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy3) 100%);
  border-radius: 18px; overflow: hidden; position: relative;
  margin-bottom: 20px;
  box-shadow: 0 8px 32px rgba(11,28,57,0.28);
}
/* decorative background */
.hero-deco { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
/* Subtle floor-plan grid texture */
.deco-grid-pattern {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(216,155,15,0.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(216,155,15,0.05) 1px, transparent 1px);
  background-size: 32px 32px;
  opacity: 0.6;
}
.deco-ring { position: absolute; border-radius: 50%; border: 1px solid rgba(216,155,15,0.12); }
.deco-r1 { width: 300px; height: 300px; top: -80px; right: 60px; }
.deco-r2 { width: 200px; height: 200px; top: -40px; right: 110px; border-style: dashed; }
.deco-r3 { width: 120px; height: 120px; top: 0; right: 155px; }
.deco-dot { position: absolute; border-radius: 50%; background: rgba(216,155,15,0.20); }
.dd1 { width: 6px; height: 6px; top: 30px; right: 240px; }
.dd2 { width: 4px; height: 4px; top: 80px; right: 300px; }
.dd3 { width: 8px; height: 8px; bottom: 40px; right: 180px; animation: float 3s ease-in-out infinite; }
@keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

.hero-body { display: flex; align-items: center; justify-content: space-between; padding: 34px 36px 28px; gap: 20px; position: relative; }
.hero-left { flex: 1; min-width: 0; }
.hero-greeting { font-family: var(--fd); font-size: 13px; color: rgba(255,255,255,0.72); font-weight: 500; margin-bottom: 4px; letter-spacing: 0.2px; }
.hero-name { font-family: var(--fd); font-size: 28px; font-weight: 800; color: #fff; line-height: 1.15; margin-bottom: 8px; }
.hero-wave { font-style: normal; display: inline-block; animation: wave 2s ease-in-out infinite; transform-origin: 70% 70%; }
@keyframes wave { 0%,100% { transform: rotate(0deg); } 20%,60% { transform: rotate(-10deg); } 40%,80% { transform: rotate(10deg); } }
.hero-sub { font-size: 14px; color: rgba(255,255,255,0.60); margin-bottom: 22px; line-height: 1.5; max-width: 420px; }
.hero-btns { display: flex; gap: 10px; flex-wrap: wrap; }
.hero-btn-primary {
  display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: #fff; border-radius: 9px; font-size: 13.5px; font-weight: 700;
  text-decoration: none; transition: all .22s;
  box-shadow: 0 3px 12px rgba(216,155,15,0.35);
}
.hero-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(216,155,15,0.45); }
.hero-btn-ghost {
  display: inline-flex; align-items: center; padding: 10px 18px;
  border: 1.5px solid rgba(216,155,15,0.35); color: rgba(255,255,255,0.75);
  border-radius: 9px; font-size: 13.5px; font-weight: 600; text-decoration: none;
  transition: all .22s; background: rgba(255,255,255,0.04);
}
.hero-btn-ghost:hover { border-color: var(--gold); color: var(--gold2); background: rgba(216,155,15,0.08); }
/* Hero quick stats (replaces decorative SVG) */
.hero-quick-stats {
  display: flex; align-items: center; gap: 0;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(216,155,15,0.20);
  border-radius: 14px; overflow: hidden; flex-shrink: 0;
}
.hqs-item {
  display: flex; flex-direction: column; align-items: center; padding: 16px 22px;
  cursor: pointer; transition: background 0.2s;
  gap: 3px;
}
.hqs-item:hover { background: rgba(216,155,15,0.10); }
.hqs-val {
  font-family: var(--fd); font-size: 26px; font-weight: 800; color: var(--gold2);
  line-height: 1; letter-spacing: -0.5px;
}
.hqs-lbl {
  font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.40);
  text-transform: uppercase; letter-spacing: 1px; white-space: nowrap;
}
.hqs-divider { width: 1px; height: 48px; background: rgba(216,155,15,0.18); flex-shrink: 0; }

/* ── PROP TEASER ── glassmorphism card (greeting box right side) */
.prop-teaser {
  background: rgba(255,255,255,0.10);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.18);
  border-radius: 16px; padding: 12px;
  min-width: 220px; max-width: 255px; flex-shrink: 0;
  cursor: pointer; transition: transform 0.22s, box-shadow 0.22s;
}
.prop-teaser:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,0.28); }
.pt-img-wrap {
  position: relative; border-radius: 10px; overflow: hidden;
  height: 112px; margin-bottom: 10px; background: rgba(0,0,0,0.22);
}
.pt-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.pt-img-placeholder { display: flex; align-items: center; justify-content: center; }
.pt-badge {
  position: absolute; top: 8px; right: 8px;
  display: flex; align-items: center; gap: 5px;
  background: rgba(216,155,15,0.90); color: #fff;
  font-size: 9.5px; font-weight: 800; letter-spacing: 1px;
  padding: 3px 9px; border-radius: 99px; backdrop-filter: blur(4px);
}
.pt-pulse-dot {
  width: 5px; height: 5px; border-radius: 50%; background: #fff;
  animation: pt-pulse 1.8s ease-in-out infinite;
}
@keyframes pt-pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.35; transform: scale(1.7); }
}
.pt-body { padding: 0 2px; }
.pt-price {
  font-family: var(--fd); font-size: 16px; font-weight: 800;
  color: var(--gold2); margin-bottom: 3px; line-height: 1.2;
}
.pt-title-text {
  font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.82);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px;
}
.pt-loc-text {
  display: flex; align-items: center; gap: 4px;
  font-size: 10.5px; color: rgba(255,255,255,0.48);
}

/* Profile bar inside hero */
.hero-progress { border-top: 1px solid rgba(255,255,255,0.08); padding: 14px 36px; position: relative; }
.hp-labels { display: flex; justify-content: space-between; margin-bottom: 7px; }
.hp-labels span { font-size: 11.5px; color: rgba(255,255,255,0.50); font-weight: 500; }
.hp-pct { color: var(--gold2) !important; font-weight: 700 !important; }
.hp-track { height: 5px; background: rgba(255,255,255,0.10); border-radius: 99px; overflow: hidden; }
.hp-fill { height: 100%; background: linear-gradient(90deg, var(--gold), var(--gold2)); border-radius: 99px; transition: width 1.2s cubic-bezier(.4,0,.2,1); }

/* ── ② JOURNEY ── */
.journey-card {
  background: var(--white); border-radius: 16px; padding: 20px 24px;
  margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.journey-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.journey-title { font-family: var(--fd); font-size: 15px; font-weight: 700; color: var(--navy); }
.journey-sub   { font-size: 12px; color: var(--s400); margin-top: 2px; }
.journey-pct-badge {
  padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 700;
  color: var(--gold3); background: rgba(216,155,15,0.10);
  border: 1px solid rgba(216,155,15,0.20);
}
.journey-track {
  display: flex; align-items: flex-start; overflow-x: auto; scrollbar-width: none; gap: 0;
}
.journey-track::-webkit-scrollbar { display: none; }
.journey-step {
  flex: 1; display: flex; flex-direction: column; align-items: center;
  gap: 8px; position: relative; min-width: 80px;
  cursor: pointer; transition: transform 0.18s;
}
.journey-step:hover { transform: translateY(-2px); }
.journey-step:hover .step-node { box-shadow: 0 4px 16px rgba(216,155,15,0.30); }
/* connecting line to next step */
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

/* ── ③ STATS ── */
.stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
.stat-card {
  background: var(--white); border-radius: 14px; padding: 18px 18px;
  display: flex; align-items: center; gap: 14px; cursor: pointer;
  transition: all .22s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  border: 1.5px solid transparent;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.09); border-color: var(--s200); }
.stat-card.urgent { border-color: rgba(216,155,15,0.25); animation: pulse-gold 2.5s ease-in-out infinite; }
@keyframes pulse-gold {
  0%,100% { box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
  50%      { box-shadow: 0 0 0 5px rgba(216,155,15,0.10), 0 1px 3px rgba(0,0,0,0.05); }
}
.stat-ic { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-ic svg { width: 21px; height: 21px; }
.ic-blue  { background: rgba(59,130,246,0.08);  color: #3b82f6; }
.ic-rose  { background: rgba(244,63,94,0.08);   color: #f43f5e; }
.ic-gold  { background: rgba(216,155,15,0.10);  color: var(--gold); }
.ic-green { background: rgba(22,163,74,0.08);   color: #16a34a; }
.stat-info { flex: 1; min-width: 0; }
.stat-n { display: block; font-family: var(--fd); font-size: 26px; font-weight: 800; color: var(--navy); line-height: 1.1; }
.stat-l { display: block; font-size: 11.5px; color: var(--s400); font-weight: 500; margin-top: 1px; white-space: nowrap; }
.stat-arr { width: 16px; height: 16px; color: var(--s300); flex-shrink: 0; transition: color .18s; }
.stat-card:hover .stat-arr { color: var(--gold); }

/* ── ④ AGENT BANNER ── */
.agent-banner {
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
  background: var(--white); border-radius: 14px; padding: 16px 20px;
  margin-bottom: 20px; border-left: 3px solid var(--gold);
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.ab-left { display: flex; align-items: flex-start; gap: 13px; flex: 1; }
.ab-icon { width: 38px; height: 38px; border-radius: 10px; background: rgba(216,155,15,0.10); color: var(--gold); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
.ab-left strong { display: block; font-size: 13.5px; font-weight: 700; color: var(--navy); margin-bottom: 2px; }
.ab-left p { font-size: 12.5px; color: var(--s500); margin: 0; line-height: 1.45; }
.ab-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.ab-cta { padding: 8px 16px; background: linear-gradient(135deg, var(--gold), var(--gold3)); color: #fff; border-radius: 8px; font-size: 12.5px; font-weight: 700; text-decoration: none; white-space: nowrap; transition: all .22s; box-shadow: 0 2px 8px rgba(216,155,15,0.28); }
.ab-cta:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(216,155,15,0.38); }
.ab-x { width: 28px; height: 28px; border-radius: 7px; border: 1.5px solid var(--s200); background: var(--white); cursor: pointer; color: var(--s400); display: flex; align-items: center; justify-content: center; transition: all .18s; }
.ab-x:hover { color: var(--s700); border-color: var(--s300); }

/* ── ⑤ QUICK ACTIONS ── */
.sh { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; margin-top: 4px; }
.sh h2 { font-family: var(--fd); font-size: 15.5px; font-weight: 700; color: var(--navy); }
.sh-link { font-size: 12.5px; color: var(--gold); text-decoration: none; font-weight: 600; transition: color .18s; }
.sh-link:hover { color: var(--gold3); }
.qa-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; margin-bottom: 28px; }
.qa-card {
  display: flex; align-items: center; gap: 13px; padding: 15px 17px;
  background: var(--white); border-radius: 12px; text-decoration: none;
  transition: all .22s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  border: 1.5px solid transparent;
}
.qa-card:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(0,0,0,0.08); border-color: var(--s200); }
.qa-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.qa-icon svg { width: 19px; height: 19px; }
.qa-blue  { background: rgba(59,130,246,0.08);  color: #3b82f6; }
.qa-rose  { background: rgba(244,63,94,0.08);   color: #f43f5e; }
.qa-gold  { background: rgba(216,155,15,0.10);  color: var(--gold); }
.qa-green { background: rgba(22,163,74,0.08);   color: #16a34a; }
.qa-text { flex: 1; min-width: 0; }
.qa-title { display: block; font-size: 13.5px; font-weight: 600; color: var(--navy); }
.qa-sub   { display: block; font-size: 11.5px; color: var(--s400); margin-top: 2px; }
.qa-arr { width: 16px; height: 16px; color: var(--s300); flex-shrink: 0; transition: color .18s; }
.qa-card:hover .qa-arr { color: var(--gold); }

/* ── ⑥ SKELETONS ── */
.skel-card { background: var(--white); border-radius: 14px; overflow: hidden; }
.skel-img { height: 170px; background: linear-gradient(90deg, var(--s100) 25%, var(--s50) 50%, var(--s100) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
.skel-body { padding: 14px; }
.skel-line { height: 11px; border-radius: 6px; background: linear-gradient(90deg, var(--s100) 25%, var(--s50) 50%, var(--s100) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; margin-bottom: 10px; }
.sl-sm { width: 38%; } .sl-lg { width: 88%; } .sl-md { width: 60%; margin-bottom: 0; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

/* ── Empty ── */
.empty-box { padding: 52px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 10px; }
.empty-ic { width: 62px; height: 62px; border-radius: 50%; background: var(--s100); display: flex; align-items: center; justify-content: center; color: var(--s400); margin-bottom: 4px; }
.empty-box h3 { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--s700); }
.empty-box p  { font-size: 13px; color: var(--s400); }
.empty-btn { margin-top: 6px; padding: 9px 20px; background: linear-gradient(135deg, var(--gold), var(--gold3)); color: #fff; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all .22s; }
.empty-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(216,155,15,0.30); }

/* ── ⑥ PROPERTY GRID — Horizontal cards ── */
.prop-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; margin-bottom: 28px; }
.prop-card {
  background: var(--white); border-radius: 14px; overflow: hidden; cursor: pointer;
  display: flex; flex-direction: row; align-items: stretch;
  transition: all .28s cubic-bezier(.4,0,.2,1); box-shadow: 0 2px 6px rgba(0,0,0,0.06);
  animation: card-in .4s ease-out both; min-height: 148px;
}
@keyframes card-in { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
.prop-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.11); transform: translateY(-2px); }
.prop-card:hover .pc-title { color: var(--gold3); }
/* Image — fixed width for horizontal layout */
.pc-img { position: relative; width: 148px; min-width: 148px; flex-shrink: 0; overflow: hidden; background: var(--s100); }
.pc-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .45s ease; }
.prop-card:hover .pc-img img { transform: scale(1.08); }
.pc-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
/* Badges */
.pc-badges { position: absolute; top: 8px; left: 8px; display: flex; gap: 4px; }
.pc-badge { padding: 3px 8px; border-radius: 6px; font-size: 9.5px; font-weight: 700; letter-spacing: .3px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 3px; }
.pb-gold { background: linear-gradient(135deg, var(--gold), var(--gold3)); color: #fff; box-shadow: 0 2px 6px rgba(216,155,15,0.30); }
.pb-green { background: #16a34a; color: #fff; }
.pb-dark  { background: rgba(11,28,57,0.80); color: #fff; backdrop-filter: blur(4px); }
/* Save btn */
.pc-save { position: absolute; top: 8px; right: 8px; width: 30px; height: 30px; border-radius: 50%; border: none; background: rgba(255,255,255,0.90); color: var(--s500); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .22s; backdrop-filter: blur(4px); }
.pc-save:hover { background: #fff; transform: scale(1.12); color: #f43f5e; }
.pc-save.saved { color: #f43f5e; background: #fff1f2; }
/* Price chip */
.pc-price { position: absolute; bottom: 8px; left: 8px; background: linear-gradient(135deg, var(--navy), var(--navy3)); color: var(--gold2); padding: 3px 10px; border-radius: 7px; font-family: var(--fd); font-weight: 700; font-size: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.30); }
/* Body — flex fills remaining space */
.pc-body { padding: 13px 15px 13px; flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: space-between; }
.pc-row1 { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
.pc-type { font-size: 10px; font-weight: 700; color: var(--gold); text-transform: uppercase; letter-spacing: .5px; }
.pc-views { display: flex; align-items: center; gap: 3px; font-size: 11px; color: var(--s400); font-weight: 500; }
.pc-title { font-family: var(--fd); font-size: 14px; font-weight: 700; color: var(--s900); margin: 0 0 3px; line-height: 1.3; transition: color .22s; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pc-loc   { font-size: 11px; color: var(--s500); margin: 0 0 8px; display: flex; align-items: center; gap: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pc-specs { display: flex; gap: 10px; padding-top: 8px; border-top: 1px solid var(--s100); margin-top: auto; }
.pc-specs span { display: flex; align-items: center; gap: 3px; font-size: 11px; color: var(--s600); font-weight: 500; }
.pc-specs svg { color: var(--s400); }

/* ── ⑦ WIDGETS ROW ── */
.widgets-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-bottom: 28px; }
.widget-card { background: var(--white); border-radius: 14px; padding: 18px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.wc-head { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
.wc-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.wi-gold { background: rgba(216,155,15,0.10); color: var(--gold); }
.wi-blue { background: rgba(59,130,246,0.08);  color: #3b82f6; }
.wc-head h3 { font-family: var(--fd); font-size: 14px; font-weight: 700; color: var(--navy); flex: 1; }
.wc-link { font-size: 11.5px; color: var(--gold); text-decoration: none; font-weight: 600; }
.wc-empty { text-align: center; padding: 16px 0; }
.wc-empty p { font-size: 12.5px; color: var(--s400); margin-bottom: 8px; }
.wc-cta { font-size: 12px; color: var(--gold); text-decoration: none; font-weight: 600; }

/* Viewing items */
.wc-list { display: flex; flex-direction: column; gap: 10px; }
.view-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; background: var(--s50); border-radius: 10px; }
.vi-date { display: flex; flex-direction: column; align-items: center; width: 36px; flex-shrink: 0; }
.vid-day { font-family: var(--fd); font-size: 18px; font-weight: 800; color: var(--navy); line-height: 1; }
.vid-mon { font-size: 10px; font-weight: 600; color: var(--s400); text-transform: uppercase; }
.vi-info { flex: 1; min-width: 0; }
.vi-title { font-size: 12.5px; font-weight: 600; color: var(--navy); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.vi-time  { font-size: 11px; color: var(--s400); margin: 1px 0 0; }
.vi-status { padding: 2px 8px; border-radius: 6px; font-size: 10.5px; font-weight: 700; text-transform: capitalize; flex-shrink: 0; }
.vs-requested { background: rgba(245,158,11,0.10); color: #d97706; }
.vs-confirmed, .vs-approved { background: rgba(22,163,74,0.10); color: #16a34a; }
.vs-cancelled { background: rgba(220,38,38,0.10); color: #dc2626; }

/* Activity */
.activity-list { display: flex; flex-direction: column; gap: 2px; }
.act-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 9px; cursor: pointer; transition: background .15s; }
.act-item:hover { background: rgba(216,155,15,0.04); }
.act-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ai-viewing_request { background: rgba(245,158,11,0.10); color: #d97706; }
.ai-message         { background: rgba(59,130,246,0.08);  color: #3b82f6; }
.ai-document        { background: rgba(22,163,74,0.08);   color: #16a34a; }
.ai-general         { background: rgba(216,155,15,0.10);  color: var(--gold); }
.act-body { flex: 1; min-width: 0; }
.act-title { font-size: 12px; font-weight: 600; color: var(--navy); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.act-time  { font-size: 10.5px; color: var(--s400); margin: 1px 0 0; }
.act-unread-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--gold); flex-shrink: 0; }

/* Tip card */
.tip-card { background: linear-gradient(135deg, var(--navy) 0%, var(--navy3) 100%); }
.tip-icon { width: 44px; height: 44px; border-radius: 12px; background: rgba(216,155,15,0.15); display: flex; align-items: center; justify-content: center; color: var(--gold2); margin-bottom: 12px; }
.tip-title { font-family: var(--fd); font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px; }
.tip-body { font-size: 12px; color: rgba(255,255,255,0.55); line-height: 1.55; margin-bottom: 14px; }
.tip-btn { display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, var(--gold), var(--gold3)); color: #fff; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; transition: all .22s; }
.tip-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(216,155,15,0.35); }

/* ── ⑧ SUGGESTED AGENTS ── */
.agents-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 28px; }
.agent-card {
  background: var(--white); border-radius: 14px; padding: 20px 16px;
  text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  transition: all .22s; border: 1.5px solid transparent;
}
.agent-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.09); border-color: var(--s200); }
.ag-photo {
  width: 56px; height: 56px; border-radius: 50%; overflow: hidden; margin: 0 auto 12px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 20px; color: #fff;
}
.ag-photo img { width: 100%; height: 100%; object-fit: cover; }
.ag-name { font-family: var(--fd); font-size: 14px; font-weight: 700; color: var(--navy); margin-bottom: 2px; }
.ag-role { font-size: 11.5px; color: var(--s400); margin-bottom: 4px; }
.ag-listings { font-size: 11.5px; color: var(--gold3); font-weight: 600; margin-bottom: 14px; }
.ag-btn {
  display: block; padding: 8px 0; border-radius: 8px;
  background: rgba(216,155,15,0.10); color: var(--gold3);
  font-size: 12.5px; font-weight: 700; text-decoration: none;
  transition: all .22s; border: 1px solid rgba(216,155,15,0.20);
}
.ag-btn:hover { background: linear-gradient(135deg, var(--gold), var(--gold3)); color: #fff; border-color: var(--gold); }

/* ── FOOTER ── */
.footer { padding: 14px 28px; border-top: 1px solid var(--s200); display: flex; align-items: center; justify-content: space-between; background: var(--white); font-size: 11.5px; color: var(--s400); }
.footer-links { display: flex; gap: 16px; }
.footer-links a { font-size: 11.5px; color: var(--s400); text-decoration: none; transition: color .18s; }
.footer-links a:hover { color: var(--gold); }

/* ── TOASTS ── */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 13px 20px; border-radius: 11px; z-index: 3000; animation: toast-in .3s ease; font-size: 13px; font-weight: 600; color: #fff; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.toast-err { background: linear-gradient(135deg, #dc2626, #b91c1c); }
.toast-ok  { background: linear-gradient(135deg, #16a34a, #15803d); }
@keyframes toast-in { from { transform: translateX(80px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

/* ── TRANSITIONS ── */
.fade-enter-active, .fade-leave-active { transition: opacity .3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.drop-enter-active { transition: all .2s ease; }
.drop-leave-active { transition: all .15s ease; }
.drop-enter-from, .drop-leave-to { opacity: 0; transform: translateY(-8px); }
.slide-up-enter-active { transition: all .3s ease; }
.slide-up-leave-active { transition: all .25s ease; }
.slide-up-enter-from { opacity: 0; transform: translateY(10px); }
.slide-up-leave-to   { opacity: 0; transform: translateY(-4px); }

/* ── RESPONSIVE ── */
@media (max-width: 1100px) {
  .stats-row { grid-template-columns: repeat(2,1fr); }
  .prop-grid  { grid-template-columns: repeat(2,1fr); }
  .agents-grid { grid-template-columns: repeat(2,1fr); }
  .widgets-row { grid-template-columns: 1fr 1fr; }
  .widgets-row .tip-card { grid-column: span 2; }
}
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .main-wrapper { margin-left: 0; }
  .hamburger { display: flex; }
  .content-inner { padding: 18px 16px 36px; }
  .hero-body { padding: 24px 22px 20px; }
  .hero-visual { display: none; }
  .hero-name { font-size: 22px; }
  .hero-progress { padding: 12px 22px; }
  .journey-card { padding: 16px 18px; }
  .journey-track { gap: 0; }
  .stats-row { grid-template-columns: repeat(2,1fr); gap: 12px; }
  .qa-grid { grid-template-columns: 1fr; }
  .prop-grid { grid-template-columns: repeat(2,1fr); gap: 14px; }
  .widgets-row { grid-template-columns: 1fr; }
  .widgets-row .tip-card { grid-column: auto; }
  .agents-grid { grid-template-columns: repeat(2,1fr); }
  .tb-cta span { display: none; }
  .tb-cta { padding: 7px 10px; }
}
@media (max-width: 480px) {
  .stats-row { grid-template-columns: 1fr 1fr; gap: 10px; }
  .stat-card { padding: 14px 12px; }
  .stat-n { font-size: 22px; }
  .prop-grid { grid-template-columns: 1fr; }
  .pc-img { height: 190px; }
  .agents-grid { grid-template-columns: 1fr 1fr; }
  .footer { flex-direction: column; gap: 8px; text-align: center; }
  .hero-name { font-size: 20px; }
  .ab-right { flex-direction: row; }
}
</style>
