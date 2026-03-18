<template>
  <div class="form-wrapper">
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link to="/agent/dashboard" class="sidebar-logo">
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
        </router-link>
        <router-link to="/agent/calendar" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
          </span>
          <span>Calendar</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Messages</span>
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
            <span class="nav-user-role">Agent</span>
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

    <!-- Main Content -->
    <main class="form-main">
      <div class="form-header">
        <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
        <router-link to="/agent/properties" class="btn-back">← Back to Properties</router-link>
        <h1 class="form-title">{{ isEditMode ? 'Edit Property' : 'Create New Property' }}</h1>
        <p class="form-subtitle">{{ isEditMode ? 'Update your property details and photos' : 'List a new property for sale or rent' }}</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <p>Loading property...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="loadError" class="error-banner">
        <p>{{ loadError }}</p>
        <button @click="loadProperty" class="btn-retry">Retry</button>
        <router-link to="/agent/properties" class="btn-back">← Back to Properties</router-link>
      </div>

      <!-- Form -->
      <form v-else @submit.prevent="submitForm" class="property-form">
        <!-- Basic Information Section -->
        <div class="form-section">
          <h2 class="section-title">Basic Information</h2>

          <div class="form-group">
            <label for="title">Property Title *</label>
            <input
              id="title"
              v-model="formData.title"
              type="text"
              placeholder="e.g., Cozy 2BR House in Cebu City"
              class="form-input"
              required
            />
            <span v-if="errors.title" class="error-text">{{ errors.title }}</span>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea
              id="description"
              v-model="formData.description"
              placeholder="Describe your property..."
              class="form-textarea"
              rows="4"
            ></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="property_type">Property Type *</label>
              <select id="property_type" v-model="formData.property_type" class="form-input" required>
                <option value="">Select type</option>
                <option value="house">House</option>
                <option value="condo">Condo</option>
                <option value="apartment">Apartment</option>
                <option value="commercial">Commercial</option>
              </select>
              <span v-if="errors.property_type" class="error-text">{{ errors.property_type }}</span>
            </div>

            <div class="form-group">
              <label for="listing_type">Listing Type *</label>
              <select id="listing_type" v-model="formData.listing_type" class="form-input" required>
                <option value="sale">For Sale</option>
                <option value="rent">For Rent</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" v-model="formData.status" class="form-input">
                <option value="available">Available</option>
                <option value="sold">Sold</option>
                <option value="rented">Rented</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Pricing Section -->
        <div class="form-section">
          <h2 class="section-title">Pricing</h2>

          <div class="form-group">
            <label for="price">Price *</label>
            <input
              id="price"
              v-model.number="formData.price"
              type="number"
              placeholder="0"
              class="form-input"
              min="0"
              required
            />
            <span v-if="errors.price" class="error-text">{{ errors.price }}</span>
          </div>
        </div>

        <!-- Location Section -->
        <div class="form-section">
          <h2 class="section-title">Location</h2>

          <div class="form-group">
            <label for="address">Address *</label>
            <input
              id="address"
              v-model="formData.address"
              type="text"
              placeholder="Street address"
              class="form-input"
              required
            />
            <span v-if="errors.address" class="error-text">{{ errors.address }}</span>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="city">City *</label>
              <input
                id="city"
                v-model="formData.city"
                type="text"
                placeholder="e.g., Cebu City"
                class="form-input"
                required
              />
              <span v-if="errors.city" class="error-text">{{ errors.city }}</span>
            </div>

            <div class="form-group">
              <label for="province">Province *</label>
              <input
                id="province"
                v-model="formData.province"
                type="text"
                placeholder="e.g., Cebu"
                class="form-input"
                required
              />
              <span v-if="errors.province" class="error-text">{{ errors.province }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="postal_code">Postal Code</label>
              <input
                id="postal_code"
                v-model="formData.postal_code"
                type="text"
                placeholder="6000"
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="country">Country</label>
              <input
                id="country"
                v-model="formData.country"
                type="text"
                placeholder="Philippines"
                class="form-input"
              />
            </div>
          </div>
        </div>

        <!-- Features Section -->
        <div class="form-section">
          <h2 class="section-title">Features</h2>

          <div class="form-row">
            <div class="form-group">
              <label for="bedrooms">Bedrooms *</label>
              <input
                id="bedrooms"
                v-model.number="formData.bedrooms"
                type="number"
                placeholder="0"
                class="form-input"
                min="0"
                required
              />
              <span v-if="errors.bedrooms" class="error-text">{{ errors.bedrooms }}</span>
            </div>

            <div class="form-group">
              <label for="bathrooms">Bathrooms *</label>
              <input
                id="bathrooms"
                v-model.number="formData.bathrooms"
                type="number"
                placeholder="0"
                class="form-input"
                min="0"
                required
              />
              <span v-if="errors.bathrooms" class="error-text">{{ errors.bathrooms }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="floor_area">Floor Area (sqm)</label>
              <input
                id="floor_area"
                v-model.number="formData.floor_area"
                type="number"
                placeholder="0"
                class="form-input"
                min="0"
              />
            </div>

            <div class="form-group">
              <label for="lot_area">Lot Area (sqm)</label>
              <input
                id="lot_area"
                v-model.number="formData.lot_area"
                type="number"
                placeholder="0"
                class="form-input"
                min="0"
              />
            </div>
          </div>
        </div>

        <!-- ✅ PHOTOS SECTION -->
        <div class="form-section">
          <h2 class="section-title">Photos</h2>

          <!-- Existing Photos (Edit Mode) -->
          <div v-if="isEditMode && property && property.photos && property.photos.length > 0" class="existing-photos">
            <h3 class="subsection-title">Current Photos</h3>
            <p class="photos-info">Click on a photo to delete it. Click the ⭐ icon to set as cover.</p>
            
            <div class="photos-grid">
              <div 
                v-for="photo in property.photos" 
                :key="photo.id" 
                class="photo-item"
              >
                <div class="photo-container">
                  <img :src="photo.photo_url" :alt="formData.title" class="photo-image" />
                  
                  <!-- Cover Badge -->
                  <div v-if="photo.is_cover" class="cover-badge">⭐ Cover</div>
                  
                  <!-- Set as Cover Button -->
                  <button
                    v-if="!photo.is_cover"
                    type="button"
                    @click="setCoverPhoto(photo.id)"
                    class="btn-set-cover"
                    title="Set as cover"
                  >
                    ⭐ Set Cover
                  </button>

                  <!-- Delete Button -->
                  <button
                    type="button"
                    @click="deletePhoto(photo.id)"
                    class="btn-delete-photo"
                    title="Delete photo"
                  >
                    🗑️ Delete
                  </button>
                </div>
                <p class="photo-filename">{{ photo.filename }}</p>
              </div>
            </div>
          </div>

          <!-- Upload New Photos -->
          <div class="upload-section">
            <h3 class="subsection-title">{{ isEditMode ? 'Add More Photos' : 'Upload Photos' }}</h3>
            
            <div class="upload-area" @click="$refs.photoInput.click()">
              <input
                ref="photoInput"
                type="file"
                multiple
                accept="image/*"
                class="photo-input"
                @change="handlePhotoSelect"
              />
              <p class="upload-icon">📤</p>
              <p class="upload-text">Click to upload or drag photos here</p>
              <p class="upload-hint">PNG, JPG, GIF up to 10MB</p>
            </div>

            <!-- Selected Photos Preview -->
            <div v-if="selectedPhotos.length > 0" class="selected-photos">
              <h4>New Photos to Upload ({{ selectedPhotos.length }})</h4>
              <div class="preview-grid">
                <div v-for="(photo, index) in selectedPhotos" :key="index" class="preview-item">
                  <img :src="photo.preview" :alt="photo.file.name" class="preview-image" />
                  <button
                    type="button"
                    @click="removeSelectedPhoto(index)"
                    class="btn-remove-preview"
                  >
                    ✕
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 360° PANORAMAS SECTION -->
        <div class="form-section">
          <h2 class="section-title">360° Virtual Tour</h2>
          <p class="section-description">
            Upload equirectangular panoramic images for an immersive 360° virtual tour.
            Each image represents a room that buyers can explore interactively.
          </p>

          <!-- Existing Panoramas (Edit Mode) -->
          <div v-if="isEditMode && property && property.panoramas && property.panoramas.length > 0" class="existing-panoramas">
            <h3 class="subsection-title">Current 360° Images</h3>
            <div class="panorama-grid">
              <div
                v-for="panorama in property.panoramas"
                :key="panorama.id"
                class="panorama-item"
              >
                <div class="panorama-preview-container">
                  <img :src="panorama.image_url" :alt="panorama.room_name" class="panorama-preview-img" />
                  <div class="panorama-room-badge">{{ panorama.room_name }}</div>
                  <button
                    type="button"
                    @click="deletePanorama(panorama.id)"
                    class="btn-delete-panorama"
                  >
                    🗑️ Delete
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload New Panoramas -->
          <div class="upload-section">
            <h3 class="subsection-title">{{ isEditMode ? 'Add More 360° Images' : 'Upload 360° Images' }}</h3>

            <div class="upload-area panorama-upload" @click="$refs.panoramaInput.click()">
              <input
                ref="panoramaInput"
                type="file"
                multiple
                accept="image/*"
                class="photo-input"
                @change="handlePanoramaSelect"
              />
              <p class="upload-icon">🔄</p>
              <p class="upload-text">Click to upload 360° panoramic images</p>
              <p class="upload-hint">Equirectangular JPG/PNG, up to 20MB each</p>
            </div>

            <!-- Selected Panoramas Preview -->
            <div v-if="selectedPanoramas.length > 0" class="selected-panoramas">
              <h4>New 360° Images to Upload ({{ selectedPanoramas.length }})</h4>
              <div v-for="(pano, index) in selectedPanoramas" :key="index" class="panorama-upload-item">
                <img :src="pano.preview" :alt="pano.file.name" class="panorama-upload-preview" />
                <div class="panorama-upload-details">
                  <label>Room Name *</label>
                  <input
                    v-model="pano.roomName"
                    type="text"
                    placeholder="e.g., Living Room, Kitchen, Master Bedroom"
                    class="form-input"
                    required
                  />
                </div>
                <button
                  type="button"
                  @click="removeSelectedPanorama(index)"
                  class="btn-remove-preview"
                >
                  ✕
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <router-link to="/agent/properties" class="btn-secondary">
            Cancel
          </router-link>
          <button type="submit" class="btn-primary" :disabled="submitting">
            {{ submitting ? 'Saving...' : (isEditMode ? 'Update Property' : 'Create Property') }}
          </button>
        </div>
      </form>

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
    </main>
  </div>
</template>

<script>
export default {
  name: 'PropertyListingForm',

  data() {
    return {
      sidebarOpen: false,
      // ✅ Dynamic API URL
      apiUrl: window.__API_URL__,

      // Form Data
      formData: {
        title: '',
        description: '',
        address: '',
        city: '',
        province: '',
        postal_code: '',
        country: 'Philippines',
        price: null,
        bedrooms: 0,
        bathrooms: 0,
        floor_area: null,
        lot_area: null,
        property_type: '',
        listing_type: 'sale',
        status: 'available',
      },

      // ✅ Property with photos
      property: null,

      // User Data
      userName: '',
      profilePhotoUrl: null,

      // Form State
      isEditMode: false,
      loading: false,
      submitting: false,
      loadError: '',
      errors: {},

      // ✅ Photos
      selectedPhotos: [],
      selectedPanoramas: [],

      // Messages
      successMessage: '',
      errorMessage: '',
    };
  },

  methods: {
    async loadProperty() {
      try {
        this.loading = true;
        this.loadError = '';
        
        const propertyId = this.$route.params.id;
        const token = localStorage.getItem('auth_token');

        const response = await fetch(
          `${this.apiUrl}/api/agent/properties/${propertyId}`,
          {
            headers: {
              'Authorization': `Bearer ${token}`,
            },
          }
        );

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: Failed to load property`);
        }

        const data = await response.json();

        if (data.success && data.data) {
          const property = data.data;
          
          // ✅ Store full property data (including photos)
          this.property = property;
          
          // Populate form
          this.formData = {
            title: property.title || '',
            description: property.description || '',
            address: property.address || '',
            city: property.city || '',
            province: property.province || '',
            postal_code: property.postal_code || '',
            country: property.country || 'Philippines',
            price: property.price || null,
            bedrooms: property.bedrooms || 0,
            bathrooms: property.bathrooms || 0,
            floor_area: property.floor_area || null,
            lot_area: property.lot_area || null,
            property_type: property.property_type || '',
            listing_type: property.listing_type || 'sale',
            status: property.status || 'available',
          };
          console.log('✅ Property loaded:', this.property);
        } else {
          throw new Error(data.message || 'Failed to load property');
        }
      } catch (error) {
        console.error('❌ Load error:', error);
        this.loadError = error.message || 'Failed to load property. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    // ✅ Handle photo selection
    handlePhotoSelect(event) {
      const files = event.target.files;
      
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Validate file
        if (!file.type.startsWith('image/')) {
          this.showError('Please select only image files');
          continue;
        }

        if (file.size > 10 * 1024 * 1024) {
          this.showError(`File ${file.name} is too large (max 10MB)`);
          continue;
        }

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.selectedPhotos.push({
            file: file,
            preview: e.target.result,
          });
        };
        reader.readAsDataURL(file);
      }

      // Clear input
      this.$refs.photoInput.value = '';
    },

    // ✅ Remove selected photo
    removeSelectedPhoto(index) {
      this.selectedPhotos.splice(index, 1);
    },

    // ✅ Delete existing photo
    async deletePhoto(photoId) {
      if (!confirm('Delete this photo?')) return;

      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(
          `${this.apiUrl}/api/agent/photos/${photoId}`,
          {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${token}`,
            },
          }
        );

        const data = await response.json();

        if (data.success) {
          this.showSuccess('Photo deleted');
          // Remove from property photos
          const index = this.property.photos.findIndex(p => p.id === photoId);
          if (index > -1) {
            this.property.photos.splice(index, 1);
          }
        } else {
          this.showError(data.message || 'Failed to delete photo');
        }
      } catch (error) {
        console.error('Delete photo error:', error);
        this.showError('Failed to delete photo');
      }
    },

    // ✅ Set cover photo
    async setCoverPhoto(photoId) {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(
          `${this.apiUrl}/api/agent/photos/${photoId}/cover`,
          {
            method: 'PUT',
            headers: {
              'Authorization': `Bearer ${token}`,
            },
          }
        );

        const data = await response.json();

        if (data.success) {
          this.showSuccess('Cover photo updated');
          // Update cover status
          this.property.photos.forEach(p => {
            p.is_cover = p.id === photoId;
          });
        } else {
          this.showError(data.message || 'Failed to set cover photo');
        }
      } catch (error) {
        console.error('Set cover photo error:', error);
        this.showError('Failed to set cover photo');
      }
    },

    validateForm() {
      this.errors = {};

      if (!this.formData.title.trim()) {
        this.errors.title = 'Title is required';
      }

      if (!this.formData.property_type) {
        this.errors.property_type = 'Property type is required';
      }

      if (!this.formData.price || this.formData.price <= 0) {
        this.errors.price = 'Valid price is required';
      }

      if (!this.formData.address.trim()) {
        this.errors.address = 'Address is required';
      }

      if (!this.formData.city.trim()) {
        this.errors.city = 'City is required';
      }

      if (!this.formData.province.trim()) {
        this.errors.province = 'Province is required';
      }

      if (this.formData.bedrooms < 0) {
        this.errors.bedrooms = 'Bedrooms must be 0 or more';
      }

      if (this.formData.bathrooms < 0) {
        this.errors.bathrooms = 'Bathrooms must be 0 or more';
      }

      return Object.keys(this.errors).length === 0;
    },

    async submitForm() {
      if (!this.validateForm()) {
        this.showError('Please fix the errors above');
        return;
      }

      try {
        this.submitting = true;
        const token = localStorage.getItem('auth_token');

        if (this.isEditMode) {
          // Update existing property
          const propertyId = this.$route.params.id;

          const response = await fetch(
            `${this.apiUrl}/api/agent/properties/${propertyId}`,
            {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
              },
              body: JSON.stringify(this.formData),
            }
          );

          const data = await response.json();

          if (data.success) {
            // ✅ Upload selected photos
            if (this.selectedPhotos.length > 0) {
              await this.uploadPhotos(propertyId, token);
            }

            // ✅ Upload 360° panoramas
            if (this.selectedPanoramas.length > 0) {
              await this.uploadPanoramas(propertyId, token);
            }

            this.showSuccess('Property updated successfully!');
            setTimeout(() => {
              this.$router.push('/agent/properties');
            }, 1500);
          } else {
            this.showError(data.message || 'Failed to update property');
          }
        } else {
          // Create new property
          const response = await fetch(
            `${this.apiUrl}/api/agent/properties`,
            {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
              },
              body: JSON.stringify(this.formData),
            }
          );

          const data = await response.json();

          if (data.success) {
            // ✅ Upload photos if any
            if (this.selectedPhotos.length > 0) {
              await this.uploadPhotos(data.data.id, token);
            }

            // ✅ Upload 360° panoramas
            if (this.selectedPanoramas.length > 0) {
              await this.uploadPanoramas(data.data.id, token);
            }

            this.showSuccess('Property created successfully!');
            setTimeout(() => {
              this.$router.push('/agent/properties');
            }, 1500);
          } else {
            this.showError(data.message || 'Failed to create property');
          }
        }
      } catch (error) {
        console.error('❌ Form error:', error);
        this.showError('An error occurred. Please try again.');
      } finally {
        this.submitting = false;
      }
    },

    // ✅ Upload photos
    async uploadPhotos(propertyId, token) {
      try {
        const formData = new FormData();
        
        this.selectedPhotos.forEach(photo => {
          formData.append('photos[]', photo.file);
        });

        const response = await fetch(
          `${this.apiUrl}/api/agent/properties/${propertyId}/photos`,
          {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
            },
            body: formData,
          }
        );

        const data = await response.json();

        if (data.success) {
          this.showSuccess(`${this.selectedPhotos.length} photo(s) uploaded!`);
          this.selectedPhotos = [];
        } else {
          console.warn('Photo upload warning:', data.message);
        }
      } catch (error) {
        console.error('Photo upload error:', error);
        console.warn('Photos uploaded but some may have failed');
      }
    },

    // 360° Panorama methods
    handlePanoramaSelect(event) {
      const files = event.target.files;

      for (let i = 0; i < files.length; i++) {
        const file = files[i];

        if (!file.type.startsWith('image/')) {
          this.showError('Please select only image files');
          continue;
        }

        if (file.size > 20 * 1024 * 1024) {
          this.showError(`File ${file.name} is too large (max 20MB)`);
          continue;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
          this.selectedPanoramas.push({
            file: file,
            preview: e.target.result,
            roomName: '',
          });
        };
        reader.readAsDataURL(file);
      }

      this.$refs.panoramaInput.value = '';
    },

    removeSelectedPanorama(index) {
      this.selectedPanoramas.splice(index, 1);
    },

    async deletePanorama(panoramaId) {
      if (!confirm('Delete this 360° image?')) return;

      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(
          `${this.apiUrl}/api/agent/panoramas/${panoramaId}`,
          {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${token}`,
            },
          }
        );

        const data = await response.json();

        if (data.success) {
          this.showSuccess('360° image deleted');
          const index = this.property.panoramas.findIndex(p => p.id === panoramaId);
          if (index > -1) {
            this.property.panoramas.splice(index, 1);
          }
        } else {
          this.showError(data.message || 'Failed to delete 360° image');
        }
      } catch (error) {
        console.error('Delete panorama error:', error);
        this.showError('Failed to delete 360° image');
      }
    },

    async uploadPanoramas(propertyId, token) {
      try {
        const formData = new FormData();

        this.selectedPanoramas.forEach((pano, index) => {
          formData.append('panoramas[]', pano.file);
          formData.append('room_names[]', pano.roomName || 'Room ' + (index + 1));
        });

        const response = await fetch(
          `${this.apiUrl}/api/agent/properties/${propertyId}/panoramas`,
          {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
            },
            body: formData,
          }
        );

        const data = await response.json();

        if (data.success) {
          this.showSuccess(`${this.selectedPanoramas.length} 360° image(s) uploaded!`);
          this.selectedPanoramas = [];
        } else {
          console.warn('Panorama upload warning:', data.message);
        }
      } catch (error) {
        console.error('Panorama upload error:', error);
      }
    },

    loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName = user.name || 'Agent';
      } catch (error) {
        console.error('Error loading user data:', error);
        this.userName = 'Agent';
      }
    },

    loadProfilePhoto() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__ || 'http://localhost:8000'}/storage/${user.profile_photo_path}`;
        }
      } catch (e) { /* non-critical */ }
    },

    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push('/');
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
  },

  mounted() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/');
      return;
    }

    this.apiUrl = localStorage.getItem('api_url') || window.__API_URL__;
    this.loadUserData();
    this.loadProfilePhoto();

    this.isEditMode = !!this.$route.params.id;
    
    if (this.isEditMode) {
      this.loadProperty();
    }
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

