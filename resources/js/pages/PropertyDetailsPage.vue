<template>
  <div class="property-details-wrapper">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p>Loading property details...</p>
    </div>

    <!-- Property Details Content -->
    <div v-else-if="property" class="property-container">
      <!-- Back Button -->
      <button @click="goBack" class="btn-back">← Back</button>

      <!-- Mosaic Hero Gallery -->
      <div class="gallery-section">
        <div :class="['mosaic-hero', { 'mosaic-single': !property.photos || property.photos.length < 2 }]">
          <!-- Large primary image -->
          <div class="mosaic-main" @click="currentPhotoIndex = 0">
            <img
              v-if="property.photos && property.photos.length > 0"
              :src="property.photos[0].photo_url"
              :alt="property.title"
              class="mosaic-img"
            />
            <div v-else class="mosaic-placeholder">
              <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              <span>No photos available</span>
            </div>
            <div v-if="property.featured" class="featured-badge">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              Featured
            </div>
          </div>

          <!-- Side column: 2 stacked thumbnails -->
          <div v-if="property.photos && property.photos.length > 1" class="mosaic-side">
            <div class="mosaic-thumb" @click="currentPhotoIndex = 1">
              <img :src="property.photos[1].photo_url" alt="Photo 2" class="mosaic-img" />
            </div>
            <div
              v-if="property.photos.length > 2"
              class="mosaic-thumb"
              @click="currentPhotoIndex = 2"
            >
              <img :src="property.photos[2].photo_url" alt="Photo 3" class="mosaic-img" />
              <div v-if="property.photos.length > 3" class="mosaic-more-overlay">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>+{{ property.photos.length - 3 }} more</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Thumbnail strip -->
        <div v-if="property.photos && property.photos.length > 1" class="thumbnail-gallery">
          <div
            v-for="(photo, index) in property.photos"
            :key="index"
            @click="currentPhotoIndex = index"
            :class="['thumbnail', { active: index === currentPhotoIndex }]"
          >
            <img :src="photo.photo_url" :alt="`Photo ${index + 1}`" />
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="content-grid">
        <!-- Left Column: Property Details -->
        <div class="details-column">
          <!-- Price and Type -->
          <div class="price-section">
            <h1 class="property-title">{{ property.title }}</h1>
            <p class="property-location">
              📍 {{ property.city }}, {{ property.province }}, {{ property.country }}
            </p>
            <div class="price-type">
              <span class="price">₱{{ formatPrice(property.price) }}{{ (property.listing_type || 'sale') === 'rent' ? '/mo' : '' }}</span>
              <span class="listing-type-tag" :class="property.listing_type || 'sale'">{{ (property.listing_type || 'sale') === 'rent' ? 'For Rent' : 'For Sale' }}</span>
              <span class="property-type">{{ formatPropertyType(property.property_type) }}</span>
            </div>
          </div>

          <!-- Property Specs -->
          <div class="specs-section">
            <div class="spec-item">
              <span class="spec-icon">🛏️</span>
              <div>
                <p class="spec-label">Bedrooms</p>
                <p class="spec-value">{{ property.bedrooms }}</p>
              </div>
            </div>
            <div class="spec-item">
              <span class="spec-icon">🚿</span>
              <div>
                <p class="spec-label">Bathrooms</p>
                <p class="spec-value">{{ property.bathrooms }}</p>
              </div>
            </div>
            <div v-if="property.area && property.area !== 'N/A'" class="spec-item">
              <span class="spec-icon">📐</span>
              <div>
                <p class="spec-label">Area</p>
                <p class="spec-value">{{ property.area }} sqm</p>
              </div>
            </div>
            <div class="spec-item">
              <span class="spec-icon">👁️</span>
              <div>
                <p class="spec-label">Views</p>
                <p class="spec-value">{{ property.view_count || 0 }}</p>
              </div>
            </div>
          </div>

          <!-- Status -->
          <div class="status-section">
            <span class="status-badge" :class="property.status">
              {{ formatStatus(property.status) }}
            </span>
          </div>

          <!-- Description -->
          <div class="description-section">
            <h3>Description</h3>
            <p>{{ property.description || 'No description available.' }}</p>
          </div>

          <!-- Features -->
          <div v-if="property.features" class="features-section">
            <h3>Features</h3>
            <ul class="features-list">
              <li v-for="(feature, index) in parseFeatures(property.features)" :key="index">
                ✓ {{ feature }}
              </li>
            </ul>
          </div>

          <!-- Location Info -->
          <div class="location-info-section">
            <h3>Location Information</h3>
            <dl class="loc-grid">
              <div v-if="property.address" class="loc-row">
                <dt class="loc-label">Address</dt>
                <dd class="loc-value">{{ property.address }}</dd>
              </div>
              <div class="loc-row">
                <dt class="loc-label">City</dt>
                <dd class="loc-value">{{ property.city }}</dd>
              </div>
              <div class="loc-row">
                <dt class="loc-label">Province</dt>
                <dd class="loc-value">{{ property.province }}</dd>
              </div>
              <div class="loc-row">
                <dt class="loc-label">Country</dt>
                <dd class="loc-value">{{ property.country }}</dd>
              </div>
              <div v-if="property.postal_code" class="loc-row">
                <dt class="loc-label">Postal Code</dt>
                <dd class="loc-value">{{ property.postal_code }}</dd>
              </div>
            </dl>
          </div>

          <!-- Property Map -->
          <div class="map-section">
            <h3>Property Location</h3>
            <div v-if="mapLoading" class="map-loading">
              <div class="spinner-sm"></div>
              <span>Loading map...</span>
            </div>
            <div v-else-if="mapError" class="map-error">
              <span>📍</span> {{ mapError }}
            </div>
            <div id="property-map" ref="mapContainer" class="map-container"></div>

            <!-- Nearby Landmarks -->
            <div class="landmarks-section">
              <h4 class="landmarks-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Nearby Landmarks
              </h4>
              <ul class="landmarks-list">
                <li v-for="place in nearbyLandmarks" :key="place.name" class="landmark-item">
                  <span class="landmark-pin-dot" :style="{ background: place.color || '#001F3F' }"></span>
                  <div class="landmark-info">
                    <span class="landmark-name">{{ place.name }}</span>
                    <span class="landmark-type">{{ place.type }}</span>
                  </div>
                  <span class="landmark-dist">{{ place.dist }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Right Column: Agent Card & Actions -->
        <aside class="sidebar-column">
          <!-- Agent Card -->
          <div class="agent-card">
            <h3 class="card-title">Listed by</h3>
            
            <!-- Agent Photo -->
            <div v-if="property.agent && property.agent.user" class="agent-photo-container">
              <div v-if="property.agent.user.profile_photo_path" class="agent-photo">
                <img 
                  :src="`${apiUrl}/storage/${property.agent.user.profile_photo_path}`" 
                  :alt="property.agent.user.name"
                />
              </div>
              <!-- ✅ FIXED: Safe navigation with fallback -->
              <div v-else class="agent-photo-placeholder">
                {{ property.agent.user?.name?.charAt(0)?.toUpperCase() || '?' }}
              </div>
            </div>

            <!-- Agent Info -->
            <!-- ✅ FIXED: Safe navigation -->
            <div v-if="property.agent && property.agent.user" class="agent-info">
              <h4 class="agent-name">{{ property.agent.user.name }}</h4>
              <p class="agent-role">Real Estate Agent</p>
              <p v-if="property.agent.user.email" class="agent-email">📧 {{ property.agent.user.email }}</p>
              <p v-if="property.agent.user.phone" class="agent-phone">📱 {{ property.agent.user.phone }}</p>
            </div>

            <!-- No Agent Info -->
            <div v-else class="agent-info">
              <p class="no-agent-text">Agent information not available</p>
            </div>

            <!-- PRIMARY CTA: Schedule Viewing -->
            <div class="property-actions">
              <button @click="scheduleViewing" class="btn-schedule">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Schedule a Viewing
              </button>

              <!-- DISTINCT: Virtual Tour = navy filled -->
              <button
                v-if="property.panoramas && property.panoramas.length > 0"
                @click="showTourModal = true"
                class="btn-tour-navy"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                </svg>
                Virtual 360° Tour
              </button>
            </div>

            <hr class="divider" />

            <!-- SECONDARY: Message + Call (navy outline) -->
            <div class="agent-actions">
              <button
                v-if="property.agent && property.agent.user"
                @click="messageAgent"
                class="btn-outline-navy"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Message
              </button>
              <button @click="callAgent" class="btn-outline-navy">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.75-.75a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.28 16.5z"/>
                </svg>
                Call
              </button>
            </div>

            <!-- TERTIARY: Save Property (text link) -->
            <button @click="saveProperty" class="btn-save-link">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
              Save Property
            </button>
          </div>

          <!-- Quick Info Box -->
          <div class="info-box">
            <h4>Quick Info</h4>
            <div class="info-items">
              <div class="info-row">
                <span>Posted</span>
                <strong>{{ formatDate(property.created_at) }}</strong>
              </div>
              <div class="info-row">
                <span>Views</span>
                <strong>{{ property.view_count || 0 }}</strong>
              </div>
              <div class="info-row">
                <span>Property ID</span>
                <strong>#{{ property.id }}</strong>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="error-container">
      <div class="error-icon">⚠️</div>
      <h2>Property Not Found</h2>
      <p>The property you're looking for doesn't exist or has been removed.</p>
      <button @click="goBack" class="btn-back-error">← Go Back</button>
    </div>

    <!-- ✅ VIEWING MODAL -->
    <div v-if="showViewingModal" class="modal-overlay" @click.self="showViewingModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>📅 Schedule a Viewing</h3>
          <button @click="showViewingModal = false" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <!-- Calendar Date Picker -->
          <div class="form-group">
            <label>Select a Date *</label>
            <ViewingCalendar
              :blocked-dates="blockedDates"
              :booked-dates="bookedDates"
              v-model:selected-date="viewingForm.date"
            />
          </div>

          <!-- Time picker (shown after date is selected) -->
          <div v-if="viewingForm.date" class="form-group">
            <label>Preferred Time *</label>
            <input v-model="viewingForm.time" type="time" class="form-input" />
          </div>

          <!-- Message -->
          <div v-if="viewingForm.date" class="form-group">
            <label>Message to Agent</label>
            <textarea v-model="viewingForm.message" class="form-textarea" placeholder="Any special requests or notes..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="showViewingModal = false" class="btn-secondary">Cancel</button>
          <button @click="submitViewing" class="btn-primary">Schedule</button>
        </div>
      </div>
    </div>

    <!-- 360° TOUR MODAL -->
    <div v-if="showTourModal" class="tour-modal-overlay" @click.self="showTourModal = false">
      <div class="tour-modal-box">
        <div class="tour-modal-header">
          <h3>🔄 Virtual 360° Tour</h3>
          <span class="tour-property-name">{{ property.title }}</span>
          <button @click="showTourModal = false" class="btn-close-tour">✕</button>
        </div>
        <div class="tour-modal-body">
          <PanoramaViewer
            v-if="showTourModal"
            :panoramas="property.panoramas"
          />
        </div>
      </div>
    </div>

    <!-- LOGIN MODAL (for unauthenticated visitors) -->
    <transition name="fade">
      <div v-if="showLoginModal" class="login-modal-overlay" @click.self="showLoginModal = false">
        <div class="login-modal-box" @click.stop>
          <button @click="showLoginModal = false" class="login-modal-close">✕</button>

          <div class="login-modal-brand">
            <span class="lm-brand">RealtyLink</span><span class="lm-brand-gold">PH</span>
            <p class="lm-tagline">Sign in to continue</p>
          </div>

          <transition name="fade">
            <div v-if="loginAlert.show" :class="['lm-alert', `lm-alert-${loginAlert.type}`]">
              {{ loginAlert.message }}
            </div>
          </transition>

          <form @submit.prevent="handleLogin" class="lm-form">
            <div class="lm-field">
              <label>Email Address</label>
              <input v-model="loginForm.email" type="email" placeholder="juan@example.com" class="lm-input" />
              <span v-if="loginErrors.email" class="lm-error">{{ loginErrors.email }}</span>
            </div>

            <div class="lm-field">
              <label>Password</label>
              <div class="lm-password-wrap">
                <input v-model="loginForm.password" :type="showLoginPassword ? 'text' : 'password'" placeholder="Enter your password" class="lm-input" />
                <button type="button" class="lm-eye" @click="showLoginPassword = !showLoginPassword">
                  {{ showLoginPassword ? '🙈' : '👁️' }}
                </button>
              </div>
              <span v-if="loginErrors.password" class="lm-error">{{ loginErrors.password }}</span>
            </div>

            <button type="submit" class="lm-btn-login" :disabled="loginLoading">
              <span v-if="!loginLoading">Sign In</span>
              <span v-else>Signing in...</span>
            </button>
          </form>

          <div class="lm-divider"><span>Don't have an account?</span></div>
          <button @click="goToRegister" class="lm-btn-register">Create Account</button>
        </div>
      </div>
    </transition>

    <!-- Success Message -->
    <transition name="fade">
      <div v-if="successMessage" class="success-message">
        ✓ {{ successMessage }}
      </div>
    </transition>

    <!-- Error Message -->
    <transition name="fade">
      <div v-if="errorMessage" class="error-message">
        ✗ {{ errorMessage }}
      </div>
    </transition>
  </div>
</template>

<script>
import ViewingCalendar from '../components/ViewingCalendar.vue';
import PanoramaViewer from '../components/PanoramaViewer.vue';
import { authAPI } from '../src/services/api';

export default {
  name: 'PropertyDetailsPage',

  components: {
    ViewingCalendar,
    PanoramaViewer,
  },

  data() {
    return {
      property: null,
      loading: true,
      currentPhotoIndex: 0,

      // ✅ ADDED: Dynamic API URL
      apiUrl: window.__API_URL__,

      // Modals
      showViewingModal: false,
      showTourModal: false,

      // Blocked & booked dates (for calendar)
      blockedDates: [],
      bookedDates: [],
      dateBlocked: false,

      // Forms
      viewingForm: {
        date: '',
        time: '',
        message: '',
      },

      // Login modal (for unauthenticated visitors)
      showLoginModal: false,
      loginLoading: false,
      showLoginPassword: false,
      loginForm: { email: '', password: '' },
      loginErrors: { email: '', password: '' },
      loginAlert: { show: false, type: 'success', message: '' },

      // Messages
      successMessage: '',
      errorMessage: '',

      // Map
      mapLoading: false,
      mapError: '',
      mapInstance: null,
      geoapifyKey: import.meta.env.VITE_GEOAPIFY_API_KEY || '',

      // Nearby landmarks (UI placeholder — wired to real API in backend phase)
      nearbyLandmarks: [
        { name: 'SM City Mall',         type: 'Shopping',    dist: '1.2 km', color: '#001F3F' },
        { name: 'Puregold Supermarket', type: 'Grocery',     dist: '0.4 km', color: '#2c7a3a' },
        { name: 'Barangay Health Center', type: 'Healthcare', dist: '0.6 km', color: '#c0392b' },
        { name: 'Elementary School',    type: 'Education',   dist: '0.9 km', color: '#e67e22' },
        { name: 'Jollibee',             type: 'Restaurant',  dist: '0.3 km', color: '#cc0000' },
        { name: 'LRT/MRT Station',      type: 'Transport',   dist: '1.8 km', color: '#2980b9' },
      ],
    };
  },

  computed: {
    currentPhoto() {
      if (this.property && this.property.photos && this.property.photos.length > 0) {
        return this.property.photos[this.currentPhotoIndex].photo_url;
      }
      return null;
    },
    today() {
      return new Date().toISOString().split('T')[0];
    },
  },

  methods: {
    async loadProperty() {
      try {
        this.loading = true;
        const propertyId = this.$route.params.id;
        const token = localStorage.getItem('auth_token');
        const apiUrl = localStorage.getItem('api_url') || window.__API_URL__;
        this.apiUrl = apiUrl;

        // ✅ FIXED: Use dynamic API URL
        const response = await fetch(
          `${apiUrl}/api/properties/${propertyId}`,
          {
            headers: {
              'Authorization': `Bearer ${token}`,
            },
          }
        );

        // ✅ FIXED: Check HTTP status before parsing JSON
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: Failed to load property`);
        }

        const data = await response.json();

        if (data.success) {
          this.property = data.data;
          console.log('✅ Property loaded:', this.property);

          // Increment view count
          this.incrementViewCount();

          // Initialize map
          this.$nextTick(() => this.initMap());
        } else {
          this.showError(data.message || 'Failed to load property');
          this.property = null;
        }
      } catch (error) {
        console.error('❌ Load error:', error);
        this.showError('Failed to load property: ' + error.message);
        this.property = null;
      } finally {
        this.loading = false;
      }
    },

    // ✅ FIXED: Use correct endpoint and dynamic API URL
    async incrementViewCount() {
      try {
        const propertyId = this.$route.params.id;
        const token = localStorage.getItem('auth_token');
        const apiUrl = localStorage.getItem('api_url') || window.__API_URL__;

        await fetch(
          `${apiUrl}/api/properties/${propertyId}/views`,  // ✅ CHANGED from /increment-views
          {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
            },
          }
        );
      } catch (error) {
        console.error('Error incrementing view count:', error);
        // Don't show error to user - it's not critical
      }
    },

    nextPhoto() {
      if (this.property && this.property.photos) {
        this.currentPhotoIndex = (this.currentPhotoIndex + 1) % this.property.photos.length;
      }
    },

    prevPhoto() {
      if (this.property && this.property.photos) {
        this.currentPhotoIndex =
          (this.currentPhotoIndex - 1 + this.property.photos.length) % this.property.photos.length;
      }
    },

    parseFeatures(features) {
      if (typeof features === 'string') {
        return features.split(',').map(f => f.trim());
      }
      return Array.isArray(features) ? features : [];
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-PH').format(price);
    },

    formatPropertyType(type) {
      const typeMap = {
        house: 'House',
        condo: 'Condo',
        apartment: 'Apartment',
        commercial: 'Commercial',
        lot: 'Lot',
        office: 'Office',
      };
      return typeMap[type] || type;
    },

    formatStatus(status) {
      const statusMap = {
        available: 'Available',
        sold: 'Sold',
        rented: 'Rented',
        unlisted: 'Unlisted',
      };
      return statusMap[status] || status;
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    },

    requireAuth() {
      const token = localStorage.getItem('auth_token');
      if (!token) {
        this.$router.push({ path: '/login', query: { redirect: this.$route.fullPath } });
        return false;
      }
      return true;
    },

    async handleLogin() {
      this.loginErrors = { email: '', password: '' };
      if (!this.loginForm.email) this.loginErrors.email = 'Email is required';
      if (!this.loginForm.password) this.loginErrors.password = 'Password is required';
      if (this.loginErrors.email || this.loginErrors.password) return;

      this.loginLoading = true;
      try {
        const res = await authAPI.login(this.loginForm);
        localStorage.setItem('auth_token', res.data.token);
        localStorage.setItem('user', JSON.stringify(res.data.user));
        if (window.reconnectEcho) window.reconnectEcho();

        this.loginAlert = { show: true, type: 'success', message: 'Login successful!' };
        setTimeout(() => {
          this.showLoginModal = false;
          this.loginAlert.show = false;
          // Reload property to get full auth context
          this.loadProperty();
        }, 800);
      } catch (e) {
        this.loginAlert = { show: true, type: 'error', message: 'Invalid credentials. Please try again.' };
        setTimeout(() => { this.loginAlert.show = false; }, 3000);
      } finally {
        this.loginLoading = false;
      }
    },

    goToRegister() {
      this.$router.push('/register');
    },

    async scheduleViewing() {
      if (!this.requireAuth()) return;
      this.showViewingModal = true;
      this.dateBlocked = false;
      this.viewingForm = { date: '', time: '', message: '' };

      const apiUrl = localStorage.getItem('api_url') || window.__API_URL__;
      const token  = localStorage.getItem('auth_token');
      const headers = { Authorization: `Bearer ${token}`, Accept: 'application/json' };

      // Load blocked dates and booked dates in parallel
      try {
        const [blockedRes, bookedRes] = await Promise.all([
          fetch(`${apiUrl}/api/properties/${this.property.id}/blocked-dates`, { headers }),
          fetch(`${apiUrl}/api/properties/${this.property.id}/booked-dates`, { headers }),
        ]);
        const blockedData = await blockedRes.json();
        const bookedData  = await bookedRes.json();

        if (blockedData.success) this.blockedDates = blockedData.blocked_dates;
        if (bookedData.success) this.bookedDates = bookedData.booked_dates;
      } catch (e) {
        // Non-critical — continue
      }
    },

    async submitViewing() {
      if (!this.viewingForm.date || !this.viewingForm.time) {
        this.showError('Please fill in date and time');
        return;
      }

      if (this.dateBlocked) {
        this.showError('This date is unavailable. Please choose another date.');
        return;
      }

      try {
        const token  = localStorage.getItem('auth_token');
        const apiUrl = localStorage.getItem('api_url') || window.__API_URL__;

        const response = await fetch(`${apiUrl}/api/buyer/viewings`, {
          method: 'POST',
          headers: {
            'Content-Type':  'application/json',
            'Authorization': `Bearer ${token}`,
            'Accept':        'application/json',
          },
          body: JSON.stringify({
            property_id:  this.property.id,
            viewing_date: this.viewingForm.date,
            viewing_time: this.viewingForm.time,
            buyer_notes:  this.viewingForm.message,
          }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
          this.showSuccess('Viewing request submitted! The agent will respond shortly.');
          this.showViewingModal = false;
          this.viewingForm = { date: '', time: '', message: '' };
        } else {
          this.showError(data.message || 'Failed to schedule viewing');
        }
      } catch (error) {
        console.error('Error:', error);
        this.showError('Failed to schedule viewing');
      }
    },

    saveProperty() {
      if (!this.requireAuth()) return;
      this.showSuccess('Property saved to your wishlist!');
    },

    callAgent() {
      if (!this.requireAuth()) return;
      if (this.property?.agent?.phone) {
        window.open(`tel:${this.property.agent.phone}`);
      }
    },

    messageAgent() {
      if (!this.requireAuth()) return;
      if (this.property?.agent?.user) {
        this.$router.push(`/conversations/${this.property.agent.user.id}?property_id=${this.property.id}`);
      }
    },

    goBack() {
      this.$router.back();
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

    // ========================
    // Map methods
    // ========================
    async loadLeaflet() {
      if (window.L) return;
      // Load Leaflet CSS
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);
      // Load Leaflet JS
      await new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
      });
    },

    async geocodeAddress() {
      const parts = [this.property.address, this.property.city, this.property.province, 'Philippines'].filter(Boolean);
      const query = parts.join(', ');
      const url = `https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(query)}&apiKey=${this.geoapifyKey}&limit=1`;
      const res = await fetch(url);
      const data = await res.json();
      if (data.features && data.features.length > 0) {
        const coords = data.features[0].geometry.coordinates;
        return { lat: coords[1], lng: coords[0] };
      }
      return null;
    },

    async initMap() {
      if (!this.property || !this.geoapifyKey) return;
      this.mapLoading = true;
      this.mapError = '';
      try {
        await this.loadLeaflet();
        // Get coordinates: use stored lat/lng or geocode from address
        let lat = parseFloat(this.property.latitude);
        let lng = parseFloat(this.property.longitude);
        if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
          const coords = await this.geocodeAddress();
          if (!coords) {
            this.mapError = 'Could not locate this property on the map.';
            this.mapLoading = false;
            return;
          }
          lat = coords.lat;
          lng = coords.lng;
        }
        this.mapLoading = false;
        // Wait for DOM to render
        await this.$nextTick();
        // Guard: component may have been unmounted during async operations
        if (!this.$refs.mapContainer) return;
        const L = window.L;
        if (this.mapInstance) {
          this.mapInstance.remove();
        }
        this.mapInstance = L.map(this.$refs.mapContainer, {
          scrollWheelZoom: false,
        }).setView([lat, lng], 16);
        // Geoapify tile layer
        L.tileLayer(
          `https://maps.geoapify.com/v1/tile/osm-bright-smooth/{z}/{x}/{y}.png?apiKey=${this.geoapifyKey}`,
          {
            attribution: '&copy; <a href="https://www.geoapify.com/">Geoapify</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 20,
          }
        ).addTo(this.mapInstance);
        // Custom gold marker
        const goldIcon = L.divIcon({
          className: 'custom-map-marker',
          html: '<div class="marker-pin"></div>',
          iconSize: [30, 42],
          iconAnchor: [15, 42],
          popupAnchor: [0, -42],
        });
        const marker = L.marker([lat, lng], { icon: goldIcon }).addTo(this.mapInstance);
        marker.bindPopup(`<strong>${this.property.title}</strong><br/>📍 ${this.property.address || ''}, ${this.property.city}`);
      } catch (e) {
        console.error('Map error:', e);
        this.mapError = 'Failed to load map.';
        this.mapLoading = false;
      }
    },
  },

  mounted() {
    this.loadProperty();
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.property-details-wrapper {
  --navy: #0B1C39; --navy2: #102445; --navy3: #1a3158;
  --gold: #D89B0F; --gold2: #E5B332; --gold3: #B07A08;
  --smoky-black: #100c08;
  --white-smoke: #F0F2F5;
  --palace-gold: #D89B0F;
  --palace-gold-dark: #B07A08;
  --light-gray: #e0e0e0;
  --border-gray: #E7E5E4;
  --font-display: 'Outfit','Inter',-apple-system,sans-serif;
  --font-body: 'Inter',-apple-system,sans-serif;
  --s50: #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s900: #1C1917;
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
  -webkit-font-smoothing: antialiased;
}

/* ============================================================================
   LOADING STATE
   ============================================================================ */

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  gap: 20px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid var(--light-gray);
  border-top-color: var(--palace-gold);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-container p {
  font-size: 16px;
  color: #999;
}

/* ============================================================================
   ERROR STATE
   ============================================================================ */

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  gap: 20px;
  padding: 20px;
}

