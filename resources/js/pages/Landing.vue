<template>
  <div class="landing-page">
    <!-- NAVBAR -->
    <nav class="navbar" :class="{ scrolled: isScrolled }">
      <div class="container navbar-inner">
        <router-link to="/" class="brand">
          <span class="brand-realty">Realty</span><span class="brand-link">Link</span><span class="brand-ph">PH</span>
        </router-link>
        <button class="nav-hamburger" @click="mobileNav = !mobileNav" aria-label="Toggle navigation menu">
          <span></span><span></span><span></span>
        </button>
        <div class="nav-actions" :class="{ open: mobileNav }">
          <button @click="showLoginModal = true; mobileNav = false" class="btn-signin">Sign In</button>
          <button @click="showRegisterModal = true; mobileNav = false" class="btn-get-started">Get Started</button>
        </div>
      </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
      <div class="hero-overlay"></div>
      <div class="hero-grain"></div>
      <div class="container hero-content">
        <span class="hero-badge">The #1 Real Estate Platform in the Philippines</span>
        <h1 class="hero-title">
          Find Your Perfect Home<br />in the <span class="text-accent">Philippines</span>
        </h1>
        <p class="hero-sub">
          Browse verified properties from trusted agents. Schedule viewings, send offers, and sign documents — all in one place.
        </p>

        <!-- Search Bar -->
        <div class="search-bar">
          <div class="search-field">
            <label>Location</label>
            <input type="text" v-model="searchLocation" placeholder="e.g., Cebu City, Makati" aria-label="Search by location" />
          </div>
          <div class="search-sep"></div>
          <div class="search-field">
            <label>Property Type</label>
            <select v-model="searchType" aria-label="Filter by property type">
              <option value="">All Types</option>
              <option value="house">House</option>
              <option value="condo">Condo</option>
              <option value="apartment">Apartment</option>
              <option value="commercial">Commercial</option>
            </select>
          </div>
          <div class="search-sep"></div>
          <div class="search-field">
            <label>Price Range</label>
            <select v-model="searchPrice" aria-label="Filter by price range">
              <option value="">Any Price</option>
              <option value="0-5000000">Under ₱5M</option>
              <option value="5000000-10000000">₱5M – ₱10M</option>
              <option value="10000000-25000000">₱10M – ₱25M</option>
              <option value="25000000-50000000">₱25M – ₱50M</option>
              <option value="50000000+">₱50M+</option>
            </select>
          </div>
          <button @click="handleSearch" class="search-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Search
          </button>
        </div>

        <!-- Stats -->
        <div class="hero-stats">
          <div class="stat-block">
            <span class="stat-num">{{ stats.properties.toLocaleString('en-PH') }}</span>
            <span class="stat-lbl">Properties</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-block">
            <span class="stat-num">{{ stats.agents.toLocaleString('en-PH') }}</span>
            <span class="stat-lbl">Verified Agents</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-block">
            <span class="stat-num">{{ stats.buyers.toLocaleString('en-PH') }}</span>
            <span class="stat-lbl">Happy Buyers</span>
          </div>
        </div>

        <!-- Social proof ticker (animated marquee) -->
        <div class="social-proof-ticker">
          <div class="ticker-track">
            <span class="ticker-item"><span class="ticker-dot"></span>500+ users joined this month</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>12 new properties in Cebu</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>All agents PRC-verified</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>Flood-free zones available</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>Virtual 360° tours now live</span>
            <span class="ticker-sep">·</span>
            <!-- Duplicate for seamless loop -->
            <span class="ticker-item"><span class="ticker-dot"></span>500+ users joined this month</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>12 new properties in Cebu</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>All agents PRC-verified</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>Flood-free zones available</span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item"><span class="ticker-dot"></span>Virtual 360° tours now live</span>
            <span class="ticker-sep">·</span>
          </div>
        </div>
      </div>

      <!-- Scroll-down indicator -->
      <button class="hero-scroll-indicator" @click="scrollToFeatures" aria-label="Scroll down to explore">
        <div class="scroll-ind-line"></div>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
    </section>

    <!-- FEATURE CARDS -->
    <section class="features" ref="featuresRef">
      <div class="container">
        <div class="features-grid">
          <div class="feat-card">
            <div class="feat-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <h3>Search Properties</h3>
            <p>Browse verified listings with powerful filters for location, type, and price range.</p>
          </div>
          <div class="feat-card feat-highlight">
            <div class="feat-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3>Connect with Agents</h3>
            <p>Message verified real estate agents directly, view their profiles, and get expert guidance.</p>
          </div>
          <div class="feat-card">
            <div class="feat-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h3>Digital Documents</h3>
            <p>Sign documents digitally, schedule viewings with calendar sync — all in one platform.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURED PROPERTIES -->
    <section class="properties-section">
      <div class="container">
        <div class="section-head">
          <span class="section-badge">Featured Listings</span>
          <h2 class="section-title">Explore Our Latest Properties</h2>
          <p class="section-desc">Discover quality homes, condos, and commercial spaces from verified agents across the Philippines.</p>
        </div>

        <div v-if="propertiesLoading" class="prop-loading">
          <div class="loading-dots">
            <span></span><span></span><span></span>
          </div>
          <p>Loading properties...</p>
        </div>

        <div v-else-if="landingProperties.length" class="prop-grid">
          <div v-for="property in landingProperties" :key="property.id" class="prop-card" @click="handleViewProperty(property.id)">
            <div class="prop-img-wrap">
              <img v-if="getCoverPhoto(property)" :src="getCoverPhoto(property)" :alt="property.title" class="prop-img" loading="lazy" />
              <div v-else class="prop-placeholder">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.4"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </div>
              <div class="prop-price-pill">₱{{ formatPrice(property.price) }}</div>
              <div class="prop-badges">
                <span v-if="property.featured" class="prop-badge badge-featured">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  Featured
                </span>
                <span v-if="property.virtual_tour_url" class="prop-badge badge-360">360°</span>
              </div>
              <div class="prop-img-overlay"></div>
            </div>
            <div class="prop-body">
              <div class="prop-meta-row">
                <span class="prop-type-tag">{{ formatPropertyType(property.property_type) }}</span>
                <span class="prop-verified-tag">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  Verified
                </span>
              </div>
              <h3 class="prop-title">{{ property.title }}</h3>
              <p class="prop-location">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ property.city }}, {{ property.province }}
              </p>
              <div class="prop-specs">
                <span v-if="property.bedrooms" class="spec-pill">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
                  {{ property.bedrooms }}
                </span>
                <span v-if="property.bathrooms" class="spec-pill">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3v2.25"/></svg>
                  {{ property.bathrooms }}
                </span>
                <span v-if="property.floor_area" class="spec-pill">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                  {{ property.floor_area }}m²
                </span>
              </div>
              <div class="prop-trust-tags">
                <span class="trust-tag">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                  Verified Listing
                </span>
                <span v-if="property.featured" class="trust-tag tag-flood">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><polyline points="20 6 9 17 4 12"/></svg>
                  Flood-Free Zone
                </span>
              </div>
              <div class="prop-view-btn">
                View Details
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="prop-empty">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <p>No properties available at the moment.</p>
        </div>

        <div v-if="landingProperties.length" class="section-cta">
          <button @click="handleSearch" class="btn-outline-dark">See All Properties →</button>
        </div>
      </div>
    </section>

    <!-- BROWSE BY LOCATION -->
    <section class="locations-section">
      <div class="container">
        <div class="section-head">
          <span class="section-badge">Browse by Location</span>
          <h2 class="section-title">Find Homes Across the Philippines</h2>
          <p class="section-desc">Explore listings in the most sought-after cities — from Metro Manila's business hubs to Cebu's coastal communities.</p>
        </div>
        <div class="locations-grid">
          <div v-for="loc in popularLocations" :key="loc.name" class="location-card" @click="browseByLocation(loc.query)">
            <div class="location-icon" v-html="getCityIcon(loc.name)"></div>
            <div class="location-name">{{ loc.name }}</div>
            <div class="location-subtitle">{{ loc.subtitle }}</div>
            <div class="location-arrow">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURED AGENTS (shown only if API returns data) -->
    <section v-if="featuredAgents.length" class="agents-section">
      <div class="container">
        <div class="section-head">
          <span class="section-badge">Meet Our Agents</span>
          <h2 class="section-title">Verified Real Estate Professionals</h2>
          <p class="section-desc">Work with PRC-licensed agents who know their markets and have a proven track record of successful deals.</p>
        </div>
        <div class="agents-grid">
          <div v-for="agent in featuredAgents" :key="agent.id" class="agent-card">
            <div class="agent-avatar">
              <img v-if="agent.avatar || agent.profile_photo_url" :src="agent.avatar || agent.profile_photo_url" :alt="agent.name" loading="lazy" />
              <div v-else class="agent-avatar-placeholder">{{ getInitials(agent.name) }}</div>
            </div>
            <div class="agent-verified-badge">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              PRC Verified
            </div>
            <div class="agent-name">{{ agent.name }}</div>
            <div class="agent-listings">{{ agent.listings_count || agent.properties_count || 0 }} Active Listings</div>
            <button class="agent-btn" @click.stop="showRegisterModal = true">View Profile</button>
          </div>
        </div>
        <div class="section-cta">
          <button @click="showRegisterModal = true" class="btn-outline-dark">Browse All Agents →</button>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonials-section">
      <div class="container">
        <div class="section-head">
          <span class="section-badge">Success Stories</span>
          <h2 class="section-title">Trusted by Thousands of Filipinos</h2>
          <p class="section-desc">Real stories from buyers who found their dream homes through RealtyLinkPH.</p>
        </div>
        <div class="testimonials-grid">
          <div v-for="t in testimonials" :key="t.name" class="testimonial-card">
            <div class="testimonial-stars">
              <svg v-for="i in t.rating" :key="i" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <p class="testimonial-text">"{{ t.text }}"</p>
            <div class="testimonial-author">
              <div class="testimonial-avatar">{{ t.initials }}</div>
              <div class="testimonial-info">
                <div class="t-name">{{ t.name }}</div>
                <div class="t-role">{{ t.role }}</div>
                <div class="t-location">{{ t.location }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="steps-section">
      <div class="container">
        <div class="section-head">
          <span class="section-badge">How It Works</span>
          <h2 class="section-title">Your Journey to Homeownership</h2>
        </div>
        <div class="steps-grid">
          <div class="step-card">
            <div class="step-icon-wrap">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <span class="step-num">01</span>
            <h3>Search Properties</h3>
            <p>Browse thousands of verified listings with powerful filters for location, type, and price range.</p>
          </div>
          <div class="step-card">
            <div class="step-icon-wrap">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <span class="step-num">02</span>
            <h3>Connect with Agents</h3>
            <p>Message verified real estate agents directly, view their profiles, and get expert guidance.</p>
          </div>
          <div class="step-card">
            <div class="step-icon-wrap">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/></svg>
            </div>
            <span class="step-num">03</span>
            <h3>Schedule & Sign</h3>
            <p>Book property viewings with calendar sync, and sign documents digitally — all in one platform.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA BANNER -->
    <section class="cta-section">
      <div class="container cta-inner">
        <h2>Ready to Find Your Dream Home?</h2>
        <p>Create a free account to save properties, schedule viewings, and connect with agents.</p>
        <div class="cta-btns">
          <button @click="showRegisterModal = true" class="btn-cta">Get Started Free</button>
          <button @click="handleSearch" class="btn-cta-outline">Browse Properties</button>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="site-footer">
      <div class="container footer-inner">
        <div class="footer-brand">
          RealtyLink<span>PH</span>
        </div>
        <div class="footer-links">
          <a href="#" @click.prevent>Terms of Service</a>
          <a href="#" @click.prevent>Privacy Policy</a>
          <a href="#" @click.prevent>Contact Us</a>
        </div>
        <p class="footer-copy">&copy; 2026 RealtyLinkPH. All rights reserved.</p>
      </div>
    </footer>

    <!-- LOGIN MODAL -->
    <transition name="modal-fade">
      <div v-if="showLoginModal" class="modal-overlay" @click="closeLoginModal">
        <div class="modal-box" @click.stop>
          <button class="modal-close" @click="closeLoginModal" aria-label="Close login modal">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>

          <div class="modal-brand">
            <div class="modal-brand-name">RealtyLink<span class="gold">PH</span></div>
          </div>

          <!-- LOGIN VIEW -->
          <template v-if="loginView === 'login'">
            <div class="modal-head">
              <h1>{{ loginGreeting }}</h1>
              <p>Sign in to your RealtyLinkPH account</p>
            </div>

            <transition name="slide-down">
              <div v-if="loginAlert.show" :class="['m-alert', `m-alert-${loginAlert.type}`]">
                <span class="m-alert-icon">{{ loginAlert.type === 'error' ? '✕' : '✓' }}</span>
                <span>{{ loginAlert.message }}</span>
              </div>
            </transition>

            <form @submit.prevent="handleLogin" class="modal-form">
              <div class="field">
                <label for="login-email">Email Address</label>
                <div class="field-input-wrap">
                  <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                  <input id="login-email" v-model="loginForm.email" type="email" placeholder="juan@example.com" @blur="validateLoginField('email')" />
                </div>
                <transition name="slide-down"><span v-if="loginErrors.email" class="field-err">{{ loginErrors.email }}</span></transition>
              </div>

              <div class="field">
                <label for="login-password">Password</label>
                <div class="field-input-wrap">
                  <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  <input id="login-password" v-model="loginForm.password" :type="showLoginPassword ? 'text' : 'password'" placeholder="Enter your password" @blur="validateLoginField('password')" />
                  <button type="button" class="field-eye" @click="showLoginPassword = !showLoginPassword" :aria-label="showLoginPassword ? 'Hide password' : 'Show password'">
                    <svg v-if="!showLoginPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                  </button>
                </div>
                <transition name="slide-down"><span v-if="loginErrors.password" class="field-err">{{ loginErrors.password }}</span></transition>
              </div>

              <div class="field-row">
                <label class="check-label"><input type="checkbox" v-model="loginForm.remember" /><span>Remember me</span></label>
                <a href="#" class="link-accent" @click.prevent="loginView = 'forgot'">Forgot password?</a>
              </div>

              <button type="submit" class="btn-primary" :disabled="loginLoading">
                <span v-if="!loginLoading">Sign In</span>
                <span v-else class="btn-loading-inner"><span class="dot-spinner"></span>Signing in...</span>
              </button>
            </form>

            <div class="modal-divider"><span>Don't have an account?</span></div>
            <button @click="switchToRegister" class="btn-secondary">Create New Account</button>

            <div class="modal-footer-links">
              <a href="#" @click.prevent>Terms & Conditions</a>
              <span>·</span>
              <a href="#" @click.prevent>Privacy Policy</a>
            </div>
          </template>

          <!-- FORGOT PASSWORD VIEW -->
          <template v-else-if="loginView === 'forgot'">
            <div class="modal-head">
              <h1>Forgot Password</h1>
              <p>Enter your email and we'll send you a reset link</p>
            </div>

            <transition name="slide-down">
              <div v-if="forgotAlert.show" :class="['m-alert', `m-alert-${forgotAlert.type}`]">
                <span class="m-alert-icon">{{ forgotAlert.type === 'error' ? '✕' : '✓' }}</span>
                <span>{{ forgotAlert.message }}</span>
              </div>
            </transition>

            <form @submit.prevent="handleForgot" class="modal-form">
              <div class="field">
                <label for="forgot-email">Email Address</label>
                <div class="field-input-wrap">
                  <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                  <input id="forgot-email" v-model="forgotForm.email" type="email" placeholder="juan@example.com" />
                </div>
                <transition name="slide-down"><span v-if="forgotErrors.email" class="field-err">{{ forgotErrors.email }}</span></transition>
              </div>

              <button type="submit" class="btn-primary" :disabled="forgotLoading">
                <span v-if="!forgotLoading">Send Reset Link</span>
                <span v-else class="btn-loading-inner"><span class="dot-spinner"></span>Sending...</span>
              </button>
            </form>

            <div class="modal-divider"><span>Remembered your password?</span></div>
            <button @click="loginView = 'login'" class="btn-secondary">Back to Login</button>
          </template>

          <!-- FORGOT PASSWORD SENT VIEW -->
          <template v-else-if="loginView === 'forgot-sent'">
            <div class="sent-view">
              <div class="sent-icon-wrap">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              </div>
              <h2>Check your email</h2>
              <p>We sent a password reset link to<br /><strong>{{ forgotForm.email }}</strong></p>
              <p class="sent-note">Didn't receive it? Check your spam folder or <a href="#" @click.prevent="loginView = 'forgot'" class="link-accent">try again</a>.</p>
              <button @click="loginView = 'login'" class="btn-primary" style="margin-top:24px">Back to Login</button>
            </div>
          </template>
        </div>
      </div>
    </transition>

    <!-- REGISTER MODAL -->
    <transition name="modal-fade">
      <div v-if="showRegisterModal" class="modal-overlay" @click="closeRegisterModal">
        <div class="modal-box modal-box-wide" @click.stop>
          <button class="modal-close" @click="closeRegisterModal" aria-label="Close registration modal">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>

          <div class="modal-brand">
            <div class="modal-brand-name">RealtyLink<span class="gold">PH</span></div>
          </div>

          <div class="modal-head">
            <h1>Create Account</h1>
            <p>Start your real estate journey today</p>
          </div>

          <transition name="slide-down">
            <div v-if="registerAlert.show" :class="['m-alert', `m-alert-${registerAlert.type}`]">
              <span class="m-alert-icon">{{ registerAlert.type === 'error' ? '✕' : '✓' }}</span>
              <span>{{ registerAlert.message }}</span>
            </div>
          </transition>

          <form @submit.prevent="handleRegister" class="modal-form">
            <div class="field">
              <label for="register-name">Full Name</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input id="register-name" v-model="registerForm.name" type="text" placeholder="Juan Dela Cruz" @blur="validateRegisterField('name')" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.name" class="field-err">{{ registerErrors.name }}</span></transition>
            </div>

            <div class="field">
              <label for="register-email">Email Address</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input id="register-email" v-model="registerForm.email" type="email" placeholder="juan@example.com" @blur="validateRegisterField('email')" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.email" class="field-err">{{ registerErrors.email }}</span></transition>
            </div>

            <div class="field">
              <label for="register-password">Password</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="register-password" v-model="registerForm.password" :type="showRegisterPassword ? 'text' : 'password'" placeholder="Minimum 8 characters" @blur="validateRegisterField('password')" />
                <button type="button" class="field-eye" @click="showRegisterPassword = !showRegisterPassword" :aria-label="showRegisterPassword ? 'Hide password' : 'Show password'">
                  <svg v-if="!showRegisterPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
              </div>
              <transition name="slide-down"><span v-if="registerErrors.password" class="field-err">{{ registerErrors.password }}</span></transition>
            </div>

            <div class="field">
              <label for="register-confirm">Confirm Password</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="register-confirm" v-model="registerForm.confirm_password" :type="showRegisterConfirm ? 'text' : 'password'" placeholder="Re-enter password" @blur="validateRegisterField('confirm_password')" />
                <button type="button" class="field-eye" @click="showRegisterConfirm = !showRegisterConfirm" :aria-label="showRegisterConfirm ? 'Hide password' : 'Show password'">
                  <svg v-if="!showRegisterConfirm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
              </div>
              <transition name="slide-down"><span v-if="registerErrors.confirm_password" class="field-err">{{ registerErrors.confirm_password }}</span></transition>
            </div>

            <div class="check-group">
              <input id="register-terms" type="checkbox" v-model="registerForm.terms" @blur="validateRegisterField('terms')" />
              <label for="register-terms">I agree to the <a href="#" class="link-accent" @click.prevent>Terms & Conditions</a> and <a href="#" class="link-accent" @click.prevent>Privacy Policy</a></label>
            </div>
            <transition name="slide-down"><span v-if="registerErrors.terms" class="field-err">{{ registerErrors.terms }}</span></transition>

            <button type="submit" class="btn-primary" :disabled="registerLoading">
              <span v-if="!registerLoading">Create Account</span>
              <span v-else class="btn-loading-inner"><span class="dot-spinner"></span>Creating account...</span>
            </button>
          </form>

          <div class="modal-divider"><span>Already have an account?</span></div>
          <button @click="switchToLogin" class="btn-secondary">Sign In Instead</button>

          <div class="modal-footer-links">
            <a href="#" @click.prevent>Need help?</a>
            <span>·</span>
            <a href="#" @click.prevent>Contact Support</a>
          </div>
        </div>
      </div>
    </transition>

    <!-- GUEST VIEW PROPERTY PROMPT -->
    <transition name="modal-fade">
      <div v-if="showGuestPrompt" class="modal-overlay" @click="cancelGuestPrompt">
        <div class="modal-box guest-prompt-box" @click.stop>
          <button class="modal-close" @click="cancelGuestPrompt" aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>

          <div class="guest-prompt-icon">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>

          <div class="modal-head">
            <h1>Unlock Full Access</h1>
            <p>Create a free account to view property details, contact agents, and more.</p>
          </div>

          <div class="guest-prompt-features">
            <div class="guest-feature">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Full property photos & 360° virtual tours
            </div>
            <div class="guest-feature">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Message verified agents directly
            </div>
            <div class="guest-feature">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Schedule property viewings with calendar sync
            </div>
            <div class="guest-feature">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Save properties to your wishlist
            </div>
          </div>

          <button class="btn-primary" @click="showRegisterModal = true; cancelGuestPrompt()">
            Create Free Account
          </button>

          <div class="modal-divider"><span>Already have an account?</span></div>
          <button class="btn-secondary" @click="showLoginModal = true; cancelGuestPrompt()">Sign In</button>

          <div class="guest-continue">
            <button class="guest-continue-btn" @click="confirmViewProperty">
              Continue as guest →
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { authAPI } from '../src/services/api'

export default {
  name: 'Landing',
  setup() {
    const router = useRouter()
    const route  = useRoute()

    // Single source of truth for API base URL
    const apiBase = window.__API_URL__ || 'http://localhost:8000'

    // Navbar state
    const isScrolled = ref(false)
    const mobileNav = ref(false)

    // Search state
    const searchLocation = ref('')
    const searchType = ref('')
    const searchPrice = ref('')

    const handleSearch = () => {
      const query = {}
      if (searchLocation.value) query.location = searchLocation.value
      if (searchType.value) query.type = searchType.value
      if (searchPrice.value) query.price = searchPrice.value
      sessionStorage.setItem('landing_search', JSON.stringify({
        location: searchLocation.value,
        type: searchType.value,
        price: searchPrice.value
      }))
      router.push({ path: '/explore', query })
    }

    // Modal states
    const showLoginModal = ref(false)
    const showRegisterModal = ref(false)

    // Dynamic greeting
    const loginGreeting = computed(() => {
      const lastLogin = localStorage.getItem('last_login_time')
      if (!lastLogin) return 'Sign In'
      const daysSince = (Date.now() - parseInt(lastLogin)) / (1000 * 60 * 60 * 24)
      return daysSince >= 1 ? 'Welcome Back' : 'Sign In'
    })

    const loginView = ref('login')

    // Login state
    const loginLoading = ref(false)
    const showLoginPassword = ref(false)
    const loginForm = reactive({ email: '', password: '', remember: false })
    const loginErrors = reactive({ email: '', password: '' })
    const loginAlert = reactive({ show: false, type: 'success', message: '' })

    // Forgot password state
    const forgotLoading = ref(false)
    const forgotForm = reactive({ email: '' })
    const forgotErrors = reactive({ email: '' })
    const forgotAlert = reactive({ show: false, type: 'success', message: '' })

    // Register state
    const registerLoading = ref(false)
    const showRegisterPassword = ref(false)
    const showRegisterConfirm = ref(false)
    const registerForm = reactive({ name: '', email: '', password: '', confirm_password: '', terms: false })
    const registerErrors = reactive({ name: '', email: '', password: '', confirm_password: '', terms: '' })
    const registerAlert = reactive({ show: false, type: 'success', message: '' })

    // Stats with count-up animation
    const stats = reactive({ properties: 0, agents: 0, buyers: 0 })

    const animateCount = (target, key) => {
      const duration = 1600
      const startTime = Date.now()
      const step = () => {
        const elapsed = Date.now() - startTime
        const progress = Math.min(elapsed / duration, 1)
        const eased = 1 - Math.pow(1 - progress, 3) // ease-out cubic
        stats[key] = Math.round(eased * target)
        if (progress < 1) requestAnimationFrame(step)
      }
      requestAnimationFrame(step)
    }

    const loadStats = async () => {
      try {
        const res = await fetch(`${apiBase}/api/landing-stats`)
        const data = await res.json()
        if (data.success && data.data) {
          animateCount(data.data.properties || 0, 'properties')
          animateCount(data.data.agents || 0, 'agents')
          animateCount(data.data.buyers || 0, 'buyers')
        }
      } catch {
        // keep defaults at 0
      }
    }

    // Landing properties
    const landingProperties = ref([])
    const propertiesLoading = ref(false)

    const loadLandingProperties = async () => {
      propertiesLoading.value = true
      try {
        const res = await fetch(`${apiBase}/api/properties`)
        const data = await res.json()
        const paginated = data.data || data || {}
        const list = paginated.data || paginated || []
        landingProperties.value = Array.isArray(list) ? list.slice(0, 6) : []
      } catch {
        landingProperties.value = []
      } finally {
        propertiesLoading.value = false
      }
    }

    // Featured agents (conditional — section only shows if API returns data)
    const featuredAgents = ref([])

    const loadFeaturedAgents = async () => {
      try {
        const res = await fetch(`${apiBase}/api/landing-agents`)
        if (!res.ok) return
        const data = await res.json()
        featuredAgents.value = (data.data || data || []).slice(0, 3)
      } catch {
        // graceful — section stays hidden
      }
    }

    // Static data
    const testimonials = [
      {
        name: 'Maria Santos',
        role: 'First-time Homebuyer',
        location: 'Cebu City',
        initials: 'MS',
        rating: 5,
        text: 'RealtyLinkPH made finding our dream home so easy. The agents were verified and professional, and we closed the deal in just 3 weeks!'
      },
      {
        name: 'Juan dela Cruz',
        role: 'Property Investor',
        location: 'Makati, Metro Manila',
        initials: 'JC',
        rating: 5,
        text: "I've bought 3 properties through this platform. The 360° virtual tours saved me so much time, and the scheduling feature is a game-changer."
      },
      {
        name: 'Ana Reyes',
        role: 'Young Professional',
        location: 'BGC, Taguig',
        initials: 'AR',
        rating: 5,
        text: 'As someone new to real estate, I appreciated how the agents guided me through the whole process. Found my perfect condo in under a month.'
      }
    ]

    // Locations: starts with static fallback, overwritten with real API data
    const popularLocations = ref([
      { name: 'Metro Manila',   subtitle: 'Makati · BGC · Quezon City', query: 'Metro Manila' },
      { name: 'Cebu City',      subtitle: 'Queen City of the South',    query: 'Cebu' },
      { name: 'Davao City',     subtitle: 'Mindanao Hub',               query: 'Davao' },
      { name: 'Iloilo City',    subtitle: 'Western Visayas',            query: 'Iloilo' },
      { name: 'Cagayan de Oro', subtitle: 'Northern Mindanao',          query: 'Cagayan de Oro' },
      { name: 'Baguio City',    subtitle: 'Summer Capital of the PH',   query: 'Baguio' }
    ])

    const loadLocations = async () => {
      try {
        const res = await fetch(`${apiBase}/api/properties?per_page=200`)
        if (!res.ok) return
        const data = await res.json()
        const all = data.data?.data || data.data || data || []
        if (!Array.isArray(all) || all.length < 3) return

        const cityMap = {}
        all.forEach(p => {
          const city = p.city
          if (!city) return
          if (!cityMap[city]) cityMap[city] = { name: city, province: p.province || '', count: 0 }
          cityMap[city].count++
        })

        const sorted = Object.values(cityMap)
          .sort((a, b) => b.count - a.count)
          .slice(0, 6)
          .map(c => ({
            name: c.name,
            subtitle: `${c.count} ${c.count === 1 ? 'property' : 'properties'} listed${c.province ? ' · ' + c.province : ''}`,
            query: c.name
          }))

        if (sorted.length >= 3) popularLocations.value = sorted
      } catch {
        // keep static fallback
      }
    }

    // Returns an inline SVG string based on city name (used with v-html)
    const getCityIcon = (cityName) => {
      const n = (cityName || '').toLowerCase()
      if (n.includes('manila') || n.includes('makati') || n.includes('quezon') || n.includes('pasig') || n.includes('taguig') || n.includes('metro')) {
        return `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="1"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="9" y1="14" x2="9" y2="17"/><line x1="12" y1="14" x2="12" y2="17"/><line x1="15" y1="14" x2="15" y2="17"/></svg>`
      }
      if (n.includes('cebu')) {
        return `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12c1.5-2 3-2 4.5 0s3 2 4.5 0 3-2 4.5 0 3 2 4.5 0"/><path d="M2 17c1.5-2 3-2 4.5 0s3 2 4.5 0 3-2 4.5 0 3 2 4.5 0"/><path d="M2 7c1.5-2 3-2 4.5 0s3 2 4.5 0 3-2 4.5 0 3 2 4.5 0"/></svg>`
      }
      if (n.includes('davao') || n.includes('baguio') || n.includes('mountain') || n.includes('benguet')) {
        return `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20l5-10 4 6 3-4 6 8H3z"/></svg>`
      }
      if (n.includes('iloilo') || n.includes('zamboanga') || n.includes('subic') || n.includes('batangas')) {
        return `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h20"/><path d="M5 4v16"/><path d="M5 10l7-5 7 5"/></svg>`
      }
      if (n.includes('cagayan') || n.includes('mindanao')) {
        return `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>`
      }
      // Default: map pin
      return `<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`
    }

    const viewProperty = (id) => router.push(`/property/${id}`)

    // Guest prompt: shown when unauthenticated user clicks "View Details"
    const showGuestPrompt = ref(false)
    const guestPromptPropertyId = ref(null)

    const handleViewProperty = (id) => {
      const token = localStorage.getItem('auth_token')
      if (!token) {
        guestPromptPropertyId.value = id
        showGuestPrompt.value = true
        return
      }
      router.push(`/property/${id}`)
    }

    const confirmViewProperty = () => {
      const id = guestPromptPropertyId.value
      showGuestPrompt.value = false
      guestPromptPropertyId.value = null
      if (id) router.push(`/property/${id}`)
    }

    const cancelGuestPrompt = () => {
      showGuestPrompt.value = false
      guestPromptPropertyId.value = null
    }

    const browseByLocation = (query) => router.push({ path: '/explore', query: { location: query } })

    const scrollToFeatures = () => {
      const el = document.querySelector('.features')
      if (el) el.scrollIntoView({ behavior: 'smooth' })
    }

    const getCoverPhoto = (property) => {
      if (!property.photos || !property.photos.length) return null
      const cover = property.photos.find(p => p.is_cover) || property.photos[0]
      return cover ? cover.photo_url : null
    }

    const formatPrice = (price) => new Intl.NumberFormat('en-PH').format(price)

    const formatPropertyType = (type) => {
      const map = { house: 'House', condo: 'Condo', apartment: 'Apartment', commercial: 'Commercial', lot: 'Lot' }
      return map[type] || type
    }

    const getInitials = (name) => {
      if (!name) return '?'
      return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase()
    }

    // Scroll handler (named so it can be removed)
    const handleScroll = () => { isScrolled.value = window.scrollY > 20 }

    // Login methods
    const validateLoginField = (field) => {
      loginErrors[field] = ''
      if (field === 'email' && !loginForm.email) loginErrors.email = 'Email is required'
      if (field === 'password' && !loginForm.password) loginErrors.password = 'Password is required'
    }

    const handleLogin = async () => {
      validateLoginField('email')
      validateLoginField('password')
      if (loginErrors.email || loginErrors.password) return

      loginLoading.value = true
      try {
        const res = await authAPI.login(loginForm)
        localStorage.setItem('auth_token', res.data.token)
        localStorage.setItem('user', JSON.stringify(res.data.user))
        localStorage.setItem('last_login_time', Date.now().toString())
        if (window.reconnectEcho) window.reconnectEcho()
        if (window.__showLoading) window.__showLoading()
        showLoginModal.value = false

        const userData = res.data.user
        const redirectTo = route.query.redirect
        if (redirectTo) router.push(redirectTo)
        else if (userData.role === 'admin') router.push('/admin/dashboard')
        else if (userData.role === 'agent') router.push('/agent/dashboard')
        else router.push('/dashboard')
      } catch {
        loginAlert.show = true
        loginAlert.type = 'error'
        loginAlert.message = 'Invalid credentials. Please try again.'
        setTimeout(() => { loginAlert.show = false }, 3000)
      } finally {
        loginLoading.value = false
      }
    }

    const handleForgot = async () => {
      forgotErrors.email = ''
      if (!forgotForm.email) { forgotErrors.email = 'Email is required'; return }

      forgotLoading.value = true
      try {
        const res = await fetch(`${apiBase}/api/auth/forgot-password`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ email: forgotForm.email })
        })
        const data = await res.json()
        if (res.ok) {
          loginView.value = 'forgot-sent'
        } else {
          forgotAlert.show = true
          forgotAlert.type = 'error'
          forgotAlert.message = data.message || 'Email not found. Please check and try again.'
          setTimeout(() => { forgotAlert.show = false }, 4000)
        }
      } catch {
        forgotAlert.show = true
        forgotAlert.type = 'error'
        forgotAlert.message = 'Something went wrong. Please try again.'
        setTimeout(() => { forgotAlert.show = false }, 4000)
      } finally {
        forgotLoading.value = false
      }
    }

    const closeLoginModal = () => { showLoginModal.value = false; loginView.value = 'login' }

    // Register methods
    const validateRegisterField = (field) => {
      registerErrors[field] = ''
      if (field === 'name' && !registerForm.name) registerErrors.name = 'Full name is required'
      if (field === 'email' && !registerForm.email) registerErrors.email = 'Email address is required'
      if (field === 'password' && registerForm.password.length < 8) registerErrors.password = 'Password must be at least 8 characters'
      if (field === 'confirm_password' && registerForm.password !== registerForm.confirm_password) registerErrors.confirm_password = 'Passwords do not match'
      if (field === 'terms' && !registerForm.terms) registerErrors.terms = 'You must accept the terms and privacy policy'
    }

    const handleRegister = async () => {
      Object.keys(registerForm).forEach(validateRegisterField)
      if (Object.values(registerErrors).some(Boolean)) {
        registerAlert.show = true
        registerAlert.type = 'error'
        registerAlert.message = 'Please fix the errors in the form'
        setTimeout(() => { registerAlert.show = false }, 3000)
        return
      }

      registerLoading.value = true
      try {
        await authAPI.register({
          name: registerForm.name,
          email: registerForm.email,
          password: registerForm.password,
          password_confirmation: registerForm.confirm_password
        })
        registerAlert.message = 'Account created successfully! Redirecting to login...'
        registerAlert.type = 'success'
        registerAlert.show = true
        setTimeout(() => {
          showRegisterModal.value = false
          showLoginModal.value = true
          registerForm.name = ''
          registerForm.email = ''
          registerForm.password = ''
          registerForm.confirm_password = ''
          registerForm.terms = false
        }, 1500)
      } catch (e) {
        registerAlert.message = e.response?.data?.message || 'Registration failed. Please try again.'
        registerAlert.type = 'error'
        registerAlert.show = true
        setTimeout(() => { registerAlert.show = false }, 3000)
      } finally {
        registerLoading.value = false
      }
    }

    const closeRegisterModal = () => { showRegisterModal.value = false }
    const switchToRegister = () => { showLoginModal.value = false; showRegisterModal.value = true }
    const switchToLogin = () => { showRegisterModal.value = false; showLoginModal.value = true }

    onMounted(() => {
      window.addEventListener('scroll', handleScroll)
      loadStats()
      loadLandingProperties()
      loadFeaturedAgents()
      loadLocations()
      // Auto-open the right modal when routed to /login or /register
      if (route.path === '/login') showLoginModal.value = true
      else if (route.path === '/register') showRegisterModal.value = true
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
    })

    return {
      isScrolled, mobileNav,
      searchLocation, searchType, searchPrice, handleSearch,
      showLoginModal, loginView, loginGreeting, loginForm, loginErrors, loginAlert, loginLoading, showLoginPassword,
      validateLoginField, handleLogin, closeLoginModal,
      forgotForm, forgotErrors, forgotAlert, forgotLoading, handleForgot,
      showRegisterModal, registerForm, registerErrors, registerAlert, registerLoading,
      showRegisterPassword, showRegisterConfirm, validateRegisterField, handleRegister, closeRegisterModal,
      switchToRegister, switchToLogin,
      stats,
      landingProperties, propertiesLoading, getCoverPhoto, formatPrice, formatPropertyType, viewProperty,
      featuredAgents, getInitials,
      testimonials, popularLocations, browseByLocation, scrollToFeatures,
      getCityIcon,
      showGuestPrompt, handleViewProperty, confirmViewProperty, cancelGuestPrompt
    }
  }
}
</script>

