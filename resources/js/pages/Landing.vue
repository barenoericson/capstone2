<template>
  <div class="landing-page">
    <nav class="navbar" :class="{ scrolled: isScrolled }">
      <div class="container navbar-content">
        <router-link to="/" class="brand-logo">
          RealtyLink<span class="text-mustard">PH</span>
        </router-link>

        <div class="nav-links hidden-mobile">
          <a href="#about">About Us</a>
          <a href="#properties">Properties</a>
          <a href="#blogs">Blogs</a>
        </div>

        <div class="nav-actions">
          <button @click="showLoginModal = true" class="btn btn-black btn-pill">
            Login
          </button>
        </div>
      </div>
    </nav>

    <section class="hero-section">
      <div class="container hero-grid">
        <!-- LEFT CONTENT -->
        <div class="hero-text-content">
          <h1 class="hero-heading">
            Gateway to <br />
            <span class="text-highlight">RealtyLinkPH</span>
          </h1>

          <p class="hero-subtext">
            Discover a curated collection of dream homes at your fingertips,
            simplified and personalized for your lifestyle.
          </p>

          <div class="hero-buttons">
            <button @click="showRegisterModal = true" class="btn btn-black btn-lg">
              Discover Now
            </button>
          </div>
        </div>

        <!-- RIGHT IMAGE -->
        <div class="hero-image-wrapper">
          <div class="image-blobs"></div>
          <img
            src="/images/logo/landingpagepicture.jpg"
            alt="Modern House"
            class="hero-house-img"
          />
        </div>
      </div>

      <!-- SEARCH BAR -->
      <div class="search-overlap-container">
        <div class="search-bar">
          <div class="search-group">
            <div class="label-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>Location</span>
            </div>
            <input
              type="text"
              placeholder="IT Park, Cebu City"
              class="search-input"
            />
          </div>

          <div class="divider"></div>

          <div class="search-group">
            <div class="label-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-2a2 2 0 0 1 4 0v2"></path>
              </svg>
              <span>Property Type</span>
            </div>
            <select class="search-select">
              <option>Condominuim</option>
              <option>Houses</option>
              <option>Penthouse</option>
              <option>Commercial Lot</option>
            </select>
          </div>

          <div class="divider"></div>

          <div class="search-group">
            <div class="label-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
              </svg>
              <span>Price Range</span>
            </div>
            <select class="search-select">
              <option> ₱1,000,000 -  ₱10,000,000 / mo</option>
              <option> ₱20,000,000 -  ₱40,000,000/ mo</option>
              <option> ₱50,000,000+ / mo</option>
            </select>
          </div>

          <button class="search-btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </button>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- ABOUT SECTION                          -->
    <!-- ═══════════════════════════════════════ -->
    <section id="about" class="about-section">
      <div class="container">
        <div class="section-header">
          <span class="section-label">About Us</span>
          <h2 class="section-title">Connecting Buyers &amp; Agents<br /><span class="text-mustard">Across the Philippines</span></h2>
          <p class="section-desc">RealtyLinkPH is a modern real estate platform built to simplify property discovery, agent communication, viewing scheduling, and document signing — all in one place.</p>
        </div>

        <!-- Mission & Vision -->
        <div class="mv-grid">
          <div class="mv-card">
            <div class="mv-icon">🎯</div>
            <h3 class="mv-title">Our Mission</h3>
            <p class="mv-text">To empower every Filipino with transparent, accessible, and trustworthy real estate services — from property discovery to final document signing.</p>
          </div>
          <div class="mv-card">
            <div class="mv-icon">🔭</div>
            <h3 class="mv-title">Our Vision</h3>
            <p class="mv-text">To become the leading digital real estate ecosystem in the Philippines, where every property transaction is seamless, secure, and stress-free.</p>
          </div>
          <div class="mv-card">
            <div class="mv-icon">💡</div>
            <h3 class="mv-title">Our Values</h3>
            <p class="mv-text">Integrity, transparency, and innovation guide everything we do — ensuring every buyer and agent experience is built on trust.</p>
          </div>
        </div>

        <!-- Team -->
        <div class="team-header-row">
          <h3 class="team-heading">Meet the Team</h3>
          <p class="team-subheading">The passionate people behind RealtyLinkPH</p>
        </div>
        <div class="team-grid">
          <div v-for="member in teamMembers" :key="member.name" class="team-card">
            <div class="team-avatar" :style="{ background: member.color }">
              {{ member.initials }}
            </div>
            <h4 class="team-name">{{ member.name }}</h4>
            <p class="team-role">{{ member.role }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- PROPERTIES SECTION                     -->
    <!-- ═══════════════════════════════════════ -->
    <section id="properties" class="pub-properties-section">
      <div class="container">
        <div class="section-header">
          <span class="section-label">Browse Listings</span>
          <h2 class="section-title">Explore Our <span class="text-mustard">Latest Properties</span></h2>
          <p class="section-desc">Discover quality homes, condos, and commercial spaces from verified agents across the Philippines. Login to save favorites and schedule viewings.</p>
        </div>

        <!-- Loading -->
        <div v-if="propertiesLoading" class="prop-loading">
          <div class="prop-loading-spinner"></div>
          <p>Loading properties...</p>
        </div>

        <!-- Grid -->
        <div v-else-if="landingProperties.length" class="pub-prop-grid">
          <div v-for="property in landingProperties" :key="property.id" class="pub-prop-card">
            <div class="pub-prop-image-wrap">
              <img v-if="getCoverPhoto(property)" :src="getCoverPhoto(property)" :alt="property.title" class="pub-prop-img" />
              <div v-else class="pub-prop-placeholder">🏠</div>
              <div class="pub-prop-overlay">
                <button @click="showLoginModal = true" class="btn-view-prop">👁️ View Details</button>
              </div>
              <div class="pub-price-tag">₱{{ formatPrice(property.price) }}</div>
              <div v-if="property.featured" class="pub-featured-badge">⭐ Featured</div>
            </div>
            <div class="pub-prop-body">
              <span class="pub-prop-type-badge">{{ formatPropertyType(property.property_type) }}</span>
              <h4 class="pub-prop-title">{{ property.title }}</h4>
              <p class="pub-prop-location">📍 {{ property.city }}, {{ property.province }}</p>
              <div class="pub-prop-specs">
                <span v-if="property.bedrooms">🛏️ {{ property.bedrooms }} bed</span>
                <span v-if="property.bathrooms">🚿 {{ property.bathrooms }} bath</span>
                <span v-if="property.area_sqm">📐 {{ property.area_sqm }} sqm</span>
              </div>
              <button @click="showLoginModal = true" class="btn-prop-cta">Login to View →</button>
            </div>
          </div>
        </div>

        <!-- Empty -->
        <div v-else class="prop-empty">No properties available at the moment.</div>

        <!-- See all CTA -->
        <div v-if="landingProperties.length" class="prop-cta-row">
          <button @click="showLoginModal = true" class="btn-see-all">Create Account to See All Properties →</button>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- BLOGS SECTION                          -->
    <!-- ═══════════════════════════════════════ -->
    <section id="blogs" class="blogs-section">
      <div class="container">
        <div class="section-header">
          <span class="section-label">Insights &amp; Updates</span>
          <h2 class="section-title">Real Estate <span class="text-mustard">Blog</span></h2>
          <p class="section-desc">Stay informed with the latest real estate tips, market insights, and property guides for Filipino buyers and investors.</p>
        </div>

        <div class="blogs-grid">
          <div v-for="blog in blogPosts" :key="blog.id" class="blog-card">
            <div class="blog-image-wrap" :style="{ background: blog.gradient }">
              <span class="blog-category-badge">{{ blog.category }}</span>
            </div>
            <div class="blog-body">
              <p class="blog-date">{{ blog.date }}</p>
              <h3 class="blog-title">{{ blog.title }}</h3>
              <p class="blog-excerpt">{{ blog.excerpt }}</p>
              <button @click="showLoginModal = true" class="blog-read-more">Read More →</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- LOGIN MODAL -->
    <transition name="modal-fade">
      <div v-if="showLoginModal" class="modal-overlay" @click="closeLoginModal">
        <div class="modal-content" @click.stop>
          <!-- CLOSE BUTTON -->
          <button class="close-btn" @click="closeLoginModal" title="Close">
            ✕
          </button>

          <!-- BRAND LOGO -->
          <div class="brand-section">
            <div class="brand-mark">
              <span class="brand-realty">RealtyLink</span><span class="brand-ph">PH</span>
            </div>
            <p class="brand-tagline">When Real Estate Meets Innovation</p>
          </div>

          <!-- ═══════════════════════════════════════ -->
          <!-- LOGIN VIEW                            -->
          <!-- ═══════════════════════════════════════ -->
          <template v-if="loginView === 'login'">
            <!-- HEADER -->
            <div class="form-header">
              <h1 class="login-title">Login your account</h1>
              <p class="login-subtitle">Sign in to continue to RealtyLinkPH</p>
            </div>

            <!-- ALERT -->
            <transition name="slide-down">
              <div v-if="loginAlert.show" :class="['alert', `alert-${loginAlert.type}`]">
                <span class="alert-icon">{{ loginAlert.type === 'error' ? '❌' : '✅' }}</span>
                <span class="alert-message">{{ loginAlert.message }}</span>
              </div>
            </transition>

            <!-- FORM -->
            <form @submit.prevent="handleLogin" class="login-form-content">
              <!-- EMAIL -->
              <div class="form-group">
                <label for="login-email">Email Address</label>
                <div class="input-wrapper">
                  <span class="input-icon">📧</span>
                  <input
                    id="login-email"
                    v-model="loginForm.email"
                    type="email"
                    placeholder="juan@example.com"
                    @blur="validateLoginField('email')"
                    class="form-input"
                  />
                </div>
                <transition name="slide-down">
                  <span v-if="loginErrors.email" class="form-error">{{ loginErrors.email }}</span>
                </transition>
              </div>

              <!-- PASSWORD -->
              <div class="form-group">
                <label for="login-password">Password</label>
                <div class="input-wrapper">
                  <span class="input-icon">🔐</span>
                  <input
                    id="login-password"
                    v-model="loginForm.password"
                    :type="showLoginPassword ? 'text' : 'password'"
                    placeholder="Enter your password"
                    @blur="validateLoginField('password')"
                    class="form-input"
                  />
                  <button
                    type="button"
                    class="eye-btn"
                    @click="showLoginPassword = !showLoginPassword"
                    :title="showLoginPassword ? 'Hide password' : 'Show password'"
                  >
                    {{ showLoginPassword ? '👁️‍🗨️' : '👁️' }}
                  </button>
                </div>
                <transition name="slide-down">
                  <span v-if="loginErrors.password" class="form-error">{{ loginErrors.password }}</span>
                </transition>
              </div>

              <!-- REMEMBER / FORGOT -->
              <div class="form-row">
                <label class="remember">
                  <input type="checkbox" v-model="loginForm.remember" />
                  <span>Remember me</span>
                </label>
                <a href="#" class="forgot" @click.prevent="loginView = 'forgot'">
                  Forgot password?
                </a>
              </div>

              <!-- LOGIN BUTTON -->
              <button type="submit" class="btn-submit btn-login" :disabled="loginLoading">
                <span v-if="!loginLoading" class="btn-text">Sign In</span>
                <span v-else class="btn-loading"><span class="spinner"></span>Signing in...</span>
              </button>
            </form>

            <!-- DIVIDER -->
            <div class="form-divider"><span>Don't have an account?</span></div>

            <!-- SIGNUP -->
            <button @click="switchToRegister" class="btn-submit btn-signup">Create New Account</button>

            <!-- FOOTER LINKS -->
            <div class="form-footer">
              <a href="#" class="footer-link">Terms & Conditions</a>
              <span class="separator">•</span>
              <a href="#" class="footer-link">Privacy Policy</a>
            </div>
          </template>

          <!-- ═══════════════════════════════════════ -->
          <!-- FORGOT PASSWORD VIEW                   -->
          <!-- ═══════════════════════════════════════ -->
          <template v-else-if="loginView === 'forgot'">
            <div class="form-header">
              <h1 class="login-title">Forgot Password</h1>
              <p class="login-subtitle">Enter your email and we'll send you a reset link</p>
            </div>

            <transition name="slide-down">
              <div v-if="forgotAlert.show" :class="['alert', `alert-${forgotAlert.type}`]">
                <span class="alert-icon">{{ forgotAlert.type === 'error' ? '❌' : '✅' }}</span>
                <span class="alert-message">{{ forgotAlert.message }}</span>
              </div>
            </transition>

            <form @submit.prevent="handleForgot" class="login-form-content">
              <div class="form-group">
                <label for="forgot-email">Email Address</label>
                <div class="input-wrapper">
                  <span class="input-icon">📧</span>
                  <input
                    id="forgot-email"
                    v-model="forgotForm.email"
                    type="email"
                    placeholder="juan@example.com"
                    class="form-input"
                  />
                </div>
                <transition name="slide-down">
                  <span v-if="forgotErrors.email" class="form-error">{{ forgotErrors.email }}</span>
                </transition>
              </div>

              <button type="submit" class="btn-submit btn-login" :disabled="forgotLoading">
                <span v-if="!forgotLoading" class="btn-text">Send Reset Link</span>
                <span v-else class="btn-loading"><span class="spinner"></span>Sending...</span>
              </button>
            </form>

            <div class="form-divider"><span>Remembered your password?</span></div>
            <button @click="loginView = 'login'" class="btn-submit btn-signup">
              Back to Login
            </button>
          </template>

          <!-- ═══════════════════════════════════════ -->
          <!-- FORGOT PASSWORD SENT VIEW              -->
          <!-- ═══════════════════════════════════════ -->
          <template v-else-if="loginView === 'forgot-sent'">
            <div class="forgot-sent-view">
              <div class="sent-icon">✉️</div>
              <h2 class="sent-title">Check your email</h2>
              <p class="sent-subtitle">
                We sent a password reset link to<br />
                <strong>{{ forgotForm.email }}</strong>
              </p>
              <p class="sent-note">
                Didn't receive it? Check your spam folder or
                <a href="#" @click.prevent="loginView = 'forgot'" class="resend-link">try again</a>.
              </p>
              <button @click="loginView = 'login'" class="btn-submit btn-login" style="margin-top:24px">
                Back to Login
              </button>
            </div>
          </template>
        </div>
      </div>
    </transition>

    <!-- REGISTER MODAL -->
    <transition name="modal-fade">
      <div v-if="showRegisterModal" class="modal-overlay" @click="closeRegisterModal">
        <div class="modal-content register-modal-content" @click.stop>
          <!-- CLOSE BUTTON -->
          <button class="close-btn" @click="closeRegisterModal" title="Close">
            ✕
          </button>

          <!-- BRAND LOGO -->
          <div class="brand-section">
            <div class="brand-mark">
              <span class="brand-realty">RealtyLink</span><span class="brand-ph">PH</span>
            </div>
            <p class="brand-tagline">Join our real estate community</p>
          </div>

          <!-- HEADER -->
          <div class="form-header">
            <h1 class="register-title">Create your account</h1>
            <p class="register-subtitle">
              Start your real estate journey today
            </p>
          </div>

          <!-- ALERT -->
          <transition name="slide-down">
            <div v-if="registerAlert.show" :class="['alert', `alert-${registerAlert.type}`]">
              <span class="alert-icon">{{ registerAlert.type === 'error' ? '❌' : '✅' }}</span>
              <span class="alert-message">{{ registerAlert.message }}</span>
            </div>
          </transition>

          <!-- FORM -->
          <form @submit.prevent="handleRegister" class="register-form-content">
            <!-- NAME -->
            <div class="form-group">
              <label for="register-name">Full Name</label>
              <div class="input-wrapper">
                <span class="input-icon">👤</span>
                <input
                  id="register-name"
                  v-model="registerForm.name"
                  type="text"
                  placeholder="Juan Dela Cruz"
                  @blur="validateRegisterField('name')"
                  class="form-input"
                />
              </div>
              <transition name="slide-down">
                <span v-if="registerErrors.name" class="form-error">
                  {{ registerErrors.name }}
                </span>
              </transition>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
              <label for="register-email">Email Address</label>
              <div class="input-wrapper">
                <span class="input-icon">📧</span>
                <input
                  id="register-email"
                  v-model="registerForm.email"
                  type="email"
                  placeholder="juan@example.com"
                  @blur="validateRegisterField('email')"
                  class="form-input"
                />
              </div>
              <transition name="slide-down">
                <span v-if="registerErrors.email" class="form-error">
                  {{ registerErrors.email }}
                </span>
              </transition>
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
              <label for="register-password">Password</label>
              <div class="input-wrapper">
                <span class="input-icon">🔐</span>
                <input
                  id="register-password"
                  v-model="registerForm.password"
                  :type="showRegisterPassword ? 'text' : 'password'"
                  placeholder="Minimum 8 characters"
                  @blur="validateRegisterField('password')"
                  class="form-input"
                />
                <button
                  type="button"
                  class="eye-btn"
                  @click="showRegisterPassword = !showRegisterPassword"
                  :title="showRegisterPassword ? 'Hide password' : 'Show password'"
                >
                  {{ showRegisterPassword ? '👁️‍🗨️' : '👁️' }}
                </button>
              </div>
              <transition name="slide-down">
                <span v-if="registerErrors.password" class="form-error">
                  {{ registerErrors.password }}
                </span>
              </transition>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="form-group">
              <label for="register-confirm">Confirm Password</label>
              <div class="input-wrapper">
                <span class="input-icon">🔐</span>
                <input
                  id="register-confirm"
                  v-model="registerForm.confirm_password"
                  :type="showRegisterConfirm ? 'text' : 'password'"
                  placeholder="Re-enter password"
                  @blur="validateRegisterField('confirm_password')"
                  class="form-input"
                />
                <button
                  type="button"
                  class="eye-btn"
                  @click="showRegisterConfirm = !showRegisterConfirm"
                  :title="showRegisterConfirm ? 'Hide password' : 'Show password'"
                >
                  {{ showRegisterConfirm ? '👁️‍🗨️' : '👁️' }}
                </button>
              </div>
              <transition name="slide-down">
                <span v-if="registerErrors.confirm_password" class="form-error">
                  {{ registerErrors.confirm_password }}
                </span>
              </transition>
            </div>

            <!-- TERMS CHECKBOX -->
            <div class="checkbox-group">
              <input 
                id="register-terms"
                type="checkbox" 
                v-model="registerForm.terms"
                @blur="validateRegisterField('terms')"
              />
              <label for="register-terms" class="checkbox-label">
                I agree to the
                <a href="#" class="link">Terms & Conditions</a>
                and
                <a href="#" class="link">Privacy Policy</a>
              </label>
            </div>
            <transition name="slide-down">
              <span v-if="registerErrors.terms" class="form-error">
                {{ registerErrors.terms }}
              </span>
            </transition>

            <!-- REGISTER BUTTON -->
            <button 
              type="submit"
              class="btn-submit btn-register" 
              :disabled="registerLoading"
            >
              <span v-if="!registerLoading" class="btn-text">Create Account</span>
              <span v-else class="btn-loading">
                <span class="spinner"></span>
                Creating account...
              </span>
            </button>
          </form>

          <!-- DIVIDER -->
          <div class="form-divider">
            <span>Already have an account?</span>
          </div>

          <!-- LOGIN LINK -->
          <button @click="switchToLogin" class="btn-submit btn-signin">
            Sign In Instead
          </button>

          <!-- FOOTER LINKS -->
          <div class="form-footer">
            <a href="#" class="footer-link">Need help?</a>
            <span class="separator">•</span>
            <a href="#" class="footer-link">Contact Support</a>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../src/services/api'

export default {
  name: 'Landing',
  setup() {
    const router = useRouter()

    // Landing state
    const isScrolled = ref(false)

    // Modal states
    const showLoginModal = ref(false)
    const showRegisterModal = ref(false)

    // Login view: 'login' | 'forgot' | 'forgot-sent'
    const loginView = ref('login')

    // Login state
    const loginLoading = ref(false)
    const showLoginPassword = ref(false)
    const loginForm = reactive({
      email: '',
      password: '',
      remember: false
    })
    const loginErrors = reactive({
      email: '',
      password: ''
    })
    const loginAlert = reactive({
      show: false,
      type: 'success',
      message: ''
    })

    // Forgot password state
    const forgotLoading = ref(false)
    const forgotForm = reactive({ email: '' })
    const forgotErrors = reactive({ email: '' })
    const forgotAlert = reactive({ show: false, type: 'success', message: '' })

    // Register state
    const registerLoading = ref(false)
    const showRegisterPassword = ref(false)
    const showRegisterConfirm = ref(false)
    const registerForm = reactive({
      name: '',
      email: '',
      password: '',
      confirm_password: '',
      terms: false
    })
    const registerErrors = reactive({
      name: '',
      email: '',
      password: '',
      confirm_password: '',
      terms: ''
    })
    const registerAlert = reactive({
      show: false,
      type: 'success',
      message: ''
    })

    // ─── Team Members ────────────────────────────────
    const teamMembers = [
      { name: 'Rozel Batomalaque',   role: 'Project Manager',          initials: 'RB', color: '#e6ae0d' },
      { name: 'Ericson Bareno',      role: 'Full Stack Web Developer',  initials: 'EB', color: '#1a1a2e' },
      { name: 'Napoleon Batoon Jr.', role: 'Documentor',               initials: 'NB', color: '#27ae60' },
      { name: 'Jenelyn Diaz',        role: 'Documentor',               initials: 'JD', color: '#8e44ad' },
      { name: 'Jeremy Apas',         role: 'Documentor',               initials: 'JA', color: '#e67e22' },
      { name: 'Axcell Sedon',        role: 'System Analyst',           initials: 'AS', color: '#2980b9' },
    ]

    // ─── Blog Posts ───────────────────────────────────
    const blogPosts = [
      {
        id: 1,
        title: 'Understanding Philippine Real Estate Laws for First-Time Buyers',
        excerpt: 'Navigating property ownership in the Philippines can be complex. Learn about the key legal requirements, taxes, and documentation every first-time buyer should know before signing.',
        category: 'Legal Guide',
        date: 'February 10, 2026',
        gradient: 'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)',
      },
      {
        id: 2,
        title: 'Top 5 Emerging Locations to Invest in Cebu in 2026',
        excerpt: 'Cebu continues to be a top real estate investment destination. We explore the up-and-coming barangays offering the best price-to-growth ratio for savvy investors this year.',
        category: 'Investment',
        date: 'February 3, 2026',
        gradient: 'linear-gradient(135deg, #e6ae0d 0%, #c99b2d 100%)',
      },
      {
        id: 3,
        title: 'Condo vs. House & Lot: Which is Right for You?',
        excerpt: 'Both property types have their pros and cons. From lifestyle flexibility to long-term investment value, this guide helps you make the right choice for your budget and goals.',
        category: 'Buyer Tips',
        date: 'January 27, 2026',
        gradient: 'linear-gradient(135deg, #27ae60 0%, #1e8449 100%)',
      },
    ]

    // ─── Landing Properties ───────────────────────────
    const landingProperties = ref([])
    const propertiesLoading = ref(false)

    const loadLandingProperties = async () => {
      propertiesLoading.value = true
      try {
        const base = window.__API_URL__ || 'http://localhost:8000'
        const res = await fetch(`${base}/api/properties`)
        const data = await res.json()
        const list = data.data || data || []
        landingProperties.value = Array.isArray(list) ? list.slice(0, 6) : []
      } catch (e) {
        landingProperties.value = []
      } finally {
        propertiesLoading.value = false
      }
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

      if (loginErrors.email || loginErrors.password) {
        return
      }

      loginLoading.value = true

      try {
        const res = await authAPI.login(loginForm)

        // Store token
        localStorage.setItem('auth_token', res.data.token)

        // Store user
        localStorage.setItem('user', JSON.stringify(res.data.user))

        // Reconnect Echo with the fresh token so WebSocket auth works
        if (window.reconnectEcho) window.reconnectEcho();

        // Show success alert
        loginAlert.show = true
        loginAlert.type = 'success'
        loginAlert.message = 'Login successful! Redirecting...'

        // Redirect after 1 second
        setTimeout(() => {
          router.push('/dashboard')
        }, 1000)
      } catch (e) {
        loginAlert.show = true
        loginAlert.type = 'error'
        loginAlert.message = 'Invalid credentials. Please try again.'

        // Hide alert after 3 seconds
        setTimeout(() => {
          loginAlert.show = false
        }, 3000)
      } finally {
        loginLoading.value = false
      }
    }

    const handleForgot = async () => {
      forgotErrors.email = ''
      if (!forgotForm.email) {
        forgotErrors.email = 'Email is required'
        return
      }

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

    const closeLoginModal = () => {
      showLoginModal.value = false
      loginView.value = 'login'
    }

    // Register methods
    const validateRegisterField = (field) => {
      registerErrors[field] = ''

      if (field === 'name' && !registerForm.name) {
        registerErrors.name = 'Full name is required'
      }
      
      if (field === 'email' && !registerForm.email) {
        registerErrors.email = 'Email address is required'
      }
      
      if (field === 'password' && registerForm.password.length < 8) {
        registerErrors.password = 'Password must be at least 8 characters'
      }
      
      if (field === 'confirm_password' && registerForm.password !== registerForm.confirm_password) {
        registerErrors.confirm_password = 'Passwords do not match'
      }
      
      if (field === 'terms' && !registerForm.terms) {
        registerErrors.terms = 'You must accept the terms and privacy policy'
      }
    }

    const handleRegister = async () => {
      // Validate all fields
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
          // Reset register form
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

    const closeRegisterModal = () => {
      showRegisterModal.value = false
    }

    // Switch between modals
    const switchToRegister = () => {
      showLoginModal.value = false
      showRegisterModal.value = true
    }

    const switchToLogin = () => {
      showRegisterModal.value = false
      showLoginModal.value = true
    }

    return {
      // Landing
      isScrolled,

      // Login
      showLoginModal,
      loginView,
      loginForm,
      loginErrors,
      loginAlert,
      loginLoading,
      showLoginPassword,
      validateLoginField,
      handleLogin,
      closeLoginModal,

      // Forgot password
      forgotForm,
      forgotErrors,
      forgotAlert,
      forgotLoading,
      handleForgot,

      // Register
      showRegisterModal,
      registerForm,
      registerErrors,
      registerAlert,
      registerLoading,
      showRegisterPassword,
      showRegisterConfirm,
      validateRegisterField,
      handleRegister,
      closeRegisterModal,

      // Modal switching
      switchToRegister,
      switchToLogin,

      // About / Team
      teamMembers,

      // Properties
      landingProperties,
      propertiesLoading,
      getCoverPhoto,
      formatPrice,
      formatPropertyType,

      // Blogs
      blogPosts,

      // Lifecycle
      onMounted
    }
  },
  mounted() {
    this.onMounted()
  }
}
</script>

<style scoped>
/* =========================================
   1. COLOR PALETTE & VARIABLES
   ========================================= */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.landing-page {
  /* Theme Definition */
  --color-smoky-black: #1A1A1A;
  --color-smoky-white: #F8F8F8;
  --color-mustard: #E3B341;
  --color-mustard-hover: #C99B2D;
  --color-pure-white: #FFFFFF;
  --color-gray-text: #666666;
  --color-border: #EEEEEE;
  
  --font-main: 'Plus Jakarta Sans', sans-serif;
  --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
  --radius-pill: 50px;
  --radius-card: 24px;

  font-family: var(--font-main);
  background-color: var(--color-smoky-white);
  color: var(--color-smoky-black);
  min-height: 100vh;
  overflow-x: hidden;
}

/* =========================================
   2. NAVBAR
   ========================================= */
.navbar {
  position: fixed;
  top: 0; left: 0; width: 100%;
  padding: 1.5rem 0;
  z-index: 1000;
  transition: all 0.3s ease;
  background-color: transparent;
}

.navbar.scrolled {
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  padding: 1rem 0;
  box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.navbar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand-logo {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--color-smoky-black);
  text-decoration: none;
  letter-spacing: -0.5px;
}

.text-mustard { color: var(--color-mustard); }

.nav-links {
  display: flex;
  gap: 3rem;
}

.nav-links a {
  text-decoration: none;
  color: var(--color-smoky-black);
  font-weight: 500;
  font-size: 0.95rem;
  transition: color 0.2s;
}

.nav-links a:hover { color: var(--color-mustard); }

/* =========================================
   3. HERO SECTION
   ========================================= */
.hero-section {
  position: relative;
  padding-top: 140px;
  padding-bottom: 100px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.hero-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  position: relative;
  z-index: 1;
}

/* Left Column: Text */
.hero-heading {
  font-size: 4.5rem;
  line-height: 1.1;
  font-weight: 800;
  margin-bottom: 1.5rem;
  color: var(--color-smoky-black);
  letter-spacing: -1px;
}

.text-highlight {
  color: var(--color-smoky-black);
  text-decoration: underline;
  text-decoration-color: var(--color-mustard);
  text-decoration-thickness: 6px;
  text-underline-offset: 4px;
}

.hero-subtext {
  font-size: 1.125rem;
  color: var(--color-gray-text);
  line-height: 1.6;
  max-width: 480px;
  margin-bottom: 2.5rem;
}

.hero-buttons {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

/* Buttons */
.btn {
  border: none;
  cursor: pointer;
  font-family: var(--font-main);
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-black {
  background-color: var(--color-smoky-black);
  color: var(--color-pure-white);
}

.btn-black:hover {
  background-color: var(--color-mustard);
  color: var(--color-smoky-black);
  transform: translateY(-2px);
}

.btn-pill {
  border-radius: 50px;
  padding: 0.75rem 2rem;
  font-size: 0.9rem;
}

.btn-lg {
  border-radius: 50px;
  padding: 1rem 2.5rem;
  font-size: 1rem;
}

/* Right Column: Image */
.hero-image-wrapper {
  position: relative;
  height: 600px;
  width: 100%;
}

.hero-house-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: var(--radius-card);
  box-shadow: 20px 20px 0px var(--color-mustard);
}

/* =========================================
   4. FLOATING SEARCH BAR
   ========================================= */
.search-overlap-container {
  margin-top: -60px;
  padding: 0 1.5rem;
  position: relative;
  z-index: 10;
  display: flex;
  justify-content: flex-start;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}

.search-bar {
  background: var(--color-pure-white);
  padding: 1rem 1.5rem;
  border-radius: 20px;
  box-shadow: var(--shadow-soft);
  display: inline-flex;
  align-items: center;
  gap: 1.5rem;
  border: 1px solid rgba(0,0,0,0.03);
}

.search-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 180px;
}

.label-icon {
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--color-mustard);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.search-input, .search-select {
  border: none;
  background: transparent;
  font-family: var(--font-main);
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--color-smoky-black);
  padding: 0;
  width: 100%;
  outline: none;
}

.search-input::placeholder { color: #999; font-weight: 500; }
.search-select { cursor: pointer; }

.divider {
  width: 1px;
  height: 40px;
  background-color: var(--color-border);
}

.search-btn-circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: var(--color-smoky-black);
  color: var(--color-pure-white);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.2s, background-color 0.2s;
  margin-left: 1rem;
}

.search-btn-circle:hover {
  background-color: var(--color-mustard);
  transform: scale(1.05);
}

/* =========================================
   5. MODAL STYLES
   ========================================= */

.modal-overlay {
  position: fixed;
  inset: 0;
  width: 100%;
  height: 100vh;
  font-family: var(--font-main);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(12px);
  background: rgba(0, 0, 0, 0.35);
  padding: 20px;
}

.modal-content {
  position: relative;
  width: 100%;
  max-width: 480px;
  background: white;
  border-radius: 20px;
  padding: 48px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.register-modal-content {
  max-width: 520px;
  max-height: 95vh;
  overflow-y: auto;
}

.register-modal-content::-webkit-scrollbar {
  width: 6px;
}

.register-modal-content::-webkit-scrollbar-track {
  background: transparent;
}

.register-modal-content::-webkit-scrollbar-thumb {
  background: var(--color-mustard);
  border-radius: 3px;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* CLOSE BUTTON */
.close-btn {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 40px;
  height: 40px;
  border: none;
  background: #f5f5f5;
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999;
  z-index: 10;
}

.close-btn:hover {
  background: var(--color-mustard);
  color: white;
  transform: rotate(90deg);
}

/* BRAND SECTION */
.brand-section {
  text-align: center;
  margin-bottom: 32px;
}

.brand-mark {
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  margin-bottom: 8px;
}

.brand-realty {
  color: #1a1a1a;
}

.brand-ph {
  color: var(--color-mustard);
  margin-left: 4px;
}

.brand-tagline {
  font-size: 0.9rem;
  color: #999;
  font-weight: 500;
  margin: 0;
}

/* FORM HEADER */
.form-header {
  margin-bottom: 32px;
  text-align: center;
}

.login-title, .register-title {
  font-size: 2rem;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.login-subtitle, .register-subtitle {
  color: #666;
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
}

/* ALERT */
.alert {
  margin-bottom: 24px;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 12px;
  animation: slideDown 0.3s ease-out;
}

.alert-icon {
  font-size: 1.1rem;
  flex-shrink: 0;
}

.alert-message {
  flex: 1;
}

.alert-error {
  background: #fee;
  color: #c33;
  border-left: 4px solid #c33;
}

.alert-success {
  background: #efe;
  color: #3c3;
  border-left: 4px solid #3c3;
}

/* FORM */
.login-form-content, .register-form-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #1a1a1a;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  font-size: 1.1rem;
  pointer-events: none;
}

.form-input {
  width: 100%;
  padding: 12px 14px 12px 44px;
  border-radius: 12px;
  border: 2px solid #eee;
  font-size: 0.95rem;
  font-family: var(--font-main);
  transition: all 0.3s ease;
  background: #f9f9f9;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-mustard);
  background: white;
  box-shadow: 0 0 0 4px rgba(227, 179, 65, 0.1);
}

.form-input::placeholder {
  color: #999;
}

.eye-btn {
  position: absolute;
  right: 12px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 4px;
  transition: transform 0.2s ease;
}

.eye-btn:hover {
  transform: scale(1.15);
}

.form-error {
  font-size: 0.8rem;
  color: #d9534f;
  font-weight: 500;
  animation: slideDown 0.2s ease-out;
}

/* REMEMBER / FORGOT */
.form-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  margin-bottom: 16px;
}

.remember {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  color: #666;
}

.remember input {
  width: 18px;
  height: 18px;
  accent-color: var(--color-mustard);
  cursor: pointer;
  border-radius: 4px;
}

.forgot {
  color: var(--color-mustard);
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
  transition: opacity 0.3s ease;
}

.forgot:hover {
  opacity: 0.8;
}

/* CHECKBOX */
.checkbox-group {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 8px;
  margin-bottom: 8px;
}

.checkbox-group input[type="checkbox"] {
  width: 20px;
  height: 20px;
  margin-top: 2px;
  cursor: pointer;
  accent-color: var(--color-mustard);
  flex-shrink: 0;
  border-radius: 4px;
  border: 2px solid #eee;
  transition: all 0.3s ease;
}

.checkbox-group input[type="checkbox"]:focus {
  border-color: var(--color-mustard);
  box-shadow: 0 0 0 3px rgba(227, 179, 65, 0.1);
}

.checkbox-label {
  font-size: 0.9rem;
  color: #666;
  line-height: 1.4;
  cursor: pointer;
  flex: 1;
}

.link {
  color: var(--color-mustard);
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.link:hover {
  opacity: 0.8;
  text-decoration: underline;
}

/* BUTTONS */
.btn-submit {
  width: 100%;
  padding: 14px;
  border-radius: 12px;
  border: none;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: var(--font-main);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  position: relative;
  overflow: hidden;
}

.btn-login, .btn-register {
  background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
  color: white;
}

.btn-login:hover:not(:disabled), .btn-register:hover:not(:disabled) {
  background: linear-gradient(135deg, var(--color-mustard) 0%, #ffd84d 100%);
  color: #1a1a1a;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(227, 179, 65, 0.3);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-signup, .btn-signin {
  background: transparent;
  color: var(--color-mustard);
  border: 2px solid var(--color-mustard);
}

.btn-signup:hover, .btn-signin:hover {
  background: var(--color-mustard);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(227, 179, 65, 0.2);
}

.btn-text {
  display: flex;
  align-items: center;
}

.btn-loading {
  display: flex;
  align-items: center;
  gap: 8px;
}

.spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

.btn-loading .spinner {
  border-top-color: #1a1a1a;
  border-color: rgba(26, 26, 26, 0.3);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* DIVIDER */
.form-divider {
  text-align: center;
  margin: 28px 0;
  font-size: 0.85rem;
  color: #999;
  position: relative;
}

.form-divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #eee;
}

.form-divider span {
  background: white;
  padding: 0 12px;
  position: relative;
}

/* FOOTER */
.form-footer {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 24px;
  font-size: 0.8rem;
}

.footer-link {
  color: #999;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: var(--color-mustard);
}

.separator {
  color: #ddd;
}

/* Forgot-sent success view */
.forgot-sent-view {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 8px 0 16px;
}
.sent-icon { font-size: 52px; margin-bottom: 16px; }
.sent-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--color-black);
  margin-bottom: 10px;
}
.sent-subtitle {
  font-size: 0.9rem;
  color: #555;
  line-height: 1.6;
  margin-bottom: 12px;
}
.sent-note { font-size: 0.82rem; color: #999; }
.resend-link {
  color: var(--color-mustard);
  font-weight: 600;
  text-decoration: none;
}
.resend-link:hover { text-decoration: underline; }

/* =========================================
   6. ANIMATIONS
   ========================================= */

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* =========================================
   7. RESPONSIVE
   ========================================= */

@media (max-width: 1024px) {
  .hero-heading { font-size: 3.5rem; }
  .hero-grid { grid-template-columns: 1fr; gap: 2rem; }
  .hero-image-wrapper { display: none; }
  .search-bar { width: 100%; display: grid; grid-template-columns: 1fr; gap: 1rem; }
  .divider { display: none; }
  .search-btn-circle { width: 100%; border-radius: 12px; height: 48px; margin: 0; }
  .nav-links { display: none; }
}

@media (max-width: 600px) {
  .modal-content {
    padding: 32px 24px;
    max-width: 100%;
    margin: 0 20px;
  }

  .login-title, .register-title {
    font-size: 1.5rem;
  }

  .brand-mark {
    font-size: 1.8rem;
  }

  .close-btn {
    width: 36px;
    height: 36px;
    font-size: 1.2rem;
  }

  .form-header {
    margin-bottom: 24px;
  }

  .login-form-content, .register-form-content {
    gap: 16px;
  }
}

@media (max-width: 400px) {
  .modal-overlay {
    padding: 10px;
  }

  .modal-content {
    padding: 24px 16px;
  }

  .login-title, .register-title {
    font-size: 1.3rem;
  }

  .brand-mark {
    font-size: 1.5rem;
  }

  .btn-submit {
    padding: 12px;
    font-size: 0.9rem;
  }
}

/* =========================================
   SHARED SECTION STYLES
   ========================================= */
.section-header {
  text-align: center;
  margin-bottom: 60px;
}
.section-label {
  display: inline-block;
  background: rgba(227,179,65,0.12);
  color: var(--color-mustard-hover);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  padding: 6px 18px;
  border-radius: 50px;
  margin-bottom: 16px;
}
.section-title {
  font-size: 2.2rem;
  font-weight: 800;
  line-height: 1.25;
  margin-bottom: 16px;
  color: var(--color-smoky-black);
}
.section-title .text-mustard { color: var(--color-mustard); }
.section-desc {
  font-size: 1rem;
  color: var(--color-gray-text);
  max-width: 620px;
  margin: 0 auto;
  line-height: 1.7;
}

/* =========================================
   ABOUT SECTION
   ========================================= */
.about-section {
  padding: 100px 0 80px;
  background: var(--color-pure-white);
}

/* Mission / Vision / Values */
.mv-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
  margin-bottom: 72px;
}
.mv-card {
  background: var(--color-smoky-white);
  border: 1px solid var(--color-border);
  border-radius: 20px;
  padding: 36px 28px;
  text-align: center;
  transition: box-shadow 0.3s, transform 0.3s;
}
.mv-card:hover {
  box-shadow: 0 16px 40px rgba(0,0,0,0.07);
  transform: translateY(-6px);
}
.mv-icon { font-size: 2.4rem; margin-bottom: 16px; }
.mv-title {
  font-size: 1.15rem;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--color-smoky-black);
}
.mv-text {
  font-size: 0.92rem;
  color: var(--color-gray-text);
  line-height: 1.7;
}

/* Team */
.team-header-row {
  text-align: center;
  margin-bottom: 40px;
}
.team-heading {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-smoky-black);
  margin-bottom: 8px;
}
.team-subheading {
  font-size: 0.95rem;
  color: var(--color-gray-text);
}
.team-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 24px;
}
.team-card {
  text-align: center;
  padding: 28px 16px;
  background: var(--color-smoky-white);
  border: 1px solid var(--color-border);
  border-radius: 20px;
  transition: box-shadow 0.3s, transform 0.3s;
}
.team-card:hover {
  box-shadow: 0 12px 32px rgba(0,0,0,0.08);
  transform: translateY(-5px);
}
.team-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  font-weight: 800;
  color: #fff;
  margin: 0 auto 16px;
  letter-spacing: 1px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
