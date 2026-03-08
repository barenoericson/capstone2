<template>
  <div class="landing-page">
    <!-- ═══════════════════════════════════════ -->
    <!-- NAVBAR                                  -->
    <!-- ═══════════════════════════════════════ -->
    <nav class="navbar" :class="{ scrolled: isScrolled }">
      <div class="container navbar-inner">
        <router-link to="/" class="brand">
          <span class="brand-realty">Realty</span><span class="brand-link">Link</span><span class="brand-ph">PH</span>
        </router-link>

        <!-- Mobile hamburger -->
        <button class="nav-hamburger" @click="mobileNav = !mobileNav">
          <span></span><span></span><span></span>
        </button>

        <div class="nav-actions" :class="{ open: mobileNav }">
          <button @click="showLoginModal = true; mobileNav = false" class="btn-signin">Sign In</button>
          <button @click="showRegisterModal = true; mobileNav = false" class="btn-get-started">Get Started</button>
        </div>
      </div>
    </nav>

    <!-- ═══════════════════════════════════════ -->
    <!-- HERO                                    -->
    <!-- ═══════════════════════════════════════ -->
    <section class="hero">
      <div class="hero-overlay"></div>
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
            <input type="text" v-model="searchLocation" placeholder="e.g., Cebu City, Makati" />
          </div>
          <div class="search-sep"></div>
          <div class="search-field">
            <label>Property Type</label>
            <select v-model="searchType">
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
            <select v-model="searchPrice">
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
            <span class="stat-num">{{ stats.properties }}</span>
            <span class="stat-lbl">Properties</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-block">
            <span class="stat-num">{{ stats.agents }}</span>
            <span class="stat-lbl">Verified Agents</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-block">
            <span class="stat-num">{{ stats.buyers }}</span>
            <span class="stat-lbl">Happy Buyers</span>
          </div>
        </div>

        <!-- Social proof ticker -->
        <div class="social-proof-ticker">
          <div class="ticker-track">
            <span class="ticker-item">
              <span class="ticker-dot"></span>
              500+ users joined this month
            </span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item">
              <span class="ticker-dot"></span>
              12 new properties in Cebu
            </span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item">
              <span class="ticker-dot"></span>
              All agents PRC-verified
            </span>
            <span class="ticker-sep">·</span>
            <span class="ticker-item">
              <span class="ticker-dot"></span>
              Flood-free zones available
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FEATURE CARDS (overlapping hero)        -->
    <!-- ═══════════════════════════════════════ -->
    <section class="features">
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

    <!-- ═══════════════════════════════════════ -->
    <!-- FEATURED PROPERTIES                     -->
    <!-- ═══════════════════════════════════════ -->
    <section class="properties-section">
      <div class="container">
        <div class="section-head">
          <span class="section-badge">Featured Listings</span>
          <h2 class="section-title">Explore Our Latest Properties</h2>
          <p class="section-desc">Discover quality homes, condos, and commercial spaces from verified agents across the Philippines.</p>
        </div>

        <!-- Loading -->
        <div v-if="propertiesLoading" class="prop-loading">
          <div class="loading-dots">
            <span></span><span></span><span></span>
          </div>
          <p>Loading properties...</p>
        </div>

        <!-- Grid -->
        <div v-else-if="landingProperties.length" class="prop-grid">
          <div v-for="property in landingProperties" :key="property.id" class="prop-card" @click="viewProperty(property.id)">
            <div class="prop-img-wrap">
              <img v-if="getCoverPhoto(property)" :src="getCoverPhoto(property)" :alt="property.title" class="prop-img" />
              <div v-else class="prop-placeholder">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.4"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </div>
              <!-- Floating price pill -->
              <div class="prop-price-pill">₱{{ formatPrice(property.price) }}</div>
              <!-- Status badges -->
              <div class="prop-badges">
                <span v-if="property.featured" class="prop-badge badge-featured">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  Featured
                </span>
                <span v-if="property.virtual_tour_url" class="prop-badge badge-360">360°</span>
              </div>
              <!-- Image gradient overlay for readability -->
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
              <!-- PH trust tags -->
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
            </div>
          </div>
        </div>

        <!-- Empty -->
        <div v-else class="prop-empty">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <p>No properties available at the moment.</p>
        </div>

        <!-- CTA -->
        <div v-if="landingProperties.length" class="section-cta">
          <button @click="showRegisterModal = true" class="btn-outline-dark">See All Properties →</button>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- HOW IT WORKS                            -->
    <!-- ═══════════════════════════════════════ -->
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

    <!-- ═══════════════════════════════════════ -->
    <!-- CTA BANNER                              -->
    <!-- ═══════════════════════════════════════ -->
    <section class="cta-section">
      <div class="container cta-inner">
        <h2>Ready to Find Your Dream Home?</h2>
        <p>Create a free account to save properties, schedule viewings, and connect with agents.</p>
        <button @click="showRegisterModal = true" class="btn-cta">Get Started Free</button>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FOOTER                                  -->
    <!-- ═══════════════════════════════════════ -->
    <footer class="site-footer">
      <div class="container footer-inner">
        <div class="footer-brand">
          RealtyLink<span>PH</span>
        </div>
        <div class="footer-links">
          <a href="#">Terms of Service</a>
          <a href="#">Privacy Policy</a>
          <a href="#">Contact Us</a>
        </div>
        <p class="footer-copy">&copy; 2026 RealtyLinkPH. All rights reserved.</p>
      </div>
    </footer>

    <!-- ═══════════════════════════════════════ -->
    <!-- LOGIN MODAL                             -->
    <!-- ═══════════════════════════════════════ -->
    <transition name="modal-fade">
      <div v-if="showLoginModal" class="modal-overlay" @click="closeLoginModal">
        <div class="modal-box" @click.stop>
          <button class="modal-close" @click="closeLoginModal" title="Close">
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
                  <button type="button" class="field-eye" @click="showLoginPassword = !showLoginPassword">
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
              <a href="#">Terms & Conditions</a>
              <span>·</span>
              <a href="#">Privacy Policy</a>
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

    <!-- ═══════════════════════════════════════ -->
    <!-- REGISTER MODAL                          -->
    <!-- ═══════════════════════════════════════ -->
    <transition name="modal-fade">
      <div v-if="showRegisterModal" class="modal-overlay" @click="closeRegisterModal">
        <div class="modal-box modal-box-wide" @click.stop>
          <button class="modal-close" @click="closeRegisterModal" title="Close">
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
                <button type="button" class="field-eye" @click="showRegisterPassword = !showRegisterPassword">
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
                <button type="button" class="field-eye" @click="showRegisterConfirm = !showRegisterConfirm">
                  <svg v-if="!showRegisterConfirm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
              </div>
              <transition name="slide-down"><span v-if="registerErrors.confirm_password" class="field-err">{{ registerErrors.confirm_password }}</span></transition>
            </div>

            <div class="check-group">
              <input id="register-terms" type="checkbox" v-model="registerForm.terms" @blur="validateRegisterField('terms')" />
              <label for="register-terms">I agree to the <a href="#" class="link-accent">Terms & Conditions</a> and <a href="#" class="link-accent">Privacy Policy</a></label>
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
            <a href="#">Need help?</a>
            <span>·</span>
            <a href="#">Contact Support</a>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../src/services/api'