<!-- Google Fonts: loaded in non-scoped block for better performance -->
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Sora:wght@300;400;500;600;700;800&display=swap');
</style>

<style scoped>
/* ═══════════════════════════════════════════════════
   LANDING PAGE — Deep Navy + Amber Palette
   #0C1A2E · #162640 · #253D58 · #F0B429 · #E7E7E7
   Typography: DM Serif Display (Headlines) + Sora (UI)
   ═══════════════════════════════════════════════════ */

.landing-page {
  --navy:        #0C1A2E;
  --navy-light:  #162640;
  --navy-mid:    #253D58;
  --frost:       #F0B429;
  --frost-light: #F7CA55;
  --frost-pale:  #FDE68A;
  --ash:         #E7E7E7;
  --ash-light:   #F4F6F8;

  --gold:        #F0B429;
  --gold-dark:   #C98A0A;
  --gold-light:  #F7CA55;
  --gold-faded:  rgba(240, 180, 41, 0.10);
  --gold-glow:   rgba(240, 180, 41, 0.30);

  --stone-50:  #F8F9FA;
  --stone-100: #F2F3F5;
  --stone-200: #E2E6EA;
  --stone-300: #C5CDD6;
  --stone-400: #8E9BAA;
  --stone-500: #637283;
  --stone-600: #475669;
  --stone-700: #2E3F52;
  --stone-800: #0C1A2E;
  --white:     #FFFFFF;

  --success: #16A34A;
  --error:   #DC2626;

  --font-ui:      'Sora', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-display: 'DM Serif Display', Georgia, serif;
  --font-brand:   'Sora', -apple-system, sans-serif;

  --radius:      14px;
  --radius-sm:   8px;
  --radius-lg:   20px;
  --radius-pill: 50px;

  font-family: var(--font-ui);
  background: var(--white);
  color: var(--stone-800);
  min-height: 100vh;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
  line-height: 1.6;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* ═══════════════════════════════════════
   NAVBAR
   ═══════════════════════════════════════ */
.navbar {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 1000;
  padding: 20px 0;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.navbar.scrolled {
  background: rgba(12, 26, 46, 0.96);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  padding: 14px 0;
  box-shadow: 0 1px 0 rgba(240, 180, 41, 0.08), 0 8px 32px rgba(0,0,0,0.18);
}

.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  font-size: 22px;
  color: var(--white);
  text-decoration: none;
  letter-spacing: -0.3px;
  font-family: var(--font-brand);
  display: inline-flex;
  align-items: baseline;
}

.brand-realty { font-weight: 300; color: rgba(255,255,255,0.75); }
.brand-link   { font-weight: 800; color: var(--white); }
.brand-ph     { font-weight: 800; color: var(--frost); }

.nav-hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px;
}

