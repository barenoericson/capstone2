<template>
  <div class="landing-page">
    <!-- ═══════════════════════════════════════ -->
    <!-- NAVBAR                                  -->
    <!-- ═══════════════════════════════════════ -->
    <nav class="navbar" :class="{ scrolled: isScrolled }">
      <div class="container navbar-content">
        <router-link to="/" class="brand-logo">
          RealtyLink<span class="brand-gold">PH</span>
        </router-link>
        <div class="nav-actions">
          <button @click="showLoginModal = true" class="btn-nav-login">Login</button>
          <button @click="showRegisterModal = true" class="btn-nav-register">Get Started</button>
        </div>
      </div>
    </nav>

    <!-- ═══════════════════════════════════════ -->
    <!-- HERO SECTION                            -->
    <!-- ═══════════════════════════════════════ -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="container hero-content">
        <h1 class="hero-title">
          Find Your Dream Home<br />in the <span class="text-gold">Philippines</span>
        </h1>
        <p class="hero-subtitle">
          Browse verified properties from trusted agents. Schedule viewings, send offers, and sign documents — all in one place.
        </p>

        <!-- Search Bar -->
        <div class="hero-search-bar">
          <div class="search-field">
            <label>Location</label>
            <input
              type="text"
              v-model="searchLocation"
              placeholder="e.g., Cebu City, Makati"
            />
          </div>
          <div class="search-divider"></div>
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
          <div class="search-divider"></div>
          <div class="search-field">
            <label>Price Range</label>
            <select v-model="searchPrice">
              <option value="">Any Price</option>
              <option value="0-5000000">Under 5M</option>
              <option value="5000000-10000000">5M - 10M</option>
              <option value="10000000-25000000">10M - 25M</option>
              <option value="25000000-50000000">25M - 50M</option>
              <option value="50000000+">50M+</option>
            </select>
          </div>
          <button @click="handleSearch" class="search-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            Search
          </button>
        </div>

        <!-- Stats -->
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Properties</span>
          </div>
          <div class="stat-dot"></div>
          <div class="stat-item">
            <span class="stat-number">100+</span>
            <span class="stat-label">Verified Agents</span>
          </div>
          <div class="stat-dot"></div>
          <div class="stat-item">
            <span class="stat-number">1,000+</span>
            <span class="stat-label">Happy Buyers</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FEATURED PROPERTIES                     -->
    <!-- ═══════════════════════════════════════ -->
    <section class="properties-section">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">Featured Listings</span>
          <h2 class="section-title">Explore Our Latest Properties</h2>
          <p class="section-desc">Discover quality homes, condos, and commercial spaces from verified agents across the Philippines.</p>
        </div>

        <!-- Loading -->
        <div v-if="propertiesLoading" class="prop-loading">
          <div class="prop-spinner"></div>
          <p>Loading properties...</p>
        </div>

        <!-- Grid -->
        <div v-else-if="landingProperties.length" class="properties-grid">
          <div v-for="property in landingProperties" :key="property.id" class="property-card" @click="showLoginModal = true">
            <div class="card-image-wrap">
              <img v-if="getCoverPhoto(property)" :src="getCoverPhoto(property)" :alt="property.title" class="card-image" />
              <div v-else class="card-placeholder">🏠</div>
              <div class="card-price">₱{{ formatPrice(property.price) }}</div>
              <div v-if="property.featured" class="card-featured">Featured</div>
            </div>
            <div class="card-body">
              <span class="card-type">{{ formatPropertyType(property.property_type) }}</span>
              <h3 class="card-title">{{ property.title }}</h3>
              <p class="card-location">📍 {{ property.city }}, {{ property.province }}</p>
              <div class="card-specs">
                <span v-if="property.bedrooms">🛏️ {{ property.bedrooms }}</span>
                <span v-if="property.bathrooms">🚿 {{ property.bathrooms }}</span>
                <span v-if="property.floor_area">📐 {{ property.floor_area }} sqm</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty -->
        <div v-else class="prop-empty">No properties available at the moment.</div>

        <!-- CTA -->
        <div v-if="landingProperties.length" class="section-cta">
          <button @click="showRegisterModal = true" class="btn-outline">See All Properties →</button>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- HOW IT WORKS                            -->
    <!-- ═══════════════════════════════════════ -->
    <section class="how-section">
      <div class="container">
        <div class="section-header">
          <span class="section-tag">How It Works</span>
          <h2 class="section-title">Your Journey to Homeownership</h2>
        </div>

        <div class="steps-grid">
          <div class="step-card">
            <div class="step-icon">🔍</div>
            <div class="step-number">01</div>
            <h3>Search Properties</h3>
            <p>Browse thousands of verified listings with powerful filters for location, type, and price range.</p>
          </div>
          <div class="step-card">
            <div class="step-icon">💬</div>
            <div class="step-number">02</div>
            <h3>Connect with Agents</h3>
            <p>Message verified real estate agents directly, view their profiles, and get expert guidance.</p>
          </div>
          <div class="step-card">
            <div class="step-icon">📝</div>
            <div class="step-number">03</div>
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
      <div class="container cta-content">
        <h2>Ready to Find Your Dream Home?</h2>
        <p>Create a free account to save properties, schedule viewings, and connect with agents.</p>
        <button @click="showRegisterModal = true" class="btn-cta">Get Started Free</button>
      </div>
    </section>

    <!-- ═══════════════════════════════════════ -->
    <!-- FOOTER                                  -->
    <!-- ═══════════════════════════════════════ -->
    <footer class="landing-footer">
      <div class="container footer-content">
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
        <div class="modal-content" @click.stop>
          <button class="close-btn" @click="closeLoginModal" title="Close">✕</button>

          <div class="brand-section">
            <div class="brand-mark">
              <span class="brand-realty">RealtyLink</span><span class="brand-ph">PH</span>
            </div>
            <p class="brand-tagline">When Real Estate Meets Innovation</p>
          </div>

          <!-- LOGIN VIEW -->
          <template v-if="loginView === 'login'">
            <div class="form-header">
              <h1 class="login-title">Login your account</h1>
              <p class="login-subtitle">Sign in to continue to RealtyLinkPH</p>
            </div>

            <transition name="slide-down">
              <div v-if="loginAlert.show" :class="['alert', `alert-${loginAlert.type}`]">
                <span class="alert-icon">{{ loginAlert.type === 'error' ? '❌' : '✅' }}</span>
                <span class="alert-message">{{ loginAlert.message }}</span>
              </div>
            </transition>

            <form @submit.prevent="handleLogin" class="login-form-content">
              <div class="form-group">
                <label for="login-email">Email Address</label>
                <div class="input-wrapper">
                  <span class="input-icon">📧</span>
                  <input id="login-email" v-model="loginForm.email" type="email" placeholder="juan@example.com" @blur="validateLoginField('email')" class="form-input" />
                </div>
                <transition name="slide-down">
                  <span v-if="loginErrors.email" class="form-error">{{ loginErrors.email }}</span>
                </transition>
              </div>

              <div class="form-group">
                <label for="login-password">Password</label>
                <div class="input-wrapper">
                  <span class="input-icon">🔐</span>
                  <input id="login-password" v-model="loginForm.password" :type="showLoginPassword ? 'text' : 'password'" placeholder="Enter your password" @blur="validateLoginField('password')" class="form-input" />
                  <button type="button" class="eye-btn" @click="showLoginPassword = !showLoginPassword">{{ showLoginPassword ? '👁️‍🗨️' : '👁️' }}</button>
                </div>
                <transition name="slide-down">
                  <span v-if="loginErrors.password" class="form-error">{{ loginErrors.password }}</span>
                </transition>
              </div>

              <div class="form-row">
                <label class="remember"><input type="checkbox" v-model="loginForm.remember" /><span>Remember me</span></label>
                <a href="#" class="forgot" @click.prevent="loginView = 'forgot'">Forgot password?</a>
              </div>

              <button type="submit" class="btn-submit btn-login" :disabled="loginLoading">
                <span v-if="!loginLoading" class="btn-text">Sign In</span>
                <span v-else class="btn-loading"><span class="spinner"></span>Signing in...</span>
              </button>
            </form>

            <div class="form-divider"><span>Don't have an account?</span></div>
            <button @click="switchToRegister" class="btn-submit btn-signup">Create New Account</button>

            <div class="form-footer">
              <a href="#" class="footer-link">Terms & Conditions</a>
              <span class="separator">•</span>
              <a href="#" class="footer-link">Privacy Policy</a>
            </div>
          </template>

          <!-- FORGOT PASSWORD VIEW -->
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
                  <input id="forgot-email" v-model="forgotForm.email" type="email" placeholder="juan@example.com" class="form-input" />
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
            <button @click="loginView = 'login'" class="btn-submit btn-signup">Back to Login</button>
          </template>

          <!-- FORGOT PASSWORD SENT VIEW -->
          <template v-else-if="loginView === 'forgot-sent'">
            <div class="forgot-sent-view">
              <div class="sent-icon">✉️</div>
              <h2 class="sent-title">Check your email</h2>
              <p class="sent-subtitle">We sent a password reset link to<br /><strong>{{ forgotForm.email }}</strong></p>
              <p class="sent-note">Didn't receive it? Check your spam folder or <a href="#" @click.prevent="loginView = 'forgot'" class="resend-link">try again</a>.</p>
              <button @click="loginView = 'login'" class="btn-submit btn-login" style="margin-top:24px">Back to Login</button>
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
        <div class="modal-content register-modal-content" @click.stop>
          <button class="close-btn" @click="closeRegisterModal" title="Close">✕</button>

          <div class="brand-section">
            <div class="brand-mark">
              <span class="brand-realty">RealtyLink</span><span class="brand-ph">PH</span>
            </div>
            <p class="brand-tagline">Join our real estate community</p>
          </div>

          <div class="form-header">
            <h1 class="register-title">Create your account</h1>
            <p class="register-subtitle">Start your real estate journey today</p>
          </div>

          <transition name="slide-down">
            <div v-if="registerAlert.show" :class="['alert', `alert-${registerAlert.type}`]">
              <span class="alert-icon">{{ registerAlert.type === 'error' ? '❌' : '✅' }}</span>
              <span class="alert-message">{{ registerAlert.message }}</span>
            </div>
          </transition>

          <form @submit.prevent="handleRegister" class="register-form-content">
            <div class="form-group">
              <label for="register-name">Full Name</label>
              <div class="input-wrapper">
                <span class="input-icon">👤</span>
                <input id="register-name" v-model="registerForm.name" type="text" placeholder="Juan Dela Cruz" @blur="validateRegisterField('name')" class="form-input" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.name" class="form-error">{{ registerErrors.name }}</span></transition>
            </div>

            <div class="form-group">
              <label for="register-email">Email Address</label>
              <div class="input-wrapper">
                <span class="input-icon">📧</span>
                <input id="register-email" v-model="registerForm.email" type="email" placeholder="juan@example.com" @blur="validateRegisterField('email')" class="form-input" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.email" class="form-error">{{ registerErrors.email }}</span></transition>
            </div>

            <div class="form-group">
              <label for="register-password">Password</label>
              <div class="input-wrapper">
                <span class="input-icon">🔐</span>
                <input id="register-password" v-model="registerForm.password" :type="showRegisterPassword ? 'text' : 'password'" placeholder="Minimum 8 characters" @blur="validateRegisterField('password')" class="form-input" />
                <button type="button" class="eye-btn" @click="showRegisterPassword = !showRegisterPassword">{{ showRegisterPassword ? '👁️‍🗨️' : '👁️' }}</button>
              </div>
              <transition name="slide-down"><span v-if="registerErrors.password" class="form-error">{{ registerErrors.password }}</span></transition>
            </div>

            <div class="form-group">
              <label for="register-confirm">Confirm Password</label>
              <div class="input-wrapper">
                <span class="input-icon">🔐</span>
                <input id="register-confirm" v-model="registerForm.confirm_password" :type="showRegisterConfirm ? 'text' : 'password'" placeholder="Re-enter password" @blur="validateRegisterField('confirm_password')" class="form-input" />
                <button type="button" class="eye-btn" @click="showRegisterConfirm = !showRegisterConfirm">{{ showRegisterConfirm ? '👁️‍🗨️' : '👁️' }}</button>
              </div>
              <transition name="slide-down"><span v-if="registerErrors.confirm_password" class="form-error">{{ registerErrors.confirm_password }}</span></transition>
            </div>

            <div class="checkbox-group">
              <input id="register-terms" type="checkbox" v-model="registerForm.terms" @blur="validateRegisterField('terms')" />
              <label for="register-terms" class="checkbox-label">I agree to the <a href="#" class="link">Terms & Conditions</a> and <a href="#" class="link">Privacy Policy</a></label>
            </div>
            <transition name="slide-down"><span v-if="registerErrors.terms" class="form-error">{{ registerErrors.terms }}</span></transition>

            <button type="submit" class="btn-submit btn-register" :disabled="registerLoading">
              <span v-if="!registerLoading" class="btn-text">Create Account</span>
              <span v-else class="btn-loading"><span class="spinner"></span>Creating account...</span>
            </button>
          </form>

          <div class="form-divider"><span>Already have an account?</span></div>
          <button @click="switchToLogin" class="btn-submit btn-signin">Sign In Instead</button>

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

    // Landing Properties
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
      if (loginErrors.email || loginErrors.password) return

      loginLoading.value = true
      try {
        const res = await authAPI.login(loginForm)
        localStorage.setItem('auth_token', res.data.token)
        localStorage.setItem('user', JSON.stringify(res.data.user))
        if (window.reconnectEcho) window.reconnectEcho()

        loginAlert.show = true
        loginAlert.type = 'success'
        loginAlert.message = 'Login successful! Redirecting...'

        setTimeout(() => {
          const userData = res.data.user
          if (userData.role === 'admin') router.push('/admin/dashboard')
          else if (userData.role === 'agent') router.push('/agent/dashboard')
          else router.push('/dashboard')
        }, 1000)
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
      isScrolled,
      searchLocation, searchType, searchPrice, handleSearch,
      showLoginModal, loginView, loginForm, loginErrors, loginAlert, loginLoading, showLoginPassword,
      validateLoginField, handleLogin, closeLoginModal,
      forgotForm, forgotErrors, forgotAlert, forgotLoading, handleForgot,
      showRegisterModal, registerForm, registerErrors, registerAlert, registerLoading,
      showRegisterPassword, showRegisterConfirm, validateRegisterField, handleRegister, closeRegisterModal,
      switchToRegister, switchToLogin,
      landingProperties, propertiesLoading, getCoverPhoto, formatPrice, formatPropertyType,
      onMounted
    }
  },
  mounted() {
    this.onMounted()
  }
}
</script>