.error-icon {
  font-size: 64px;
}

.error-container h2 {
  font-size: 28px;
  font-weight: 800;
  color: var(--smoky-black);
  font-family: var(--font-display);
}

.error-container p {
  font-size: 16px;
  color: #999;
  margin-bottom: 20px;
}

.btn-back-error {
  padding: 12px 24px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  border: none;
  border-radius: 50px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
}

.btn-back-error:hover {
  background: var(--palace-gold-dark);
  transform: translateY(-2px);
}

/* ============================================================================
   PROPERTY CONTAINER
   ============================================================================ */

.property-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.btn-back {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: white;
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  color: var(--smoky-black);
  font-weight: 700;
  cursor: pointer;
  margin-bottom: 24px;
  transition: all 0.3s;
}

.btn-back:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

/* ============================================================================
   GALLERY SECTION — MOSAIC HERO
   ============================================================================ */

.gallery-section {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 32px;
}

/* Mosaic grid: large left + 2 stacked right */
.mosaic-hero {
  display: grid;
  grid-template-columns: 1fr 0.48fr;
  grid-template-rows: 480px;
  gap: 8px;
  border-radius: 14px;
  overflow: hidden;
}

.mosaic-hero.mosaic-single {
  grid-template-columns: 1fr;
}

.mosaic-main {
  position: relative;
  overflow: hidden;
  cursor: pointer;
  background: #f0f0f0;
}