export default {
  name: 'Landing',
  setup() {
    const router = useRouter()

    // Landing state
    const isScrolled = ref(false)
    const mobileNav = ref(false)

    // Search state
    const searchLocation = ref('')
    const searchType = ref('')
    const searchPrice = ref('')

    const handleSearch = () => {
      sessionStorage.setItem('landing_search', JSON.stringify({
        location: searchLocation.value,
        type: searchType.value,
        price: searchPrice.value
      }))
      showLoginModal.value = true
    }

    // Modal states
    const showLoginModal = ref(false)
    const showRegisterModal = ref(false)

    // Dynamic greeting: "Welcome Back" only if last login was 1+ days ago
    const loginGreeting = computed(() => {
      const lastLogin = localStorage.getItem('last_login_time')
      if (!lastLogin) return 'Sign In'
      const daysSince = (Date.now() - parseInt(lastLogin)) / (1000 * 60 * 60 * 24)
      return daysSince >= 1 ? 'Welcome Back' : 'Sign In'
    })

    // Login view: 'login' | 'forgot' | 'forgot-sent'
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

    // Landing stats (real data from DB)
    const stats = reactive({ properties: 0, agents: 0, buyers: 0 })

    const loadStats = async () => {
      try {
        const base = window.__API_URL__ || 'http://localhost:8000'
        const res = await fetch(`${base}/api/landing-stats`)
        const data = await res.json()
        if (data.success && data.data) {
          stats.properties = data.data.properties || 0
          stats.agents = data.data.agents || 0
          stats.buyers = data.data.buyers || 0
        }
      } catch (e) {
        // keep defaults
      }
    }

    // Landing Properties
    const landingProperties = ref([])
    const propertiesLoading = ref(false)

    const loadLandingProperties = async () => {
      propertiesLoading.value = true
      try {
        const base = window.__API_URL__ || 'http://localhost:8000'
        const res = await fetch(`${base}/api/properties`)
        const data = await res.json()
        const paginated = data.data || data || {}
        const list = paginated.data || paginated || []
        landingProperties.value = Array.isArray(list) ? list.slice(0, 6) : []
      } catch (e) {
        landingProperties.value = []
      } finally {
        propertiesLoading.value = false
      }
    }

    const viewProperty = (id) => {
      router.push(`/property/${id}`)
    }

    const getCoverPhoto = (property) => {
      if (!property.photos || !property.photos.length) return null
      const cover = property.photos.find(p => p.is_cover) || property.photos[0]
      return cover ? cover.photo_url : null
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-PH').format(price)
    }

    const formatPropertyType = (type) => {
      const map = { house: 'House', condo: 'Condo', apartment: 'Apartment', commercial: 'Commercial', lot: 'Lot' }
      return map[type] || type
    }

    // Mounted
    const onMounted = () => {
      window.addEventListener('scroll', () => {
        isScrolled.value = window.scrollY > 20
      })
      loadStats()
      loadLandingProperties()
    }

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

        // Show the premium loading screen immediately
        if (window.__showLoading) window.__showLoading()
        showLoginModal.value = false

        const userData = res.data.user
        if (userData.role === 'admin') router.push('/admin/dashboard')
        else if (userData.role === 'agent') router.push('/agent/dashboard')
        else router.push('/dashboard')
      } catch (e) {
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
        const apiUrl = localStorage.getItem('api_url') || window.__API_URL__
        const res = await fetch(`${apiUrl}/api/auth/forgot-password`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ email: forgotForm.email }),
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
      } catch (e) {
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
      onMounted
    }
  },
  mounted() {
    this.onMounted()
  }
}
</script>