.nav-hamburger span {
  width: 22px; height: 2px;
  background: var(--white);
  border-radius: 2px;
  transition: 0.3s;
}

.nav-actions {
  display: flex;
  gap: 10px;
  align-items: center;
}

.btn-signin {
  padding: 9px 22px;
  background: transparent;
  color: rgba(255,255,255,0.80);
  border: 1.5px solid rgba(240,180,41,0.25);
  border-radius: var(--radius-pill);
  font-weight: 500; font-size: 13px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
}

.btn-signin:hover {
  color: var(--white);
  border-color: rgba(240,180,41,0.55);
  background: rgba(240,180,41,0.08);
}

.btn-get-started {
  padding: 9px 24px;
  background: var(--frost);
  color: var(--navy);
  border: none;
  border-radius: var(--radius-pill);
  font-weight: 700; font-size: 13px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font-ui);
  box-shadow: 0 4px 16px rgba(240,180,41,0.30);
}

.btn-get-started:hover {
  background: var(--frost-light);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(240,180,41,0.40);
}

/* ═══════════════════════════════════════
   HERO
   ═══════════════════════════════════════ */
.hero {
  position: relative;
  min-height: 95vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: url('/images/logo/landingpagepicture.jpg') center/cover no-repeat;
  padding: 130px 0 110px;
}

.hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(180deg, rgba(12,26,46,0.88) 0%, rgba(12,26,46,0.55) 40%, rgba(12,26,46,0.82) 100%);
}

.hero-grain {
  position: absolute; inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
  background-size: 200px 200px;
  opacity: 0.6;
  pointer-events: none;
}

.hero-content {
  position: relative; z-index: 1;
  text-align: center; width: 100%;
}

.hero-badge {
  display: inline-block;
  font-size: 11px; font-weight: 600;
  text-transform: uppercase; letter-spacing: 2.5px;
  color: var(--frost);
  background: rgba(240,180,41,0.10);
  border: 1px solid rgba(240,180,41,0.22);
  padding: 8px 22px;
  border-radius: var(--radius-pill);
  margin-bottom: 30px;
  backdrop-filter: blur(8px);
}

.hero-title {
  font-family: var(--font-display);
  font-size: 4.4rem; font-weight: 400;
  color: var(--white);
  line-height: 1.06;
  margin-bottom: 24px;
  letter-spacing: -0.5px;
}

.text-accent { color: var(--frost); font-style: italic; }

.hero-sub {
  font-size: 1.05rem;
  color: rgba(255,255,255,0.58);
  max-width: 560px;
  margin: 0 auto 48px;
  line-height: 1.75; font-weight: 300;
}