.mosaic-main:hover .mosaic-img {
  transform: scale(1.02);
}

.mosaic-side {
  display: grid;
  grid-template-rows: 1fr 1fr;
  gap: 8px;
}

.mosaic-thumb {
  position: relative;
  overflow: hidden;
  cursor: pointer;
  background: #f0f0f0;
}

.mosaic-thumb:hover .mosaic-img {
  transform: scale(1.04);
}

.mosaic-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.35s ease;
  display: block;
}

.mosaic-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: #f5f5f5;
  color: #bbb;
  font-size: 14px;
}

/* +N more overlay on 3rd thumbnail */
.mosaic-more-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 31, 63, 0.62);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  color: white;
}

.mosaic-more-overlay span {
  font-size: 22px;
  font-weight: 800;
  font-family: var(--font-display);
  line-height: 1;
}

.mosaic-more-overlay small {
  font-size: 11px;
  opacity: 0.8;
  letter-spacing: 0.5px;
}

/* Featured badge */
.featured-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 12px;
  display: flex;
  align-items: center;
  gap: 5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Thumbnail strip */
.thumbnail-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
  gap: 6px;
}

.thumbnail {
  position: relative;
  height: 72px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.25s;
}

.thumbnail:hover {
  border-color: var(--palace-gold);
  opacity: 0.9;
}