<style scoped>
/* ═══════════════════════════════════════════════════
   LANDING PAGE — Trust & Luxury Palette
   Navy (#0F172A) + Warm Gold (#CA8A04) + Stone Neutrals
   Typography: Outfit (Headlines) + Inter (Body/UI)
   ═══════════════════════════════════════════════════ */

.landing-page {
  /* Primary — Deep Navy matching Dashboard theme */
  --navy: #0B1C39;
  --navy-light: #102445;
  --navy-soft: #1a3158;

  /* Accent — Warm Gold matching Dashboard theme */
  --gold: #D89B0F;
  --gold-light: #E5B332;
  --gold-dark: #B07A08;
  --gold-faded: rgba(216,155,15,0.10);
  --gold-glow: rgba(216,155,15,0.35);

  /* Neutrals (stone scale) */
  --stone-50: #FAFAF9;
  --stone-100: #F5F5F4;
  --stone-200: #E7E5E4;
  --stone-300: #D6D3D1;
  --stone-400: #A8A29E;
  --stone-500: #78716C;
  --stone-600: #57534E;
  --stone-700: #44403C;
  --stone-800: #292524;
  --white: #FFFFFF;

  /* Functional */
  --success: #16A34A;
  --error: #DC2626;

  /* Typography — Plus Jakarta Sans for logo/brand, Outfit for headlines, Inter for UI */
  --font-ui: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --font-display: 'Outfit', 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
  --font-brand: 'Plus Jakarta Sans', 'Outfit', -apple-system, sans-serif;

  /* Layout */
  --radius: 14px;
  --radius-sm: 8px;
  --radius-lg: 20px;
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
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  padding: 18px 0;
  transition: all 0.35s ease;
}

.navbar.scrolled {
  background: rgba(15, 23, 42, 0.97);
  backdrop-filter: blur(16px);
  padding: 12px 0;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
}

.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  font-size: 24px;
  color: var(--white);
  text-decoration: none;
  letter-spacing: -0.4px;
  font-family: var(--font-brand);
  display: inline-flex; align-items: baseline; gap: 0;
}

/* 60/30/10 logo weight distinction */
.brand-realty { font-weight: 400; color: rgba(255,255,255,0.85); }
.brand-link   { font-weight: 800; color: var(--white); }
.brand-ph     { font-weight: 800; color: var(--gold); }

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
  width: 22px;
  height: 2px;
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
  color: rgba(255, 255, 255, 0.85);
  border: 1.5px solid rgba(255, 255, 255, 0.25);
  border-radius: var(--radius-pill);
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
}

.btn-signin:hover {
  color: var(--white);
  border-color: rgba(255, 255, 255, 0.5);
  background: rgba(255, 255, 255, 0.08);
}