.team-name {
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--color-smoky-black);
  margin-bottom: 4px;
}
.team-role {
  font-size: 0.78rem;
  color: var(--color-gray-text);
}

/* =========================================
   PROPERTIES SECTION
   ========================================= */
.pub-properties-section {
  padding: 100px 0 80px;
  background: var(--color-smoky-white);
}

.pub-prop-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
  margin-bottom: 48px;
}
.pub-prop-card {
  background: var(--color-pure-white);
  border: 1px solid var(--color-border);
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s;
}
.pub-prop-card:hover {
  box-shadow: 0 16px 40px rgba(0,0,0,0.1);
  transform: translateY(-6px);
  border-color: var(--color-mustard);
}
.pub-prop-image-wrap {
  position: relative;
  height: 210px;
  overflow: hidden;
  background: #f0f0f0;
}
.pub-prop-img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.4s;
}
.pub-prop-card:hover .pub-prop-img { transform: scale(1.06); }
.pub-prop-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-size: 56px; color: #ccc;
}
.pub-prop-overlay {
  position: absolute; inset: 0;
  background: rgba(16,12,8,0.5);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity 0.3s;
}
.pub-prop-card:hover .pub-prop-overlay { opacity: 1; }
.btn-view-prop {
  padding: 11px 22px;
  background: white; color: var(--color-smoky-black);
  border: none; border-radius: 8px;
  font-weight: 700; font-size: 13px; cursor: pointer;
  transition: background 0.2s;
}
.btn-view-prop:hover { background: var(--color-mustard); }
.pub-price-tag {
  position: absolute; bottom: 12px; left: 12px;
  background: var(--color-mustard); color: var(--color-smoky-black);
  padding: 6px 14px; border-radius: 6px;
  font-weight: 800; font-size: 13px;
}
.pub-featured-badge {
  position: absolute; top: 12px; right: 12px;
  background: white; color: var(--color-mustard-hover);
  padding: 5px 10px; border-radius: 6px;
  font-weight: 700; font-size: 11px;
}
.pub-prop-body { padding: 18px; }
.pub-prop-type-badge {
  display: inline-block;
  background: rgba(227,179,65,0.12);
  color: var(--color-mustard-hover);
  font-size: 11px; font-weight: 700;
  padding: 4px 10px; border-radius: 50px;
  margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;
}
.pub-prop-title {
  font-size: 1rem; font-weight: 700;
  color: var(--color-smoky-black);
  margin-bottom: 6px;
  display: -webkit-box; -webkit-line-clamp: 2;
  -webkit-box-orient: vertical; overflow: hidden;
}
.pub-prop-location {
  font-size: 0.85rem; color: var(--color-gray-text);
  margin-bottom: 10px;
}
.pub-prop-specs {
  display: flex; gap: 12px;
  font-size: 0.82rem; color: #888;
  margin-bottom: 14px;
}
.btn-prop-cta {
  width: 100%;
  padding: 10px;
  background: var(--color-smoky-black); color: white;
  border: none; border-radius: 8px;
  font-weight: 700; font-size: 13px; cursor: pointer;
  transition: background 0.2s;
}
.btn-prop-cta:hover { background: var(--color-mustard); color: var(--color-smoky-black); }