<style scoped>
/* ═══════════════════════════════════════════════
   LANDING PAGE - COMPLETE STYLES
   Inspired by Lamudi & Airbnb
   ═══════════════════════════════════════════════ */

.landing-page {
  --gold: #FFD700;
  --gold-dark: #DAB600;
  --gold-light: #FFE44D;
  --black: #1A1A1A;
  --white: #FFFFFF;
  --off-white: #F9F9F9;
  --gray-100: #F3F3F3;
  --gray-300: #D1D1D1;
  --gray-500: #888888;
  --gray-700: #555555;
  --font: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  --radius: 16px;
  --radius-sm: 10px;
  --radius-pill: 50px;

  font-family: var(--font);
  background: var(--white);
  color: var(--black);
  min-height: 100vh;
  overflow-x: hidden;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* ─── NAVBAR ─── */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  padding: 16px 0;
  transition: all 0.3s ease;
}

.navbar.scrolled {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(12px);
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
  padding: 12px 0;
}

.navbar-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand-logo {
  font-size: 22px;
  font-weight: 800;
  color: var(--white);
  text-decoration: none;
  letter-spacing: -0.5px;
  transition: color 0.3s;
}

.navbar.scrolled .brand-logo {
  color: var(--black);
}

.brand-gold {
  color: var(--gold);
}