.thumbnail.active {
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.35);
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ============================================================================
   CONTENT GRID
   ============================================================================ */

.content-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 32px;
  align-items: start;
}

/* ============================================================================
   DETAILS COLUMN
   ============================================================================ */

.details-column {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

/* Price Section */
.price-section {
  padding: 24px;
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
}

.property-title {
  font-size: 32px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 12px;
  font-family: var(--font-display);
}

.property-location {
  font-size: 16px;
  color: #999;
  margin-bottom: 16px;
}

.price-type {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid var(--light-gray);
}

.price {
  font-size: 42px;
  font-weight: 800;
  color: var(--palace-gold);
  font-family: var(--font-display);
  line-height: 1;
  letter-spacing: -0.5px;
}

.listing-type-tag {
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 13px;
  text-transform: uppercase;
}
.listing-type-tag.sale { background: #dcfce7; color: #15803d; }
.listing-type-tag.rent { background: #ede9fe; color: #7c3aed; }

.property-type {
  background: var(--white-smoke);
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 13px;
  text-transform: uppercase;
  color: var(--smoky-black);
}

/* Specs Section */
.specs-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  padding: 24px;
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
}

.spec-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.spec-icon {
  font-size: 28px;
  min-width: 40px;
}

.spec-label {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  margin: 0;
}

.spec-value {
  font-size: 20px;
  font-weight: 800;
  color: var(--smoky-black);
  margin: 4px 0 0 0;
}

/* Status Section */
.status-section {
  padding: 16px 24px;
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
}

.status-badge {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 13px;
  text-transform: uppercase;
}

.status-badge.available {
  background: #e8f5e9;
  color: #2e7d32;
}

.status-badge.sold {
  background: #ffebee;
  color: #c62828;
}

.status-badge.rented {
  background: #e3f2fd;
  color: #1565c0;
}

.status-badge.unlisted {
  background: #f3e5f5;
  color: #6a1b9a;
}

/* Description Section */
.description-section {
  padding: 24px;
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
}

.description-section h3 {
  font-size: 18px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 16px;
  font-family: var(--font-display);
}

.description-section p {
  font-size: 15px;
  color: #666;
  line-height: 1.6;
}

/* Features Section */
.features-section {
  padding: 24px;
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
}

.features-section h3 {
  font-size: 18px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 16px;
  font-family: var(--font-display);
}

.features-list {
  list-style: none;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.features-list li {
  font-size: 14px;
  color: #666;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Location Info */
.location-info-section {
  padding: 24px;
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
}

.location-info-section h3 {
  font-size: 18px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 16px;
  font-family: var(--font-display);
}

/* Clean location label/value grid */
.loc-grid {
  display: flex;
  flex-direction: column;
  gap: 0;
  margin: 0;
  padding: 0;
}

.loc-row {
  display: grid;
  grid-template-columns: 110px 1fr;
  align-items: baseline;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
}

.loc-row:last-child {
  border-bottom: none;
}

.loc-label {
  font-size: 11px;
  color: #aaa;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.6px;
}

.loc-value {
  font-size: 15px;
  color: var(--smoky-black);
  font-weight: 600;
  margin: 0;
}

/* ============================================================================
   MAP SECTION
   ============================================================================ */

.map-section {
  padding: 24px;
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
}

.map-section h3 {
  font-size: 18px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 16px;
  font-family: var(--font-display);
}

.map-container {
  width: 100%;
  height: 380px;
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid var(--light-gray);
  z-index: 0;
}

.map-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  height: 200px;
  color: #999;
  font-size: 14px;
}

.spinner-sm {
  width: 22px;
  height: 22px;
  border: 3px solid var(--light-gray);
  border-top-color: var(--palace-gold);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.map-error {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 100px;
  color: #999;
  font-size: 14px;
  background: var(--white-smoke);
  border-radius: 10px;
}

/* Custom gold map marker */
:deep(.custom-map-marker) {
  background: none !important;
  border: none !important;
}

:deep(.marker-pin) {
  width: 30px;
  height: 30px;
  border-radius: 50% 50% 50% 0;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  position: absolute;
  transform: rotate(-45deg);
  left: 50%;
  top: 50%;
  margin: -15px 0 0 -15px;
  box-shadow: 0 3px 10px rgba(255, 215, 0, 0.5);
}

:deep(.marker-pin::after) {
  content: '';
  width: 14px;
  height: 14px;
  margin: 8px 0 0 8px;
  background: var(--smoky-black);
  position: absolute;
  border-radius: 50%;
}

:deep(.leaflet-popup-content-wrapper) {
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

:deep(.leaflet-popup-content) {
  margin: 12px 16px;
  font-family: var(--font-body);
  font-size: 13px;
  line-height: 1.5;
}

:deep(.leaflet-popup-content strong) {
  font-family: var(--font-display);
  font-size: 14px;
  color: var(--smoky-black);
}

/* ============================================================================
   NEARBY LANDMARKS
   ============================================================================ */

.landmarks-section {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #f0f0f0;
}

.landmarks-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--smoky-black);
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 7px;
  font-family: var(--font-display);
  color: #555;
}

.landmarks-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0;
  margin: 0;
  padding: 0;
}

.landmark-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 9px 0;
  border-bottom: 1px solid #f5f5f5;
}

.landmark-item:last-child {
  border-bottom: none;
}

.landmark-pin-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  flex-shrink: 0;
}

