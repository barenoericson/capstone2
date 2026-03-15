<template>
  <div class="admin-layout">
    <!-- ========== SIDEBAR ========== -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
        <span class="admin-badge">ADMIN</span>
      </div>

      <nav class="sidebar-nav">
        <button
          v-for="tab in navTabs"
          :key="tab.id"
          :class="['nav-item', { active: activeTab === tab.id }]"
          @click="activeTab = tab.id"
        >
          <span class="nav-icon" v-html="getTabIcon(tab.id)"></span>
          <span class="nav-label">{{ tab.label }}</span>
          <span v-if="tab.badge" class="nav-badge">{{ tab.badge }}</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name">{{ userName }}</p>
            <p class="user-role">Administrator</p>
          </div>
        </div>
        <button @click="logout" class="btn-logout">Logout</button>
      </div>
    </aside>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="main-content">
      <nav class="topbar">
        <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 class="page-title">{{ currentTabTitle }}</h1>
      </nav>

      <div class="page-wrapper">
        <!-- ===== OVERVIEW TAB ===== -->
        <section v-if="activeTab === 'overview'" class="tab-content">
          <div v-if="loadingOverview" class="loading-state">
            <div class="spinner"></div>
            <p>Loading dashboard...</p>
          </div>

          <template v-else>
            <!-- Stat Cards -->
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-icon stat-icon-users">👥</div>
                <div class="stat-body">
                  <p class="stat-number">{{ overview.total_users }}</p>
                  <p class="stat-label">Total Users</p>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon stat-icon-properties">🏠</div>
                <div class="stat-body">
                  <p class="stat-number">{{ overview.total_properties }}</p>
                  <p class="stat-label">Total Properties</p>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon stat-icon-agents">✅</div>
                <div class="stat-body">
                  <p class="stat-number">{{ overview.verified_agents }}</p>
                  <p class="stat-label">Verified Agents</p>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon stat-icon-pending">⏳</div>
                <div class="stat-body">
                  <p class="stat-number">{{ overview.pending_agents }}</p>
                  <p class="stat-label">Pending Applications</p>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon stat-icon-active">📍</div>
                <div class="stat-body">
                  <p class="stat-number">{{ overview.active_properties }}</p>
                  <p class="stat-label">Active Properties</p>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon stat-icon-photos">📷</div>
                <div class="stat-body">
                  <p class="stat-number">{{ overview.pending_photos }}</p>
                  <p class="stat-label">Pending Photos</p>
                </div>
              </div>
              <div class="stat-card" v-if="overview.flagged_properties > 0" @click="activeTab = 'flagged'" style="cursor:pointer">
                <div class="stat-icon stat-icon-flagged">🚩</div>
                <div class="stat-body">
                  <p class="stat-number">{{ overview.flagged_properties }}</p>
                  <p class="stat-label">Flagged Properties</p>
                </div>
              </div>
            </div>

            <!-- Role Breakdown -->
            <div class="section-card">
              <h3 class="section-title">Users by Role</h3>
              <div class="role-grid">
                <div class="role-item">
                  <span class="role-badge role-buyer">Buyers</span>
                  <span class="role-count">{{ overview.users_by_role?.buyers || 0 }}</span>
                </div>
                <div class="role-item">
                  <span class="role-badge role-agent">Agents</span>
                  <span class="role-count">{{ overview.users_by_role?.agents || 0 }}</span>
                </div>
                <div class="role-item">
                  <span class="role-badge role-admin">Admins</span>
                  <span class="role-count">{{ overview.users_by_role?.admins || 0 }}</span>
                </div>
              </div>
            </div>

            <!-- Period Stats -->
            <div class="section-card">
              <h3 class="section-title">Last 30 Days</h3>
              <div class="period-grid">
                <div class="period-item">
                  <p class="period-number">{{ periodStats.users_registered }}</p>
                  <p class="period-label">New Users</p>
                </div>
                <div class="period-item">
                  <p class="period-number">{{ periodStats.properties_added }}</p>
                  <p class="period-label">New Properties</p>
                </div>
                <div class="period-item">
                  <p class="period-number">{{ periodStats.messages_sent }}</p>
                  <p class="period-label">Messages Sent</p>
                </div>
                <div class="period-item">
                  <p class="period-number">{{ periodStats.viewings_requested }}</p>
                  <p class="period-label">Viewings Requested</p>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="section-card">
              <h3 class="section-title">Recent Activity</h3>
              <div v-if="recentActivity.length === 0" class="empty-state">
                <p>No recent activity</p>
              </div>
              <div v-else class="activity-feed">
                <div v-for="log in recentActivity" :key="log.id" class="activity-item">
                  <div class="activity-dot" :class="'dot-' + log.action_type"></div>
                  <div class="activity-body">
                    <p class="activity-desc">{{ log.description }}</p>
                    <p class="activity-meta">
                      {{ log.user?.name || 'System' }} &middot; {{ formatDateTime(log.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Analytics Section -->
            <div class="section-card analytics-section">
              <div class="analytics-header">
                <h3 class="section-title" style="margin-bottom:0">Analytics &amp; Trends</h3>
                <div class="period-selector">
                  <button v-for="p in [7, 30, 90]" :key="p"
                    :class="['period-btn', { active: analyticsPeriod === p }]"
                    @click="analyticsPeriod = p; loadAnalytics()">
                    {{ p }}d
                  </button>
                </div>
              </div>

              <div v-if="loadingAnalytics" class="loading-state" style="padding:30px">
                <div class="spinner"></div>
                <p>Loading analytics...</p>
              </div>

              <template v-else>
                <!-- User Registration Trend -->
                <div class="chart-block">
                  <h4 class="chart-title">User Registrations (Last {{ analyticsPeriod }} Days)</h4>
                  <div class="bar-chart">
                    <div v-for="(day, idx) in analyticsData.user_trend" :key="'u'+day.date"
                      class="bar-column" :title="day.date + ': ' + day.count + ' users'">
                      <span class="bar-value" v-if="day.count > 0">{{ day.count }}</span>
                      <div class="bar-fill bar-fill-users"
                        :style="{ height: getBarHeight(day.count, analyticsData.user_trend) + '%' }"></div>
                      <span class="bar-label" v-if="shouldShowLabel(idx, analyticsData.user_trend)">
                        {{ formatShortDate(day.date) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Property Listing Trend -->
                <div class="chart-block">
                  <h4 class="chart-title">Property Listings (Last {{ analyticsPeriod }} Days)</h4>
                  <div class="bar-chart">
                    <div v-for="(day, idx) in analyticsData.property_trend" :key="'p'+day.date"
                      class="bar-column" :title="day.date + ': ' + day.count + ' properties'">
                      <span class="bar-value" v-if="day.count > 0">{{ day.count }}</span>
                      <div class="bar-fill bar-fill-properties"
                        :style="{ height: getBarHeight(day.count, analyticsData.property_trend) + '%' }"></div>
                      <span class="bar-label" v-if="shouldShowLabel(idx, analyticsData.property_trend)">
                        {{ formatShortDate(day.date) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Top Cities + Top Agents -->
                <div class="two-col-grid">
                  <div class="rank-block">
                    <h4 class="chart-title">Most Active Cities</h4>
                    <div v-if="analyticsData.top_cities.length === 0" class="rank-empty">No data</div>
                    <div v-for="(city, i) in analyticsData.top_cities" :key="city.city" class="rank-row">
                      <span class="rank-num">{{ i + 1 }}</span>
                      <span class="rank-name">{{ city.city }}</span>
                      <div class="rank-bar-wrap">
                        <div class="rank-bar" :style="{ width: getRankBarWidth(city.count, analyticsData.top_cities) + '%' }"></div>
                      </div>
                      <span class="rank-count">{{ city.count }}</span>
                    </div>
                  </div>

                  <div class="rank-block">
                    <h4 class="chart-title">Top Agents by Listings</h4>
                    <div v-if="analyticsData.top_agents.length === 0" class="rank-empty">No data</div>
                    <div v-for="(agent, i) in analyticsData.top_agents" :key="i" class="rank-row">
                      <span class="rank-num">{{ i + 1 }}</span>
                      <span class="rank-name">{{ agent.name }}</span>
                      <span class="rank-count">{{ agent.properties_count }} listings</span>
                      <span class="rank-rating" v-if="agent.average_rating">
                        &#9733; {{ Number(agent.average_rating).toFixed(1) }}
                      </span>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </template>
        </section>

        <!-- ===== AGENT VERIFICATION TAB ===== -->
        <section v-if="activeTab === 'agents'" class="tab-content">
          <!-- Sub-filters -->
          <div class="sub-filters">
            <button
              v-for="f in ['pending', 'verified', 'rejected']"
              :key="f"
              :class="['filter-btn', { active: agentFilter === f }]"
              @click="agentFilter = f"
            >
              {{ f === 'pending' ? '⏳' : f === 'verified' ? '✅' : '❌' }}
              {{ capitalize(f) }}
              <span class="filter-count">{{ getAgentCount(f) }}</span>
            </button>
          </div>

          <div v-if="loadingAgents" class="loading-state">
            <div class="spinner"></div>
            <p>Loading applications...</p>
          </div>

          <!-- PENDING -->
          <template v-if="agentFilter === 'pending' && !loadingAgents">
            <div v-if="pendingAgents.length === 0" class="empty-state">
              <span class="empty-icon">✓</span>
              <p>No pending applications</p>
            </div>

            <div v-for="app in pendingAgents" :key="app.id" class="agent-card">
              <div class="agent-card-header">
                <div class="avatar">{{ (app.applicant_name || app.user?.name || '?').charAt(0) }}</div>
                <div class="agent-info">
                  <h3>{{ app.applicant_name || app.user?.name }}</h3>
                  <p>{{ app.user?.email }}</p>
                </div>
                <div class="header-badges">
                  <span v-if="app.ai_decision" class="ai-badge" :class="'ai-' + app.ai_decision">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    AI: {{ capitalize(app.ai_decision) }}
                  </span>
                  <span v-if="parseAiResult(app.ai_verification_result).verification_method === 'dual_ai'" class="dual-ai-badge">
                    Dual AI
                  </span>
                  <span class="status-pill status-pending">Pending Review</span>
                </div>
              </div>

              <div class="agent-card-body">
                <!-- Two-column layout: left = fields, right = PRC photo -->
                <div class="card-cols">
                  <div class="card-col-main">
                    <div class="field-grid">
                      <div class="field">
                        <label>License Number</label>
                        <p>{{ app.license_number }}</p>
                      </div>
                      <div class="field">
                        <label>PRC ID</label>
                        <p>{{ app.prc_id }}</p>
                      </div>
                      <div class="field">
                        <label>Company</label>
                        <p>{{ app.company_name || 'N/A' }}</p>
                      </div>
                      <div class="field">
                        <label>Accreditation</label>
                        <p>{{ app.accreditation || 'N/A' }}</p>
                      </div>
                      <div class="field">
                        <label>License Expiry</label>
                        <p>{{ formatDate(app.license_expiry_date) }}</p>
                      </div>
                      <div class="field">
                        <label>Applied</label>
                        <p>{{ formatDateTime(app.applied_at) }}</p>
                      </div>
                      <div class="field full-width">
                        <label>Company Address</label>
                        <p>{{ app.company_address || 'N/A' }}</p>
                      </div>
                      <div class="field full-width">
                        <label>Bio</label>
                        <p>{{ app.bio || 'No bio provided' }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- PRC License Photo -->
                  <div v-if="app.prc_license_photo" class="card-col-photo">
                    <label class="photo-label">PRC License Photo</label>
                    <img
                      :src="'/storage/' + app.prc_license_photo"
                      alt="PRC License"
                      class="prc-photo"
                      @click="openPhotoPreview('/storage/' + app.prc_license_photo)"
                    />
                    <p class="photo-hint">Click to enlarge</p>
                  </div>
                </div>

                <!-- ── AI Analysis Panel ── -->
                <div v-if="app.ai_verification_result" class="ai-analysis-panel">
                  <div class="ai-panel-header">
                    <div class="ai-panel-title">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                      RealtyLinkPH Buddy — AI Analysis
                    </div>
                    <!-- Scores row -->
                    <div class="ai-scores">
                      <div v-if="parseAiResult(app.ai_verification_result).confidence != null" class="ai-score-item">
                        <span class="score-label">Confidence</span>
                        <span class="score-val" :class="scoreClass(parseAiResult(app.ai_verification_result).confidence)">
                          {{ Math.round(parseAiResult(app.ai_verification_result).confidence * 100) }}%
                        </span>
                      </div>
                      <div v-if="parseAiResult(app.ai_verification_result).authenticity_score != null" class="ai-score-item">
                        <span class="score-label">Authenticity</span>
                        <span class="score-val" :class="scoreClass(parseAiResult(app.ai_verification_result).authenticity_score)">
                          {{ Math.round(parseAiResult(app.ai_verification_result).authenticity_score * 100) }}%
                        </span>
                      </div>
                      <div v-if="parseAiResult(app.ai_verification_result).extracted?.document_type" class="ai-score-item">
                        <span class="score-label">Document Type</span>
                        <span class="score-val score-neutral">{{ parseAiResult(app.ai_verification_result).extracted.document_type }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Buddy's Reasoning -->
                  <div v-if="parseAiResult(app.ai_verification_result).reasoning" class="ai-reasoning">
                    <div class="buddy-row">
                      <img src="/images/buddy-avatar.jpg" alt="Buddy" class="buddy-mini-avatar" />
                      <div class="buddy-bubble">
                        <strong>Buddy's reasoning:</strong>
                        <p>{{ parseAiResult(app.ai_verification_result).reasoning }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Info Match Checks -->
                  <div v-if="parseAiResult(app.ai_verification_result).extracted" class="ai-checks">
                    <div class="check-item" :class="parseAiResult(app.ai_verification_result).extracted.name_matches ? 'check-pass' : 'check-fail'">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline v-if="parseAiResult(app.ai_verification_result).extracted.name_matches" points="20 6 9 17 4 12"/><g v-else><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g></svg>
                      Name matches form
                    </div>
                    <div class="check-item" :class="parseAiResult(app.ai_verification_result).extracted.name_matches_account ? 'check-pass' : 'check-fail'">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline v-if="parseAiResult(app.ai_verification_result).extracted.name_matches_account" points="20 6 9 17 4 12"/><g v-else><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g></svg>
                      Name matches account
                    </div>
                    <div class="check-item" :class="parseAiResult(app.ai_verification_result).extracted.license_number_matches ? 'check-pass' : 'check-warn'">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline v-if="parseAiResult(app.ai_verification_result).extracted.license_number_matches" points="20 6 9 17 4 12"/><g v-else><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g></svg>
                      License number matches
                    </div>
                    <div class="check-item" :class="parseAiResult(app.ai_verification_result).extracted.profession_is_real_estate ? 'check-pass' : 'check-fail'">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline v-if="parseAiResult(app.ai_verification_result).extracted.profession_is_real_estate" points="20 6 9 17 4 12"/><g v-else><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g></svg>
                      Real estate profession
                    </div>
                    <div v-if="parseAiResult(app.ai_verification_result).extracted.extracted_profession" class="check-item check-info">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                      Detected: {{ parseAiResult(app.ai_verification_result).extracted.extracted_profession }}
                    </div>
                  </div>

                  <!-- Security Features Found -->
                  <div v-if="parseAiResult(app.ai_verification_result).security_features_found?.length" class="ai-tags-section ai-tags-good">
                    <div class="tags-label">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                      Security Features Found
                    </div>
                    <div class="tags-row">
                      <span v-for="f in parseAiResult(app.ai_verification_result).security_features_found" :key="f" class="tag tag-good">{{ f }}</span>
                    </div>
                  </div>

                  <!-- Security Features Missing -->
                  <div v-if="parseAiResult(app.ai_verification_result).security_features_missing?.length" class="ai-tags-section ai-tags-warn">
                    <div class="tags-label">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                      Security Features Missing
                    </div>
                    <div class="tags-row">
                      <span v-for="f in parseAiResult(app.ai_verification_result).security_features_missing" :key="f" class="tag tag-warn">{{ f }}</span>
                    </div>
                  </div>

                  <!-- Red Flags -->
                  <div v-if="parseAiResult(app.ai_verification_result).red_flags_detected?.length" class="ai-tags-section ai-tags-danger">
                    <div class="tags-label">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                      Red Flags Detected
                    </div>
                    <div class="tags-row">
                      <span v-for="f in parseAiResult(app.ai_verification_result).red_flags_detected" :key="f" class="tag tag-danger">{{ f }}</span>
                    </div>
                  </div>

                  <!-- DeepSeek Cross-Verification -->
                  <div v-if="parseAiResult(app.ai_verification_result).deepseek_decision" class="deepseek-section">
                    <div class="deepseek-header">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                      DeepSeek Cross-Verification
                      <span class="deepseek-verdict" :class="'ds-' + (parseAiResult(app.ai_verification_result).deepseek_decision || '').replace(/_/g, '-')">
                        {{ parseAiResult(app.ai_verification_result).deepseek_decision?.replace(/_/g, ' ') }}
                      </span>
                    </div>
                    <p v-if="parseAiResult(app.ai_verification_result).deepseek_reasoning" class="deepseek-reasoning">
                      {{ parseAiResult(app.ai_verification_result).deepseek_reasoning }}
                    </p>
                    <div v-if="parseAiResult(app.ai_verification_result).deepseek_concerns?.length" class="tags-row" style="margin-top:6px">
                      <span v-for="c in parseAiResult(app.ai_verification_result).deepseek_concerns" :key="c" class="tag tag-warn">{{ c }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="agent-card-actions">
                <button
                  @click="approveAgent(app)"
                  class="btn-approve"
                  :disabled="processingId === app.id"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                  {{ processingId === app.id ? 'Approving...' : 'Approve Application' }}
                </button>
                <button @click="openRejectModal(app)" class="btn-reject">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  Reject
                </button>
              </div>
            </div>
          </template>

          <!-- VERIFIED -->
          <template v-if="agentFilter === 'verified' && !loadingAgents">
            <div v-if="verifiedAgents.length === 0" class="empty-state">
              <p>No verified agents yet</p>
            </div>
            <div v-for="app in verifiedAgents" :key="app.id" class="agent-card card-verified">
              <div class="agent-card-header">
                <div class="avatar avatar-verified">{{ (app.applicant_name || app.user?.name || '?').charAt(0) }}</div>
                <div class="agent-info">
                  <h3>{{ app.applicant_name || app.user?.name }}</h3>
                  <p>{{ app.user?.email }}</p>
                </div>
                <span class="status-pill status-verified">Verified</span>
              </div>
              <div class="agent-card-body">
                <div class="field-grid">
                  <div class="field">
                    <label>License Number</label>
                    <p>{{ app.license_number }}</p>
                  </div>
                  <div class="field">
                    <label>Verified</label>
                    <p>{{ formatDateTime(app.verified_at) }}</p>
                  </div>
                  <div class="field">
                    <label>AI Decision</label>
                    <p>{{ app.ai_decision || 'N/A' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <!-- REJECTED -->
          <template v-if="agentFilter === 'rejected' && !loadingAgents">
            <div v-if="rejectedAgents.length === 0" class="empty-state">
              <p>No rejected applications</p>
            </div>
            <div v-for="app in rejectedAgents" :key="app.id" class="agent-card card-rejected">
              <div class="agent-card-header">
                <div class="avatar avatar-rejected">{{ (app.applicant_name || app.user?.name || '?').charAt(0) }}</div>
                <div class="agent-info">
                  <h3>{{ app.applicant_name || app.user?.name }}</h3>
                  <p>{{ app.user?.email }}</p>
                </div>
                <span class="status-pill status-rejected">Rejected</span>
              </div>
              <div class="agent-card-body">
                <div class="field-grid">
                  <div class="field full-width">
                    <label>Rejection Reason</label>
                    <p>{{ app.rejection_reason || 'No reason provided' }}</p>
                  </div>
                  <div class="field">
                    <label>Rejected</label>
                    <p>{{ formatDateTime(app.rejected_at) }}</p>
                  </div>
                  <div class="field">
                    <label>AI Decision</label>
                    <p>{{ app.ai_decision || 'N/A' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </section>

        <!-- ===== USER MANAGEMENT TAB ===== -->
        <section v-if="activeTab === 'users'" class="tab-content">
          <!-- Filters -->
          <div class="filter-bar">
            <div class="search-box">
              <input
                v-model="userSearch"
                type="text"
                placeholder="Search by name or email..."
                class="search-input"
                @input="debounceUserSearch"
              />
            </div>
            <select v-model="userRoleFilter" class="filter-select" @change="loadUsers">
              <option value="">All Roles</option>
              <option value="buyer">Buyer</option>
              <option value="agent">Agent</option>
              <option value="admin">Admin</option>
            </select>
            <select v-model="userStatusFilter" class="filter-select" @change="loadUsers">
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
            </select>
            <button @click="showCreateAdminModal = true" class="btn-create-admin">+ Create Admin</button>
          </div>

          <div v-if="loadingUsers" class="loading-state">
            <div class="spinner"></div>
            <p>Loading users...</p>
          </div>

          <template v-else>
            <div v-if="users.length === 0" class="empty-state">
              <p>No users found</p>
            </div>

            <div v-else class="table-wrapper">
              <table class="data-table">
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
                  <tr v-for="user in users" :key="user.id">
                    <td class="td-name">{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>
                      <span class="role-badge" :class="'role-' + user.role">{{ capitalize(user.role) }}</span>
                    </td>
                    <td>
                      <span class="status-badge" :class="'status-' + user.status">{{ capitalize(user.status) }}</span>
                    </td>
                    <td>{{ formatDate(user.created_at) }}</td>
                    <td class="td-actions">
                      <button
                        v-if="user.status === 'active'"
                        @click="suspendUser(user)"
                        class="btn-sm btn-suspend"
                        :disabled="processingId === user.id"
                      >
                        Suspend
                      </button>
                      <button
                        v-else
                        @click="activateUser(user)"
                        class="btn-sm btn-activate"
                        :disabled="processingId === user.id"
                      >
                        Activate
                      </button>
                      <button
                        @click="confirmDeleteUser(user)"
                        class="btn-sm btn-delete"
                        :disabled="processingId === user.id"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="usersPagination.last_page > 1" class="pagination">
              <button
                :disabled="usersPagination.current_page <= 1"
                @click="loadUsers(usersPagination.current_page - 1)"
                class="page-btn"
              >
                Prev
              </button>
              <span class="page-info">
                Page {{ usersPagination.current_page }} of {{ usersPagination.last_page }}
                ({{ usersPagination.total }} total)
              </span>
              <button
                :disabled="usersPagination.current_page >= usersPagination.last_page"
                @click="loadUsers(usersPagination.current_page + 1)"
                class="page-btn"
              >
                Next
              </button>
            </div>
          </template>
        </section>

        <!-- ===== PROPERTIES TAB ===== -->
        <section v-if="activeTab === 'properties'" class="tab-content">
          <!-- Filters -->
          <div class="filter-bar">
            <select v-model="propertyStatusFilter" class="filter-select" @change="loadProperties">
              <option value="">All Statuses</option>
              <option value="available">Available</option>
              <option value="sold">Sold</option>
              <option value="rented">Rented</option>
              <option value="unlisted">Unlisted</option>
            </select>
          </div>

          <div v-if="loadingProperties" class="loading-state">
            <div class="spinner"></div>
            <p>Loading properties...</p>
          </div>

          <template v-else>
            <div v-if="properties.length === 0" class="empty-state">
              <p>No properties found</p>
            </div>

            <div v-else class="table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Photo</th>
                    <th>Title</th>
                    <th>Agent</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>City</th>
                    <th>Featured</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="prop in properties" :key="prop.id">
                    <td>
                      <img
                        v-if="getCoverPhoto(prop)"
                        :src="getCoverPhoto(prop)"
                        class="property-thumb"
                        alt="Cover"
                      />
                      <div v-else class="no-photo">No Photo</div>
                    </td>
                    <td class="td-name">{{ prop.title }}</td>
                    <td>{{ prop.agent?.user?.name || 'N/A' }}</td>
                    <td>{{ formatPrice(prop.price) }}</td>
                    <td>
                      <span class="status-badge" :class="'status-' + prop.status">{{ capitalize(prop.status) }}</span>
                    </td>
                    <td>{{ prop.city }}</td>
                    <td>
                      <button
                        @click="toggleFeatured(prop)"
                        class="btn-feature"
                        :class="{ featured: prop.featured }"
                        :disabled="processingId === prop.id"
                      >
                        {{ prop.featured ? 'Featured' : 'Feature' }}
                      </button>
                    </td>
                    <td>
                      <button
                        @click="confirmDeleteProperty(prop)"
                        class="btn-sm btn-delete"
                        :disabled="processingId === prop.id"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="propertiesPagination.last_page > 1" class="pagination">
              <button
                :disabled="propertiesPagination.current_page <= 1"
                @click="loadProperties(propertiesPagination.current_page - 1)"
                class="page-btn"
              >
                Prev
              </button>
              <span class="page-info">
                Page {{ propertiesPagination.current_page }} of {{ propertiesPagination.last_page }}
                ({{ propertiesPagination.total }} total)
              </span>
              <button
                :disabled="propertiesPagination.current_page >= propertiesPagination.last_page"
                @click="loadProperties(propertiesPagination.current_page + 1)"
                class="page-btn"
              >
                Next
              </button>
            </div>
          </template>
        </section>

        <!-- ===== AUDIT LOGS TAB ===== -->
        <section v-if="activeTab === 'audit'" class="tab-content">
          <!-- Filters -->
          <div class="filter-bar">
            <select v-model="auditActionFilter" class="filter-select" @change="loadAuditLogs">
              <option value="">All Actions</option>
              <option value="create">Create</option>
              <option value="update">Update</option>
              <option value="delete">Delete</option>
              <option value="verify">Verify</option>
              <option value="approve">Approve</option>
              <option value="reject">Reject</option>
            </select>
            <input v-model="auditStartDate" type="date" class="filter-input" @change="loadAuditLogs" />
            <input v-model="auditEndDate" type="date" class="filter-input" @change="loadAuditLogs" />
          </div>

          <div v-if="loadingAudit" class="loading-state">
            <div class="spinner"></div>
            <p>Loading audit logs...</p>
          </div>

          <template v-else>
            <div v-if="auditLogs.length === 0" class="empty-state">
              <p>No audit logs found</p>
            </div>

            <div v-else class="table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="log in auditLogs" :key="log.id">
                    <td>{{ formatDateTime(log.created_at) }}</td>
                    <td>{{ log.user?.name || 'System' }}</td>
                    <td>
                      <span class="action-badge" :class="'action-' + log.action_type">
                        {{ capitalize(log.action_type) }}
                      </span>
                    </td>
                    <td>{{ log.model_type }} #{{ log.model_id }}</td>
                    <td>{{ log.description }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="auditPagination.last_page > 1" class="pagination">
              <button
                :disabled="auditPagination.current_page <= 1"
                @click="loadAuditLogs(auditPagination.current_page - 1)"
                class="page-btn"
              >
                Prev
              </button>
              <span class="page-info">
                Page {{ auditPagination.current_page }} of {{ auditPagination.last_page }}
                ({{ auditPagination.total }} total)
              </span>
              <button
                :disabled="auditPagination.current_page >= auditPagination.last_page"
                @click="loadAuditLogs(auditPagination.current_page + 1)"
                class="page-btn"
              >
                Next
              </button>
            </div>
          </template>
        </section>

        <!-- ===== PHOTO MODERATION TAB ===== -->
        <section v-if="activeTab === 'photos'" class="tab-content">
          <div v-if="loadingPhotos" class="loading-state">
            <div class="spinner"></div>
            <p>Loading pending photos...</p>
          </div>

          <template v-else>
            <div v-if="pendingPhotos.length === 0" class="empty-state">
              <span class="empty-icon">✓</span>
              <p>No photos pending review</p>
            </div>

            <div v-for="photo in pendingPhotos" :key="photo.id" class="photo-mod-card">
              <div class="photo-mod-img-wrap">
                <img
                  :src="photo.photo_url ? photo.photo_url : '/storage/' + photo.photo_path"
                  alt="Property Photo"
                  class="photo-mod-img"
                  @click="openPhotoPreview(photo.photo_url ? photo.photo_url : '/storage/' + photo.photo_path)"
                />
                <span class="photo-mod-hint">Click to enlarge</span>
              </div>
              <div class="photo-mod-info">
                <div class="photo-mod-meta">
                  <div class="field">
                    <label>Property</label>
                    <p>{{ photo.property?.title || 'Unknown Property' }}</p>
                  </div>
                  <div class="field">
                    <label>Agent</label>
                    <p>{{ photo.property?.agent?.user?.name || 'N/A' }}</p>
                  </div>
                  <div class="field">
                    <label>Uploaded</label>
                    <p>{{ formatDateTime(photo.created_at) }}</p>
                  </div>
                  <div class="field">
                    <label>Cover Photo</label>
                    <p>{{ photo.is_cover ? 'Yes' : 'No' }}</p>
                  </div>
                </div>
                <div class="photo-mod-actions">
                  <button
                    @click="approvePhotoItem(photo)"
                    class="btn-approve"
                    :disabled="processingId === photo.id"
                  >
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ processingId === photo.id ? 'Approving...' : 'Approve' }}
                  </button>
                  <button
                    @click="openPhotoRejectModal(photo)"
                    class="btn-reject"
                    :disabled="processingId === photo.id"
                  >
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Reject
                  </button>
                </div>
              </div>
            </div>
          </template>
        </section>

        <!-- ===== FLAGGED PROPERTIES TAB ===== -->
        <section v-if="activeTab === 'flagged'" class="tab-content">
          <div class="sub-filters">
            <button v-for="f in ['pending_review', 'approved', 'dismissed']" :key="f"
              :class="['filter-btn', { active: flaggedFilter === f }]"
              @click="flaggedFilter = f; loadFlaggedProperties()">
              {{ f === 'pending_review' ? '🚩 Pending' : f === 'approved' ? '✅ Cleared' : '⚡ Actioned' }}
            </button>
          </div>

          <div v-if="loadingFlagged" class="loading-state">
            <div class="spinner"></div>
            <p>Loading flagged properties...</p>
          </div>

          <template v-else>
            <div v-if="flaggedProperties.length === 0" class="empty-state">
              <span class="empty-icon">✓</span>
              <p>No flagged properties in this category</p>
            </div>

            <div v-for="flag in flaggedProperties" :key="flag.id" class="flag-card">
              <div class="flag-card-header">
                <img v-if="getCoverPhoto(flag.property)" :src="getCoverPhoto(flag.property)" class="property-thumb flag-thumb" alt="Property" />
                <div v-else class="no-photo flag-thumb">No Photo</div>
                <div class="flag-info">
                  <h3>{{ flag.property?.title || 'Deleted Property' }}</h3>
                  <p>{{ flag.property?.city }}, {{ flag.property?.province }} — {{ formatPrice(flag.property?.price) }}</p>
                  <p class="flag-agent">Agent: {{ flag.property?.agent?.user?.name || 'N/A' }}</p>
                </div>
                <div class="flag-meta">
                  <span class="confidence-badge" :class="getConfidenceClass(flag.ai_confidence)">
                    AI Confidence: {{ flag.ai_confidence }}%
                  </span>
                  <p class="flag-date">Flagged: {{ formatDateTime(flag.flagged_at) }}</p>
                </div>
              </div>

              <div class="flag-reason-box">
                <label>AI Flag Reason</label>
                <p>{{ flag.flag_reason }}</p>
              </div>

              <div v-if="flag.ai_raw_response && flag.ai_raw_response.red_flags" class="flag-details">
                <label>Red Flags Detected</label>
                <ul>
                  <li v-for="(rf, i) in flag.ai_raw_response.red_flags" :key="i">{{ rf }}</li>
                </ul>
              </div>

              <div v-if="flag.status === 'pending_review'" class="flag-actions">
                <button @click="openFlagReviewModal(flag, 'approve')" class="btn-approve">
                  Clear Flag (Legit)
                </button>
                <button @click="openFlagReviewModal(flag, 'dismiss')" class="btn-reject">
                  Take Action
                </button>
              </div>
              <div v-else class="flag-reviewed-info">
                <span>Reviewed by {{ flag.reviewer?.name || 'Admin' }} — {{ formatDateTime(flag.reviewed_at) }}</span>
                <p v-if="flag.admin_notes" class="flag-admin-note">Note: {{ flag.admin_notes }}</p>
              </div>
            </div>

            <div v-if="flaggedPagination.last_page > 1" class="pagination">
              <button :disabled="flaggedPagination.current_page <= 1"
                @click="loadFlaggedProperties(flaggedPagination.current_page - 1)" class="page-btn">Prev</button>
              <span class="page-info">Page {{ flaggedPagination.current_page }} of {{ flaggedPagination.last_page }}
                ({{ flaggedPagination.total }} total)</span>
              <button :disabled="flaggedPagination.current_page >= flaggedPagination.last_page"
                @click="loadFlaggedProperties(flaggedPagination.current_page + 1)" class="page-btn">Next</button>
            </div>
          </template>
        </section>
      </div>
    </main>

    <!-- ========== FLAG REVIEW MODAL ========== -->
    <div v-if="showFlagReviewModal" class="modal-backdrop" @click.self="showFlagReviewModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>{{ flagReviewAction === 'approve' ? 'Clear Flag' : 'Take Action on Flagged Property' }}</h3>
          <button @click="showFlagReviewModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p><strong>Property:</strong> {{ selectedFlag?.property?.title }}</p>
          <p class="modal-info"><strong>Reason:</strong> {{ selectedFlag?.flag_reason }}</p>

          <template v-if="flagReviewAction === 'dismiss'">
            <div class="checkbox-field">
              <input type="checkbox" v-model="flagSuspendAgent" id="suspendAgent" />
              <label for="suspendAgent">Suspend the agent's account</label>
            </div>
            <div class="checkbox-field">
              <input type="checkbox" v-model="flagDeleteProperty" id="deleteProperty" />
              <label for="deleteProperty">Delete this property listing</label>
            </div>
          </template>

          <div class="form-field">
            <label>Admin Notes (optional)</label>
            <textarea v-model="flagAdminNotes" rows="3" class="textarea-field"
              placeholder="Add a note about this review decision..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showFlagReviewModal = false" class="btn-cancel">Cancel</button>
          <button @click="confirmFlagReview" :disabled="processingId"
            :class="flagReviewAction === 'approve' ? 'btn-approve' : 'btn-reject'">
            {{ processingId ? 'Processing...' : (flagReviewAction === 'approve' ? 'Clear Flag' : 'Confirm Action') }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========== CREATE ADMIN MODAL ========== -->
    <div v-if="showCreateAdminModal" class="modal-backdrop" @click.self="showCreateAdminModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Create Admin Account</h3>
          <button @click="showCreateAdminModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-field">
            <label>Full Name *</label>
            <input v-model="createAdminForm.name" type="text" class="text-input" placeholder="Enter full name" />
            <p v-if="createAdminErrors.name" class="field-error">{{ createAdminErrors.name[0] }}</p>
          </div>
          <div class="form-field">
            <label>Email Address *</label>
            <input v-model="createAdminForm.email" type="email" class="text-input" placeholder="Enter email" />
            <p v-if="createAdminErrors.email" class="field-error">{{ createAdminErrors.email[0] }}</p>
          </div>
          <div class="form-field">
            <label>Phone (optional)</label>
            <input v-model="createAdminForm.phone" type="text" class="text-input" placeholder="Enter phone number" />
          </div>
          <div class="form-field">
            <label>Password *</label>
            <input v-model="createAdminForm.password" type="password" class="text-input" placeholder="Min 8 characters" />
            <p v-if="createAdminErrors.password" class="field-error">{{ createAdminErrors.password[0] }}</p>
          </div>
          <div class="form-field">
            <label>Confirm Password *</label>
            <input v-model="createAdminForm.password_confirmation" type="password" class="text-input" placeholder="Confirm password" />
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showCreateAdminModal = false" class="btn-cancel">Cancel</button>
          <button @click="submitCreateAdmin" :disabled="creatingAdmin" class="btn-approve">
            {{ creatingAdmin ? 'Creating...' : 'Create Admin' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========== REJECT MODAL ========== -->
    <div v-if="showRejectModal" class="modal-backdrop" @click.self="showRejectModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Reject Application</h3>
          <button @click="showRejectModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p v-if="selectedAgent" class="modal-info">
            Agent: <strong>{{ selectedAgent.applicant_name || selectedAgent.user?.name }}</strong>
          </p>
          <div class="form-field">
            <label>Reason for Rejection</label>
            <textarea
              v-model="rejectionReason"
              placeholder="Explain why this application is rejected..."
              rows="4"
              class="textarea-field"
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showRejectModal = false" class="btn-cancel">Cancel</button>
          <button
            @click="confirmReject"
            class="btn-reject"
            :disabled="!rejectionReason.trim() || processingId"
          >
            {{ processingId ? 'Rejecting...' : 'Reject Application' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========== DELETE CONFIRM MODAL ========== -->
    <div v-if="showDeleteModal" class="modal-backdrop" @click.self="showDeleteModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Confirm Delete</h3>
          <button @click="showDeleteModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong>{{ deleteTargetName }}</strong>?</p>
          <p class="modal-warning">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button @click="showDeleteModal = false" class="btn-cancel">Cancel</button>
          <button @click="confirmDelete" class="btn-reject" :disabled="processingId">
            {{ processingId ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========== PHOTO PREVIEW MODAL ========== -->
    <div v-if="photoPreviewUrl" class="modal-backdrop" @click.self="photoPreviewUrl = null">
      <div class="photo-preview-box">
        <button @click="photoPreviewUrl = null" class="close-btn photo-close">&times;</button>
        <img :src="photoPreviewUrl" alt="PRC License Preview" class="photo-preview-img" />
      </div>
    </div>

    <!-- ========== PHOTO REJECT MODAL ========== -->
    <div v-if="showPhotoRejectModal" class="modal-backdrop" @click.self="showPhotoRejectModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Reject Photo</h3>
          <button @click="showPhotoRejectModal = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-field">
            <label>Reason for rejection (optional)</label>
            <textarea v-model="photoRejectReason" rows="3" class="textarea-field" placeholder="e.g. Blurry, inappropriate content, not related to property..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showPhotoRejectModal = false" class="btn-cancel">Cancel</button>
          <button @click="confirmPhotoReject" :disabled="processingId" class="btn-reject">
            {{ processingId ? 'Rejecting...' : 'Reject Photo' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========== GENERIC CONFIRM MODAL ========== -->
    <div v-if="confirmModal.show" class="modal-backdrop" @click.self="confirmModal.show = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>{{ confirmModal.title }}</h3>
          <button @click="confirmModal.show = false" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <p>{{ confirmModal.message }}</p>
          <p v-if="confirmModal.warning" class="modal-warning">{{ confirmModal.warning }}</p>
        </div>
        <div class="modal-footer">
          <button @click="confirmModal.show = false" class="btn-cancel">Cancel</button>
          <button
            @click="confirmModal.onConfirm(); confirmModal.show = false"
            :class="confirmModal.danger ? 'btn-reject' : 'btn-approve'"
          >{{ confirmModal.confirmLabel || 'Confirm' }}</button>
        </div>
      </div>
    </div>

    <!-- ========== TOAST NOTIFICATIONS ========== -->
    <div class="toast-container">
      <transition-group name="toast">
        <div v-for="toast in toasts" :key="toast.id" class="toast" :class="'toast-' + toast.type">
          <svg v-if="toast.type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          <svg v-else-if="toast.type === 'error'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span>{{ toast.message }}</span>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
import { adminAPI } from '../src/services/api';

export default {
  name: 'AdminDashboard',

  data() {
    return {
      sidebarOpen: false,
      // Navigation
      activeTab: 'overview',
      navTabs: [
        { id: 'overview',    label: 'Overview',           icon: '', badge: null },
        { id: 'agents',      label: 'Agent Verification', icon: '', badge: null },
        { id: 'users',       label: 'User Management',    icon: '', badge: null },
        { id: 'properties',  label: 'Properties',         icon: '', badge: null },
        { id: 'photos',      label: 'Photo Moderation',   icon: '', badge: null },
        { id: 'flagged',     label: 'Flagged Properties', icon: '', badge: null },
        { id: 'audit',       label: 'Audit Logs',         icon: '', badge: null },
      ],

      // User info
      userName: 'Admin',

      // Overview
      overview: {
        total_users: 0,
        total_agents: 0,
        verified_agents: 0,
        pending_agents: 0,
        total_properties: 0,
        active_properties: 0,
        pending_photos: 0,
        flagged_properties: 0,
        users_by_role: { buyers: 0, agents: 0, admins: 0 },
      },
      periodStats: {
        users_registered: 0,
        properties_added: 0,
        messages_sent: 0,
        viewings_requested: 0,
      },
      recentActivity: [],

      // Agent Verification
      agentFilter: 'pending',
      pendingAgents: [],
      verifiedAgents: [],
      rejectedAgents: [],

      // User Management
      userSearch: '',
      userRoleFilter: '',
      userStatusFilter: '',
      users: [],
      usersPagination: { current_page: 1, last_page: 1, total: 0 },
      userSearchTimeout: null,

      // Properties
      propertyStatusFilter: '',
      properties: [],
      propertiesPagination: { current_page: 1, last_page: 1, total: 0 },

      // Audit Logs
      auditActionFilter: '',
      auditStartDate: '',
      auditEndDate: '',
      auditLogs: [],
      auditPagination: { current_page: 1, last_page: 1, total: 0 },

      // Analytics
      analyticsData: {
        user_trend: [],
        property_trend: [],
        top_cities: [],
        top_agents: [],
      },
      analyticsPeriod: 30,
      loadingAnalytics: false,

      // Photo Moderation
      pendingPhotos: [],
      loadingPhotos: false,
      photoActionNote: '',
      selectedPhotoId: null,
      showPhotoRejectModal: false,
      photoRejectReason: '',

      // Flagged Properties
      flaggedProperties: [],
      flaggedFilter: 'pending_review',
      flaggedPagination: { current_page: 1, last_page: 1, total: 0 },
      loadingFlagged: false,

      // Flag Review Modal
      showFlagReviewModal: false,
      selectedFlag: null,
      flagReviewAction: '',
      flagAdminNotes: '',
      flagSuspendAgent: false,
      flagDeleteProperty: false,

      // Create Admin Modal
      showCreateAdminModal: false,
      createAdminForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        phone: '',
      },
      createAdminErrors: {},
      creatingAdmin: false,

      // Modals
      showRejectModal: false,
      selectedAgent: null,
      rejectionReason: '',
      showDeleteModal: false,
      deleteTarget: null,
      deleteType: '',
      photoPreviewUrl: null,

      // Loading
      loadingOverview: false,
      loadingAgents: false,
      loadingUsers: false,
      loadingProperties: false,
      loadingAudit: false,
      processingId: null,

      // Toast notifications
      toasts: [],
      toastCounter: 0,

      // Generic confirm modal
      confirmModal: {
        show: false,
        title: '',
        message: '',
        warning: '',
        confirmLabel: 'Confirm',
        danger: false,
        onConfirm: () => {},
      },
    };
  },

  computed: {
    currentTabTitle() {
      const tab = this.navTabs.find((t) => t.id === this.activeTab);
      return tab ? tab.label : 'Admin Dashboard';
    },
    deleteTargetName() {
      if (!this.deleteTarget) return '';
      if (this.deleteType === 'user') return this.deleteTarget.name;
      if (this.deleteType === 'property') return this.deleteTarget.title;
      return '';
    },
  },

  watch: {
    activeTab(newTab) {
      if (newTab === 'overview') this.loadOverview();
      else if (newTab === 'agents') this.loadAgentData();
      else if (newTab === 'users') this.loadUsers();
      else if (newTab === 'properties') this.loadProperties();
      else if (newTab === 'photos') this.loadPendingPhotos();
      else if (newTab === 'flagged') this.loadFlaggedProperties();
      else if (newTab === 'audit') this.loadAuditLogs();
    },
    agentFilter() {
      // Data already loaded when tab was switched
    },
  },

  methods: {
    // ========================
    // Overview
    // ========================
    async loadOverview() {
      this.loadingOverview = true;
      try {
        const [overviewRes, statsRes, activityRes, analyticsRes] = await Promise.all([
          adminAPI.getDashboardOverview(),
          adminAPI.getDashboardStats({ days: 30 }),
          adminAPI.getRecentActivity({ limit: 10 }),
          adminAPI.getAnalyticsData({ period: this.analyticsPeriod }),
        ]);
        this.overview = overviewRes.data.data || this.overview;
        this.periodStats = statsRes.data.data || this.periodStats;
        this.recentActivity = activityRes.data.data || [];
        this.analyticsData = analyticsRes.data.data || this.analyticsData;

        // Update nav badge for pending agents
        const pendingTab = this.navTabs.find((t) => t.id === 'agents');
        if (pendingTab) pendingTab.badge = this.overview.pending_agents > 0 ? this.overview.pending_agents : null;

        // Update nav badge for pending photos
        const photosTab = this.navTabs.find((t) => t.id === 'photos');
        if (photosTab) photosTab.badge = this.overview.pending_photos > 0 ? this.overview.pending_photos : null;

        // Update nav badge for flagged properties
        const flaggedTab = this.navTabs.find((t) => t.id === 'flagged');
        if (flaggedTab && this.overview.flagged_properties > 0) {
          flaggedTab.badge = this.overview.flagged_properties;
        } else if (flaggedTab) {
          flaggedTab.badge = null;
        }
      } catch (error) {
        console.error('Failed to load overview:', error);
      } finally {
        this.loadingOverview = false;
      }
    },

    // ========================
    // Agent Verification
    // ========================
    async loadAgentData() {
      this.loadingAgents = true;
      try {
        const [pendingRes, verifiedRes, rejectedRes] = await Promise.all([
          adminAPI.getPendingAgents(),
          adminAPI.getVerifiedAgents(),
          adminAPI.getRejectedAgents(),
        ]);
        this.pendingAgents = pendingRes.data.data?.data || pendingRes.data.data || pendingRes.data.applications || [];
        this.verifiedAgents = verifiedRes.data.data?.data || verifiedRes.data.data || verifiedRes.data.agents || [];
        this.rejectedAgents = rejectedRes.data.data?.data || rejectedRes.data.data || rejectedRes.data.applications || [];
      } catch (error) {
        console.error('Failed to load agents:', error);
      } finally {
        this.loadingAgents = false;
      }
    },

    getAgentCount(filter) {
      if (filter === 'pending') return this.pendingAgents.length;
      if (filter === 'verified') return this.verifiedAgents.length;
      if (filter === 'rejected') return this.rejectedAgents.length;
      return 0;
    },

    approveAgent(app) {
      const name = app.applicant_name || app.user?.name;
      this.askConfirm({
        title: 'Approve Agent',
        message: `Approve ${name} as a verified agent?`,
        confirmLabel: 'Approve',
        danger: false,
        onConfirm: async () => {
          try {
            this.processingId = app.id;
            await adminAPI.verifyAgent(app.id);
            this.showToast(`${name} has been approved as an agent.`);
            await this.loadAgentData();
          } catch (error) {
            this.showToast('Failed to approve agent: ' + (error.response?.data?.message || error.message), 'error');
          } finally {
            this.processingId = null;
          }
        },
      });
    },

    openRejectModal(app) {
      this.selectedAgent = app;
      this.rejectionReason = '';
      this.showRejectModal = true;
    },

    async confirmReject() {
      if (!this.rejectionReason.trim()) return;
      try {
        this.processingId = this.selectedAgent.id;
        await adminAPI.rejectAgent(this.selectedAgent.id, this.rejectionReason);
        this.showToast('Application has been rejected.');
        this.showRejectModal = false;
        await this.loadAgentData();
      } catch (error) {
        console.error('Failed to reject:', error);
        this.showToast('Failed to reject: ' + (error.response?.data?.message || error.message), 'error');
      } finally {
        this.processingId = null;
      }
    },

    formatAiResult(result) {
      if (!result) return 'N/A';
      try {
        const parsed = typeof result === 'string' ? JSON.parse(result) : result;
        return JSON.stringify(parsed, null, 2);
      } catch {
        return result;
      }
    },

    parseAiResult(result) {
      if (!result) return {};
      try {
        return typeof result === 'string' ? JSON.parse(result) : result;
      } catch {
        return {};
      }
    },

    scoreClass(score) {
      if (score == null) return 'score-neutral';
      if (score >= 0.75) return 'score-good';
      if (score >= 0.5) return 'score-warn';
      return 'score-bad';
    },

    openPhotoPreview(url) {
      this.photoPreviewUrl = url;
    },

    // ========================
    // User Management
    // ========================
    async loadUsers(page = 1) {
      this.loadingUsers = true;
      try {
        const res = await adminAPI.getUsers({
          search: this.userSearch,
          role: this.userRoleFilter,
          status: this.userStatusFilter,
          page,
          per_page: 15,
        });
        const data = res.data.data;
        this.users = data.data || data || [];
        this.usersPagination = {
          current_page: data.current_page || 1,
          last_page: data.last_page || 1,
          total: data.total || 0,
        };
      } catch (error) {
        console.error('Failed to load users:', error);
      } finally {
        this.loadingUsers = false;
      }
    },

    debounceUserSearch() {
      clearTimeout(this.userSearchTimeout);
      this.userSearchTimeout = setTimeout(() => {
        this.loadUsers();
      }, 400);
    },

    suspendUser(user) {
      this.askConfirm({
        title: 'Suspend User',
        message: `Suspend ${user.name}? They will lose access until reactivated.`,
        confirmLabel: 'Suspend',
        danger: true,
        onConfirm: async () => {
          try {
            this.processingId = user.id;
            await adminAPI.suspendUser(user.id);
            this.showToast(`${user.name} has been suspended.`, 'info');
            await this.loadUsers(this.usersPagination.current_page);
          } catch (error) {
            this.showToast('Failed to suspend user.', 'error');
          } finally {
            this.processingId = null;
          }
        },
      });
    },

    activateUser(user) {
      this.askConfirm({
        title: 'Reactivate User',
        message: `Reactivate ${user.name}? They will regain access to the platform.`,
        confirmLabel: 'Reactivate',
        danger: false,
        onConfirm: async () => {
          try {
            this.processingId = user.id;
            await adminAPI.activateUser(user.id);
            this.showToast(`${user.name} has been reactivated.`);
            await this.loadUsers(this.usersPagination.current_page);
          } catch (error) {
            this.showToast('Failed to reactivate user.', 'error');
          } finally {
            this.processingId = null;
          }
        },
      });
    },

    confirmDeleteUser(user) {
      this.deleteTarget = user;
      this.deleteType = 'user';
      this.showDeleteModal = true;
    },

    // ========================
    // Properties
    // ========================
    async loadProperties(page = 1) {
      this.loadingProperties = true;
      try {
        const res = await adminAPI.getProperties({
          status: this.propertyStatusFilter,
          page,
          per_page: 15,
        });
        const data = res.data.data;
        this.properties = data.data || data || [];
        this.propertiesPagination = {
          current_page: data.current_page || 1,
          last_page: data.last_page || 1,
          total: data.total || 0,
        };
      } catch (error) {
        console.error('Failed to load properties:', error);
      } finally {
        this.loadingProperties = false;
      }
    },

    getCoverPhoto(property) {
      const cover = property.photos?.find((p) => p.is_cover);
      const photo = cover || property.photos?.[0];
      if (!photo) return null;
      const path = photo.photo_url || photo.photo_path;
      if (!path) return null;
      return path.startsWith('http') ? path : '/storage/' + path;
    },

    formatPrice(price) {
      if (!price) return 'N/A';
      return '\u20B1' + Number(price).toLocaleString();
    },

    async toggleFeatured(prop) {
      try {
        this.processingId = prop.id;
        if (prop.featured) {
          await adminAPI.unfeatureProperty(prop.id);
          this.showToast(`"${prop.title}" removed from featured.`, 'info');
        } else {
          await adminAPI.featureProperty(prop.id);
          this.showToast(`"${prop.title}" is now featured.`);
        }
        prop.featured = !prop.featured;
      } catch (error) {
        console.error('Failed to toggle featured:', error);
        this.showToast('Failed to update featured status.', 'error');
      } finally {
        this.processingId = null;
      }
    },

    confirmDeleteProperty(prop) {
      this.deleteTarget = prop;
      this.deleteType = 'property';
      this.showDeleteModal = true;
    },

    // ========================
    // Delete Confirm
    // ========================
    async confirmDelete() {
      if (!this.deleteTarget) return;
      try {
        this.processingId = this.deleteTarget.id;
        if (this.deleteType === 'user') {
          await adminAPI.deleteUser(this.deleteTarget.id);
          this.showToast(`${this.deleteTarget.name} has been deleted.`, 'info');
          await this.loadUsers(this.usersPagination.current_page);
        } else if (this.deleteType === 'property') {
          await adminAPI.deleteProperty(this.deleteTarget.id);
          this.showToast(`"${this.deleteTarget.title}" has been deleted.`, 'info');
          await this.loadProperties(this.propertiesPagination.current_page);
        }
        this.showDeleteModal = false;
      } catch (error) {
        console.error('Delete failed:', error);
        this.showToast('Failed to delete: ' + (error.response?.data?.message || error.message), 'error');
      } finally {
        this.processingId = null;
      }
    },

    // ========================
    // Audit Logs
    // ========================
    async loadAuditLogs(page = 1) {
      this.loadingAudit = true;
      try {
        const params = { page, per_page: 20 };
        if (this.auditActionFilter) params.action_type = this.auditActionFilter;
        if (this.auditStartDate) params.start_date = this.auditStartDate;
        if (this.auditEndDate) params.end_date = this.auditEndDate;

        const res = await adminAPI.getAuditLogs(params);
        const data = res.data.data;
        this.auditLogs = data.data || data || [];
        this.auditPagination = {
          current_page: data.current_page || 1,
          last_page: data.last_page || 1,
          total: data.total || 0,
        };
      } catch (error) {
        console.error('Failed to load audit logs:', error);
      } finally {
        this.loadingAudit = false;
      }
    },

    // ========================
    // Analytics
    // ========================
    async loadAnalytics() {
      this.loadingAnalytics = true;
      try {
        const res = await adminAPI.getAnalyticsData({ period: this.analyticsPeriod });
        this.analyticsData = res.data.data || this.analyticsData;
      } catch (e) {
        console.error('Failed to load analytics:', e);
      } finally {
        this.loadingAnalytics = false;
      }
    },

    getBarHeight(value, dataset) {
      const max = Math.max(...dataset.map((d) => d.count), 1);
      return Math.round((value / max) * 100);
    },

    getRankBarWidth(value, dataset) {
      const max = Math.max(...dataset.map((d) => d.count), 1);
      return Math.round((value / max) * 100);
    },

    shouldShowLabel(index, dataset) {
      const step = Math.max(Math.ceil(dataset.length / 7), 1);
      return index % step === 0 || index === dataset.length - 1;
    },

    formatShortDate(dateStr) {
      const d = new Date(dateStr);
      return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    },

    // ========================
    // Photo Moderation
    // ========================
    async loadPendingPhotos() {
      this.loadingPhotos = true;
      try {
        const res = await adminAPI.getPendingPhotos();
        this.pendingPhotos = res.data.data || res.data.photos || [];
        // Refresh badge
        const photosTab = this.navTabs.find(t => t.id === 'photos');
        if (photosTab) photosTab.badge = this.pendingPhotos.length > 0 ? this.pendingPhotos.length : null;
      } catch (error) {
        console.error('Failed to load pending photos:', error);
        this.showToast('Failed to load pending photos.', 'error');
      } finally {
        this.loadingPhotos = false;
      }
    },

    async approvePhotoItem(photo) {
      try {
        this.processingId = photo.id;
        await adminAPI.approvePhoto(photo.id);
        this.showToast('Photo approved successfully.');
        this.pendingPhotos = this.pendingPhotos.filter(p => p.id !== photo.id);
        const photosTab = this.navTabs.find(t => t.id === 'photos');
        if (photosTab) photosTab.badge = this.pendingPhotos.length > 0 ? this.pendingPhotos.length : null;
      } catch (error) {
        this.showToast('Failed to approve photo: ' + (error.response?.data?.message || error.message), 'error');
      } finally {
        this.processingId = null;
      }
    },

    openPhotoRejectModal(photo) {
      this.selectedPhotoId = photo.id;
      this.photoRejectReason = '';
      this.showPhotoRejectModal = true;
    },

    async confirmPhotoReject() {
      try {
        this.processingId = this.selectedPhotoId;
        await adminAPI.rejectPhoto(this.selectedPhotoId);
        this.showToast('Photo rejected.', 'info');
        this.pendingPhotos = this.pendingPhotos.filter(p => p.id !== this.selectedPhotoId);
        const photosTab = this.navTabs.find(t => t.id === 'photos');
        if (photosTab) photosTab.badge = this.pendingPhotos.length > 0 ? this.pendingPhotos.length : null;
        this.showPhotoRejectModal = false;
      } catch (error) {
        this.showToast('Failed to reject photo: ' + (error.response?.data?.message || error.message), 'error');
      } finally {
        this.processingId = null;
      }
    },

    // ========================
    // Flagged Properties
    // ========================
    async loadFlaggedProperties(page = 1) {
      this.loadingFlagged = true;
      try {
        const res = await adminAPI.getFlaggedProperties({
          status: this.flaggedFilter,
          page,
          per_page: 15,
        });
        const data = res.data.data;
        this.flaggedProperties = data.data || data || [];
        this.flaggedPagination = {
          current_page: data.current_page || 1,
          last_page: data.last_page || 1,
          total: data.total || 0,
        };
      } catch (error) {
        console.error('Failed to load flagged properties:', error);
      } finally {
        this.loadingFlagged = false;
      }
    },

    openFlagReviewModal(flag, action) {
      this.selectedFlag = flag;
      this.flagReviewAction = action;
      this.flagAdminNotes = '';
      this.flagSuspendAgent = false;
      this.flagDeleteProperty = false;
      this.showFlagReviewModal = true;
    },

    async confirmFlagReview() {
      try {
        this.processingId = this.selectedFlag.id;
        await adminAPI.reviewFlaggedProperty(this.selectedFlag.id, {
          action: this.flagReviewAction,
          admin_notes: this.flagAdminNotes,
          suspend_agent: this.flagSuspendAgent,
          delete_property: this.flagDeleteProperty,
        });
        this.showToast(this.flagReviewAction === 'approve' ? 'Flag cleared successfully!' : 'Action taken successfully!');
        this.showFlagReviewModal = false;
        await this.loadFlaggedProperties(this.flaggedPagination.current_page);
      } catch (error) {
        this.showToast('Failed to review flag: ' + (error.response?.data?.message || error.message), 'error');
      } finally {
        this.processingId = null;
      }
    },

    getConfidenceClass(confidence) {
      if (confidence >= 80) return 'confidence-high';
      if (confidence >= 60) return 'confidence-medium';
      return 'confidence-low';
    },

    // ========================
    // Create Admin
    // ========================
    async submitCreateAdmin() {
      this.createAdminErrors = {};
      this.creatingAdmin = true;
      try {
        await adminAPI.createAdmin(this.createAdminForm);
        this.showToast('Admin account created successfully!');
        this.showCreateAdminModal = false;
        this.createAdminForm = { name: '', email: '', password: '', password_confirmation: '', phone: '' };
        await this.loadUsers(this.usersPagination.current_page);
      } catch (error) {
        if (error.response?.status === 422) {
          this.createAdminErrors = error.response.data.errors || {};
        } else {
          this.showToast('Failed to create admin: ' + (error.response?.data?.message || error.message), 'error');
        }
      } finally {
        this.creatingAdmin = false;
      }
    },

    // ========================
    // Helpers
    // ========================
    // ========================
    // Toast + Confirm helpers
    // ========================
    showToast(message, type = 'success') {
      const id = ++this.toastCounter;
      this.toasts.push({ id, message, type });
      setTimeout(() => {
        this.toasts = this.toasts.filter(t => t.id !== id);
      }, 3500);
    },

    askConfirm({ title, message, warning = '', confirmLabel = 'Confirm', danger = false, onConfirm }) {
      this.confirmModal = { show: true, title, message, warning, confirmLabel, danger, onConfirm };
    },

    getTabIcon(tabId) {
      const s = 'stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';
      const icons = {
        overview:   `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" ${s}><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>`,
        agents:     `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" ${s}><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/><path d="M13 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`,
        users:      `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" ${s}><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
        properties: `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" ${s}><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
        photos:     `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" ${s}><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>`,
        flagged:    `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" ${s}><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>`,
        audit:      `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" ${s}><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`,
      };
      return icons[tabId] || '';
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      });
    },

    formatDateTime(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },

    capitalize(str) {
      if (!str) return '';
      return str.charAt(0).toUpperCase() + str.slice(1);
    },

    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push('/');
    },
  },

  mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.role !== 'admin') {
      this.$router.push('/dashboard');
      return;
    }
    this.userName = user.name || 'Admin';
    this.loadOverview();
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@700;800&family=Inter:wght@400;500;600;700&display=swap');

* { margin: 0; padding: 0; box-sizing: border-box; }

/* CSS variables on the component root so they work correctly with Vue scoped styles */
.admin-layout {
  --navy: #0B1C39;
  --navy2: #102445;
  --bg: #f5f5f5;
  --gold: #D89B0F;
  --gold3: #B07A08;
  --white: #ffffff;
  --light-gray: #e0e0e0;
  --text-secondary: #666;
  --success: #16a34a;
  --danger: #dc2626;
  --warning: #d97706;
}

/* ============================================================================
   LAYOUT
   ============================================================================ */

.admin-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  background: var(--bg);
}

/* ============================================================================
   SIDEBAR
   ============================================================================ */

.sidebar {
  width: 260px;
  background: var(--navy);
  color: #fff;
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 100;
}

.sidebar-header {
  padding: 24px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  gap: 12px;
}

.sidebar-logo {
  font-family: 'Outfit', sans-serif;
  font-size: 1.2rem;
  font-weight: 800;
}

.logo-realty { color: #fff; }
.logo-ph { color: var(--gold); }

.admin-badge {
  background: var(--gold);
  color: var(--navy);
  font-size: 10px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 4px;
  letter-spacing: 1px;
}

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s;
  text-align: left;
  width: 100%;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.06);
  color: #fff;
}

.nav-item.active {
  background: var(--gold);
  color: var(--navy);
  font-weight: 600;
}

.nav-icon { font-size: 18px; }

.nav-badge {
  margin-left: auto;
  background: var(--danger);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 10px;
  min-width: 20px;
  text-align: center;
}

.nav-item.active .nav-badge {
  background: var(--navy);
  color: var(--gold);
}

.sidebar-footer {
  padding: 16px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.user-card {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--gold);
  color: var(--navy);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 15px;
}

.user-info { flex: 1; }
.user-name { font-size: 13px; font-weight: 600; color: #fff; }
.user-role { font-size: 11px; color: rgba(255, 255, 255, 0.5); }

.btn-logout {
  width: 100%;
  padding: 8px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
  border-radius: 50px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout:hover {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
}

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.main-content {
  flex: 1;
  margin-left: 260px;
  min-height: 100vh;
}

.topbar {
  background: var(--white);
  padding: 20px 32px;
  border-bottom: 1px solid var(--light-gray);
  position: sticky;
  top: 0;
  z-index: 50;
}

.page-title {
  font-family: 'Outfit', sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: var(--navy);
}

.page-wrapper {
  padding: 24px 32px;
}

.tab-content {
  animation: fadeIn 0.25s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ============================================================================
   LOADING & EMPTY STATES
   ============================================================================ */

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px;
  color: var(--text-secondary);
  gap: 12px;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid var(--light-gray);
  border-top-color: var(--gold);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px;
  background: var(--white);
  border-radius: 12px;
  color: var(--text-secondary);
  font-size: 15px;
}

.empty-icon {
  font-size: 40px;
  margin-bottom: 12px;
}

/* ============================================================================
   OVERVIEW TAB
   ============================================================================ */

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: var(--white);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid var(--light-gray);
  transition: box-shadow 0.2s;
}

.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.stat-icon-users { background: #e3f2fd; }
.stat-icon-properties { background: #fce4ec; }
.stat-icon-agents { background: #e8f5e9; }
.stat-icon-pending { background: #fff8e1; }
.stat-icon-active { background: #f3e5f5; }
.stat-icon-photos { background: #e0f2f1; }

.stat-number {
  font-family: 'Outfit', sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: var(--navy);
  line-height: 1;
}

.stat-label {
  font-size: 13px;
  color: var(--text-secondary);
  margin-top: 4px;
}

.section-card {
  background: var(--white);
  border-radius: 12px;
  padding: 24px;
  border: 1px solid var(--light-gray);
  margin-bottom: 24px;
}

.section-title {
  font-family: 'Outfit', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: var(--navy);
  margin-bottom: 16px;
}

.role-grid {
  display: flex;
  gap: 24px;
}

.role-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.role-count {
  font-size: 20px;
  font-weight: 700;
  color: var(--navy);
}

.period-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.period-item {
  text-align: center;
  padding: 16px;
  background: var(--bg);
  border-radius: 10px;
}

.period-number {
  font-family: 'Outfit', sans-serif;
  font-size: 24px;
  font-weight: 700;
  color: var(--gold);
}

.period-label {
  font-size: 12px;
  color: var(--text-secondary);
  margin-top: 4px;
}

/* Activity Feed */
.activity-feed {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.activity-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: 8px;
  background: var(--bg);
}

.activity-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin-top: 5px;
  flex-shrink: 0;
}

.dot-create { background: var(--success); }
.dot-update { background: #2196f3; }
.dot-delete { background: var(--danger); }
.dot-verify { background: var(--gold); }
.dot-approve { background: var(--success); }
.dot-reject { background: var(--danger); }

.activity-desc {
  font-size: 13px;
  color: var(--navy);
  margin-bottom: 4px;
}

.activity-meta {
  font-size: 12px;
  color: var(--text-secondary);
}

/* ============================================================================
   AGENT VERIFICATION TAB
   ============================================================================ */

.sub-filters {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
}

.filter-btn {
  padding: 8px 16px;
  background: var(--white);
  border: 2px solid var(--light-gray);
  border-radius: 50px;
  color: var(--text-secondary);
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.filter-btn.active {
  background: var(--gold);
  border-color: var(--gold);
  color: var(--navy);
}

.filter-count {
  background: rgba(0, 0, 0, 0.08);
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 11px;
}

.filter-btn.active .filter-count {
  background: rgba(0, 0, 0, 0.15);
}

/* Agent Cards */
.agent-card {
  background: var(--white);
  border: 1px solid var(--light-gray);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 16px;
  transition: box-shadow 0.2s;
}

.agent-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.card-verified { border-left: 4px solid var(--success); }
.card-rejected { border-left: 4px solid var(--danger); }

.agent-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 16px;
}

.avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: var(--gold);
  color: var(--navy);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  flex-shrink: 0;
}

.avatar-verified { background: var(--success); color: #fff; }
.avatar-rejected { background: var(--danger); color: #fff; }

.agent-info { flex: 1; }
.agent-info h3 { font-size: 15px; font-weight: 600; color: var(--navy); }
.agent-info p { font-size: 13px; color: var(--text-secondary); margin-top: 2px; }

.ai-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.ai-approved { background: #d4edda; color: #155724; }
.ai-unclear { background: #fff3cd; color: #856404; }
.ai-rejected { background: #f8d7da; color: #721c24; }
.ai-error { background: #f8d7da; color: #721c24; }

.status-pill {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-verified { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }

.agent-card-body {
  padding: 16px;
  background: var(--bg);
  border-radius: 10px;
  margin-bottom: 16px;
}

.field-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.field label {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
  display: block;
}

.field p {
  font-size: 14px;
  color: var(--navy);
  font-weight: 500;
}

.field.full-width { grid-column: 1 / -1; }

/* AI Section */
/* ── Two-column card layout ── */
.card-cols { display: flex; gap: 20px; align-items: flex-start; }
.card-col-main { flex: 1; min-width: 0; }
.card-col-photo { flex-shrink: 0; width: 200px; }
.photo-label { display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
.photo-hint { font-size: 10px; color: var(--text-secondary); margin-top: 4px; text-align: center; }
.prc-photo { width: 100%; border-radius: 8px; border: 2px solid var(--light-gray); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
.prc-photo:hover { transform: scale(1.02); box-shadow: 0 4px 16px rgba(0,0,0,0.12); }

/* ── Header badges ── */
.header-badges { display: flex; align-items: center; gap: 6px; margin-left: auto; flex-wrap: wrap; }
.dual-ai-badge { background: rgba(139,92,246,0.10); color: #7c3aed; border: 1px solid rgba(139,92,246,0.25); border-radius: 99px; padding: 2px 9px; font-size: 10px; font-weight: 700; }

/* ── AI Analysis Panel ── */
.ai-analysis-panel {
  margin-top: 18px; padding: 16px;
  background: #F8F7FF; border: 1px solid #E4E0FF;
  border-radius: 12px;
}
.ai-panel-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
.ai-panel-title { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #1e1b4b; }
.ai-scores { display: flex; gap: 12px; flex-wrap: wrap; }
.ai-score-item { display: flex; flex-direction: column; align-items: flex-end; }
.score-label { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #6b7280; }
.score-val { font-size: 14px; font-weight: 800; font-family: 'Outfit', sans-serif; }
.score-good { color: #15803d; }
.score-warn { color: #b45309; }
.score-bad  { color: #b91c1c; }
.score-neutral { color: #374151; font-size: 12px; font-weight: 600; }

/* Buddy reasoning */
.ai-reasoning { margin-bottom: 12px; }
.buddy-row { display: flex; align-items: flex-start; gap: 10px; }
.buddy-mini-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
.buddy-bubble { background: #fff; border: 1px solid #ddd6fe; border-radius: 10px; padding: 10px 14px; flex: 1; }
.buddy-bubble strong { font-size: 11px; color: #5b21b6; display: block; margin-bottom: 4px; }
.buddy-bubble p { font-size: 12.5px; color: #374151; line-height: 1.5; margin: 0; }

/* Info match checks */
.ai-checks { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
.check-item { display: flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 99px; font-size: 11.5px; font-weight: 600; }
.check-pass { background: rgba(22,163,74,0.08); color: #15803d; }
.check-fail { background: rgba(185,28,28,0.08); color: #b91c1c; }
.check-warn { background: rgba(180,83,9,0.08); color: #b45309; }
.check-info { background: rgba(59,130,246,0.08); color: #1d4ed8; }

/* Tag sections */
.ai-tags-section { margin-bottom: 10px; padding: 10px 12px; border-radius: 8px; }
.ai-tags-good { background: rgba(22,163,74,0.05); border: 1px solid rgba(22,163,74,0.15); }
.ai-tags-warn { background: rgba(180,83,9,0.05); border: 1px solid rgba(180,83,9,0.15); }
.ai-tags-danger { background: rgba(185,28,28,0.05); border: 1px solid rgba(185,28,28,0.15); }
.tags-label { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 7px; }
.ai-tags-good .tags-label { color: #15803d; }
.ai-tags-warn .tags-label { color: #b45309; }
.ai-tags-danger .tags-label { color: #b91c1c; }
.tags-row { display: flex; flex-wrap: wrap; gap: 5px; }
.tag { padding: 3px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }
.tag-good { background: rgba(22,163,74,0.10); color: #15803d; }
.tag-warn { background: rgba(180,83,9,0.10); color: #b45309; }
.tag-danger { background: rgba(185,28,28,0.10); color: #b91c1c; }

/* DeepSeek section */
.deepseek-section { margin-top: 10px; padding: 10px 12px; background: rgba(139,92,246,0.05); border: 1px solid rgba(139,92,246,0.15); border-radius: 8px; }
.deepseek-header { display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 700; color: #5b21b6; margin-bottom: 5px; flex-wrap: wrap; }
.deepseek-verdict { padding: 2px 9px; border-radius: 99px; font-size: 10px; font-weight: 700; background: rgba(139,92,246,0.12); color: #5b21b6; text-transform: capitalize; }
.ds-agree-approved { background: rgba(22,163,74,0.12); color: #15803d; }
.ds-agree-rejected, .ds-disagree-should-reject { background: rgba(185,28,28,0.12); color: #b91c1c; }
.ds-disagree-needs-review { background: rgba(180,83,9,0.12); color: #b45309; }
.deepseek-reasoning { font-size: 12px; color: #4c1d95; line-height: 1.5; margin: 0; }

.ai-section {
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px solid var(--light-gray);
}
.ai-section label {
  font-size: 11px; font-weight: 600; color: var(--text-secondary);
  text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block;
}
.ai-result {
  background: var(--white); border: 1px solid var(--light-gray);
  border-radius: 8px; padding: 12px; font-size: 12px;
  font-family: 'Courier New', monospace; white-space: pre-wrap;
  word-break: break-word; max-height: 200px; overflow-y: auto;
}

.agent-card-actions {
  display: flex;
  gap: 12px;
}

.btn-approve,
.btn-reject {
  flex: 1; padding: 10px 16px; border: none; border-radius: 50px;
  font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}

.btn-approve {
  background: var(--success);
  color: #fff;
}

.btn-approve:hover:not(:disabled) { background: #15803d; transform: translateY(-1px); }
.btn-approve:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-reject {
  background: var(--danger);
  color: #fff;
}

.btn-reject:hover:not(:disabled) { background: #b91c1c; transform: translateY(-1px); }
.btn-reject:disabled { opacity: 0.6; cursor: not-allowed; }

/* ============================================================================
   FILTER BAR
   ============================================================================ */

.filter-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.search-box { flex: 1; min-width: 240px; }

.search-input {
  width: 100%;
  padding: 10px 14px;
  border: 2px solid var(--light-gray);
  border-radius: 8px;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: var(--gold);
}

.filter-select,
.filter-input {
  padding: 10px 14px;
  border: 2px solid var(--light-gray);
  border-radius: 8px;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  background: var(--white);
  cursor: pointer;
  transition: border-color 0.2s;
}

.filter-select:focus,
.filter-input:focus {
  outline: none;
  border-color: var(--gold);
}

/* ============================================================================
   DATA TABLE
   ============================================================================ */

.table-wrapper {
  background: var(--white);
  border-radius: 12px;
  border: 1px solid var(--light-gray);
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table thead {
  background: var(--bg);
}

.data-table th {
  padding: 14px 16px;
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid var(--light-gray);
}

.data-table td {
  padding: 14px 16px;
  font-size: 14px;
  color: var(--navy);
  border-bottom: 1px solid var(--light-gray);
}

.data-table tbody tr:hover {
  background: rgba(216,155,15,0.04);
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}

.td-name { font-weight: 600; }

.td-actions {
  display: flex;
  gap: 6px;
  align-items: center;
}

/* Badges */
.role-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.role-buyer { background: #e3f2fd; color: #1565c0; }
.role-agent { background: #f3e5f5; color: #6a1b9a; }
.role-admin { background: #fff8e1; color: #e65100; }

.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.status-active { background: #d4edda; color: #155724; }
.status-suspended { background: #f8d7da; color: #721c24; }
.status-available { background: #d4edda; color: #155724; }
.status-sold { background: #e3f2fd; color: #1565c0; }
.status-rented { background: #f3e5f5; color: #6a1b9a; }
.status-unlisted { background: #f5f5f5; color: #666; }

.action-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.action-create { background: #d4edda; color: #155724; }
.action-update { background: #e3f2fd; color: #1565c0; }
.action-delete { background: #f8d7da; color: #721c24; }
.action-verify { background: #fff8e1; color: #e65100; }
.action-approve { background: #d4edda; color: #155724; }
.action-reject { background: #f8d7da; color: #721c24; }

/* Small Action Buttons */
.btn-sm {
  padding: 6px 12px;
  border: none;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-suspend { background: #fff3cd; color: #856404; }
.btn-suspend:hover:not(:disabled) { background: #ffe69c; }

.btn-activate { background: #d4edda; color: #155724; }
.btn-activate:hover:not(:disabled) { background: #b7e4c7; }

.btn-delete { background: #f8d7da; color: #721c24; }
.btn-delete:hover:not(:disabled) { background: #f1aeb5; }

.btn-sm:disabled { opacity: 0.6; cursor: not-allowed; }

/* Property thumbnail */
.property-thumb {
  width: 60px;
  height: 40px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid var(--light-gray);
}

.no-photo {
  width: 60px;
  height: 40px;
  background: var(--bg);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  color: var(--text-secondary);
}

/* Feature button */
.btn-feature {
  padding: 5px 12px;
  border: 2px solid var(--light-gray);
  border-radius: 50px;
  background: var(--white);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  color: var(--text-secondary);
  transition: all 0.2s;
}

.btn-feature.featured {
  background: var(--gold);
  border-color: var(--gold);
  color: var(--navy);
}

.btn-feature:hover:not(:disabled) {
  border-color: var(--gold);
}

.btn-feature:disabled { opacity: 0.6; cursor: not-allowed; }

/* ============================================================================
   PAGINATION
   ============================================================================ */

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-top: 24px;
}

.page-btn {
  padding: 8px 16px;
  border: 2px solid var(--light-gray);
  border-radius: 50px;
  background: var(--white);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  color: var(--navy);
}

.page-btn:hover:not(:disabled) {
  border-color: var(--gold);
  background: var(--gold);
  color: var(--navy);
}

.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.page-info {
  font-size: 13px;
  color: var(--text-secondary);
}

/* ============================================================================
   MODALS
   ============================================================================ */

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
  background: var(--white);
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  max-width: 480px;
  width: 90%;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--light-gray);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 17px;
  font-weight: 700;
  color: var(--navy);
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: var(--text-secondary);
  line-height: 1;
}

.close-btn:hover { color: var(--navy); }

.modal-body {
  padding: 24px;
}

.modal-info {
  padding: 12px;
  background: var(--bg);
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.modal-warning {
  margin-top: 8px;
  font-size: 13px;
  color: var(--danger);
}

.form-field {
  margin-bottom: 16px;
}

.form-field label {
  display: block;
  font-weight: 600;
  color: var(--navy);
  margin-bottom: 8px;
  font-size: 14px;
}

.textarea-field {
  width: 100%;
  padding: 12px;
  border: 2px solid var(--light-gray);
  border-radius: 8px;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  resize: vertical;
}

.textarea-field:focus {
  outline: none;
  border-color: var(--gold);
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid var(--light-gray);
}

.btn-cancel {
  flex: 1;
  padding: 10px;
  background: var(--bg);
  color: var(--navy);
  border: 1px solid var(--light-gray);
  border-radius: 50px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover { background: var(--light-gray); }

.modal-footer .btn-reject {
  flex: 1;
}

/* Photo Preview Modal */
.photo-preview-box {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
}

.photo-close {
  position: absolute;
  top: -12px;
  right: -12px;
  width: 32px;
  height: 32px;
  background: var(--white);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  font-size: 18px;
}

.photo-preview-img {
  max-width: 90vw;
  max-height: 85vh;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

/* ============================================================================
   ANALYTICS
   ============================================================================ */

.analytics-section { margin-top: 20px; }
.analytics-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.period-selector { display: flex; gap: 6px; }
.period-btn {
  padding: 5px 14px;
  border: 1px solid var(--light-gray);
  border-radius: 50px;
  cursor: pointer;
  font-size: 13px;
  background: #fff;
  font-weight: 500;
  transition: all 0.2s;
}
.period-btn.active {
  background: var(--gold);
  border-color: var(--gold);
  color: var(--navy);
  font-weight: 600;
}

.chart-block { margin-bottom: 28px; }
.chart-title { font-size: 14px; font-weight: 600; color: var(--navy); margin-bottom: 12px; }
.bar-chart {
  display: flex;
  align-items: flex-end;
  gap: 2px;
  height: 140px;
  padding: 8px 0 24px;
  overflow-x: auto;
  position: relative;
}
.bar-column {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  min-width: 10px;
  max-width: 28px;
  height: 100%;
  justify-content: flex-end;
  gap: 2px;
  position: relative;
}
.bar-fill {
  width: 100%;
  border-radius: 3px 3px 0 0;
  transition: height 0.3s ease;
  min-height: 2px;
}
.bar-fill-users { background: var(--gold); }
.bar-fill-properties { background: var(--navy); }
.bar-value { font-size: 9px; color: var(--text-secondary); line-height: 1; }
.bar-label {
  font-size: 9px;
  color: var(--text-secondary);
  white-space: nowrap;
  position: absolute;
  bottom: -18px;
}

.two-col-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 20px; }
.rank-block { }
.rank-empty { font-size: 13px; color: var(--text-secondary); padding: 12px 0; }
.rank-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 0;
  border-bottom: 1px solid var(--light-gray);
  font-size: 13px;
}
.rank-num { font-weight: 700; color: var(--gold); width: 20px; }
.rank-name { flex: 1; color: var(--navy); font-weight: 500; }
.rank-bar-wrap { width: 80px; height: 8px; background: var(--light-gray); border-radius: 4px; }
.rank-bar { height: 100%; background: var(--gold); border-radius: 4px; transition: width 0.3s; }
.rank-count { font-size: 12px; color: var(--text-secondary); white-space: nowrap; }
.rank-rating { font-size: 12px; color: var(--warning); }

/* ============================================================================
   FLAGGED PROPERTIES TAB
   ============================================================================ */

.stat-icon-flagged { background: #fff0f0; }

.flag-card {
  background: var(--white);
  border: 1px solid var(--light-gray);
  border-left: 4px solid var(--danger);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 16px;
  transition: box-shadow 0.2s;
}
.flag-card:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }

.flag-card-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}
.flag-thumb {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  flex-shrink: 0;
}
.flag-info { flex: 1; }
.flag-info h3 { font-size: 15px; font-weight: 600; color: var(--navy); }
.flag-info p { font-size: 13px; color: var(--text-secondary); margin-top: 2px; }
.flag-agent { font-weight: 500; }
.flag-meta { text-align: right; }
.flag-date { font-size: 12px; color: var(--text-secondary); margin-top: 4px; }

.confidence-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
  display: inline-block;
}
.confidence-high { background: #f8d7da; color: #721c24; }
.confidence-medium { background: #fff3cd; color: #856404; }
.confidence-low { background: #d4edda; color: #155724; }

.flag-reason-box {
  padding: 14px;
  background: #fff8f8;
  border: 1px solid #ffe0e0;
  border-radius: 10px;
  margin-bottom: 12px;
}
.flag-reason-box label {
  font-size: 11px;
  font-weight: 600;
  color: var(--danger);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
  display: block;
}
.flag-reason-box p { font-size: 14px; color: var(--navy); }

.flag-details {
  padding: 12px;
  background: var(--bg);
  border-radius: 8px;
  margin-bottom: 12px;
}
.flag-details label {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
  display: block;
}
.flag-details ul { margin: 0; padding-left: 18px; }
.flag-details li { font-size: 13px; color: var(--navy); margin-bottom: 4px; }

.flag-actions {
  display: flex;
  gap: 12px;
}

.flag-reviewed-info {
  padding: 12px;
  background: var(--bg);
  border-radius: 8px;
  font-size: 13px;
  color: var(--text-secondary);
}
.flag-admin-note {
  margin-top: 6px;
  font-style: italic;
  color: var(--navy);
}

/* ============================================================================
   CREATE ADMIN
   ============================================================================ */

.btn-create-admin {
  padding: 10px 18px;
  background: var(--gold);
  color: var(--navy);
  border: none;
  border-radius: 8px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-create-admin:hover { background: var(--gold3); transform: translateY(-1px); }

.text-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid var(--light-gray);
  border-radius: 8px;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  transition: border-color 0.2s;
}
.text-input:focus { outline: none; border-color: var(--gold); }

.field-error {
  color: var(--danger);
  font-size: 12px;
  margin-top: 4px;
}

.checkbox-field {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  padding: 10px 12px;
  background: #fff8f8;
  border-radius: 8px;
  border: 1px solid #ffe0e0;
}
.checkbox-field input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: var(--danger);
}
.checkbox-field label {
  font-size: 14px;
  font-weight: 500;
  color: var(--navy);
  margin: 0;
  cursor: pointer;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */

@media (max-width: 1024px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .period-grid { grid-template-columns: repeat(2, 1fr); }
  .two-col-grid { grid-template-columns: 1fr; }
}

/* ============================================================================
   PHOTO MODERATION TAB
   ============================================================================ */
.photo-mod-card {
  display: flex;
  gap: 20px;
  background: var(--white);
  border: 1px solid var(--light-gray);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 16px;
  transition: box-shadow 0.2s;
}
.photo-mod-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.photo-mod-img-wrap { flex-shrink: 0; text-align: center; }
.photo-mod-img {
  width: 200px;
  height: 140px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid var(--light-gray);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}
.photo-mod-img:hover { transform: scale(1.02); box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
.photo-mod-hint { display: block; font-size: 10px; color: var(--text-secondary); margin-top: 4px; }
.photo-mod-info { flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.photo-mod-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
.photo-mod-actions { display: flex; gap: 12px; }
.photo-mod-actions .btn-approve,
.photo-mod-actions .btn-reject { flex: 1; }

@media (max-width: 768px) {
  .photo-mod-card { flex-direction: column; }
  .photo-mod-img { width: 100%; height: 200px; }
}

/* ============================================================================
   TOAST NOTIFICATIONS
   ============================================================================ */
.toast-container {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  pointer-events: none;
}
.toast {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  pointer-events: auto;
  max-width: 360px;
  animation: slideUp 0.3s ease;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-leave-to { opacity: 0; transform: translateX(40px); }

.toast-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.toast-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.toast-info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

/* Mobile sidebar overlay */
.sidebar-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 99;
}

/* hamburger only visible on mobile */
.hamburger-btn {
  display: none;
  background: none;
  border: none;
  color: var(--navy);
  cursor: pointer;
  padding: 4px;
  margin-right: 12px;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: background 0.2s;
}
.hamburger-btn:hover { background: var(--bg); }

@media (max-width: 768px) {
  .hamburger-btn { display: flex; }
  .sidebar-overlay { display: block; }

  /* hide sidebar off-screen by default on mobile */
  .sidebar {
    width: 260px;
    transform: translateX(-100%);
    transition: transform 0.28s ease;
  }
  .sidebar.open {
    transform: translateX(0);
  }

  .main-content { margin-left: 0; }
  .page-wrapper { padding: 16px; }
  .stats-grid { grid-template-columns: 1fr; }
  .period-grid { grid-template-columns: repeat(2, 1fr); }
  .field-grid { grid-template-columns: 1fr; }
  .filter-bar { flex-direction: column; }
  .card-cols { flex-direction: column; }
  .card-col-photo { width: 100%; }
}
</style>