.prop-loading {
  text-align: center; padding: 60px 0; color: var(--color-gray-text);
}
.prop-loading-spinner {
  width: 36px; height: 36px; margin: 0 auto 16px;
  border: 3px solid var(--color-border);
  border-top-color: var(--color-mustard);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.prop-empty {
  text-align: center; padding: 60px 0;
  color: var(--color-gray-text); font-size: 1rem;
}
.prop-cta-row {
  text-align: center;
}
.btn-see-all {
  padding: 14px 36px;
  background: var(--color-smoky-black); color: white;
  border: none; border-radius: 50px;
  font-weight: 700; font-size: 14px; cursor: pointer;
  transition: all 0.2s;
}
.btn-see-all:hover {
  background: var(--color-mustard); color: var(--color-smoky-black);
  transform: translateY(-2px);
}

/* =========================================
   BLOGS SECTION
   ========================================= */
.blogs-section {
  padding: 100px 0;
  background: var(--color-pure-white);
}
.blogs-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
}
.blog-card {
  background: var(--color-smoky-white);
  border: 1px solid var(--color-border);
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s;
}
.blog-card:hover {
  box-shadow: 0 16px 40px rgba(0,0,0,0.1);
  transform: translateY(-6px);
}
.blog-image-wrap {
  height: 180px;
  position: relative;
  display: flex; align-items: flex-end; padding: 16px;
}
.blog-category-badge {
  background: rgba(255,255,255,0.9);
  color: var(--color-smoky-black);
  font-size: 11px; font-weight: 700;
  padding: 5px 12px; border-radius: 50px;
  text-transform: uppercase; letter-spacing: 1px;
}
.blog-body { padding: 22px; }
.blog-date {
  font-size: 0.78rem; color: var(--color-gray-text);
  margin-bottom: 8px;
}
.blog-title {
  font-size: 1rem; font-weight: 700;
  color: var(--color-smoky-black);
  margin-bottom: 10px; line-height: 1.4;
}
.blog-excerpt {
  font-size: 0.87rem; color: var(--color-gray-text);
  line-height: 1.65; margin-bottom: 16px;
  display: -webkit-box; -webkit-line-clamp: 3;
  -webkit-box-orient: vertical; overflow: hidden;
}
.blog-read-more {
  background: none; border: none; padding: 0;
  color: var(--color-mustard-hover);
  font-weight: 700; font-size: 13px; cursor: pointer;
  transition: color 0.2s;
}
.blog-read-more:hover { color: var(--color-smoky-black); }

/* ─── Responsive ─────────────────────────── */
@media (max-width: 1024px) {
  .team-grid { grid-template-columns: repeat(3, 1fr); }
  .pub-prop-grid, .blogs-grid { grid-template-columns: repeat(2, 1fr); }
  .mv-grid { grid-template-columns: repeat(1, 1fr); }
}
@media (max-width: 640px) {
  .section-title { font-size: 1.7rem; }
  .team-grid { grid-template-columns: repeat(2, 1fr); }
  .pub-prop-grid, .blogs-grid { grid-template-columns: 1fr; }
  .mv-grid { grid-template-columns: 1fr; }
}
</style>