.landmark-info {
  flex: 1;
  min-width: 0;
}

.landmark-name {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--smoky-black);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.landmark-type {
  display: block;
  font-size: 11px;
  color: #aaa;
  margin-top: 1px;
}

.landmark-dist {
  font-size: 12px;
  color: #888;
  font-weight: 600;
  white-space: nowrap;
  flex-shrink: 0;
}

/* ============================================================================
   SIDEBAR COLUMN
   ============================================================================ */

.sidebar-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
  position: sticky;
  top: 20px;
}

/* Agent Card */
.agent-card {
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.card-title {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  margin-bottom: 16px;
  letter-spacing: 1px;
}

.agent-photo-container {
  text-align: center;
  margin-bottom: 16px;
}

.agent-photo {
  width: 100px;
  height: 100px;
  margin: 0 auto 12px;
  border-radius: 50%;
  overflow: hidden;
  background: #f0f0f0;
}

.agent-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.agent-photo-placeholder {
  width: 100px;
  height: 100px;
  margin: 0 auto 12px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  font-weight: 800;
}

.agent-info {
  text-align: center;
  margin-bottom: 20px;
}

.agent-name {
  font-size: 16px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 4px;
  font-family: var(--font-display);
}

.agent-role {
  font-size: 12px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  margin-bottom: 12px;
}

.agent-email, .agent-phone {
  font-size: 13px;
  color: #666;
  margin: 6px 0;
  word-break: break-all;
}

.no-agent-text {
  font-size: 14px;
  color: #999;
  margin: 0;
  padding: 12px 0;
}

/* ============================================================================
   BUTTON HIERARCHY
   ============================================================================ */

/* PRIMARY: Schedule Viewing — solid gold */
.btn-schedule {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 14px 16px;
  background: linear-gradient(135deg, var(--gold), var(--gold2));
  color: var(--navy);
  border: none;
  border-radius: 50px;
  font-weight: 800;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.25s ease;
  font-family: var(--font-display);
  box-shadow: 0 4px 14px rgba(216, 155, 15, 0.35);
  letter-spacing: 0.2px;
}

.btn-schedule:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(216, 155, 15, 0.45);
}

