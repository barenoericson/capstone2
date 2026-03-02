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

      <!-- Property Gallery Section -->
      <div class="gallery-section">
        <!-- Main Photo -->
        <div class="main-photo-container">
          <img 
            v-if="currentPhoto" 
            :src="currentPhoto" 
            :alt="property.title"
            class="main-photo"
          />
          <div v-else class="photo-placeholder">📷</div>

          <!-- Photo Navigation -->
          <button 
            v-if="property.photos && property.photos.length > 1"
            @click="prevPhoto" 
            class="btn-nav prev"
          >
            ‹
          </button>
          <button 
            v-if="property.photos && property.photos.length > 1"
            @click="nextPhoto" 
            class="btn-nav next"
          >
            ›
          </button>

          <!-- Photo Counter -->
          <div v-if="property.photos && property.photos.length > 1" class="photo-counter">
            {{ currentPhotoIndex + 1 }} / {{ property.photos.length }}
          </div>

          <!-- Featured Badge -->
          <div v-if="property.featured" class="featured-badge">⭐ Featured</div>
        </div>

        <!-- Thumbnail Gallery -->
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
              <span class="price">₱{{ formatPrice(property.price) }}</span>
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
            <div class="spec-item">
              <span class="spec-icon">📐</span>
              <div>
                <p class="spec-label">Area</p>
                <p class="spec-value">{{ property.area || 'N/A' }} sqm</p>
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
            <div class="info-grid">
              <div class="info-item">
                <label>City</label>
                <p>{{ property.city }}</p>
              </div>
              <div class="info-item">
                <label>Province</label>
                <p>{{ property.province }}</p>
              </div>
              <div class="info-item">
                <label>Country</label>
                <p>{{ property.country }}</p>
              </div>
              <div class="info-item">
                <label>Postal Code</label>
                <p>{{ property.postal_code || 'N/A' }}</p>
              </div>
            </div>
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

            <!-- Agent Actions -->
            <div class="agent-actions">
              <button @click="callAgent" class="btn-action btn-call">
                📞 Call Agent
              </button>
              <router-link
                v-if="property.agent && property.agent.user"
                :to="`/conversations/${property.agent.user.id}?property_id=${property.id}`"
                class="btn-action btn-message"
              >
                💬 Message
              </router-link>
            </div>

            <hr class="divider" />

            <!-- Property Actions -->
            <div class="property-actions">
              <button @click="scheduleViewing" class="btn-primary">
                📅 Schedule Viewing
              </button>
              <button @click="saveProperty" class="btn-secondary">
                ❤️ Save Property
              </button>
              <button
                v-if="property.panoramas && property.panoramas.length > 0"
                @click="showTourModal = true"
                class="btn-tour"
              >
                🔄 Virtual 360° Tour
              </button>
            </div>
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

      // Messages
      successMessage: '',
      errorMessage: '',

      // Map
      mapLoading: false,
      mapError: '',
      mapInstance: null,
      geoapifyKey: import.meta.env.VITE_GEOAPIFY_API_KEY || '',
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

    async scheduleViewing() {
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

    callAgent() {
      this.showSuccess('Call feature coming soon!');
    },

    saveProperty() {
      this.showSuccess('Property saved to your wishlist!');
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
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/');
      return;
    }
    this.loadProperty();
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

:root {
  --smoky-black: #100c08;
  --white-smoke: #f5f5f5;
  --palace-gold: #FFD700;
  --palace-gold-dark: #DAB600;
  --light-gray: #e0e0e0;
  --border-gray: #ddd;
  --font-display: 'Poppins', sans-serif;
  --font-body: 'Inter', sans-serif;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.property-details-wrapper {
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
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
  border-radius: 8px;
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
   GALLERY SECTION
   ============================================================================ */

.gallery-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 32px;
}

.main-photo-container {
  position: relative;
  width: 100%;
  height: 500px;
  background: #f0f0f0;
  border-radius: 12px;
  overflow: hidden;
}

.main-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.photo-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 100px;
  color: #ddd;
}

.btn-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 50px;
  height: 50px;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 28px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.btn-nav:hover {
  background: rgba(0, 0, 0, 0.8);
}

.btn-nav.prev {
  left: 20px;
}

.btn-nav.next {
  right: 20px;
}

.photo-counter {
  position: absolute;
  bottom: 20px;
  right: 20px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 700;
}

.featured-badge {
  position: absolute;
  top: 20px;
  left: 20px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 14px;
}

/* Thumbnail Gallery */
.thumbnail-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 8px;
}

.thumbnail {
  position: relative;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.3s;
}

.thumbnail:hover {
  border-color: var(--palace-gold);
}

.thumbnail.active {
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.3);
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
  font-size: 36px;
  font-weight: 800;
  color: var(--palace-gold);
  font-family: var(--font-display);
}

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

.info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.info-item {
  padding: 12px;
  background: var(--white-smoke);
  border-radius: 8px;
}

.info-item label {
  font-size: 11px;
  color: #999;
  text-transform: uppercase;
  font-weight: 700;
  display: block;
  margin-bottom: 4px;
}

.info-item p {
  font-size: 15px;
  color: var(--smoky-black);
  font-weight: 700;
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

/* Agent Actions */
.agent-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 20px;
}

.btn-action {
  padding: 12px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
  width: 100%;
}

.btn-call {
  background: var(--white-smoke);
  color: var(--smoky-black);
  border: 1px solid var(--light-gray);
}

.btn-call:hover {
  background: var(--palace-gold);
  border-color: var(--palace-gold);
}

.btn-message {
  background: var(--palace-gold);
  color: var(--smoky-black);
}

.btn-message:hover {
  background: var(--palace-gold-dark);
}

.divider {
  border: none;
  border-top: 1px solid var(--light-gray);
  margin: 0;
}

/* Property Actions */
.property-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-primary {
  padding: 12px 16px;
  background: var(--palace-gold);
  color: var(--smoky-black);
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
  width: 100%;
}

.btn-primary:hover {
  background: var(--palace-gold-dark);
  transform: translateY(-2px);
}

.btn-secondary {
  padding: 12px 16px;
  background: white;
  color: var(--smoky-black);
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 13px;
  width: 100%;
}

.btn-secondary:hover {
  border-color: var(--palace-gold);
  background: var(--white-smoke);
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
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 20px;
}

.modal-box {
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

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

@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .sidebar-column {
    position: static;
  }

  .main-photo-container {
    height: 400px;
  }
}

@media (max-width: 768px) {
  .property-container {
    padding: 12px;
  }

  .property-title {
    font-size: 24px;
  }

  .price {
    font-size: 28px;
  }

  .specs-section {
    grid-template-columns: 1fr;
  }

  .main-photo-container {
    height: 300px;
  }

  .btn-nav {
    width: 40px;
    height: 40px;
    font-size: 20px;
  }

  .thumbnail-gallery {
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
  }

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
}

@media (max-width: 480px) {
  .property-title {
    font-size: 20px;
  }

  .price {
    font-size: 24px;
  }

  .price-type {
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }

  .main-photo-container {
    height: 250px;
  }

  .btn-nav {
    width: 36px;
    height: 36px;
    font-size: 18px;
  }

  .specs-section {
    grid-template-columns: 1fr;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .featured-badge {
    font-size: 12px;
    padding: 6px 12px;
  }

  .photo-counter {
    font-size: 12px;
    padding: 6px 12px;
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
</style>