.btn-get-started {
  padding: 9px 24px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy); /* dark navy text on gold — accessibility fix */
  border: none;
  border-radius: var(--radius-pill);
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
  box-shadow: 0 4px 16px var(--gold-glow);
}

.btn-get-started:hover {
  background: linear-gradient(135deg, var(--gold-light), var(--gold));
  transform: translateY(-2px);
  box-shadow: 0 6px 24px var(--gold-glow);
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
  padding: 130px 0 100px;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(15, 23, 42, 0.82) 0%,
    rgba(15, 23, 42, 0.60) 45%,
    rgba(15, 23, 42, 0.85) 100%
  );
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  width: 100%;
}

.hero-badge {
  display: inline-block;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: var(--gold);
  background: rgba(202, 138, 4, 0.12);
  border: 1px solid rgba(202, 138, 4, 0.2);
  padding: 8px 20px;
  border-radius: var(--radius-pill);
  margin-bottom: 28px;
}

.hero-title {
  font-family: var(--font-display);
  font-size: 4.2rem;
  font-weight: 800;
  color: var(--white);
  line-height: 1.08;
  margin-bottom: 24px;
  letter-spacing: -1px;
}

.text-accent {
  color: var(--gold);
}

.hero-sub {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.65);
  max-width: 580px;
  margin: 0 auto 44px;
  line-height: 1.7;
  font-weight: 400;
}

/* ─── Search Bar — Glassmorphism ─── */
.search-bar {
  display: flex;
  align-items: stretch;
  background: rgba(255, 255, 255, 0.10);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: var(--radius-lg);
  padding: 8px;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255,255,255,0.12);
  max-width: 860px;
  margin: 0 auto 52px;
}

.search-field {
  flex: 1;
  padding: 12px 20px;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.search-field label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.55);
  letter-spacing: 1px;
}

.search-field input,
.search-field select {
  border: none;
  outline: none;
  font-size: 14px;
  font-weight: 600;
  color: var(--white);
  background: transparent;
  font-family: var(--font-ui);
  padding: 0;
  cursor: pointer;
}

.search-field input::placeholder { color: rgba(255,255,255,0.40); }
.search-field select option { color: var(--navy); background: var(--white); }

.search-sep {
  width: 1px;
  background: rgba(255, 255, 255, 0.15);
  margin: 10px 0;
}

.search-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 14px 32px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy); /* dark navy text on gold — accessibility */
  border: none;
  border-radius: calc(var(--radius-lg) - 8px);
  font-weight: 800;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
  white-space: nowrap;
  box-shadow: 0 4px 20px var(--gold-glow);
}

.search-btn:hover {
  background: linear-gradient(135deg, var(--gold-light), var(--gold));
  transform: translateY(-1px);
  box-shadow: 0 8px 28px var(--gold-glow);
}

/* ─── Stats ─── */
.hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 40px;
}

.stat-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.stat-num {
  font-family: var(--font-display);
  font-size: 2.2rem;
  font-weight: 800;
  color: var(--gold);
  letter-spacing: -0.5px;
}

.stat-lbl {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  font-weight: 500;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.stat-divider {
  width: 1px;
  height: 36px;
  background: rgba(255, 255, 255, 0.15);
}

/* ═══════════════════════════════════════
   FEATURE CARDS (overlapping hero)
   ═══════════════════════════════════════ */
.features {
  position: relative;
  margin-top: -60px;
  z-index: 2;
  padding-bottom: 80px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.feat-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  padding: 36px 28px;
  text-align: center;
  border: 1px solid var(--stone-200);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
  transition: all 0.35s ease;
}

.feat-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
}

.feat-highlight {
  background: linear-gradient(145deg, var(--navy) 0%, var(--navy-light) 100%);
  border-color: rgba(216, 155, 15, 0.25);
  box-shadow: 0 8px 32px rgba(11, 28, 57, 0.18), 0 0 0 1px rgba(216,155,15,0.1) inset;
}

.feat-highlight .feat-icon {
  background: rgba(216, 155, 15, 0.15);
  color: var(--gold);
  border: 2px solid rgba(216, 155, 15, 0.2);
}

.feat-highlight h3 {
  color: var(--white);
}

.feat-highlight p {
  color: rgba(255, 255, 255, 0.6);
}

.feat-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: var(--gold-faded);
  color: var(--gold);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.feat-card h3 {
  font-family: var(--font-display);
  font-size: 19px;
  font-weight: 700;
  color: var(--stone-800);
  margin-bottom: 10px;
}

