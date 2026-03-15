<template>
  <div class="explore-page">

    <!-- TOP BAR -->
    <header class="explore-header" :class="{ scrolled: isScrolled }">
      <div class="container header-inner">
        <router-link to="/" class="brand">
          <span class="brand-realty">Realty</span><span class="brand-link">Link</span><span class="brand-ph">PH</span>
        </router-link>
        <div class="header-actions">
          <button @click="showLoginModal = true" class="btn-header-signin">Sign In</button>
          <button @click="showRegisterModal = true" class="btn-header-register">Get Started Free</button>
        </div>
      </div>
    </header>

    <!-- SIGN-UP BANNER -->
    <div class="signup-banner">
      <div class="container signup-banner-inner">
        <div class="signup-banner-text">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><polyline points="20 6 9 17 4 12"/></svg>
          You're browsing as a guest. Sign up free to save properties, contact agents & schedule viewings.
        </div>
        <button @click="showRegisterModal = true" class="btn-banner-cta">Create Free Account</button>
      </div>
    </div>

    <!-- SEARCH / FILTER BAR -->
    <div class="filter-bar">
      <div class="container filter-inner">
        <div class="filter-field">
          <svg class="filter-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <input v-model="filters.location" type="text" placeholder="Location (city, province...)" aria-label="Filter by location" @input="debouncedLoad" />
        </div>

        <div class="filter-sep"></div>

        <div class="filter-field filter-field-select">
          <svg class="filter-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <select v-model="filters.type" @change="loadProperties" aria-label="Property type">
            <option value="">All Types</option>
            <option value="house">House</option>
            <option value="condo">Condo</option>
            <option value="apartment">Apartment</option>
            <option value="commercial">Commercial</option>
            <option value="lot">Lot</option>
          </select>
        </div>

        <div class="filter-sep"></div>

        <div class="filter-field filter-field-select">
          <svg class="filter-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          <select v-model="filters.price" @change="loadProperties" aria-label="Price range">
            <option value="">Any Price</option>
            <option value="0-5000000">Under ₱5M</option>
            <option value="5000000-10000000">₱5M – ₱10M</option>
            <option value="10000000-25000000">₱10M – ₱25M</option>
            <option value="25000000-50000000">₱25M – ₱50M</option>
            <option value="50000000+">₱50M+</option>
          </select>
        </div>

        <button class="btn-filter-search" @click="loadProperties">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Search
        </button>

        <div class="filter-result-count" v-if="!loading">
          {{ total }} {{ total === 1 ? 'property' : 'properties' }} found
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="explore-main">
      <div class="container">

        <!-- LOADING -->
        <div v-if="loading" class="state-loading">
          <div class="loading-dots">
            <span></span><span></span><span></span>
          </div>
          <p>Finding properties...</p>
        </div>

        <!-- EMPTY -->
        <div v-else-if="!properties.length" class="state-empty">
          <div class="state-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <h3>No properties found</h3>
          <p>Try adjusting your filters or search a different location.</p>
          <button class="btn-reset" @click="resetFilters">Clear Filters</button>
        </div>

        <!-- GRID -->
        <div v-else class="prop-grid">
          <div v-for="property in properties" :key="property.id" class="prop-card" @click="viewProperty(property.id)">
            <div class="prop-img-wrap">
              <img v-if="getCoverPhoto(property)" :src="getCoverPhoto(property)" :alt="property.title" class="prop-img" loading="lazy" />
              <div v-else class="prop-placeholder">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.3"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
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
                  {{ property.bedrooms }} bed
                </span>
                <span v-if="property.bathrooms" class="spec-pill">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3v2.25"/></svg>
                  {{ property.bathrooms }} bath
                </span>
                <span v-if="property.floor_area" class="spec-pill">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                  {{ property.floor_area }}m²
                </span>
              </div>
              <div class="prop-view-btn">
                View Details
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </div>
            </div>
          </div>
        </div>

        <!-- PAGINATION -->
        <div v-if="lastPage > 1" class="pagination">
          <button class="page-btn" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          </button>

          <template v-for="p in visiblePages" :key="p">
            <button v-if="p !== '...'" class="page-btn" :class="{ active: p === currentPage }" @click="changePage(p)">{{ p }}</button>
            <span v-else class="page-ellipsis">…</span>
          </template>

          <button class="page-btn" :disabled="currentPage === lastPage" @click="changePage(currentPage + 1)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>

      </div>
    </main>

    <!-- STICKY BOTTOM CTA (mobile) -->
    <div class="sticky-cta">
      <div class="sticky-cta-text">Sign up to save, contact agents & more</div>
      <button class="btn-sticky-cta" @click="showRegisterModal = true">Join Free</button>
    </div>

    <!-- LOGIN MODAL -->
    <transition name="modal-fade">
      <div v-if="showLoginModal" class="modal-overlay" @click="showLoginModal = false">
        <div class="modal-box" @click.stop>
          <button class="modal-close" @click="showLoginModal = false" aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
          <div class="modal-brand"><div class="modal-brand-name">RealtyLink<span class="gold">PH</span></div></div>
          <div class="modal-head">
            <h1>Sign In</h1>
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
              <label for="l-email">Email Address</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input id="l-email" v-model="loginForm.email" type="email" placeholder="juan@example.com" />
              </div>
              <transition name="slide-down"><span v-if="loginErrors.email" class="field-err">{{ loginErrors.email }}</span></transition>
            </div>
            <div class="field">
              <label for="l-pass">Password</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="l-pass" v-model="loginForm.password" :type="showPassword ? 'text' : 'password'" placeholder="Password" />
                <button type="button" class="field-eye" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide' : 'Show'">
                  <svg v-if="!showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
              </div>
              <transition name="slide-down"><span v-if="loginErrors.password" class="field-err">{{ loginErrors.password }}</span></transition>
            </div>
            <button type="submit" class="btn-primary" :disabled="loginLoading">
              <span v-if="!loginLoading">Sign In</span>
              <span v-else class="btn-loading-inner"><span class="dot-spinner"></span>Signing in...</span>
            </button>
          </form>
          <div class="modal-divider"><span>Don't have an account?</span></div>
          <button class="btn-secondary" @click="showLoginModal = false; showRegisterModal = true">Create Account</button>
        </div>
      </div>
    </transition>

    <!-- REGISTER MODAL -->
    <transition name="modal-fade">
      <div v-if="showRegisterModal" class="modal-overlay" @click="showRegisterModal = false">
        <div class="modal-box modal-box-wide" @click.stop>
          <button class="modal-close" @click="showRegisterModal = false" aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
          <div class="modal-brand"><div class="modal-brand-name">RealtyLink<span class="gold">PH</span></div></div>
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
              <label for="r-name">Full Name</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input id="r-name" v-model="registerForm.name" type="text" placeholder="Juan Dela Cruz" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.name" class="field-err">{{ registerErrors.name }}</span></transition>
            </div>
            <div class="field">
              <label for="r-email">Email Address</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input id="r-email" v-model="registerForm.email" type="email" placeholder="juan@example.com" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.email" class="field-err">{{ registerErrors.email }}</span></transition>
            </div>
            <div class="field">
              <label for="r-pass">Password</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="r-pass" v-model="registerForm.password" type="password" placeholder="Minimum 8 characters" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.password" class="field-err">{{ registerErrors.password }}</span></transition>
            </div>
            <div class="field">
              <label for="r-confirm">Confirm Password</label>
              <div class="field-input-wrap">
                <svg class="field-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="r-confirm" v-model="registerForm.confirm_password" type="password" placeholder="Re-enter password" />
              </div>
              <transition name="slide-down"><span v-if="registerErrors.confirm_password" class="field-err">{{ registerErrors.confirm_password }}</span></transition>
            </div>
            <div class="check-group">
              <input id="r-terms" type="checkbox" v-model="registerForm.terms" />
              <label for="r-terms">I agree to the <a href="#" @click.prevent class="link-accent">Terms & Conditions</a> and <a href="#" @click.prevent class="link-accent">Privacy Policy</a></label>
            </div>
            <transition name="slide-down"><span v-if="registerErrors.terms" class="field-err">{{ registerErrors.terms }}</span></transition>
            <button type="submit" class="btn-primary" :disabled="registerLoading">
              <span v-if="!registerLoading">Create Account</span>
              <span v-else class="btn-loading-inner"><span class="dot-spinner"></span>Creating...</span>
            </button>
          </form>
          <div class="modal-divider"><span>Already have an account?</span></div>
          <button class="btn-secondary" @click="showRegisterModal = false; showLoginModal = true">Sign In Instead</button>
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
  name: 'GuestProperties',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const apiBase = window.__API_URL__ || 'http://localhost:8000'

    // Header scroll state
    const isScrolled = ref(false)
    const handleScroll = () => { isScrolled.value = window.scrollY > 10 }

    // Filter state — pre-populated from URL query params
    const filters = reactive({
      location: route.query.location || '',
      type:     route.query.type     || '',
      price:    route.query.price    || ''
    })

    // Properties state
    const properties = ref([])
    const loading = ref(false)
    const currentPage = ref(1)
    const lastPage = ref(1)
    const total = ref(0)

    const loadProperties = async (page = 1) => {
      loading.value = true
      currentPage.value = page

      try {
        const params = new URLSearchParams({ page, per_page: 12 })
        if (filters.location) params.append('location', filters.location)
        if (filters.type)     params.append('property_type', filters.type)
        if (filters.price) {
          const [min, max] = filters.price.split('-')
          if (min) params.append('min_price', min)
          if (max && max !== '+') params.append('max_price', max)
        }

        const res = await fetch(`${apiBase}/api/properties?${params}`)
        const data = await res.json()

        const paginated = data.data || data
        const list = paginated.data || paginated
        properties.value = Array.isArray(list) ? list : []
        lastPage.value = paginated.last_page || 1
        total.value = paginated.total || properties.value.length
      } catch {
        properties.value = []
      } finally {
        loading.value = false
      }
    }

    // Debounce for location text input
    let debounceTimer = null
    const debouncedLoad = () => {
      clearTimeout(debounceTimer)
      debounceTimer = setTimeout(() => loadProperties(1), 450)
    }

    const resetFilters = () => {
      filters.location = ''
      filters.type = ''
      filters.price = ''
      loadProperties(1)
    }

    const changePage = (p) => {
      if (p < 1 || p > lastPage.value) return
      window.scrollTo({ top: 0, behavior: 'smooth' })
      loadProperties(p)
    }

    // Visible page numbers with ellipsis
    const visiblePages = computed(() => {
      const total = lastPage.value
      const cur = currentPage.value
      if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
      const pages = []
      if (cur <= 4) {
        for (let i = 1; i <= 5; i++) pages.push(i)
        pages.push('...')
        pages.push(total)
      } else if (cur >= total - 3) {
        pages.push(1)
        pages.push('...')
        for (let i = total - 4; i <= total; i++) pages.push(i)
      } else {
        pages.push(1)
        pages.push('...')
        for (let i = cur - 1; i <= cur + 1; i++) pages.push(i)
        pages.push('...')
        pages.push(total)
      }
      return pages
    })

    const viewProperty = (id) => router.push(`/property/${id}`)

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

    // Modal states
    const showLoginModal = ref(false)
    const showRegisterModal = ref(false)

    // Login
    const loginLoading = ref(false)
    const showPassword = ref(false)
    const loginForm = reactive({ email: '', password: '' })
    const loginErrors = reactive({ email: '', password: '' })
    const loginAlert = reactive({ show: false, type: 'success', message: '' })

    const handleLogin = async () => {
      loginErrors.email = loginForm.email ? '' : 'Email is required'
      loginErrors.password = loginForm.password ? '' : 'Password is required'
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
        const role = res.data.user?.role
        if (role === 'admin') router.push('/admin/dashboard')
        else if (role === 'agent') router.push('/agent/dashboard')
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

    // Register
    const registerLoading = ref(false)
    const registerForm = reactive({ name: '', email: '', password: '', confirm_password: '', terms: false })
    const registerErrors = reactive({ name: '', email: '', password: '', confirm_password: '', terms: '' })
    const registerAlert = reactive({ show: false, type: 'success', message: '' })

    const handleRegister = async () => {
      registerErrors.name = registerForm.name ? '' : 'Full name is required'
      registerErrors.email = registerForm.email ? '' : 'Email is required'
      registerErrors.password = registerForm.password.length >= 8 ? '' : 'At least 8 characters'
      registerErrors.confirm_password = registerForm.password === registerForm.confirm_password ? '' : 'Passwords do not match'
      registerErrors.terms = registerForm.terms ? '' : 'You must accept the terms'
      if (Object.values(registerErrors).some(Boolean)) return

      registerLoading.value = true
      try {
        await authAPI.register({
          name: registerForm.name,
          email: registerForm.email,
          password: registerForm.password,
          password_confirmation: registerForm.confirm_password
        })
        registerAlert.message = 'Account created! Redirecting to sign in...'
        registerAlert.type = 'success'
        registerAlert.show = true
        setTimeout(() => {
          showRegisterModal.value = false
          showLoginModal.value = true
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

    onMounted(() => {
      window.addEventListener('scroll', handleScroll)
      loadProperties(1)
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
      clearTimeout(debounceTimer)
    })

    return {
      isScrolled,
      filters, loadProperties, debouncedLoad, resetFilters,
      properties, loading, total, currentPage, lastPage, visiblePages, changePage,
      viewProperty, getCoverPhoto, formatPrice, formatPropertyType,
      showLoginModal, showRegisterModal,
      loginForm, loginErrors, loginAlert, loginLoading, showPassword, handleLogin,
      registerForm, registerErrors, registerAlert, registerLoading, handleRegister
    }
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Sora:wght@300;400;500;600;700;800&display=swap');
</style>

<style scoped>
.explore-page {
  --navy:        #0C1A2E;
  --navy-light:  #162640;
  --navy-mid:    #253D58;
  --frost:       #F0B429;
  --frost-light: #F7CA55;
  --frost-pale:  #FDE68A;
  --ash-light:   #F4F6F8;
  --stone-50:  #F8F9FA;
  --stone-100: #F2F3F5;
  --stone-200: #E2E6EA;
  --stone-400: #8E9BAA;
  --stone-500: #637283;
  --stone-600: #475669;
  --stone-800: #0C1A2E;
  --white:     #FFFFFF;

  --font-ui:      'Sora', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-display: 'DM Serif Display', Georgia, serif;
  --font-brand:   'Sora', -apple-system, sans-serif;
  --radius:      14px;
  --radius-sm:   8px;
  --radius-lg:   20px;
  --radius-pill: 50px;

  font-family: var(--font-ui);
  background: var(--ash-light);
  color: var(--stone-800);
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
  padding-bottom: 72px; /* space for sticky CTA */
}

.container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

/* ── Header ── */
.explore-header {
  position: sticky; top: 0; z-index: 100;
  background: rgba(12,26,46,0.96);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  padding: 14px 0;
  border-bottom: 1px solid rgba(240,180,41,0.08);
  transition: box-shadow 0.3s;
}

.explore-header.scrolled { box-shadow: 0 4px 24px rgba(0,0,0,0.18); }

.header-inner {
  display: flex; align-items: center; justify-content: space-between;
}

.brand {
  font-family: var(--font-brand);
  font-size: 20px; text-decoration: none;
  display: inline-flex; align-items: baseline;
}

.brand-realty { font-weight: 300; color: rgba(255,255,255,0.72); }
.brand-link   { font-weight: 800; color: var(--white); }
.brand-ph     { font-weight: 800; color: var(--frost); }

.header-actions { display: flex; gap: 10px; align-items: center; }

.btn-header-signin {
  padding: 8px 20px; background: transparent;
  color: rgba(255,255,255,0.75);
  border: 1.5px solid rgba(240,180,41,0.22);
  border-radius: var(--radius-pill);
  font-size: 13px; font-weight: 500;
  cursor: pointer; transition: all 0.3s;
  font-family: var(--font-ui);
}

.btn-header-signin:hover {
  color: var(--white); border-color: rgba(240,180,41,0.55);
  background: rgba(240,180,41,0.08);
}

.btn-header-register {
  padding: 8px 22px;
  background: var(--frost); color: var(--navy);
  border: none; border-radius: var(--radius-pill);
  font-size: 13px; font-weight: 700;
  cursor: pointer; transition: all 0.3s;
  font-family: var(--font-ui);
  box-shadow: 0 4px 14px rgba(240,180,41,0.28);
}

.btn-header-register:hover { background: var(--frost-light); transform: translateY(-1px); }

/* ── Sign-up Banner ── */
.signup-banner {
  background: linear-gradient(90deg, var(--navy) 0%, var(--navy-mid) 100%);
  border-bottom: 1px solid rgba(240,180,41,0.12);
  padding: 12px 0;
}

.signup-banner-inner {
  display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
}

.signup-banner-text {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: rgba(255,255,255,0.65); font-weight: 300;
}

.signup-banner-text svg { flex-shrink: 0; color: var(--frost); }

.btn-banner-cta {
  padding: 7px 20px;
  background: var(--frost); color: var(--navy);
  border: none; border-radius: var(--radius-pill);
  font-size: 12px; font-weight: 700;
  cursor: pointer; white-space: nowrap;
  transition: all 0.3s;
  font-family: var(--font-ui);
}

.btn-banner-cta:hover { background: var(--frost-light); }

/* ── Filter Bar ── */
.filter-bar {
  background: var(--white);
  border-bottom: 1px solid var(--stone-200);
  padding: 14px 0;
  position: sticky; top: 57px; z-index: 90;
  box-shadow: 0 2px 12px rgba(12,26,46,0.06);
}

.filter-inner {
  display: flex; align-items: center; gap: 0; flex-wrap: wrap; gap: 8px;
}

.filter-field {
  flex: 1; min-width: 140px;
  position: relative; display: flex; align-items: center;
}

.filter-field-select { flex: 0 0 auto; }

.filter-ico {
  position: absolute; left: 12px;
  color: var(--stone-400); pointer-events: none;
}

.filter-field input,
.filter-field select {
  width: 100%;
  padding: 10px 12px 10px 36px;
  border: 1.5px solid var(--stone-200);
  border-radius: 10px; font-size: 13px;
  font-family: var(--font-ui); color: var(--stone-800);
  background: var(--stone-50); outline: none;
  transition: border-color 0.25s;
}

.filter-field input:focus,
.filter-field select:focus { border-color: var(--frost); }

.filter-sep { display: none; }

.btn-filter-search {
  display: flex; align-items: center; gap: 7px;
  padding: 10px 22px;
  background: var(--navy); color: var(--white);
  border: none; border-radius: 50px;
  font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.3s;
  font-family: var(--font-ui); white-space: nowrap;
}

.btn-filter-search:hover { background: var(--navy-mid); }

.filter-result-count {
  font-size: 12px; color: var(--stone-500);
  font-weight: 400; white-space: nowrap; margin-left: 4px;
}

/* ── Main ── */
.explore-main { padding: 40px 0 60px; }

/* States */
.state-loading { text-align: center; padding: 80px 0; color: var(--stone-500); }
.loading-dots  { display: flex; justify-content: center; gap: 6px; margin-bottom: 16px; }
.loading-dots span {
  width: 9px; height: 9px; border-radius: 50%;
  background: var(--frost);
  animation: dotPulse 1.2s ease-in-out infinite;
}
.loading-dots span:nth-child(2) { animation-delay: 0.15s; }
.loading-dots span:nth-child(3) { animation-delay: 0.30s; }
@keyframes dotPulse {
  0%,80%,100% { opacity: 0.3; transform: scale(0.8); }
  40%         { opacity: 1;   transform: scale(1); }
}

.state-empty {
  text-align: center; padding: 80px 0;
  display: flex; flex-direction: column; align-items: center; gap: 12px;
}

.state-icon {
  width: 80px; height: 80px; border-radius: 50%;
  background: var(--stone-100);
  display: flex; align-items: center; justify-content: center;
  color: var(--stone-400); margin-bottom: 8px;
}

.state-empty h3 { font-family: var(--font-display); font-size: 22px; font-weight: 400; color: var(--navy); }
.state-empty p  { font-size: 14px; color: var(--stone-500); font-weight: 300; }

.btn-reset {
  padding: 10px 28px; margin-top: 8px;
  background: transparent; color: var(--navy);
  border: 1.5px solid var(--navy);
  border-radius: var(--radius-pill);
  font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.3s;
  font-family: var(--font-ui);
}

.btn-reset:hover { background: var(--navy); color: var(--frost-light); }

/* ── Property Grid ── */
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

.prop-card:hover { transform: translateY(-6px); box-shadow: 0 24px 52px rgba(12,26,46,0.10); border-color: var(--frost-pale); }

.prop-img-wrap { position: relative; height: 220px; overflow: hidden; background: var(--stone-100); }

.prop-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.16,1,0.3,1); }
.prop-card:hover .prop-img { transform: scale(1.05); }

.prop-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--stone-400); }

.prop-price-pill {
  position: absolute; bottom: 12px; left: 12px;
  background: rgba(12,26,46,0.90); backdrop-filter: blur(10px);
  color: var(--frost-light);
  padding: 6px 14px; border-radius: var(--radius-sm);
  font-weight: 700; font-size: 14px; font-family: var(--font-display);
  z-index: 3; border: 1px solid rgba(240,180,41,0.18);
  box-shadow: 0 4px 12px rgba(0,0,0,0.22);
}

.prop-badges { position: absolute; top: 10px; left: 10px; display: flex; gap: 5px; z-index: 3; }

.prop-badge {
  display: flex; align-items: center; gap: 4px;
  padding: 4px 10px; border-radius: var(--radius-pill);
  font-size: 10px; font-weight: 700; letter-spacing: 0.3px; text-transform: uppercase;
}

.badge-featured { background: var(--frost); color: var(--navy); box-shadow: 0 2px 8px rgba(240,180,41,0.35); }
.badge-360 { background: rgba(12,26,46,0.80); color: var(--white); border: 1px solid rgba(255,255,255,0.18); backdrop-filter: blur(4px); }

.prop-img-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(12,26,46,0.60) 0%, rgba(12,26,46,0.08) 50%, transparent 100%);
  z-index: 2; pointer-events: none;
}