.btn-schedule:active {
  transform: translateY(0);
}

/* DISTINCT: Virtual 360° Tour — dark navy filled */
.btn-tour-navy {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 11px 16px;
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  color: white;
  border: none;
  border-radius: 50px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.25s ease;
  font-family: var(--font-display);
  box-shadow: 0 2px 8px rgba(11, 28, 57, 0.20);
}

.btn-tour-navy:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(11, 28, 57, 0.30);
}

/* Property actions container */
.property-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 4px;
}

/* SECONDARY: Message + Call — navy outline */
.agent-actions {
  display: flex;
  gap: 8px;
  margin-bottom: 4px;
}

.btn-outline-navy {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  flex: 1;
  padding: 10px 12px;
  background: transparent;
  color: var(--navy);
  border: 1.5px solid var(--navy);
  border-radius: 50px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.25s ease;
  font-family: var(--font-display);
}

.btn-outline-navy:hover {
  background: var(--navy);
  color: white;
  box-shadow: 0 4px 12px rgba(11, 28, 57, 0.20);
}

/* TERTIARY: Save Property — text link style */
.btn-save-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 100%;
  padding: 8px;
  background: none;
  border: none;
  color: #888;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.2s;
  font-family: var(--font-body);
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-color: transparent;
}

.btn-save-link:hover {
  color: var(--gold);
  text-decoration-color: var(--gold);
}

.divider {
  border: none;
  border-top: 1px solid var(--light-gray);
  margin: 0;
}

/* Modal action buttons (used in viewing modal footer) */
.btn-primary {
  padding: 12px 16px;
  background: linear-gradient(135deg, var(--gold), var(--gold2));
  color: var(--navy);
  border: none;
  border-radius: 50px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s;
  font-size: 13px;
  width: 100%;
  box-shadow: 0 3px 10px rgba(216,155,15,0.25);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(216,155,15,0.35);
}

.btn-secondary {
  padding: 12px 16px;
  background: white;
  color: var(--navy);
  border: 1.5px solid var(--s200);
  border-radius: 50px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s;
  font-size: 13px;
  width: 100%;
}

.btn-secondary:hover {
  border-color: var(--gold);
  color: var(--gold3);
}

/* Info Box */
.info-box {
  background: white;
  border-radius: 12px;
  border: 1px solid var(--light-gray);
  padding: 20px;
}