.form-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--bg);
  font-family: var(--fb);
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
}

/* ── SIDEBAR ── */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--navy); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; display: block; }
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
.nav-item.router-link-active:not(.router-link-exact-active) { color: rgba(255,255,255,0.65); }
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

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.form-main {
  flex: 1;
  margin-left: var(--sw);
  padding: 40px;
  overflow-y: auto;
}

.form-header {
  margin-bottom: 40px;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: white;
  border: 1px solid var(--s200);
  border-radius: 50px;
  color: var(--navy);
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s;
  margin-bottom: 20px;
  font-size: 14px;
}

.btn-back:hover {
  background: var(--s100);
  border-color: var(--gold);
}

.form-title {
  font-size: 32px;
  font-weight: 800;
  color: var(--navy);
  font-family: var(--fd);
  margin: 0 0 8px 0;
}

.form-subtitle {
  font-size: 16px;
  color: #999;
  margin: 0;
}

/* Loading & Error States */
.loading-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  color: #999;
  font-size: 16px;
}

.error-banner {
  background: #ffebee;
  border: 1px solid #f44336;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  color: #c62828;
}

.error-banner p {
  margin: 0 0 12px 0;
  font-weight: 700;
}

.btn-retry {
  display: inline-block;
  padding: 10px 16px;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 700;
  margin-right: 8px;
  transition: all 0.3s;
}