.nav-actions {
  display: flex;
  gap: 10px;
  align-items: center;
}

.btn-nav-login {
  padding: 10px 20px;
  background: transparent;
  color: var(--white);
  border: 1.5px solid rgba(255, 255, 255, 0.4);
  border-radius: var(--radius-pill);
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font);
}

.navbar.scrolled .btn-nav-login {
  color: var(--black);
  border-color: var(--gray-300);
}

.btn-nav-login:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: var(--white);
}

.navbar.scrolled .btn-nav-login:hover {
  background: var(--gray-100);
  border-color: var(--black);
}

.btn-nav-register {
  padding: 10px 22px;
  background: var(--gold);
  color: var(--black);
  border: none;
  border-radius: var(--radius-pill);
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font);
}

.btn-nav-register:hover {
  background: var(--gold-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.35);
}

/* ─── HERO ─── */
.hero-section {
  position: relative;
  min-height: 92vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: url('/images/logo/landingpagepicture.jpg') center/cover no-repeat;
  padding: 120px 0 80px;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(10, 10, 10, 0.7) 0%, rgba(10, 10, 10, 0.55) 50%, rgba(10, 10, 10, 0.75) 100%);
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  width: 100%;
}

.hero-title {
  font-size: 3.8rem;
  font-weight: 800;
  color: var(--white);
  line-height: 1.12;
  margin-bottom: 20px;
  letter-spacing: -1px;
}