.prop-body { padding: 20px; }

.prop-meta-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }

.prop-type-tag {
  display: inline-block; font-size: 10px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.6px; color: var(--navy-light);
  background: rgba(240,180,41,0.14); padding: 4px 12px; border-radius: var(--radius-pill);
}

.prop-verified-tag { display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #10B981; text-transform: uppercase; }

.prop-title { font-family: var(--font-display); font-size: 16px; font-weight: 400; margin-bottom: 5px; color: var(--navy); line-height: 1.3; }

.prop-location { display: flex; align-items: center; gap: 5px; font-size: 13px; color: var(--stone-500); margin-bottom: 12px; font-weight: 300; }
.prop-location svg { flex-shrink: 0; color: var(--stone-400); }

.prop-specs { display: flex; gap: 6px; flex-wrap: wrap; padding-top: 10px; border-top: 1px solid var(--stone-100); }

.spec-pill { display: flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 500; color: var(--stone-600); background: var(--stone-100); padding: 4px 10px; border-radius: var(--radius-pill); }
.spec-pill svg { color: var(--stone-400); flex-shrink: 0; }

.prop-view-btn {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  margin-top: 12px; padding: 9px 16px;
  font-size: 13px; font-weight: 600; color: var(--navy);
  border: 1.5px solid var(--stone-200); border-radius: var(--radius-pill);
  transition: all 0.3s;
}

.prop-card:hover .prop-view-btn { background: var(--navy); color: var(--frost-light); border-color: var(--navy); }

/* ── Pagination ── */
.pagination {
  display: flex; align-items: center; justify-content: center;
  gap: 6px; margin-top: 52px;
}

.page-btn {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  background: var(--white); color: var(--stone-600);
  border: 1.5px solid var(--stone-200);
  font-size: 13px; font-weight: 500;
  cursor: pointer; transition: all 0.25s;
  font-family: var(--font-ui);
}

.page-btn:hover:not(:disabled) { border-color: var(--navy); color: var(--navy); }
.page-btn.active { background: var(--navy); color: var(--white); border-color: var(--navy); }
.page-btn:disabled { opacity: 0.35; cursor: not-allowed; }

.page-ellipsis { color: var(--stone-400); font-size: 14px; padding: 0 4px; }

/* ── Sticky CTA (mobile) ── */
.sticky-cta {
  position: fixed; bottom: 0; left: 0; right: 0; z-index: 200;
  display: none;
  background: var(--navy);
  border-top: 1px solid rgba(240,180,41,0.14);
  padding: 12px 24px;
  align-items: center; justify-content: space-between; gap: 12px;
}

.sticky-cta-text { font-size: 13px; color: rgba(255,255,255,0.60); font-weight: 300; }

.btn-sticky-cta {
  padding: 9px 22px; background: var(--frost); color: var(--navy);
  border: none; border-radius: var(--radius-pill);
  font-size: 13px; font-weight: 700; cursor: pointer;
  font-family: var(--font-ui); white-space: nowrap;
}

/* ── Modal styles (same palette as Landing) ── */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(8,16,30,0.72);
  display: flex; align-items: center; justify-content: center;
  z-index: 2000; backdrop-filter: blur(14px); padding: 20px;
}