/* ─── Search Bar ─── */
.search-bar {
  display: flex;
  align-items: stretch;
  background: rgba(12,26,46,0.55);
  backdrop-filter: blur(28px);
  -webkit-backdrop-filter: blur(28px);
  border: 1px solid rgba(240,180,41,0.18);
  border-radius: var(--radius-lg);
  padding: 8px;
  box-shadow: 0 24px 64px rgba(0,0,0,0.28), inset 0 1px 0 rgba(240,180,41,0.12), inset 0 -1px 0 rgba(0,0,0,0.1);
  max-width: 860px;
  margin: 0 auto 56px;
}

.search-field {
  flex: 1; padding: 12px 20px;
  display: flex; flex-direction: column; gap: 4px;
}

.search-field label {
  font-size: 10px; font-weight: 700;
  text-transform: uppercase;
  color: rgba(240,180,41,0.60);
  letter-spacing: 1.2px;
}

.search-field input,
.search-field select {
  border: none; outline: none;
  font-size: 14px; font-weight: 500;
  color: var(--white);
  background: transparent;
  font-family: var(--font-ui);
  padding: 0; cursor: pointer;
}

.search-field input::placeholder { color: rgba(255,255,255,0.32); }
.search-field select option { color: var(--navy); background: var(--white); }

.search-sep {
  width: 1px;
  background: rgba(240,180,41,0.12);
  margin: 10px 0;
}

.search-btn {
  display: flex; align-items: center; gap: 8px;
  padding: 14px 30px;
  background: var(--frost);
  color: var(--navy);
  border: none;
  border-radius: calc(var(--radius-lg) - 8px);
  font-weight: 700; font-size: 14px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font-ui);
  white-space: nowrap;
  box-shadow: 0 4px 20px rgba(240,180,41,0.28);
}

.search-btn:hover {
  background: var(--frost-light);
  transform: translateY(-1px);
  box-shadow: 0 8px 28px rgba(240,180,41,0.38);
}

/* ─── Stats ─── */
.hero-stats {
  display: flex; align-items: center; justify-content: center; gap: 44px;
}

.stat-block {
  display: flex; flex-direction: column; align-items: center; gap: 3px;
}

.stat-num {
  font-family: var(--font-display);
  font-size: 2.4rem; font-weight: 400;
  color: var(--frost);
  letter-spacing: -0.5px; line-height: 1;
}

.stat-lbl {
  font-size: 11px;
  color: rgba(255,255,255,0.42);
  font-weight: 500; letter-spacing: 0.1em;
  text-transform: uppercase;
}

.stat-divider {
  width: 1px; height: 38px;
  background: rgba(240,180,41,0.15);
}

/* ─── Social Proof Ticker (animated marquee) ─── */
.social-proof-ticker {
  margin-top: 32px;
  overflow: hidden;
}

.ticker-track {
  display: flex;
  align-items: center;
  gap: 24px;
  width: max-content;
  animation: tickerScroll 28s linear infinite;
}

.social-proof-ticker:hover .ticker-track {
  animation-play-state: paused;
}

@keyframes tickerScroll {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.ticker-item {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; font-weight: 400;
  color: rgba(255,255,255,0.42);
  letter-spacing: 0.03em;
  white-space: nowrap;
}

.ticker-dot {
  width: 5px; height: 5px;
  border-radius: 50%;
  background: var(--frost);
  opacity: 0.7; flex-shrink: 0;
}

.ticker-sep {
  color: rgba(255,255,255,0.15);
  font-size: 16px; font-weight: 300;
}

/* ─── Hero Scroll Indicator ─── */
.hero-scroll-indicator {
  position: absolute;
  bottom: 32px; left: 50%;
  transform: translateX(-50%);
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  background: none; border: none;
  cursor: pointer;
  color: rgba(255,255,255,0.35);
  transition: color 0.3s;
  animation: scrollBounce 2.2s ease-in-out infinite;
  z-index: 2;
}

.hero-scroll-indicator:hover { color: var(--frost); }

.scroll-ind-line {
  width: 1px; height: 28px;
  background: currentColor;
  opacity: 0.5;
}

@keyframes scrollBounce {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50%       { transform: translateX(-50%) translateY(7px); }
}

/* ═══════════════════════════════════════
   FEATURE CARDS
   ═══════════════════════════════════════ */
.features {
  position: relative;
  margin-top: -64px; z-index: 2;
  padding-bottom: 88px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.feat-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  padding: 36px 28px;
  text-align: center;
  border: 1px solid var(--stone-200);
  box-shadow: 0 4px 24px rgba(12,26,46,0.06), 0 1px 3px rgba(12,26,46,0.04);
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
}

.feat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 52px rgba(12,26,46,0.10);
  border-color: var(--frost-pale);
}

.feat-highlight {
  background: linear-gradient(145deg, var(--navy) 0%, var(--navy-light) 100%);
  border-color: rgba(240,180,41,0.20);
  box-shadow: 0 8px 32px rgba(12,26,46,0.20), 0 0 0 1px rgba(240,180,41,0.08) inset;
}

.feat-highlight .feat-icon { background: rgba(240,180,41,0.12); color: var(--frost); border-color: rgba(240,180,41,0.20); }
.feat-highlight h3 { color: var(--white); }
.feat-highlight p  { color: rgba(255,255,255,0.52); }

.feat-icon {
  width: 60px; height: 60px; border-radius: 50%;
  background: var(--gold-faded); color: var(--navy-mid);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 22px;
  border: 2px solid rgba(240,180,41,0.15);
  transition: all 0.3s;
}

.feat-card:hover .feat-icon { background: rgba(240,180,41,0.15); }

.feat-card h3 {
  font-family: var(--font-display);
  font-size: 20px; font-weight: 400;
  color: var(--stone-800); margin-bottom: 10px;
}

.feat-card p { font-size: 14px; color: var(--stone-500); line-height: 1.7; font-weight: 300; }

/* ═══════════════════════════════════════
   SECTION COMMON
   ═══════════════════════════════════════ */
.section-head { text-align: center; margin-bottom: 56px; }

.section-badge {
  display: inline-block;
  font-size: 11px; font-weight: 600;
  text-transform: uppercase; letter-spacing: 2.5px;
  color: var(--navy-mid); margin-bottom: 14px;
}

.section-title {
  font-family: var(--font-display);
  font-size: 2.7rem; font-weight: 400;
  color: var(--navy); margin-bottom: 14px;
  letter-spacing: -0.3px; line-height: 1.12;
}