.info-box h4 {
  font-size: 14px;
  font-weight: 800;
  color: var(--smoky-black);
  margin-bottom: 16px;
  font-family: var(--font-display);
}

.info-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid var(--light-gray);
}

.info-row:last-child {
  border-bottom: none;
}

.info-row span {
  font-size: 13px;
  color: #999;
}

.info-row strong {
  font-size: 14px;
  color: var(--smoky-black);
  font-weight: 700;
}

/* ============================================================================
   MODAL STYLES
   ============================================================================ */

.blocked-dates-notice {
  background: #fef9c3;
  border: 1px solid #fde68a;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 0.82rem;
  color: #854d0e;
  margin-bottom: 14px;
  line-height: 1.5;
}
.form-error { font-size: 0.78rem; color: #dc2626; margin-top: 2px; }

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(11, 28, 57, 0.50);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 20px;
}

.modal-box {
  background: white;
  border-radius: 18px;
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: modalIn 0.3s ease-out;
}
@keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.modal-header {
  padding: 24px;
  border-bottom: 1px solid var(--light-gray);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 18px;
  font-weight: 800;
  margin: 0;
  color: var(--smoky-black);
  font-family: var(--font-display);
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
  padding: 0;
  transition: color 0.3s;
}

.btn-close:hover {
  color: var(--smoky-black);
}

.modal-body {
  padding: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 16px;
}

.form-group label {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  text-transform: uppercase;
}

.form-input, .form-textarea {
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-size: 14px;
  font-family: var(--font-body);
  transition: all 0.3s;
}

.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid var(--light-gray);
}

.modal-footer .btn-secondary,
.modal-footer .btn-primary {
  flex: 1;
}

/* ============================================================================
   MESSAGE MODAL
   ============================================================================ */

.message-modal-overlay {
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
  padding: 20px;
}

.message-modal-content {
  position: relative;
  width: 100%;
  max-width: 600px;
}