.btn-retry:hover {
  background: #da190b;
}

/* Form Sections */
.property-form {
  background: white;
  border-radius: 12px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.form-section {
  margin-bottom: 32px;
  padding-bottom: 32px;
  border-bottom: 1px solid var(--s200);
}

.form-section:last-of-type {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.section-title {
  font-size: 18px;
  font-weight: 800;
  color: var(--navy);
  font-family: var(--fd);
  margin: 0 0 20px 0;
}

.subsection-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--navy);
  margin: 0 0 12px 0;
}

/* Form Groups */
.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: var(--navy);
  text-transform: uppercase;
  margin-bottom: 8px;
  letter-spacing: 0.5px;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--s200);
  border-radius: 6px;
  font-size: 14px;
  font-family: var(--fb);
  transition: all 0.3s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: var(--gold);
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.error-text {
  display: block;
  color: #f44336;
  font-size: 12px;
  margin-top: 4px;
  font-weight: 600;
}

/* ✅ PHOTOS SECTION */
.existing-photos {
  margin-bottom: 32px;
  padding-bottom: 32px;
  border-bottom: 1px solid var(--s200);
}

.photos-info {
  font-size: 13px;
  color: #999;
  margin: 0 0 16px 0;
  padding: 12px;
  background: var(--s100);
  border-radius: 6px;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.photo-item {
  border: 1px solid var(--s200);
  border-radius: 8px;
  overflow: hidden;
}

.photo-container {
  position: relative;
  width: 100%;
  height: 150px;
  background: #f0f0f0;
  overflow: hidden;
  group: 'photo';
}

.photo-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cover-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: var(--gold);
  color: var(--navy);
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 700;
}