.text-gold {
  color: var(--gold);
}

.hero-subtitle {
  font-size: 1.15rem;
  color: rgba(255, 255, 255, 0.75);
  max-width: 600px;
  margin: 0 auto 40px;
  line-height: 1.6;
  font-weight: 400;
}

/* ─── HERO SEARCH BAR ─── */
.hero-search-bar {
  display: flex;
  align-items: stretch;
  background: var(--white);
  border-radius: var(--radius);
  padding: 8px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  max-width: 820px;
  margin: 0 auto 48px;
}

.search-field {
  flex: 1;
  padding: 12px 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.search-field label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--gray-500);
  letter-spacing: 0.5px;
}

.search-field input,
.search-field select {
  border: none;
  outline: none;
  font-size: 14px;
  font-weight: 500;
  color: var(--black);
  background: transparent;
  font-family: var(--font);
  padding: 0;
  cursor: pointer;
}

.search-field input::placeholder {
  color: var(--gray-300);
}

.search-divider {
  width: 1px;
  background: var(--gray-100);
  margin: 12px 0;
}

.search-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 14px 28px;
  background: var(--gold);
  color: var(--black);
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font);
  white-space: nowrap;
}

.search-btn:hover {
  background: var(--gold-dark);
  box-shadow: 0 4px 16px rgba(255, 215, 0, 0.4);
}