.section-desc {
  font-size: 1rem; color: var(--stone-500);
  max-width: 540px; margin: 0 auto;
  line-height: 1.70; font-weight: 300;
}

.section-cta { text-align: center; margin-top: 52px; }

.btn-outline-dark {
  padding: 14px 36px;
  background: transparent; color: var(--navy);
  border: 1.5px solid var(--navy);
  border-radius: var(--radius-pill);
  font-weight: 600; font-size: 14px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font-ui);
}

.btn-outline-dark:hover {
  background: var(--navy); color: var(--frost-light);
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(12,26,46,0.18);
}

/* ═══════════════════════════════════════
   FEATURED PROPERTIES
   ═══════════════════════════════════════ */
.properties-section {
  padding: 100px 0;
  background: var(--ash-light);
  background-image:
    radial-gradient(ellipse at 20% 50%, rgba(240,180,41,0.06) 0%, transparent 60%),
    radial-gradient(ellipse at 80% 20%, rgba(69,87,109,0.04) 0%, transparent 50%);
}

.prop-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.prop-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 1px solid var(--stone-200);
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
  cursor: pointer;
}

.prop-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 24px 52px rgba(12,26,46,0.10);
  border-color: var(--frost-pale);
}

.prop-img-wrap {
  position: relative; height: 230px;
  overflow: hidden; background: var(--stone-100);
}

.prop-img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.16,1,0.3,1);
}

.prop-card:hover .prop-img { transform: scale(1.06); }

.prop-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  color: var(--stone-400); background: var(--stone-100);
}

.prop-price-pill {
  position: absolute; bottom: 12px; left: 12px;
  background: rgba(12,26,46,0.90); backdrop-filter: blur(10px);
  color: var(--frost-light);
  padding: 6px 14px; border-radius: var(--radius-sm);
  font-weight: 700; font-size: 14px;
  font-family: var(--font-display);
  z-index: 3;
  box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  border: 1px solid rgba(240,180,41,0.18);
}

.prop-badges {
  position: absolute; top: 10px; left: 10px;
  display: flex; gap: 5px; z-index: 3;
}

.prop-badge {
  display: flex; align-items: center; gap: 4px;
  padding: 4px 10px; border-radius: var(--radius-pill);
  font-size: 10px; font-weight: 700;
  letter-spacing: 0.3px; text-transform: uppercase;
}

.badge-featured { background: var(--frost); color: var(--navy); box-shadow: 0 2px 8px rgba(240,180,41,0.35); }
.badge-360 { background: rgba(12,26,46,0.80); color: var(--white); border: 1px solid rgba(255,255,255,0.18); backdrop-filter: blur(4px); }

.prop-img-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(12,26,46,0.65) 0%, rgba(12,26,46,0.10) 50%, transparent 100%);
  z-index: 2; pointer-events: none;
}

.prop-body { padding: 22px; }

.prop-meta-row {
  display: flex; align-items: center;
  justify-content: space-between; margin-bottom: 9px;
}

.prop-type-tag {
  display: inline-block;
  font-size: 10px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.6px;
  color: var(--navy-light);
  background: rgba(240,180,41,0.14);
  padding: 4px 12px; border-radius: var(--radius-pill);
}

.prop-verified-tag {
  display: flex; align-items: center; gap: 4px;
  font-size: 10px; font-weight: 700;
  color: #10B981; text-transform: uppercase; letter-spacing: 0.4px;
}

.prop-title {
  font-family: var(--font-display);
  font-size: 17px; font-weight: 400;
  margin-bottom: 6px; color: var(--navy); line-height: 1.3;
}

.prop-location {
  display: flex; align-items: center; gap: 5px;
  font-size: 13px; color: var(--stone-500);
  margin-bottom: 14px; font-weight: 300;
}

.prop-location svg { flex-shrink: 0; color: var(--stone-400); }

.prop-specs {
  display: flex; gap: 6px; flex-wrap: wrap;
  padding-top: 12px; border-top: 1px solid var(--stone-100);
}

.spec-pill {
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; font-weight: 500;
  color: var(--stone-600); background: var(--stone-100);
  padding: 4px 10px; border-radius: var(--radius-pill);
}

.spec-pill svg { color: var(--stone-400); flex-shrink: 0; }

.prop-trust-tags {
  display: flex; gap: 6px; flex-wrap: wrap;
  margin-top: 10px; padding-top: 10px;
  border-top: 1px solid var(--stone-100);
}

.trust-tag {
  display: flex; align-items: center; gap: 4px;
  font-size: 10px; font-weight: 600;
  color: #10B981;
  background: rgba(16,185,129,0.07);
  border: 1px solid rgba(16,185,129,0.16);
  padding: 3px 9px; border-radius: var(--radius-pill);
  text-transform: uppercase; letter-spacing: 0.3px;
}

.trust-tag svg { flex-shrink: 0; }

.tag-flood {
  color: var(--frost);
  background: rgba(240,180,41,0.08);
  border-color: rgba(240,180,41,0.18);
}

/* View Details button */
.prop-view-btn {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  margin-top: 14px;
  padding: 9px 16px;
  font-size: 13px; font-weight: 600;
  color: var(--navy);
  border: 1.5px solid var(--stone-200);
  border-radius: var(--radius-pill);
  transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
}

.prop-card:hover .prop-view-btn {
  background: var(--navy);
  color: var(--frost-light);
  border-color: var(--navy);
}

/* Loading / Empty */
.prop-loading { text-align: center; padding: 60px 0; color: var(--stone-500); }

.loading-dots {
  display: flex; justify-content: center; gap: 6px; margin-bottom: 16px;
}

.loading-dots span {
  width: 9px; height: 9px; border-radius: 50%;
  background: var(--frost);
  animation: dotPulse 1.2s ease-in-out infinite;
}

.loading-dots span:nth-child(2) { animation-delay: 0.15s; }
.loading-dots span:nth-child(3) { animation-delay: 0.30s; }

@keyframes dotPulse {
  0%, 80%, 100% { opacity: 0.3; transform: scale(0.8); }
  40%           { opacity: 1;   transform: scale(1); }
}

.prop-empty {
  text-align: center; padding: 60px 0; color: var(--stone-500);
  display: flex; flex-direction: column; align-items: center; gap: 12px;
}

/* ═══════════════════════════════════════
   BROWSE BY LOCATION
   ═══════════════════════════════════════ */
.locations-section {
  padding: 100px 0;
  background: var(--white);
}

.locations-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.location-card {
  position: relative;
  background: var(--stone-50);
  border-radius: var(--radius-lg);
  padding: 32px 28px 44px;
  border: 1px solid var(--stone-200);
  cursor: pointer;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
}

.location-card::before {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
  opacity: 0;
  transition: opacity 0.4s;
}

.location-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(12,26,46,0.12); }
.location-card:hover::before { opacity: 1; }
.location-card:hover .location-name     { color: var(--white); }
.location-card:hover .location-subtitle { color: rgba(255,255,255,0.50); }
.location-card:hover .location-arrow    { background: var(--frost); color: var(--navy); }

.location-icon {
  position: relative; z-index: 1;
  width: 52px; height: 52px;
  background: rgba(240,180,41,0.08);
  border: 1px solid rgba(240,180,41,0.14);
  border-radius: 14px;
  margin-bottom: 16px;
  display: flex; align-items: center; justify-content: center;
  color: var(--frost);
  transition: all 0.3s;
}

.location-card:hover .location-icon {
  background: rgba(240,180,41,0.18);
  border-color: rgba(240,180,41,0.30);
  color: var(--white);
}

.location-name {
  position: relative; z-index: 1;
  font-family: var(--font-display);
  font-size: 19px; font-weight: 400;
  color: var(--navy); margin-bottom: 5px;
  transition: color 0.3s;
}

.location-subtitle {
  position: relative; z-index: 1;
  font-size: 13px; color: var(--stone-500);
  font-weight: 300;
  transition: color 0.3s;
}

.location-arrow {
  position: absolute; bottom: 20px; right: 20px; z-index: 1;
  width: 34px; height: 34px; border-radius: 50%;
  background: rgba(240,180,41,0.10); color: var(--frost);
  display: flex; align-items: center; justify-content: center;
  transition: all 0.3s;
}

/* ═══════════════════════════════════════
   FEATURED AGENTS
   ═══════════════════════════════════════ */
.agents-section {
  padding: 100px 0;
  background: var(--ash-light);
}

.agents-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.agent-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  padding: 40px 24px 32px;
  text-align: center;
  border: 1px solid var(--stone-200);
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
}

.agent-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 48px rgba(12,26,46,0.10);
  border-color: var(--frost-pale);
}

.agent-avatar {
  width: 80px; height: 80px;
  border-radius: 50%; margin: 0 auto 16px;
  overflow: hidden;
  border: 3px solid var(--frost-pale);
  background: var(--stone-100);
}

.agent-avatar img { width: 100%; height: 100%; object-fit: cover; }

.agent-avatar-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-display);
  font-size: 26px; color: var(--navy-mid);
  background: linear-gradient(135deg, var(--stone-100) 0%, var(--stone-200) 100%);
}

.agent-verified-badge {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 10px; font-weight: 700;
  color: #10B981;
  background: rgba(16,185,129,0.08);
  border: 1px solid rgba(16,185,129,0.18);
  padding: 4px 10px; border-radius: var(--radius-pill);
  text-transform: uppercase; letter-spacing: 0.4px;
  margin-bottom: 12px;
}

.agent-name {
  font-family: var(--font-display);
  font-size: 18px; font-weight: 400;
  color: var(--navy); margin-bottom: 4px;
}

.agent-listings {
  font-size: 13px; color: var(--stone-500);
  font-weight: 300; margin-bottom: 20px;
}