.btn-set-cover {
  position: absolute;
  bottom: 8px;
  left: 8px;
  background: var(--gold);
  color: var(--navy);
  border: none;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  opacity: 0;
}

.photo-container:hover .btn-set-cover {
  opacity: 1;
}

.btn-set-cover:hover {
  background: var(--gold3);
}

.btn-delete-photo {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  opacity: 0;
}

.photo-container:hover .btn-delete-photo {
  opacity: 1;
}

.btn-delete-photo:hover {
  background: #da190b;
}

.photo-filename {
  padding: 8px;
  font-size: 12px;
  color: #999;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin: 0;
}

/* Upload Section */
.upload-section {
  margin-bottom: 20px;
}

.upload-area {
  border: 2px dashed var(--gold);
  border-radius: 8px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  background: #fafaf8;
}

.upload-area:hover {
  background: var(--s100);
  border-color: var(--gold3);
}

.photo-input {
  display: none;
}

.upload-icon {
  font-size: 48px;
  margin: 0 0 12px 0;
}

.upload-text {
  font-size: 14px;
  font-weight: 700;
  color: var(--navy);
  margin: 0;
}

.upload-hint {
  font-size: 12px;
  color: #999;
  margin: 4px 0 0 0;
}

/* Selected Photos Preview */
.selected-photos {
  margin-top: 20px;
  padding: 16px;
  background: var(--s100);
  border-radius: 8px;
}