/* ─── HERO STATS ─── */
.hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 32px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.stat-number {
  font-size: 1.8rem;
  font-weight: 800;
  color: var(--gold);
}

.stat-label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
}

.stat-dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
}

/* ─── SECTIONS COMMON ─── */
.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-tag {
  display: inline-block;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: var(--gold-dark);
  background: rgba(255, 215, 0, 0.1);
  padding: 6px 16px;
  border-radius: var(--radius-pill);
  margin-bottom: 16px;
}

.section-title {
  font-size: 2.2rem;
  font-weight: 800;
  color: var(--black);
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.section-desc {
  font-size: 1rem;
  color: var(--gray-500);
  max-width: 550px;
  margin: 0 auto;
  line-height: 1.6;
}

.section-cta {
  text-align: center;
  margin-top: 40px;
}

.btn-outline {
  padding: 14px 32px;
  background: transparent;
  color: var(--black);
  border: 2px solid var(--black);
  border-radius: var(--radius-pill);
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font);
}

.btn-outline:hover {
  background: var(--black);
  color: var(--white);
}

/* ─── FEATURED PROPERTIES ─── */
.properties-section {
  padding: 100px 0;
  background: var(--off-white);
}

.properties-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.property-card {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--gray-100);
  transition: all 0.3s;
  cursor: pointer;
}

.property-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.1);
}

.card-image-wrap {
  position: relative;
  height: 220px;
  overflow: hidden;
  background: var(--gray-100);
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s;
}

.property-card:hover .card-image {
  transform: scale(1.05);
}

.card-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  background: var(--gray-100);
}

.card-price {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: rgba(0, 0, 0, 0.8);
  color: var(--gold);
  padding: 6px 14px;
  border-radius: 8px;
  font-weight: 800;
  font-size: 14px;
  backdrop-filter: blur(4px);
}

.card-featured {
  position: absolute;
  top: 12px;
  right: 12px;
  background: var(--gold);
  color: var(--black);
  padding: 4px 12px;
  border-radius: var(--radius-pill);
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.card-body {
  padding: 20px;
}

.card-type {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--gold-dark);
  background: rgba(255, 215, 0, 0.1);
  padding: 3px 10px;
  border-radius: var(--radius-pill);
  margin-bottom: 8px;
}

.card-title {
  font-size: 17px;
  font-weight: 700;
  margin-bottom: 4px;
  color: var(--black);
}