.agent-btn {
  padding: 9px 28px;
  background: transparent; color: var(--navy);
  border: 1.5px solid var(--navy);
  border-radius: var(--radius-pill);
  font-weight: 600; font-size: 13px;
  cursor: pointer; transition: all 0.3s;
  font-family: var(--font-ui);
}

.agent-btn:hover { background: var(--navy); color: var(--frost-light); }

/* ═══════════════════════════════════════
   TESTIMONIALS
   ═══════════════════════════════════════ */
.testimonials-section {
  padding: 100px 0;
  background: linear-gradient(160deg, var(--navy) 0%, var(--navy-light) 100%);
  position: relative; overflow: hidden;
}

.testimonials-section::before {
  content: '';
  position: absolute; top: -100px; left: -100px;
  width: 420px; height: 420px; border-radius: 50%;
  background: radial-gradient(circle, rgba(240,180,41,0.06) 0%, transparent 70%);
  pointer-events: none;
}

.testimonials-section::after {
  content: '';
  position: absolute; bottom: -80px; right: -80px;
  width: 300px; height: 300px; border-radius: 50%;
  background: radial-gradient(circle, rgba(240,180,41,0.04) 0%, transparent 70%);
  pointer-events: none;
}

.testimonials-section .section-badge  { color: rgba(240,180,41,0.70); }
.testimonials-section .section-title  { color: var(--white); }
.testimonials-section .section-desc   { color: rgba(255,255,255,0.40); }

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  position: relative; z-index: 1;
}

.testimonial-card {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(240,180,41,0.10);
  border-radius: var(--radius-lg);
  padding: 32px 28px;
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
}

.testimonial-card:hover {
  background: rgba(255,255,255,0.07);
  border-color: rgba(240,180,41,0.22);
  transform: translateY(-4px);
}

.testimonial-stars {
  display: flex; gap: 3px;
  margin-bottom: 18px; color: var(--frost);
}

.testimonial-text {
  font-size: 14.5px;
  color: rgba(255,255,255,0.68);
  line-height: 1.78; font-weight: 300;
  margin-bottom: 24px; font-style: italic;
}

.testimonial-author { display: flex; align-items: center; gap: 12px; }

.testimonial-avatar {
  width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
  background: linear-gradient(135deg, var(--navy-mid) 0%, var(--navy-light) 100%);
  border: 2px solid rgba(240,180,41,0.22);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-display);
  font-size: 14px; color: var(--frost-light); font-weight: 600;
}

.t-name     { font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 2px; }
.t-role     { font-size: 12px; color: rgba(255,255,255,0.35); font-weight: 300; }
.t-location { font-size: 11px; color: var(--frost); font-weight: 400; margin-top: 2px; }

/* ═══════════════════════════════════════
   HOW IT WORKS
   ═══════════════════════════════════════ */
.steps-section { padding: 100px 0; background: var(--white); }

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
}

.step-card {
  position: relative; text-align: center;
  padding: 48px 32px;
  background: var(--stone-50);
  border-radius: var(--radius-lg);
  border: 1px solid var(--stone-200);
  transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
  overflow: hidden;
}

.step-card::before {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(240,180,41,0.05) 0%, transparent 60%);
  opacity: 0; transition: opacity 0.4s;
}

.step-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(12,26,46,0.08); border-color: rgba(240,180,41,0.35); }
.step-card:hover::before { opacity: 1; }

.step-icon-wrap {
  width: 64px; height: 64px; border-radius: 50%;
  background: rgba(240,180,41,0.12); color: var(--navy-mid);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 22px;
  border: 2px solid rgba(240,180,41,0.18);
  transition: all 0.3s;
}

.step-card:hover .step-icon-wrap {
  background: rgba(240,180,41,0.20);
  border-color: rgba(240,180,41,0.35);
}

.step-num {
  font-family: var(--font-display);
  font-size: 56px; font-weight: 400;
  color: rgba(240,180,41,0.10);
  position: absolute; top: 16px; right: 22px;
  line-height: 1; font-style: italic;
}

.step-card h3 { font-family: var(--font-display); font-size: 20px; font-weight: 400; margin-bottom: 10px; color: var(--navy); }
.step-card p  { font-size: 14px; color: var(--stone-500); line-height: 1.70; font-weight: 300; }

/* ═══════════════════════════════════════
   CTA BANNER
   ═══════════════════════════════════════ */
.cta-section {
  padding: 96px 0;
  background: linear-gradient(140deg, var(--navy) 0%, var(--navy-light) 100%);
  position: relative; overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute; top: -80px; right: -80px;
  width: 320px; height: 320px; border-radius: 50%;
  background: radial-gradient(circle, rgba(240,180,41,0.08) 0%, transparent 70%);
  pointer-events: none;
}

.cta-section::after {
  content: '';
  position: absolute; bottom: -60px; left: -60px;
  width: 240px; height: 240px; border-radius: 50%;
  background: radial-gradient(circle, rgba(240,180,41,0.05) 0%, transparent 70%);
  pointer-events: none;
}

.cta-inner { text-align: center; position: relative; z-index: 1; }

.cta-inner h2 {
  font-family: var(--font-display);
  font-size: 2.8rem; font-weight: 400;
  color: var(--white); margin-bottom: 14px; letter-spacing: -0.3px;
}

.cta-inner p {
  font-size: 1.05rem;
  color: rgba(255,255,255,0.48);
  margin-bottom: 40px;
  max-width: 480px; margin-left: auto; margin-right: auto;
  font-weight: 300; line-height: 1.70;
}

.cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

.btn-cta {
  padding: 16px 48px;
  background: var(--frost); color: var(--navy);
  border: none; border-radius: var(--radius-pill);
  font-weight: 700; font-size: 15px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font-ui);
  box-shadow: 0 4px 24px rgba(240,180,41,0.30);
}

.btn-cta:hover {
  background: var(--frost-light);
  transform: translateY(-3px);
  box-shadow: 0 12px 40px rgba(240,180,41,0.40);
}

.btn-cta-outline {
  padding: 16px 40px;
  background: transparent; color: rgba(255,255,255,0.80);
  border: 1.5px solid rgba(255,255,255,0.22);
  border-radius: var(--radius-pill);
  font-weight: 600; font-size: 15px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
}

.btn-cta-outline:hover {
  border-color: rgba(255,255,255,0.50);
  color: var(--white);
  background: rgba(255,255,255,0.06);
}

/* ═══════════════════════════════════════
   FOOTER
   ═══════════════════════════════════════ */
.site-footer {
  padding: 44px 0;
  background: var(--navy);
  border-top: 1px solid rgba(240,180,41,0.07);
}

.footer-inner { text-align: center; }

.footer-brand {
  font-family: var(--font-brand);
  font-size: 20px; font-weight: 700;
  color: rgba(255,255,255,0.75);
  margin-bottom: 18px; letter-spacing: -0.2px;
}

.footer-brand span { color: var(--frost); }

.footer-links {
  display: flex; justify-content: center; gap: 28px; margin-bottom: 18px;
}

.footer-links a {
  font-size: 13px; color: rgba(255,255,255,0.35);
  text-decoration: none; transition: color 0.3s; font-weight: 300;
}

.footer-links a:hover { color: var(--frost); }

.footer-copy { font-size: 12px; color: rgba(255,255,255,0.20); font-weight: 300; }

/* ═══════════════════════════════════════════════
   MODAL STYLES
   ═══════════════════════════════════════════════ */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(8,16,30,0.72);
  display: flex; align-items: center; justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  padding: 20px;
}

.modal-box {
  background: linear-gradient(165deg, rgba(18,32,52,0.98) 0%, rgba(8,16,30,0.99) 100%);
  border: 1px solid rgba(240,180,41,0.14);
  border-radius: 22px; width: 100%; max-width: 440px;
  padding: 40px 36px; position: relative;
  max-height: 90vh; overflow-y: auto;
  box-shadow: 0 32px 80px rgba(0,0,0,0.45), 0 0 0 1px rgba(255,255,255,0.03) inset, inset 0 1px 0 rgba(240,180,41,0.08);
}

.modal-box-wide { max-width: 480px; padding: 36px 32px; }

.modal-close {
  position: absolute; top: 16px; right: 16px;
  width: 36px; height: 36px; border-radius: 50%;
  border: 1px solid rgba(240,180,41,0.10);
  background: rgba(240,180,41,0.04);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: rgba(255,255,255,0.35); transition: all 0.3s;
}

.modal-close:hover {
  background: rgba(240,180,41,0.10);
  color: rgba(255,255,255,0.75);
  border-color: rgba(240,180,41,0.22);
}

.modal-brand { text-align: center; margin-bottom: 28px; }

.modal-brand-name {
  font-size: 26px; font-weight: 700;
  color: #FAFAF9; font-family: var(--font-brand); letter-spacing: -0.3px;
}

.modal-brand-name .gold { color: var(--frost); }