.btn-modal-close {
  position: absolute;
  top: -40px;
  right: 0;
  background: white;
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 24px;
  z-index: 10;
  transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-modal-close:hover {
  background: #f0f0f0;
  transform: scale(1.1);
}

/* ============================================================================
   NOTIFICATIONS
   ============================================================================ */

.success-message {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #4caf50;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3000;
  animation: slideIn 0.3s ease;
  font-weight: 700;
}

.error-message {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #f44336;
  color: white;
  padding: 16px 24px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3000;
  animation: slideIn 0.3s ease;
  font-weight: 700;
}

@keyframes slideIn {
  from {
    transform: translateX(400px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ============================================================================
   RESPONSIVE
   ============================================================================ */

/* ═══════════════════════════════════════════════
   RESPONSIVE - TABLET (max 1024px)
   ═══════════════════════════════════════════════ */
@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .sidebar-column {
    position: static;
  }

  .mosaic-hero {
    grid-template-rows: 340px;
  }

  /* Agent card: horizontal layout when sidebar drops below */
  .agent-card {
    display: grid;
    grid-template-columns: auto 1fr;
    grid-template-rows: auto auto auto;
    gap: 0 20px;
    align-items: start;
  }

  .agent-card .card-title {
    grid-column: 1 / -1;
  }

  .agent-card .agent-photo-container {
    grid-row: 2;
    grid-column: 1;
    margin-bottom: 0;
  }

  .agent-card .agent-info {
    grid-row: 2;
    grid-column: 2;
    text-align: left;
    margin-bottom: 0;
  }

  .agent-card .agent-actions {
    grid-column: 1 / -1;
  }

  .agent-card .divider {
    grid-column: 1 / -1;
  }

  .agent-card .property-actions {
    grid-column: 1 / -1;
    flex-direction: row;
    flex-wrap: wrap;
  }

  .agent-card .property-actions .btn-schedule,
  .agent-card .property-actions .btn-tour-navy {
    flex: 1;
    min-width: 140px;
  }

  .agent-card .agent-actions .btn-outline-navy {
    flex: 1;
  }

  .specs-section {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* ═══════════════════════════════════════════════
   RESPONSIVE - MOBILE (max 768px)
   ═══════════════════════════════════════════════ */
@media (max-width: 768px) {
  .property-container {
    padding: 12px;
  }

  .btn-back {
    padding: 8px 14px;
    font-size: 13px;
    margin-bottom: 16px;
  }

  .property-title {
    font-size: 22px;
  }

  .property-location {
    font-size: 13px;
  }

  .price {
    font-size: 28px;
  }

  .specs-section {
    grid-template-columns: repeat(2, 1fr);
    padding: 16px;
    gap: 12px;
  }

  .mosaic-hero {
    grid-template-columns: 1fr;
    grid-template-rows: 260px;
    border-radius: 10px;
  }

  .mosaic-side {
    display: none;
  }

  .thumbnail-gallery {
    grid-template-columns: repeat(auto-fill, minmax(58px, 1fr));
    gap: 5px;
  }

  /* Agent card: back to vertical on mobile */
  .agent-card {
    display: block;
    padding: 20px;
  }

  .agent-card .agent-actions {
    flex-direction: column;
  }

  .agent-card .property-actions {
    flex-direction: column;
  }

  .agent-photo, .agent-photo-placeholder {
    width: 80px;
    height: 80px;
  }

  .agent-photo-placeholder {
    font-size: 32px;
  }

  .agent-card .agent-info {
    text-align: center;
  }

  /* Description, features, location sections */
  .description-section,
  .features-section,
  .location-info-section,
  .map-section {
    padding: 16px;
  }

  .description-section h3,
  .features-section h3,
  .location-info-section h3,
  .map-section h3 {
    font-size: 16px;
    margin-bottom: 12px;
  }

  .map-container {
    height: 260px;
  }

  /* Viewing modal - fullscreen */
  .modal-overlay {
    padding: 10px;
  }

  .modal-box {
    max-width: 100%;
    border-radius: 10px;
  }

  .modal-header {
    padding: 16px;
  }

  .modal-header h3 {
    font-size: 16px;
  }

  .modal-body {
    padding: 16px;
  }

  .modal-footer {
    padding: 16px;
  }

  /* Info box */
  .info-box {
    padding: 16px;
  }

  /* Message modal */
  .message-modal-overlay {
    padding: 0;
  }

  .message-modal-content {
    max-width: 100%;
    height: calc(100vh - 40px);
  }

  .btn-modal-close {
    top: 10px;
    right: 10px;
  }

  /* Login modal on mobile */
  .login-modal-overlay {
    padding: 12px;
  }

  .login-modal-box {
    padding: 28px 20px;
    border-radius: 16px;
  }
}

/* ═══════════════════════════════════════════════
   RESPONSIVE - SMALL MOBILE (max 480px)
   ═══════════════════════════════════════════════ */
@media (max-width: 480px) {
  .property-container {
    padding: 8px;
  }

  .property-title {
    font-size: 18px;
  }

  .price {
    font-size: 22px;
  }

  .price-type {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }

  .price-section {
    padding: 16px;
  }

  .mosaic-hero {
    grid-template-rows: 200px;
  }

  .specs-section {
    grid-template-columns: 1fr;
    padding: 14px;
  }

  .spec-item {
    padding: 10px;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .featured-badge {
    font-size: 11px;
    padding: 4px 10px;
  }

  .photo-counter {
    font-size: 11px;
    padding: 4px 10px;
  }

  .gallery-section {
    gap: 8px;
    margin-bottom: 16px;
  }

  .content-grid {
    gap: 16px;
  }

  /* Agent card compact */
  .agent-card {
    padding: 16px;
  }

  .agent-photo, .agent-photo-placeholder {
    width: 64px;
    height: 64px;
  }

  .agent-photo-placeholder {
    font-size: 26px;
  }

  .agent-name {
    font-size: 15px;
  }

  .btn-action, .btn-primary, .btn-secondary, .btn-tour {
    padding: 11px 14px;
    font-size: 13px;
  }

  /* Map reduced */
  .map-container {
    height: 200px;
  }

  /* Viewing modal fullscreen */
  .modal-overlay {
    padding: 0;
  }

  .modal-box {
    border-radius: 0;
    max-height: 100vh;
    height: 100%;
  }

  .modal-header {
    padding: 14px 16px;
  }

  .modal-body {
    padding: 14px 16px;
  }

  /* Login modal fullscreen-ish */
  .login-modal-overlay {
    padding: 0;
    align-items: flex-end;
  }

  .login-modal-box {
    border-radius: 16px 16px 0 0;
    padding: 28px 20px;
    max-height: 90vh;
    overflow-y: auto;
  }

  /* Descriptions */
  .description-section p,
  .features-section li {
    font-size: 13px;
  }

  .features-list li {
    padding: 6px 0;
  }

  /* Quick info */
  .info-box h4 {
    font-size: 13px;
  }

  .info-row span {
    font-size: 12px;
  }

  .info-row strong {
    font-size: 13px;
  }

  .btn-back {
    padding: 8px 12px;
    font-size: 12px;
    margin-bottom: 12px;
  }
}

/* 360° Tour Button */
.btn-tour {
  padding: 12px 16px;
  background: linear-gradient(135deg, #1a1a2e, #16213e);
  color: white;
  border: 2px solid var(--palace-gold, #FFD700);
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
  width: 100%;
  font-family: 'Inter', sans-serif;
}

.btn-tour:hover {
  background: linear-gradient(135deg, #16213e, #0f3460);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

/* 360° Tour Modal -- Fullscreen */
.tour-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.92);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3000;
  padding: 16px;
}

.tour-modal-box {
  width: 95vw;
  height: 90vh;
  background: #100c08;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 10px 60px rgba(0, 0, 0, 0.5);
}

.tour-modal-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 24px;
  background: #100c08;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  flex-shrink: 0;
}

.tour-modal-header h3 {
  font-size: 18px;
  font-weight: 800;
  color: #FFD700;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.tour-property-name {
  flex: 1;
  font-size: 14px;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 500;
  font-family: 'Inter', sans-serif;
}

.btn-close-tour {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.btn-close-tour:hover {
  background: rgba(255, 255, 255, 0.2);
  color: #FFD700;
}

.tour-modal-body {
  flex: 1;
  position: relative;
  min-height: 0;
}

@media (max-width: 768px) {
  .tour-modal-box {
    width: 100vw;
    height: 100vh;
    border-radius: 0;
  }

  .tour-modal-overlay {
    padding: 0;
  }

  .tour-modal-header h3 {
    font-size: 14px;
  }

  .tour-property-name {
    display: none;
  }
}

/* ─── LOGIN MODAL ─── */
.login-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3100;
  backdrop-filter: blur(4px);
  padding: 20px;
}

.login-modal-box {
  background: #fff;
  border-radius: 20px;
  width: 100%;
  max-width: 420px;
  padding: 36px 32px;
  position: relative;
  box-shadow: 0 24px 64px rgba(0, 0, 0, 0.2);
}

.login-modal-close {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: #f3f3f3;
  font-size: 15px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #888;
  transition: all 0.2s;
}

.login-modal-close:hover {
  background: #d1d1d1;
  color: #1A1A1A;
}

.login-modal-brand {
  text-align: center;
  margin-bottom: 24px;
}

.lm-brand {
  font-size: 22px;
  font-weight: 800;
  color: #1A1A1A;
}

.lm-brand-gold {
  font-size: 22px;
  font-weight: 800;
  color: #FFD700;
}

.lm-tagline {
  font-size: 14px;
  color: #888;
  margin-top: 4px;
}

.lm-alert {
  padding: 10px 14px;
  border-radius: 10px;
  margin-bottom: 16px;
  font-size: 13px;
  font-weight: 500;
  text-align: center;
}

.lm-alert-success {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.lm-alert-error {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.lm-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.lm-field label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #1A1A1A;
  margin-bottom: 6px;
}

.lm-input {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #eee;
  border-radius: 10px;
  font-size: 14px;
  color: #1A1A1A;
  background: #f9f9f9;
  outline: none;
  transition: all 0.2s;
  font-family: inherit;
  box-sizing: border-box;
}

.lm-input:focus {
  border-color: #FFD700;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.lm-password-wrap {
  position: relative;
}

.lm-password-wrap .lm-input {
  padding-right: 42px;
}

.lm-eye {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 16px;
  padding: 4px;
}

.lm-error {
  display: block;
  font-size: 12px;
  color: #dc2626;
  margin-top: 4px;
}

.lm-btn-login {
  width: 100%;
  padding: 12px;
  background: #FFD700;
  color: #1A1A1A;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
  margin-top: 4px;
}

.lm-btn-login:hover {
  background: #DAB600;
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.lm-btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.lm-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0;
  color: #888;
  font-size: 13px;
}

.lm-divider::before,
.lm-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #eee;
}

.lm-btn-register {
  width: 100%;
  padding: 12px;
  background: transparent;
  color: #1A1A1A;
  border: 1.5px solid #eee;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}

.lm-btn-register:hover {
  background: #f3f3f3;
  border-color: #d1d1d1;
}
</style>