.card-location {
  font-size: 13px;
  color: var(--gray-500);
  margin-bottom: 12px;
}

.card-specs {
  display: flex;
  gap: 12px;
  font-size: 13px;
  color: var(--gray-700);
  padding-top: 12px;
  border-top: 1px solid var(--gray-100);
}

/* ─── HOW IT WORKS ─── */
.how-section {
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
  padding: 40px 30px;
  background: var(--off-white);
  border-radius: var(--radius);
  border: 1px solid var(--gray-100);
  transition: all 0.3s;
}

.step-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
  border-color: var(--gold);
}

.step-icon {
  font-size: 40px;
  margin-bottom: 16px;
}

.step-number {
  font-size: 48px;
  font-weight: 900;
  color: rgba(255, 215, 0, 0.15);
  position: absolute;
  top: 16px;
  right: 24px;
}

.step-card h3 {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 10px;
  color: var(--black);
}

.step-card p {
  font-size: 14px;
  color: var(--gray-500);
  line-height: 1.6;
}

/* ─── CTA BANNER ─── */
.cta-section {
  padding: 80px 0;
  background: linear-gradient(135deg, var(--black) 0%, #2a2520 100%);
}

.cta-content {
  text-align: center;
}

.cta-content h2 {
  font-size: 2.4rem;
  font-weight: 800;
  color: var(--white);
  margin-bottom: 12px;
}

.cta-content p {
  font-size: 1.05rem;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 32px;
}

.btn-cta {
  padding: 16px 40px;
  background: var(--gold);
  color: var(--black);
  border: none;
  border-radius: var(--radius-pill);
  font-weight: 800;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font);
}

.btn-cta:hover {
  background: var(--gold-light);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(255, 215, 0, 0.35);
}

/* ─── FOOTER ─── */
.landing-footer {
  padding: 40px 0;
  background: var(--black);
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.footer-content {
  text-align: center;
}

.footer-brand {
  font-size: 20px;
  font-weight: 800;
  color: var(--white);
  margin-bottom: 16px;
}

.footer-brand span {
  color: var(--gold);
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: 24px;
  margin-bottom: 16px;
}

.footer-links a {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: var(--gold);
}

.footer-copy {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.3);
}

/* ─── LOADING ─── */
.prop-loading {
  text-align: center;
  padding: 60px 0;
  color: var(--gray-500);
}

.prop-spinner {
  width: 36px;
  height: 36px;
  border: 3px solid var(--gray-100);
  border-top-color: var(--gold);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 12px;
}

.prop-empty {
  text-align: center;
  padding: 60px 0;
  color: var(--gray-500);
  font-size: 15px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ═══════════════════════════════════════════════
   MODAL STYLES (Login & Register)
   ═══════════════════════════════════════════════ */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(4px);
  padding: 20px;
}

.modal-content {
  background: var(--white);
  border-radius: 20px;
  width: 100%;
  max-width: 440px;
  padding: 40px 36px;
  position: relative;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.2);
}

.register-modal-content {
  max-width: 480px;
  padding: 36px 32px;
}

.close-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: none;
  background: var(--gray-100);
  font-size: 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
  color: var(--gray-500);
}

.close-btn:hover {
  background: var(--gray-300);
  color: var(--black);
}

.brand-section {
  text-align: center;
  margin-bottom: 24px;
}

.brand-mark {
  font-size: 22px;
  font-weight: 800;
}

.brand-realty { color: var(--black); }
.brand-ph { color: var(--gold); }

.brand-tagline {
  font-size: 13px;
  color: var(--gray-500);
  margin-top: 4px;
}

.form-header {
  text-align: center;
  margin-bottom: 24px;
}

.login-title, .register-title {
  font-size: 22px;
  font-weight: 800;
  color: var(--black);
  margin-bottom: 4px;
}

.login-subtitle, .register-subtitle {
  font-size: 14px;
  color: var(--gray-500);
}

.alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 16px;
  font-size: 13px;
  font-weight: 500;
}

.alert-success {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert-error {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.form-group {
  margin-bottom: 18px;
}

.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--black);
  margin-bottom: 6px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  font-size: 15px;
  pointer-events: none;
}