.feat-card p {
  font-size: 14px;
  color: var(--stone-500);
  line-height: 1.65;
  font-weight: 400;
}

/* ═══════════════════════════════════════
   SECTION COMMON
   ═══════════════════════════════════════ */
.section-head {
  text-align: center;
  margin-bottom: 52px;
}

.section-badge {
  display: inline-block;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: var(--gold);
  margin-bottom: 14px;
}

.section-title {
  font-family: var(--font-display);
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 14px;
  letter-spacing: -0.5px;
  line-height: 1.12;
}

.section-desc {
  font-size: 1rem;
  color: var(--stone-500);
  max-width: 560px;
  margin: 0 auto;
  line-height: 1.65;
  font-weight: 400;
}

.section-cta {
  text-align: center;
  margin-top: 48px;
}

.btn-outline-dark {
  padding: 14px 34px;
  background: transparent;
  color: var(--navy);
  border: 2px solid var(--navy);
  border-radius: var(--radius-pill);
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
}

.btn-outline-dark:hover {
  background: var(--navy);
  color: var(--white);
}

/* ═══════════════════════════════════════
   FEATURED PROPERTIES
   ═══════════════════════════════════════ */
.properties-section {
  padding: 100px 0;
  background: var(--stone-50);
}

.prop-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
}

.prop-card {
  background: var(--white);
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 1px solid var(--stone-200);
  transition: all 0.35s ease;
  cursor: pointer;
}

.prop-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 48px rgba(0, 0, 0, 0.08);
  border-color: var(--stone-300);
}

.prop-img-wrap {
  position: relative;
  height: 230px;
  overflow: hidden;
  background: var(--stone-100);
}

.prop-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.prop-card:hover .prop-img {
  transform: scale(1.05);
}

.prop-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--stone-400);
  background: var(--stone-100);
}

.prop-price {
  position: absolute;
  bottom: 14px;
  left: 14px;
  background: var(--navy);
  color: var(--gold);
  padding: 7px 16px;
  border-radius: var(--radius-sm);
  font-weight: 700;
  font-size: 15px;
  font-family: var(--font-display);
  letter-spacing: -0.2px;
}

.prop-featured-badge {
  position: absolute;
  top: 14px;
  right: 14px;
  background: var(--gold);
  color: var(--white);
  padding: 5px 14px;
  border-radius: var(--radius-pill);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.prop-body {
  padding: 22px;
}

.prop-type-tag {
  display: inline-block;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--gold-dark);
  background: var(--gold-faded);
  padding: 4px 12px;
  border-radius: var(--radius-pill);
  margin-bottom: 10px;
}

.prop-title {
  font-family: var(--font-display);
  font-size: 17px;
  font-weight: 700;
  margin-bottom: 6px;
  color: var(--navy);
  line-height: 1.3;
  letter-spacing: -0.2px;
}

.prop-location {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 13px;
  color: var(--stone-500);
  margin-bottom: 14px;
}

.prop-location svg {
  flex-shrink: 0;
  color: var(--stone-400);
}

.prop-specs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  padding-top: 12px;
  border-top: 1px solid var(--stone-100);
}

/* ─── Loading ─── */
.prop-loading {
  text-align: center;
  padding: 60px 0;
  color: var(--stone-500);
}

.loading-dots {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-bottom: 16px;
}

.loading-dots span {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--gold);
  animation: dotPulse 1.2s ease-in-out infinite;
}

.loading-dots span:nth-child(2) { animation-delay: 0.15s; }
.loading-dots span:nth-child(3) { animation-delay: 0.3s; }

@keyframes dotPulse {
  0%, 80%, 100% { opacity: 0.3; transform: scale(0.8); }
  40% { opacity: 1; transform: scale(1); }
}

.prop-empty {
  text-align: center;
  padding: 60px 0;
  color: var(--stone-500);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

/* ═══════════════════════════════════════
   HOW IT WORKS
   ═══════════════════════════════════════ */
.steps-section {
  padding: 100px 0;
  background: var(--white);
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.step-card {
  position: relative;
  text-align: center;
  padding: 44px 32px;
  background: var(--stone-50);
  border-radius: var(--radius-lg);
  border: 1px solid var(--stone-200);
  transition: all 0.35s ease;
}

.step-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.06);
  border-color: var(--gold);
}

.step-icon-wrap {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--gold-faded);
  color: var(--gold);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  border: 2px solid rgba(202, 138, 4, 0.15);
}

