<template>
  <div class="ad-layout">

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
          <span v-if="pendingViewingsCount > 0" class="nav-badge">{{ pendingViewingsCount }}</span>
        </router-link>
        <router-link to="/agent/calendar" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
          </span>
          <span>Calendar</span>
        </router-link>

        <div class="nav-group-label">Community</div>
        <router-link to="/leaderboard" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
          </span>
          <span>Leaderboard</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Messages</span>
          <span v-if="unreadMessagesCount > 0" class="nav-badge">{{ unreadMessagesCount }}</span>
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
          <span class="topbar-title">Agent Overview</span>
        </div>
        <div class="topbar-right">
          <div class="notif-wrap">
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
          <router-link to="/agent/properties/create" class="tb-cta">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Add Property</span>
          </router-link>
        </div>
      </header>

      <!-- ═══════════════════════════════════
           CONTENT
      ═══════════════════════════════════ -->
      <main class="content-area">
        <div class="content-inner">

          <!-- Stats Cards -->
          <div class="stats-row">
            <div class="stat-card">
              <div class="stat-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </div>
              <div class="stat-body">
                <span class="stat-label">Total Properties</span>
                <span class="stat-number">{{ stats.totalProperties }}</span>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon-wrap si-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <div class="stat-body">
                <span class="stat-label">Active Listings</span>
                <span class="stat-number">{{ stats.activeListings }}</span>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon-wrap si-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </div>
              <div class="stat-body">
                <span class="stat-label">Total Views</span>
                <span class="stat-number">{{ stats.totalViews }}</span>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon-wrap si-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
              </div>
              <div class="stat-body">
                <span class="stat-label">Total Photos</span>
                <span class="stat-number">{{ stats.totalPhotos }}</span>
              </div>
            </div>
          </div>

          <!-- Recent Properties -->
          <div class="section-card">
            <div class="section-head">
              <div>
                <h3 class="section-title">Recent Properties</h3>
                <p class="section-sub">Your latest listings</p>
              </div>
              <router-link to="/agent/properties" class="see-all">View all
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
              </router-link>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="state-center">
              <div class="spinner"></div>
              <p>Loading properties...</p>
            </div>

            <!-- Empty: no properties -->
            <div v-else-if="properties.length === 0" class="state-center">
              <div class="state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </div>
              <h4>No Properties Yet</h4>
              <p>Start by creating your first property listing</p>
              <router-link to="/agent/properties/create" class="btn-gold">Create First Property</router-link>
            </div>

            <!-- Empty: all sold -->
            <div v-else-if="recentProperties.length === 0" class="state-center">
              <div class="state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
              </div>
              <h4>No Active Listings</h4>
              <p>All your properties are marked as Sold. Change a status back to Available or add a new listing.</p>
              <router-link to="/agent/properties" class="btn-gold">Manage Properties</router-link>
            </div>

            <!-- Properties Grid -->
            <div v-else class="prop-grid">
              <div v-for="property in recentProperties" :key="property.id" class="prop-card">
                <div class="prop-img-wrap">
                  <img v-if="getCoverPhoto(property)" :src="getCoverPhoto(property)" :alt="property.title" class="prop-img" />
                  <div v-else class="prop-img-ph">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D6D3D1" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                  </div>
                  <span :class="['status-chip', 'sc-' + property.status]">{{ formatStatus(property.status) }}</span>
                  <span v-if="property.photos && property.photos.length > 0" class="photo-chip">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    {{ property.photos.length }}
                  </span>
                </div>
                <div class="prop-body">
                  <h4 class="prop-title">{{ property.title }}</h4>
                  <p class="prop-loc">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ property.city }}, {{ property.province }}
                  </p>
                  <div class="prop-specs">
                    <span class="prop-spec">{{ property.bedrooms }} Beds</span>
                    <span class="prop-spec">{{ property.bathrooms }} Baths</span>
                    <span class="prop-spec">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      {{ property.view_count || 0 }}
                    </span>
                  </div>
                  <div class="prop-price">&#8369;{{ formatPrice(property.price) }}</div>
                </div>
                <div class="prop-actions">
                  <router-link :to="`/agent/properties/${property.id}/edit`" class="btn-action btn-edit">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                  </router-link>
                  <button @click="deleteProperty(property.id)" class="btn-action btn-del">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Messages -->
          <div class="section-card">
            <div class="section-head">
              <div>
                <h3 class="section-title">Recent Messages</h3>
                <p class="section-sub">Buyers contacting you about properties</p>
              </div>
              <router-link to="/conversations" class="see-all">View all
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
              </router-link>
            </div>

            <div v-if="recentConversations.length === 0" class="state-empty-sm">
              No messages yet. Buyers will contact you through your property listings.
            </div>

            <div v-else class="conv-list">
              <div v-for="conv in recentConversations.slice(0, 5)" :key="convOtherId(conv)" class="conv-item" @click="goToConversation(conv)">
                <div class="conv-av">{{ convOtherName(conv).charAt(0).toUpperCase() }}</div>
                <div class="conv-body">
                  <div class="conv-top">
                    <span class="conv-name">{{ convOtherName(conv) }}</span>
                    <span class="conv-time">{{ formatConvTime(conv.last_message?.created_at) }}</span>
                  </div>
                  <p class="conv-preview">{{ conv.last_message?.message_content || 'Attachment' }}</p>
                  <div v-if="conv.last_message?.property" class="conv-prop-tag">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    {{ conv.last_message.property.title }}
                  </div>
                </div>
                <span v-if="conv.unread_count > 0" class="conv-badge">{{ conv.unread_count }}</span>
              </div>
            </div>
          </div>

          <!-- Reviews & Feedback -->
          <div class="section-card">
            <div class="section-head">
              <div>
                <h3 class="section-title">Reviews & Feedback</h3>
                <p class="section-sub">What buyers are saying about you</p>
              </div>
            </div>

            <!-- Rating summary -->
            <div v-if="reviewStats.totalReviews > 0" class="review-summary">
              <div class="review-avg">
                <span class="review-avg-num">{{ reviewStats.averageRating }}</span>
                <div class="review-avg-detail">
                  <div class="review-stars">
                    <svg v-for="s in 5" :key="'star-'+s" width="16" height="16" viewBox="0 0 24 24"
                      :fill="s <= Math.round(reviewStats.averageRating) ? 'var(--gold)' : 'none'"
                      :stroke="s <= Math.round(reviewStats.averageRating) ? 'var(--gold)' : 'var(--s300)'"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                  </div>
                  <span class="review-count">{{ reviewStats.totalReviews }} review{{ reviewStats.totalReviews !== 1 ? 's' : '' }}</span>
                </div>
              </div>
            </div>

            <!-- Reviews list -->
            <div v-if="recentReviews.length > 0" class="reviews-list">
              <div v-for="rev in recentReviews" :key="rev.id" class="review-item">
                <div class="review-top">
                  <div class="review-author">
                    <div class="review-av">{{ (rev.buyer_name || 'B').charAt(0).toUpperCase() }}</div>
                    <div>
                      <span class="review-name">{{ rev.buyer_name || 'Buyer' }}</span>
                      <span class="review-date">{{ formatReviewDate(rev.created_at) }}</span>
                    </div>
                  </div>
                  <div class="review-rating-sm">
                    <svg v-for="s in 5" :key="'rs-'+s" width="13" height="13" viewBox="0 0 24 24"
                      :fill="s <= rev.rating ? 'var(--gold)' : 'none'"
                      :stroke="s <= rev.rating ? 'var(--gold)' : 'var(--s300)'"
                      stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  </div>
                </div>
                <p v-if="rev.review_title" class="review-title-text">{{ rev.review_title }}</p>
                <p v-if="rev.review_content" class="review-content-text">{{ rev.review_content }}</p>
              </div>
            </div>

            <div v-else class="state-empty-sm">
              No reviews yet. Buyers can rate your service from your public profile.
            </div>
          </div>

          <!-- Summary -->
          <div v-if="properties.length > 0" class="summary-row">
            <div class="summary-item">
              <span class="summary-label">Total Properties</span>
              <span class="summary-value">{{ properties.length }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Active Listings</span>
              <span class="summary-value">{{ stats.activeListings }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Total Views</span>
              <span class="summary-value">{{ stats.totalViews }}</span>
            </div>
          </div>

        </div>
      </main>
    </div>

    <!-- Toast Messages -->
    <transition name="toast">
      <div v-if="successMessage" class="toast toast-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        {{ successMessage }}
      </div>
    </transition>
    <transition name="toast">
      <div v-if="errorMessage" class="toast toast-error">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        {{ errorMessage }}
      </div>
    </transition>

  </div>
</template>

<script>
export default {
  name: 'AgentDashboard',
  data() {
    return {
      sidebarOpen: false,
      // User
      userName: '',
      userRole: '',
      showUserMenu: false,
      profilePhotoUrl: null,

      // Properties
      properties: [],
      loading: false,

      // Stats
      stats: {
        totalProperties: 0,
        activeListings: 0,
        totalViews: 0,
        totalPhotos: 0,
      },

      // Messaging
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      myId: null,
      recentConversations: [],
      unreadMessagesCount: 0,
      echoChannel: null,
      showNotifBell: false,

      // Notifications
      notifications: [],
      unreadNotificationsCount: 0,
      pendingViewingsCount: 0,
      viewingNotifs: [],
      notifEchoChannel: null,

      // Reviews
      recentReviews: [],
      reviewStats: { averageRating: 0, totalReviews: 0 },

      // UI Messages
      successMessage: '',
      errorMessage: '',
    };
  },

  computed: {
    recentProperties() {
      return this.properties.filter(p => p.status === 'available').slice(0, 6);
    },
    totalNotifCount() {
      return this.unreadNotificationsCount + this.unreadMessagesCount;
    },
  },

  methods: {
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },

    capitalizeRole(role) {
      return role.charAt(0).toUpperCase() + role.slice(1);
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH').format(price);
    },

    formatStatus(status) {
      const statusMap = {
        available: 'Available',
        sold: 'Sold',
      };
      return statusMap[status] || status;
    },

    getCoverPhoto(property) {
      if (!property.photos || property.photos.length === 0) return null;
      const cover = property.photos.find(p => p.is_cover);
      return cover ? cover.photo_url : property.photos[0].photo_url;
    },

    async loadProperties() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');

        const response = await fetch(window.__API_URL__ + '/api/agent/properties', {
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();

        if (data.success) {
          this.properties = data.data.data || data.data || [];
          this.calculateStats();
          console.log('✅ Properties loaded:', this.properties);
        } else {
          this.showError(data.message || 'Failed to load properties');
        }
      } catch (error) {
        console.error('Load error:', error);
        this.showError('Failed to load properties');
      } finally {
        this.loading = false;
      }
    },

    calculateStats() {
      this.stats.totalProperties = this.properties.length;
      this.stats.activeListings = this.properties.filter(p => p.status === 'available').length;
      this.stats.totalViews = this.properties.reduce((sum, p) => sum + (p.view_count || 0), 0);
      this.stats.totalPhotos = this.properties.reduce((sum, p) => sum + (p.photos ? p.photos.length : 0), 0);
    },

    async deleteProperty(id) {
      if (!confirm('Are you sure you want to delete this property?')) return;

      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`${window.__API_URL__}/api/agent/properties/${id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`,
          },
        });

        const data = await response.json();

        if (data.success) {
          this.showSuccess('Property deleted successfully');
          this.loadProperties();
        } else {
          this.showError(data.message || 'Failed to delete property');
        }
      } catch (error) {
        console.error('Delete error:', error);
        this.showError('Failed to delete property');
      }
    },

    loadProfilePhoto() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__}/storage/${user.profile_photo_path}`;
        }
      } catch (error) {
        console.error('Error loading profile photo:', error);
      }
    },

    // ── Conversations ──────────────────────────────────────────
    convOtherName(conv) {
      return conv.agent?.name ?? conv.buyer?.name ?? 'Unknown';
    },

    convOtherId(conv) {
      return conv.agent?.id ?? conv.buyer?.id ?? 0;
    },

    formatConvTime(iso) {
      if (!iso) return '';
      const d = new Date(iso);
      const now = new Date();
      const diffDays = Math.floor((now - d) / 86400000);
      if (diffDays === 0) return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      if (diffDays === 1) return 'Yesterday';
      return d.toLocaleDateString();
    },

    toggleNotifBell() {
      this.showNotifBell = !this.showNotifBell;
      if (this.showNotifBell) {
        this.loadNotifications();
        // Mark all notifications as read when the panel is opened
        this.markAllNotificationsRead();
      }
    },

    async loadNotifications() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/notifications`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.notifications = data.notifications || [];
          this.unreadNotificationsCount = this.notifications.filter(n => !n.is_read).length;
        }
      } catch (e) {
        console.error('Load notifications error:', e);
      }
    },

    async markAllNotificationsRead() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(`${this.apiUrl}/api/notifications/mark-all-read`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        this.notifications.forEach(n => n.is_read = true);
        this.unreadNotificationsCount = 0;
      } catch (e) { /* silent */ }
    },

    async handleNotifClick(notif) {
      if (!notif.is_read) {
        try {
          const token = localStorage.getItem('auth_token');
          await fetch(`${this.apiUrl}/api/notifications/${notif.id}/read`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
          });
          notif.is_read = true;
          this.unreadNotificationsCount = Math.max(0, this.unreadNotificationsCount - 1);
        } catch (e) { /* silent */ }
      }

      this.showNotifBell = false;

      if (notif.notification_type === 'viewing_request') {
        this.$router.push('/agent/viewings');
      } else if (notif.notification_type === 'message') {
        this.$router.push('/conversations');
      } else if (notif.notification_type === 'document') {
        this.$router.push('/agent/documents');
      } else {
        this.$router.push('/agent/dashboard');
      }
    },

    goToConversation(conv) {
      this.showNotifBell = false;
      this.$router.push(`/conversations/${this.convOtherId(conv)}`);
    },

    async loadConversations() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/conversations`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.recentConversations = data.data;
          this.unreadMessagesCount = data.data.reduce((sum, c) => sum + (c.unread_count || 0), 0);
        }
      } catch (e) {
        console.error('Load conversations error:', e);
      }
    },

    subscribeToEcho() {
      if (!window.Echo || !this.myId) return;
      try {
        window.Echo.connector.pusher.config.auth.headers.Authorization =
          'Bearer ' + localStorage.getItem('auth_token');
      } catch (e) { /* ignore */ }

      // Subscribe to message notifications
      this.echoChannel = window.Echo.private(`chat.${this.myId}`)
        .listen('.MessageSent', () => {
          this.loadConversations();
        });

      // Subscribe to notifications channel (viewings, documents, reviews, messages)
      try {
        this.notifEchoChannel = window.Echo.private(`notifications.${this.myId}`)
          .listen('.NotificationSent', (data) => {
            this.notifications.unshift(data);
            this.unreadNotificationsCount++;
          })
          .listen('.ViewingRequested', (data) => {
            this.viewingNotifs.unshift(data);
            this.pendingViewingsCount++;
          })
          .listen('.DocumentSigned', (data) => {
            this.viewingNotifs.unshift({
              ...data,
              type: 'document_signed',
            });
          });
      } catch (e) { /* ignore */ }
    },

    async loadPendingViewingsCount() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch(`${this.apiUrl}/api/agent/viewings`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          const list = data.data || data.viewings || [];
          this.pendingViewingsCount = list.filter(v => v.status === 'requested').length;
        }
      } catch (e) { /* non-critical */ }
    },

    leaveEcho() {
      if (this.echoChannel) {
        window.Echo.leave(`chat.${this.myId}`);
        this.echoChannel = null;
      }
      if (this.notifEchoChannel) {
        window.Echo.leave(`notifications.${this.myId}`);
        this.notifEchoChannel = null;
      }
    },
    // ───────────────────────────────────────────────────────────

    async loadReviews() {
      try {
        const token = localStorage.getItem('auth_token');
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const res = await fetch(`${this.apiUrl}/api/users/${user.id}/agent-profile`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.recentReviews = (data.reviews || []).slice(0, 5);
          this.reviewStats.averageRating = parseFloat(data.average_rating || 0).toFixed(1);
          this.reviewStats.totalReviews = data.total_reviews || 0;
        }
      } catch (e) {
        console.error('Load reviews error:', e);
      }
    },

    formatReviewDate(iso) {
      if (!iso) return '';
      const d = new Date(iso);
      const now = new Date();
      const diffDays = Math.floor((now - d) / 86400000);
      if (diffDays === 0) return 'Today';
      if (diffDays === 1) return 'Yesterday';
      if (diffDays < 7) return `${diffDays} days ago`;
      return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
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
        this.userName = user.name || 'Agent';
        this.userRole = user.role || 'agent';
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

    this.myId = JSON.parse(localStorage.getItem('user') || '{}').id || null;

    // Fire all independent API calls in parallel for faster loading
    Promise.all([
      this.loadUserData(),
      this.loadProfilePhoto(),
      this.loadProperties(),
      this.loadConversations(),
      this.loadPendingViewingsCount(),
      this.loadNotifications(),
      this.loadReviews(),
    ]).catch(() => {});
    this.subscribeToEcho();

    // Watch for photo changes every 2s
    this.photoWatcher = setInterval(() => {
      const user = JSON.parse(localStorage.getItem('user') || '{}');
      if (user.profile_photo_path && user.profile_photo_path !== this.profilePhotoUrl?.split('/storage/')[1]) {
        this.loadProfilePhoto();
      }
    }, 2000);
  },

  beforeUnmount() {
    this.showUserMenu = false;
    this.leaveEcho();
    if (this.photoWatcher) {
      clearInterval(this.photoWatcher);
    }
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

/* ── TOKENS ── */
.ad-layout {
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
.drop-enter-active { animation: dropIn .22s ease; }
.drop-leave-active { animation: dropIn .18s ease reverse; }
@keyframes dropIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

/* ══════════════════════════════════
   CONTENT
══════════════════════════════════ */
.content-area { flex: 1; background: var(--bg); }
.content-inner { max-width: 1080px; margin: 0 auto; padding: 28px 30px 48px; }

/* ── STAT CARDS ── */
.stats-row {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
}
.stat-card {
  background: var(--white); border-radius: var(--r); padding: 20px;
  display: flex; align-items: center; gap: 14px;
  border: 1px solid var(--s200); transition: all .22s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); border-color: var(--s300); }
.stat-icon-wrap {
  width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
  background: rgba(216,155,15,0.08); color: var(--gold);
  display: flex; align-items: center; justify-content: center;
}
.stat-icon-wrap svg { width: 20px; height: 20px; }
.stat-icon-wrap.si-green { background: rgba(34,197,94,0.08); color: #16a34a; }
.stat-icon-wrap.si-blue  { background: rgba(59,130,246,0.08); color: #2563eb; }
.stat-icon-wrap.si-purple { background: rgba(139,92,246,0.08); color: #7c3aed; }
.stat-body { flex: 1; min-width: 0; }
.stat-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--s500); margin-bottom: 4px; }
.stat-number { display: block; font-size: 26px; font-weight: 800; color: var(--navy); font-family: var(--fd); line-height: 1; }

/* ── SECTION CARDS ── */
.section-card {
  background: var(--white); border-radius: var(--r); padding: 24px;
  border: 1px solid var(--s200); margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.section-head {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--s100);
}
.section-title { font-size: 17px; font-weight: 700; color: var(--navy); font-family: var(--fd); margin: 0; }
.section-sub { font-size: 12.5px; color: var(--s400); margin: 4px 0 0; }
.see-all {
  display: flex; align-items: center; gap: 4px;
  color: var(--gold); text-decoration: none; font-weight: 600; font-size: 13px;
  transition: color .2s; white-space: nowrap;
}
.see-all:hover { color: var(--gold3); }

/* ── LOADING / EMPTY STATES ── */
.state-center {
  padding: 48px 20px; text-align: center; color: var(--s500); font-size: 13px;
  display: flex; flex-direction: column; align-items: center; gap: 12px;
}
.state-center h4 { font-size: 16px; font-weight: 700; color: var(--navy); }
.state-center p { color: var(--s400); max-width: 360px; line-height: 1.5; }
.state-icon {
  width: 56px; height: 56px; border-radius: 50%;
  background: rgba(216,155,15,0.08); color: var(--gold);
  display: flex; align-items: center; justify-content: center;
}
.state-icon svg { width: 28px; height: 28px; }
.spinner {
  width: 28px; height: 28px; border: 3px solid var(--s200);
  border-top-color: var(--gold); border-radius: 50%;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.btn-gold {
  display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: var(--white); border-radius: 8px; font-size: 13px; font-weight: 600;
  text-decoration: none; transition: all .22s;
  box-shadow: 0 2px 8px rgba(216,155,15,0.30);
}
.btn-gold:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(216,155,15,0.40); }
.state-empty-sm {
  padding: 20px; text-align: center; color: var(--s400); font-size: 13px;
  background: var(--s50); border-radius: 8px; border: 1px solid var(--s200);
}

/* ── PROPERTY GRID ── */
.prop-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 18px;
}
.prop-card {
  background: var(--white); border-radius: var(--r); overflow: hidden;
  border: 1px solid var(--s200); transition: all .22s; display: flex; flex-direction: column;
}
.prop-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.10); border-color: var(--s300); transform: translateY(-3px); }
.prop-img-wrap { position: relative; width: 100%; height: 170px; background: var(--s50); overflow: hidden; }
.prop-img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
.prop-card:hover .prop-img { transform: scale(1.04); }
.prop-img-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--s50); }
.status-chip {
  position: absolute; top: 10px; right: 10px;
  padding: 4px 10px; border-radius: 6px; font-size: 10.5px; font-weight: 700; text-transform: uppercase;
}
.sc-available { background: rgba(34,197,94,0.12); color: #15803d; }
.sc-sold { background: rgba(239,68,68,0.10); color: #b91c1c; }
.sc-rented { background: rgba(59,130,246,0.10); color: #1d4ed8; }
.sc-unlisted { background: var(--s100); color: var(--s600); }
.photo-chip {
  position: absolute; bottom: 10px; left: 10px;
  background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 5px;
  font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px;
}
.prop-body { padding: 14px; flex: 1; }
.prop-title { font-size: 14.5px; font-weight: 700; color: var(--navy); margin: 0 0 6px; line-height: 1.3; }
.prop-loc { font-size: 12px; color: var(--s400); margin: 0 0 10px; display: flex; align-items: center; gap: 4px; }
.prop-specs { display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
.prop-spec {
  font-size: 11px; color: var(--s600); font-weight: 600;
  padding: 3px 8px; background: var(--s50); border-radius: 5px;
  display: flex; align-items: center; gap: 3px;
}
.prop-price { font-size: 17px; font-weight: 800; color: var(--gold); font-family: var(--fd); }
.prop-actions {
  padding: 10px 14px; border-top: 1px solid var(--s100); display: flex; gap: 8px;
}
.btn-action {
  flex: 1; padding: 8px 10px; border: none; border-radius: 7px;
  font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s;
  text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 5px;
}
.btn-edit { background: rgba(216,155,15,0.10); color: var(--gold3); }
.btn-edit:hover { background: rgba(216,155,15,0.18); }
.btn-del { background: rgba(239,68,68,0.08); color: #dc2626; }
.btn-del:hover { background: rgba(239,68,68,0.14); }

/* ── CONVERSATION LIST ── */
.conv-list { border: 1px solid var(--s200); border-radius: 10px; overflow: hidden; }
.conv-item {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 16px; cursor: pointer; transition: background .15s;
  border-bottom: 1px solid var(--s50);
}
.conv-item:last-child { border-bottom: none; }
.conv-item:hover { background: rgba(216,155,15,0.03); }
.conv-av {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  color: var(--gold2); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 14px;
}
.conv-body { flex: 1; min-width: 0; }
.conv-top { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; }
.conv-name { font-weight: 700; font-size: 13px; color: var(--s900); }
.conv-time { font-size: 11px; color: var(--s400); white-space: nowrap; flex-shrink: 0; }
.conv-preview {
  font-size: 12.5px; color: var(--s500); margin: 2px 0 0;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.conv-prop-tag {
  font-size: 11px; color: var(--gold3);
  background: rgba(216,155,15,0.06); border: 1px solid rgba(216,155,15,0.18);
  border-radius: 4px; padding: 2px 7px;
  display: inline-flex; align-items: center; gap: 3px; margin-top: 3px;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;
}
.conv-badge {
  background: rgba(216,155,15,0.12); color: var(--gold);
  border-radius: 99px; padding: 2px 8px; font-size: 11px; font-weight: 700; flex-shrink: 0;
}

/* ── REVIEWS ── */
.review-summary {
  display: flex; align-items: center; gap: 18px;
  padding: 18px 20px; background: var(--s50);
  border-radius: 10px; margin-bottom: 16px;
  border: 1px solid var(--s200);
}
.review-avg { display: flex; align-items: center; gap: 14px; }
.review-avg-num {
  font-size: 36px; font-weight: 800; color: var(--navy);
  font-family: var(--fd); line-height: 1;
}
.review-avg-detail { display: flex; flex-direction: column; gap: 3px; }
.review-stars { display: flex; gap: 2px; }
.review-count { font-size: 12px; color: var(--s500); font-weight: 500; }

.reviews-list { display: flex; flex-direction: column; gap: 12px; }
.review-item {
  padding: 16px 18px; background: var(--s50);
  border-radius: 10px; border: 1px solid var(--s200);
  transition: border-color .2s;
}
.review-item:hover { border-color: rgba(216,155,15,.25); }
.review-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.review-author { display: flex; align-items: center; gap: 10px; }
.review-av {
  width: 32px; height: 32px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700; color: var(--navy); flex-shrink: 0;
}
.review-name { font-size: 13px; font-weight: 600; color: var(--s800); display: block; }
.review-date { font-size: 11px; color: var(--s400); }
.review-rating-sm { display: flex; gap: 1px; }
.review-title-text { font-size: 14px; font-weight: 700; color: var(--s800); margin: 0 0 4px; }
.review-content-text { font-size: 13px; color: var(--s600); margin: 0; line-height: 1.5; }

/* ── SUMMARY ROW ── */
.summary-row {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;
  background: var(--white); border-radius: var(--r); padding: 20px;
  border: 1px solid var(--s200); box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.summary-item {
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  text-align: center; padding: 12px; background: var(--s50); border-radius: 8px;
  border-left: 3px solid var(--gold);
}
.summary-label { font-size: 11px; color: var(--s500); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
.summary-value { font-size: 24px; font-weight: 800; color: var(--navy); font-family: var(--fd); }

/* ── TOAST ── */
.toast {
  position: fixed; bottom: 24px; right: 24px; z-index: 3000;
  padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600;
  display: flex; align-items: center; gap: 8px;
  box-shadow: 0 8px 28px rgba(0,0,0,0.14);
}
.toast-success { background: #065f46; color: #d1fae5; }
.toast-error   { background: #991b1b; color: #fee2e2; }
.toast-enter-active { animation: slideUp .3s ease; }
.toast-leave-active { animation: slideUp .25s ease reverse; }
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

/* ══════════════════════════════════
   RESPONSIVE
══════════════════════════════════ */
@media (max-width: 1024px) {
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .hamburger { display: flex; }
  .main-wrapper { margin-left: 0; }
  .topbar { padding: 0 16px; }
  .content-inner { padding: 20px 16px 40px; }
  .stats-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .prop-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 14px; }
  .summary-row { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
  .stats-row { grid-template-columns: 1fr; }
  .prop-grid { grid-template-columns: 1fr; }
  .tb-cta span { display: none; }
  .tb-cta { padding: 7px 10px; }
}
</style>