.selected-photos h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 700;
  color: var(--navy);
}

.preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 12px;
}

.preview-item {
  position: relative;
  width: 100%;
  height: 100px;
  border-radius: 6px;
  overflow: hidden;
  background: #f0f0f0;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.btn-remove-preview {
  position: absolute;
  top: 4px;
  right: 4px;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-remove-preview:hover {
  background: #da190b;
  transform: scale(1.1);
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 16px;
  justify-content: flex-end;
  margin-top: 40px;
  padding-top: 24px;
  border-top: 1px solid var(--s200);
}

.btn-primary,
.btn-secondary {
  padding: 12px 32px;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 14px;
}

.btn-primary {
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: var(--navy);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: white;
  color: var(--navy);
  border: 1px solid var(--s200);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-secondary:hover {
  background: var(--s100);
  border-color: var(--gold);
}

/* Messages */
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

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: calc(-1 * var(--sw));
    transition: left 0.3s ease;
    width: var(--sw);
    z-index: 1001;
  }

  .form-main {
    margin-left: 0 !important;
    padding: 20px;
  }

  .property-form {
    padding: 20px;
  }

  .form-title {
    font-size: 24px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-primary,
  .btn-secondary {
    width: 100%;
  }

  .photos-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  }
}

@media (max-width: 480px) {
  .form-main {
    padding: 16px;
  }

  .property-form {
    padding: 16px;
  }

  .form-title {
    font-size: 20px;
  }

  .section-title {
    font-size: 16px;
  }

  .upload-area {
    padding: 24px;
  }

  .photos-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 12px;
  }

  .panorama-grid {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  }

  .panorama-upload-item {
    flex-direction: column;
    align-items: stretch;
  }

  .panorama-upload-preview {
    width: 100%;
    height: 80px;
  }
}