.step-num {
  font-family: var(--font-display);
  font-size: 52px;
  font-weight: 700;
  color: rgba(202, 138, 4, 0.08);
  position: absolute;
  top: 16px;
  right: 24px;
  line-height: 1;
}

.step-card h3 {
  font-family: var(--font-display);
  font-size: 19px;
  font-weight: 700;
  margin-bottom: 10px;
  color: var(--navy);
}

.step-card p {
  font-size: 14px;
  color: var(--stone-500);
  line-height: 1.65;
  font-weight: 400;
}

/* ═══════════════════════════════════════
   CTA BANNER
   ═══════════════════════════════════════ */
.cta-section {
  padding: 90px 0;
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
}

.cta-inner {
  text-align: center;
}

.cta-inner h2 {
  font-family: var(--font-display);
  font-size: 2.6rem;
  font-weight: 800;
  color: var(--white);
  margin-bottom: 14px;
}

.cta-inner p {
  font-size: 1.05rem;
  color: rgba(255, 255, 255, 0.55);
  margin-bottom: 36px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
  font-weight: 400;
  line-height: 1.65;
}

.btn-cta {
  padding: 16px 44px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy);
  border: none;
  border-radius: var(--radius-pill);
  font-weight: 800;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
  box-shadow: 0 4px 24px var(--gold-glow);
  letter-spacing: -0.01em;
}

.btn-cta:hover {
  background: linear-gradient(135deg, var(--gold-light), var(--gold));
  transform: translateY(-3px);
  box-shadow: 0 12px 40px var(--gold-glow);
}

/* ═══════════════════════════════════════
   FOOTER
   ═══════════════════════════════════════ */
.site-footer {
  padding: 44px 0;
  background: var(--navy);
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.footer-inner {
  text-align: center;
}

.footer-brand {
  font-family: var(--font-display);
  font-size: 22px;
  font-weight: 800;
  color: var(--white);
  margin-bottom: 18px;
}

.footer-brand span {
  color: var(--gold);
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: 28px;
  margin-bottom: 18px;
}

.footer-links a {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.45);
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: var(--gold);
}

.footer-copy {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.25);
}

/* ═══════════════════════════════════════════════
   MODAL STYLES (Login & Register)
   ═══════════════════════════════════════════════ */
/* ═══════════════════════════════════════
   MODAL — Navy/Gold Premium Dark Theme
   ═══════════════════════════════════════ */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(11, 17, 32, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(12px);
  padding: 20px;
}

.modal-box {
  background: linear-gradient(165deg, #1a2540 0%, #0F172A 100%);
  border: 1px solid rgba(212, 168, 83, 0.15);
  border-radius: 20px;
  width: 100%;
  max-width: 440px;
  padding: 40px 36px;
  position: relative;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.04) inset;
}

.modal-box-wide {
  max-width: 480px;
  padding: 36px 32px;
}

.modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid rgba(255,255,255,0.08);
  background: rgba(255, 255, 255, 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: rgba(255, 255, 255, 0.4);
  transition: all 0.3s;
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.8);
  border-color: rgba(255,255,255,0.15);
}

.modal-brand {
  text-align: center;
  margin-bottom: 28px;
}

.modal-brand-name {
  font-size: 28px;
  font-weight: 700;
  color: #FAFAF9;
  font-family: var(--font-display);
  letter-spacing: -0.3px;
}

.modal-brand-name .gold {
  color: var(--gold);
}

.modal-head {
  text-align: center;
  margin-bottom: 28px;
}

.modal-head h1 {
  font-family: var(--font-display);
  font-size: 22px;
  font-weight: 700;
  color: #FAFAF9;
  margin-bottom: 6px;
  letter-spacing: -0.3px;
}

.modal-head p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.45);
}

/* ─── Alerts ─── */
.m-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 18px;
  font-size: 13px;
  font-weight: 400;
}

.m-alert-success {
  background: rgba(16, 185, 129, 0.12);
  color: #6EE7B7;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.m-alert-error {
  background: rgba(239, 68, 68, 0.12);
  color: #FCA5A5;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.m-alert-icon {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 800;
  flex-shrink: 0;
}

.m-alert-success .m-alert-icon {
  background: rgba(16, 185, 129, 0.25);
  color: #6EE7B7;
}

.m-alert-error .m-alert-icon {
  background: rgba(239, 68, 68, 0.25);
  color: #FCA5A5;
}

/* ─── Form Fields ─── */
.modal-form {
  display: flex;
  flex-direction: column;
}

.field {
  margin-bottom: 18px;
}

.field label {
  display: block;
  font-size: 13px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.55);
  margin-bottom: 6px;
  letter-spacing: 0.01em;
}