.modal-box {
  background: linear-gradient(165deg, rgba(18,32,52,0.98) 0%, rgba(8,16,30,0.99) 100%);
  border: 1px solid rgba(240,180,41,0.14);
  border-radius: 22px; width: 100%; max-width: 440px;
  padding: 40px 36px; position: relative;
  max-height: 90vh; overflow-y: auto;
  box-shadow: 0 32px 80px rgba(0,0,0,0.45), inset 0 1px 0 rgba(240,180,41,0.08);
}

.modal-box-wide { max-width: 480px; padding: 36px 32px; }

.modal-close {
  position: absolute; top: 16px; right: 16px;
  width: 36px; height: 36px; border-radius: 50%;
  border: 1px solid rgba(240,180,41,0.10); background: rgba(240,180,41,0.04);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: rgba(255,255,255,0.35); transition: all 0.3s;
}

.modal-close:hover { background: rgba(240,180,41,0.10); color: rgba(255,255,255,0.75); }

.modal-brand { text-align: center; margin-bottom: 28px; }
.modal-brand-name { font-size: 24px; font-weight: 700; color: #FAFAF9; font-family: var(--font-brand); }
.modal-brand-name .gold { color: var(--frost); }
.modal-head { text-align: center; margin-bottom: 24px; }
.modal-head h1 { font-family: var(--font-display); font-size: 22px; font-weight: 400; color: #FAFAF9; margin-bottom: 6px; }
.modal-head p  { font-size: 13px; color: rgba(255,255,255,0.40); font-weight: 300; }

.m-alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 13px; }
.m-alert-success { background: rgba(16,185,129,0.10); color: #6EE7B7; border: 1px solid rgba(16,185,129,0.18); }
.m-alert-error   { background: rgba(239,68,68,0.10);  color: #FCA5A5; border: 1px solid rgba(239,68,68,0.18); }
.m-alert-icon { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0; }
.m-alert-success .m-alert-icon { background: rgba(16,185,129,0.22); color: #6EE7B7; }
.m-alert-error   .m-alert-icon { background: rgba(239,68,68,0.22);  color: #FCA5A5; }

.modal-form { display: flex; flex-direction: column; }
.field { margin-bottom: 16px; }
.field label { display: block; font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.45); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
.field-input-wrap { position: relative; display: flex; align-items: center; }
.field-ico { position: absolute; left: 14px; color: rgba(240,180,41,0.25); pointer-events: none; }

.field-input-wrap input {
  width: 100%; padding: 12px 14px 12px 42px;
  border: 1.5px solid rgba(240,180,41,0.10); border-radius: 12px;
  font-size: 14px; font-family: var(--font-ui); color: #FAFAF9;
  background: rgba(240,180,41,0.04); transition: all 0.25s; outline: none;
}

.field-input-wrap input:focus { border-color: var(--frost); background: rgba(240,180,41,0.07); box-shadow: 0 0 0 3px rgba(240,180,41,0.12); }
.field-input-wrap input::placeholder { color: rgba(255,255,255,0.18); }

.field-eye { position: absolute; right: 12px; background: none; border: none; cursor: pointer; padding: 4px; color: rgba(240,180,41,0.25); display: flex; transition: color 0.2s; }
.field-eye:hover { color: rgba(240,180,41,0.55); }
.field-err { display: block; font-size: 12px; color: #FCA5A5; margin-top: 4px; }

.check-group { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 14px; }
.check-group input[type="checkbox"] { accent-color: var(--frost); width: 15px; height: 15px; margin-top: 2px; flex-shrink: 0; }
.check-group label { font-size: 13px; color: rgba(255,255,255,0.40); line-height: 1.4; font-weight: 300; }

.link-accent { color: var(--frost); text-decoration: none; font-weight: 500; }
.link-accent:hover { color: var(--frost-light); }

.btn-primary { width: 100%; padding: 13px; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: var(--font-ui); background: var(--frost); color: var(--navy); }
.btn-primary:hover { background: var(--frost-light); box-shadow: 0 4px 20px rgba(240,180,41,0.28); }
.btn-primary:disabled { opacity: 0.45; cursor: not-allowed; }

.btn-secondary { width: 100%; padding: 12px; border: 1.5px solid rgba(240,180,41,0.12); border-radius: 12px; font-size: 14px; font-weight: 400; cursor: pointer; transition: all 0.3s; font-family: var(--font-ui); background: transparent; color: rgba(255,255,255,0.50); }
.btn-secondary:hover { background: rgba(240,180,41,0.06); border-color: rgba(240,180,41,0.22); color: rgba(255,255,255,0.80); }

.btn-loading-inner { display: flex; align-items: center; justify-content: center; gap: 8px; }
.dot-spinner { width: 16px; height: 16px; border: 2px solid rgba(12,26,46,0.15); border-top-color: var(--navy); border-radius: 50%; animation: spin 0.7s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

.modal-divider { display: flex; align-items: center; gap: 14px; margin: 20px 0; color: rgba(255,255,255,0.20); font-size: 13px; }
.modal-divider::before, .modal-divider::after { content: ''; flex: 1; height: 1px; background: rgba(240,180,41,0.08); }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-active .modal-box, .modal-fade-leave-active .modal-box { transition: transform 0.35s cubic-bezier(0.16,1,0.3,1), opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-from .modal-box { transform: scale(0.96) translateY(10px); opacity: 0; }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.28s cubic-bezier(0.16,1,0.3,1); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-6px); }

/* ── Responsive ── */
@media (max-width: 1024px) {
  .prop-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .prop-grid { grid-template-columns: 1fr; }
  .filter-inner { flex-wrap: wrap; }
  .filter-field { min-width: 100%; }
  .btn-filter-search { width: 100%; justify-content: center; }
  .header-actions .btn-header-signin { display: none; }
  .sticky-cta { display: flex; }
  .signup-banner-inner { flex-direction: column; align-items: flex-start; gap: 8px; }
}

@media (max-width: 480px) {
  .container { padding: 0 16px; }
  .modal-box { padding: 28px 20px; margin: 12px; }
}
</style>