/* 360° Panorama Section */
.section-description {
  font-size: 13px;
  color: #999;
  margin: -12px 0 20px 0;
  line-height: 1.5;
}

.panorama-upload {
  border-color: #7c3aed !important;
  background: #faf8ff !important;
}

.panorama-upload:hover {
  background: #f5f0ff !important;
  border-color: #6d28d9 !important;
}

.panorama-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.panorama-item {
  border: 1px solid var(--s200);
  border-radius: 8px;
  overflow: hidden;
}

.panorama-preview-container {
  position: relative;
  width: 100%;
  height: 120px;
  background: #f0f0f0;
  overflow: hidden;
}

.panorama-preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.panorama-room-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 700;
}

.btn-delete-panorama {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  opacity: 0;
  transition: all 0.3s;
}

.panorama-preview-container:hover .btn-delete-panorama {
  opacity: 1;
}

.selected-panoramas {
  margin-top: 20px;
  padding: 16px;
  background: var(--s100);
  border-radius: 8px;
}

.selected-panoramas h4 {
  margin: 0 0 16px 0;
  font-size: 14px;
  font-weight: 700;
  color: var(--navy);
}

.panorama-upload-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px;
  background: white;
  border: 1px solid var(--s200);
  border-radius: 8px;
  margin-bottom: 12px;
}

.panorama-upload-preview {
  width: 120px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
  flex-shrink: 0;
}

.panorama-upload-details {
  flex: 1;
}

.panorama-upload-details label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: var(--navy);
  text-transform: uppercase;
  margin-bottom: 6px;
}

.existing-panoramas {
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--s200);
}
</style>