.field-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.field-ico {
  position: absolute;
  left: 14px;
  color: rgba(255, 255, 255, 0.25);
  pointer-events: none;
}

.field-input-wrap input {
  width: 100%;
  padding: 13px 14px 13px 42px;
  border: 1.5px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  font-size: 14px;
  font-family: var(--font-ui);
  color: #FAFAF9;
  background: rgba(255, 255, 255, 0.04);
  transition: all 0.25s;
  outline: none;
}

.field-input-wrap input:focus {
  border-color: var(--gold);
  background: rgba(202, 138, 4, 0.06);
  box-shadow: 0 0 0 3px rgba(202, 138, 4, 0.15);
}

.field-input-wrap input::placeholder {
  color: rgba(255, 255, 255, 0.2);
}

.field-eye {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  color: rgba(255, 255, 255, 0.25);
  display: flex;
}

.field-eye:hover {
  color: rgba(255, 255, 255, 0.5);
}

.field-err {
  display: block;
  font-size: 12px;
  color: #FCA5A5;
  margin-top: 5px;
  font-weight: 400;
}

.field-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  font-size: 13px;
}

.check-label {
  display: flex;
  align-items: center;
  gap: 7px;
  cursor: pointer;
  color: rgba(255, 255, 255, 0.45);
}

.check-label input[type="checkbox"] {
  accent-color: #D4A853;
  width: 16px;
  height: 16px;
}

.link-accent {
  color: #D4A853;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.link-accent:hover {
  color: #EAB308;
}

/* ─── Buttons ─── */
.btn-primary {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
  background: linear-gradient(135deg, #D4A853 0%, #B8942E 100%);
  color: #0F172A;
  letter-spacing: 0.01em;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #EAB308 0%, #D4A853 100%);
  box-shadow: 0 4px 20px rgba(212, 168, 83, 0.3);
  transform: translateY(-1px);
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  width: 100%;
  padding: 13px;
  border: 1.5px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font-ui);
  background: transparent;
  color: rgba(255, 255, 255, 0.6);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(255, 255, 255, 0.18);
  color: rgba(255, 255, 255, 0.85);
}

.btn-loading-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.dot-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(15, 23, 42, 0.2);
  border-top-color: #0F172A;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ─── Divider / Footer ─── */
.modal-divider {
  display: flex;
  align-items: center;
  gap: 14px;
  margin: 24px 0;
  color: rgba(255, 255, 255, 0.25);
  font-size: 13px;
}

.modal-divider::before,
.modal-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: rgba(255, 255, 255, 0.08);
}

.modal-footer-links {
  text-align: center;
  margin-top: 20px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.25);
}

.modal-footer-links a {
  color: rgba(255, 255, 255, 0.3);
  text-decoration: none;
  transition: color 0.2s;
}

.modal-footer-links a:hover {
  color: #D4A853;
}

.modal-footer-links span {
  margin: 0 6px;
}

/* ─── Checkbox Group ─── */
.check-group {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 16px;
}

.check-group input[type="checkbox"] {
  accent-color: #D4A853;
  width: 16px;
  height: 16px;
  margin-top: 2px;
  flex-shrink: 0;
}

.check-group label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.45);
  line-height: 1.4;
}

/* ─── Forgot Sent ─── */
.sent-view {
  text-align: center;
  padding: 16px 0;
}

.sent-icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(212, 168, 83, 0.1);
  color: #D4A853;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  border: 1px solid rgba(212, 168, 83, 0.2);
}

.sent-view h2 {
  font-family: var(--font-display);
  font-size: 22px;
  font-weight: 700;
  color: #FAFAF9;
  margin-bottom: 10px;
}

.sent-view p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.45);
  line-height: 1.5;
  margin-bottom: 12px;
}

.sent-note {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.3);
}

/* ═══════════════════════════════════════
   SOCIAL PROOF TICKER
   ═══════════════════════════════════════ */
.social-proof-ticker {
  margin-top: 28px;
  overflow: hidden;
}

.ticker-track {
  display: flex;
  align-items: center;
  gap: 24px;
  justify-content: center;
  flex-wrap: wrap;
}

.ticker-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.5);
  letter-spacing: 0.02em;
}

.ticker-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--gold);
  opacity: 0.8;
  flex-shrink: 0;
}

.ticker-sep {
  color: rgba(255, 255, 255, 0.2);
  font-size: 16px;
  font-weight: 300;
}

/* ═══════════════════════════════════════
   PROPERTY CARD ENHANCEMENTS
   ═══════════════════════════════════════ */