.form-input {
  width: 100%;
  padding: 12px 14px 12px 42px;
  border: 1.5px solid var(--gray-100);
  border-radius: 12px;
  font-size: 14px;
  font-family: var(--font);
  color: var(--black);
  background: var(--off-white);
  transition: all 0.3s;
  outline: none;
}

.form-input:focus {
  border-color: var(--gold);
  background: var(--white);
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.eye-btn {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 16px;
  padding: 4px;
}

.form-error {
  display: block;
  font-size: 12px;
  color: #dc2626;
  margin-top: 4px;
  font-weight: 500;
}

.form-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  font-size: 13px;
}

.remember {
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  color: var(--gray-700);
}

.remember input[type="checkbox"] {
  accent-color: var(--gold);
  width: 16px;
  height: 16px;
}

.forgot {
  color: var(--gold-dark);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s;
}

.forgot:hover {
  color: var(--black);
}

.btn-submit {
  width: 100%;
  padding: 13px;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-family: var(--font);
}

.btn-login, .btn-register {
  background: var(--gold);
  color: var(--black);
}

.btn-login:hover, .btn-register:hover {
  background: var(--gold-dark);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.btn-login:disabled, .btn-register:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-signup, .btn-signin {
  background: transparent;
  color: var(--black);
  border: 1.5px solid var(--gray-100);
}

.btn-signup:hover, .btn-signin:hover {
  background: var(--gray-100);
  border-color: var(--gray-300);
}

.btn-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(0, 0, 0, 0.15);
  border-top-color: var(--black);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

.form-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0;
  color: var(--gray-500);
  font-size: 13px;
}

.form-divider::before,
.form-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--gray-100);
}

.form-footer {
  text-align: center;
  margin-top: 20px;
  font-size: 12px;
  color: var(--gray-500);
}

.form-footer .footer-link {
  color: var(--gray-500);
  text-decoration: none;
  transition: color 0.3s;
}

.form-footer .footer-link:hover {
  color: var(--gold-dark);
}

.form-footer .separator {
  margin: 0 6px;
}

.checkbox-group {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 16px;
}

.checkbox-group input[type="checkbox"] {
  accent-color: var(--gold);
  width: 16px;
  height: 16px;
  margin-top: 2px;
  flex-shrink: 0;
}

.checkbox-label {
  font-size: 13px;
  color: var(--gray-700);
  line-height: 1.4;
}

.checkbox-label .link {
  color: var(--gold-dark);
  text-decoration: none;
  font-weight: 600;
}

.checkbox-label .link:hover {
  color: var(--black);
}

/* Forgot password sent */
.forgot-sent-view {
  text-align: center;
  padding: 16px 0;
}

.sent-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.sent-title {
  font-size: 22px;
  font-weight: 800;
  color: var(--black);
  margin-bottom: 8px;
}

.sent-subtitle {
  font-size: 14px;
  color: var(--gray-500);
  line-height: 1.5;
  margin-bottom: 16px;
}

.sent-note {
  font-size: 13px;
  color: var(--gray-500);
}

.resend-link {
  color: var(--gold-dark);
  font-weight: 600;
  text-decoration: none;
}

.resend-link:hover {
  color: var(--black);
}

/* ─── TRANSITIONS ─── */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-active .modal-content,
.modal-fade-leave-active .modal-content {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-from .modal-content {
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

/* ═══════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════ */
@media (max-width: 1024px) {
  .hero-title { font-size: 3rem; }
  .properties-grid { grid-template-columns: repeat(2, 1fr); }
  .steps-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .hero-title { font-size: 2.4rem; }
  .hero-subtitle { font-size: 1rem; }

  .hero-search-bar {
    flex-direction: column;
    gap: 0;
  }

  .search-divider {
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
    gap: 16px;
  }

  .stat-dot { display: none; }

  .properties-grid { grid-template-columns: 1fr; }
  .steps-grid { grid-template-columns: 1fr; }

  .section-title { font-size: 1.8rem; }
  .cta-content h2 { font-size: 1.8rem; }

  .modal-content {
    padding: 32px 24px;
    margin: 16px;
  }
}

@media (max-width: 480px) {
  .hero-title { font-size: 2rem; }
  .btn-nav-register { display: none; }
  .container { padding: 0 16px; }

  .properties-section,
  .how-section { padding: 60px 0; }

  .cta-section { padding: 50px 0; }
}
</style>