.modal-head { text-align: center; margin-bottom: 28px; }
.modal-head h1 { font-family: var(--font-display); font-size: 24px; font-weight: 400; color: #FAFAF9; margin-bottom: 6px; }
.modal-head p  { font-size: 14px; color: rgba(255,255,255,0.40); font-weight: 300; }

.m-alert {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px; border-radius: 12px;
  margin-bottom: 18px; font-size: 13px;
}

.m-alert-success { background: rgba(16,185,129,0.10); color: #6EE7B7; border: 1px solid rgba(16,185,129,0.18); }
.m-alert-error   { background: rgba(239,68,68,0.10);  color: #FCA5A5; border: 1px solid rgba(239,68,68,0.18); }

.m-alert-icon {
  width: 22px; height: 22px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 800; flex-shrink: 0;
}

.m-alert-success .m-alert-icon { background: rgba(16,185,129,0.22); color: #6EE7B7; }
.m-alert-error   .m-alert-icon { background: rgba(239,68,68,0.22);  color: #FCA5A5; }

.modal-form { display: flex; flex-direction: column; }
.field { margin-bottom: 18px; }

.field label {
  display: block; font-size: 12px; font-weight: 500;
  color: rgba(255,255,255,0.45); margin-bottom: 7px;
  letter-spacing: 0.04em; text-transform: uppercase;
}

.field-input-wrap { position: relative; display: flex; align-items: center; }

.field-ico { position: absolute; left: 14px; color: rgba(240,180,41,0.25); pointer-events: none; }

.field-input-wrap input {
  width: 100%;
  padding: 13px 14px 13px 42px;
  border: 1.5px solid rgba(240,180,41,0.10);
  border-radius: 12px; font-size: 14px;
  font-family: var(--font-ui); color: #FAFAF9;
  background: rgba(240,180,41,0.04);
  transition: all 0.25s; outline: none; font-weight: 300;
}

.field-input-wrap input:focus {
  border-color: var(--frost);
  background: rgba(240,180,41,0.07);
  box-shadow: 0 0 0 3px rgba(240,180,41,0.12);
}

.field-input-wrap input::placeholder { color: rgba(255,255,255,0.18); }

.field-eye {
  position: absolute; right: 12px;
  background: none; border: none; cursor: pointer; padding: 4px;
  color: rgba(240,180,41,0.25); display: flex; transition: color 0.2s;
}

.field-eye:hover { color: rgba(240,180,41,0.55); }

.field-err { display: block; font-size: 12px; color: #FCA5A5; margin-top: 5px; }

.field-row {
  display: flex; align-items: center;
  justify-content: space-between; margin-bottom: 20px; font-size: 13px;
}

.check-label { display: flex; align-items: center; gap: 7px; cursor: pointer; color: rgba(255,255,255,0.40); font-weight: 300; }
.check-label input[type="checkbox"] { accent-color: var(--frost); width: 15px; height: 15px; }

.link-accent { color: var(--frost); text-decoration: none; font-weight: 500; transition: color 0.2s; }
.link-accent:hover { color: var(--frost-light); }

.btn-primary {
  width: 100%; padding: 14px; border: none; border-radius: 12px;
  font-size: 14px; font-weight: 600; cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
  font-family: var(--font-ui);
  background: var(--frost); color: var(--navy);
}

.btn-primary:hover { background: var(--frost-light); box-shadow: 0 4px 20px rgba(240,180,41,0.28); transform: translateY(-1px); }
.btn-primary:disabled { opacity: 0.45; cursor: not-allowed; transform: none; }

.btn-secondary {
  width: 100%; padding: 13px;
  border: 1.5px solid rgba(240,180,41,0.12); border-radius: 12px;
  font-size: 14px; font-weight: 400; cursor: pointer;
  transition: all 0.3s; font-family: var(--font-ui);
  background: transparent; color: rgba(255,255,255,0.50);
}

.btn-secondary:hover {
  background: rgba(240,180,41,0.06);
  border-color: rgba(240,180,41,0.22);
  color: rgba(255,255,255,0.80);
}

.btn-loading-inner { display: flex; align-items: center; justify-content: center; gap: 8px; }

.dot-spinner {
  width: 17px; height: 17px;
  border: 2px solid rgba(12,26,46,0.15); border-top-color: var(--navy);
  border-radius: 50%; animation: spin 0.7s linear infinite; display: inline-block;
}

@keyframes spin { to { transform: rotate(360deg); } }

.modal-divider {
  display: flex; align-items: center; gap: 14px;
  margin: 24px 0; color: rgba(255,255,255,0.20);
  font-size: 13px; font-weight: 300;
}

.modal-divider::before,
.modal-divider::after { content: ''; flex: 1; height: 1px; background: rgba(240,180,41,0.08); }

.modal-footer-links { text-align: center; margin-top: 20px; font-size: 12px; color: rgba(255,255,255,0.20); }
.modal-footer-links a { color: rgba(255,255,255,0.25); text-decoration: none; transition: color 0.2s; }
.modal-footer-links a:hover { color: var(--frost); }
.modal-footer-links span { margin: 0 6px; }

.check-group { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 16px; }
.check-group input[type="checkbox"] { accent-color: var(--frost); width: 15px; height: 15px; margin-top: 2px; flex-shrink: 0; }
.check-group label { font-size: 13px; color: rgba(255,255,255,0.40); line-height: 1.4; font-weight: 300; }

.sent-view { text-align: center; padding: 16px 0; }

.sent-icon-wrap {
  width: 72px; height: 72px; border-radius: 50%;
  background: rgba(240,180,41,0.08); color: var(--frost);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px; border: 1px solid rgba(240,180,41,0.16);
}

.sent-view h2 { font-family: var(--font-display); font-size: 22px; font-weight: 400; color: #FAFAF9; margin-bottom: 10px; }
.sent-view p  { font-size: 14px; color: rgba(255,255,255,0.40); line-height: 1.5; margin-bottom: 12px; font-weight: 300; }
.sent-note    { font-size: 13px; color: rgba(255,255,255,0.25); }

/* ═══════════════════════════════════════
   GUEST PROMPT MODAL
   ═══════════════════════════════════════ */
.guest-prompt-box { max-width: 420px; }

.guest-prompt-icon {
  width: 68px; height: 68px; border-radius: 50%;
  background: rgba(240,180,41,0.10); color: var(--frost);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
  border: 1px solid rgba(240,180,41,0.20);
}

.guest-prompt-features {
  background: rgba(240,180,41,0.04);
  border: 1px solid rgba(240,180,41,0.08);
  border-radius: 12px;
  padding: 18px 20px; margin-bottom: 24px;
  display: flex; flex-direction: column; gap: 12px;
}

.guest-feature {
  display: flex; align-items: center; gap: 10px;
  font-size: 13px; color: rgba(255,255,255,0.65); font-weight: 300;
}

.guest-feature svg { flex-shrink: 0; color: var(--frost); }

.guest-continue { text-align: center; margin-top: 16px; }

.guest-continue-btn {
  background: none; border: none;
  font-size: 13px; color: rgba(255,255,255,0.28);
  cursor: pointer; font-family: var(--font-ui);
  transition: color 0.2s; padding: 4px;
}

.guest-continue-btn:hover { color: rgba(255,255,255,0.60); }

/* ═══════════════════════════════════════
   TRANSITIONS
   ═══════════════════════════════════════ */
.modal-fade-enter-active,
.modal-fade-leave-active { transition: opacity 0.3s ease; }

.modal-fade-enter-active .modal-box,
.modal-fade-leave-active .modal-box { transition: transform 0.35s cubic-bezier(0.16,1,0.3,1), opacity 0.3s ease; }

.modal-fade-enter-from,
.modal-fade-leave-to { opacity: 0; }

.modal-fade-enter-from .modal-box { transform: scale(0.96) translateY(12px); opacity: 0; }

.slide-down-enter-active,
.slide-down-leave-active { transition: all 0.28s cubic-bezier(0.16,1,0.3,1); }

.slide-down-enter-from,
.slide-down-leave-to { opacity: 0; transform: translateY(-6px); }

/* ═══════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════ */
@media (max-width: 1024px) {
  .hero-title         { font-size: 3.4rem; }
  .prop-grid          { grid-template-columns: repeat(2, 1fr); }
  .steps-grid         { grid-template-columns: repeat(2, 1fr); }
  .locations-grid     { grid-template-columns: repeat(2, 1fr); }
  .agents-grid        { grid-template-columns: repeat(2, 1fr); }
  .testimonials-grid  { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .nav-hamburger { display: flex; }

  .nav-actions {
    display: none;
    position: absolute; top: 100%; right: 24px;
    background: var(--navy);
    border: 1px solid rgba(240,180,41,0.10);
    border-radius: var(--radius);
    padding: 16px; flex-direction: column; gap: 10px;
    min-width: 180px;
    box-shadow: 0 16px 40px rgba(0,0,0,0.25);
  }

  .nav-actions.open { display: flex; }

  .hero-title { font-size: 2.8rem; }
  .hero-sub   { font-size: 1rem; }

  .search-bar { flex-direction: column; gap: 0; }
  .search-sep { width: 100%; height: 1px; margin: 0; }
  .search-btn { margin: 8px; justify-content: center; }

  .hero-stats   { flex-direction: column; gap: 20px; }
  .stat-divider { display: none; }

  .features-grid     { grid-template-columns: 1fr; max-width: 400px; margin: 0 auto; }
  .prop-grid         { grid-template-columns: 1fr; }
  .steps-grid        { grid-template-columns: 1fr; }
  .locations-grid    { grid-template-columns: repeat(2, 1fr); }
  .agents-grid       { grid-template-columns: 1fr; max-width: 360px; margin: 0 auto; }
  .testimonials-grid { grid-template-columns: 1fr; }

  .section-title { font-size: 2.1rem; }
  .cta-inner h2  { font-size: 2.2rem; }

  .modal-box { padding: 32px 24px; margin: 16px; }
  .hero-scroll-indicator { display: none; }
}

@media (max-width: 480px) {
  .hero-title  { font-size: 2.2rem; }
  .hero-badge  { font-size: 10px; padding: 7px 16px; letter-spacing: 1.8px; }
  .container   { padding: 0 16px; }
  .hero        { padding: 110px 0 80px; min-height: 88vh; }

  .properties-section,
  .steps-section,
  .locations-section,
  .agents-section,
  .testimonials-section { padding: 64px 0; }

  .features    { margin-top: -40px; padding-bottom: 48px; }
  .cta-section { padding: 60px 0; }
  .cta-inner h2 { font-size: 1.8rem; }
  .stat-num    { font-size: 1.8rem; }

  .btn-signin      { padding: 8px 16px; font-size: 12px; }
  .btn-get-started { padding: 8px 18px; font-size: 12px; }

  .locations-grid { grid-template-columns: 1fr; }
  .cta-btns       { flex-direction: column; align-items: center; }
  .btn-cta, .btn-cta-outline { width: 100%; max-width: 300px; }
}
</style>