/* Floating price pill on image */
.prop-price-pill {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: linear-gradient(135deg, var(--navy), var(--navy-light));
  color: var(--gold);
  padding: 6px 14px;
  border-radius: var(--radius-sm);
  font-weight: 800;
  font-size: 14px;
  font-family: var(--font-display);
  letter-spacing: -0.3px;
  z-index: 3;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(216, 155, 15, 0.2);
}

/* Status badges top-left */
.prop-badges {
  position: absolute;
  top: 10px;
  left: 10px;
  display: flex;
  gap: 5px;
  z-index: 3;
}

.prop-badge {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: var(--radius-pill);
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.3px;
  text-transform: uppercase;
}

.badge-featured {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--navy);
  box-shadow: 0 2px 8px rgba(216, 155, 15, 0.35);
}

.badge-360 {
  background: rgba(11, 28, 57, 0.85);
  color: var(--white);
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(4px);
}

/* Dark gradient overlay at bottom of image (for price readability) */
.prop-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(11, 28, 57, 0.70) 0%,
    rgba(11, 28, 57, 0.15) 45%,
    transparent 100%
  );
  z-index: 2;
  pointer-events: none;
}

/* Meta row: type tag + verified badge */
.prop-meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.prop-verified-tag {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  font-weight: 700;
  color: #10B981;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.prop-verified-tag svg {
  color: #10B981;
}

/* Compact icon + number spec pills */
.spec-pill {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
  color: var(--stone-600);
  background: var(--stone-100);
  padding: 4px 10px;
  border-radius: var(--radius-pill);
}

.spec-pill svg {
  color: var(--stone-400);
  flex-shrink: 0;
}

/* Trust tags below specs */
.prop-trust-tags {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid var(--stone-100);
}

.trust-tag {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  font-weight: 600;
  color: #10B981;
  background: rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.18);
  padding: 3px 9px;
  border-radius: var(--radius-pill);
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.trust-tag svg {
  flex-shrink: 0;
}

.tag-flood {
  color: #3B82F6;
  background: rgba(59, 130, 246, 0.08);
  border-color: rgba(59, 130, 246, 0.18);
}

/* ═══════════════════════════════════════
   TRANSITIONS
   ═══════════════════════════════════════ */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-active .modal-box,
.modal-fade-leave-active .modal-box {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-from .modal-box {
  transform: scale(0.95) translateY(10px);
  opacity: 0;
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* ═══════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════ */
@media (max-width: 1024px) {
  .hero-title { font-size: 3.2rem; }
  .prop-grid { grid-template-columns: repeat(2, 1fr); }
  .steps-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .nav-hamburger {
    display: flex;
  }

  .nav-actions {
    display: none;
    position: absolute;
    top: 100%;
    right: 24px;
    background: var(--navy);
    border-radius: var(--radius);
    padding: 16px;
    flex-direction: column;
    gap: 10px;
    min-width: 180px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
  }

  .nav-actions.open {
    display: flex;
  }

  .hero-title { font-size: 2.6rem; }
  .hero-sub { font-size: 1rem; }

  .search-bar {
    flex-direction: column;
    gap: 0;
  }

  .search-sep {
    width: 100%;
    height: 1px;
    margin: 0;
  }

  .search-btn {
    margin: 8px;
    justify-content: center;
  }

  .hero-stats {
    flex-direction: column;
    gap: 20px;
  }

  .stat-divider { display: none; }

  .features-grid { grid-template-columns: 1fr; max-width: 400px; margin: 0 auto; }
  .prop-grid { grid-template-columns: 1fr; }
  .steps-grid { grid-template-columns: 1fr; }

  .section-title { font-size: 1.9rem; }
  .cta-inner h2 { font-size: 2rem; }

  .modal-box {
    padding: 32px 24px;
    margin: 16px;
  }
}

@media (max-width: 480px) {
  .hero-title { font-size: 2.1rem; }
  .hero-badge { font-size: 10px; padding: 6px 14px; letter-spacing: 1.5px; }
  .container { padding: 0 16px; }
  .hero { padding: 110px 0 80px; min-height: 88vh; }

  .properties-section,
  .steps-section { padding: 64px 0; }
  .features { margin-top: -40px; padding-bottom: 40px; }

  .cta-section { padding: 56px 0; }
  .cta-inner h2 { font-size: 1.7rem; }

  .stat-num { font-size: 1.6rem; }

  .btn-signin { padding: 8px 16px; font-size: 13px; }
  .btn-get-started { padding: 8px 18px; font-size: 13px; }
}
</style>
