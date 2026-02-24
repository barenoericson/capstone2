<template>
  <div class="form-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/agent/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>
        <router-link to="/agent/properties" class="nav-item active">
          <span class="nav-icon">🏠</span>
          <span class="nav-label">My Properties</span>
        </router-link>
        <router-link to="/agent/saved-properties" class="nav-item">
          <span class="nav-icon">❤️</span>
          <span class="nav-label">My Wallet</span>
        </router-link>
        <router-link to="/agent/viewings" class="nav-item">
          <span class="nav-icon">📅</span>
          <span class="nav-label">Viewings</span>
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar">{{ userName.charAt(0).toUpperCase() }}</div>
          <p class="user-name">{{ userName }}</p>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="form-main">
      <div class="form-header">
        <router-link to="/agent/properties" class="btn-back">← Back to Properties</router-link>
        <h1 class="form-title">{{ isEditMode ? '✏️ Edit Property' : '➕ Create New Property' }}</h1>
        <p class="form-subtitle">{{ isEditMode ? 'Update your property details and photos' : 'List a new property for sale or rent' }}</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <p>⏳ Loading property...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="loadError" class="error-banner">
        <p>❌ {{ loadError }}</p>
        <button @click="loadProperty" class="btn-retry">Retry</button>
        <router-link to="/agent/properties" class="btn-back">← Back to Properties</router-link>
      </div>

      <!-- Form -->
      <form v-else @submit.prevent="submitForm" class="property-form">
        <!-- Basic Information Section -->
        <div class="form-section">
          <h2 class="section-title">📋 Basic Information</h2>

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
              <label for="status">Status</label>
              <select id="status" v-model="formData.status" class="form-input">
                <option value="available">Available</option>
                <option value="sold">Sold</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Pricing Section -->
        <div class="form-section">
          <h2 class="section-title">💰 Pricing</h2>

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
          <h2 class="section-title">📍 Location</h2>

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
          <h2 class="section-title">🏡 Features</h2>

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
          <h2 class="section-title">📸 Photos</h2>

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

        <!-- Form Actions -->
        <div class="form-actions">
          <router-link to="/agent/properties" class="btn-secondary">
            Cancel
          </router-link>
          <button type="submit" class="btn-primary" :disabled="submitting">
            {{ submitting ? '⏳ Saving...' : (isEditMode ? '💾 Update Property' : '✅ Create Property') }}
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
        status: 'available',
      },

      // ✅ Property with photos
      property: null,

      // User Data
      userName: '',

      // Form State
      isEditMode: false,
      loading: false,
      submitting: false,
      loadError: '',
      errors: {},

      // ✅ Photos
      selectedPhotos: [],

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

    loadUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        this.userName = user.name || 'Agent';
      } catch (error) {
        console.error('Error loading user data:', error);
        this.userName = 'Agent';
      }
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
      this.$router.push('/login');
      return;
    }

    this.apiUrl = localStorage.getItem('api_url') || window.__API_URL__;
    this.loadUserData();

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

:root {
  --smoky-black: #100c08;
  --white-smoke: #f5f5f5;
  --palace-gold: #e6ae0d;
  --palace-gold-dark: #d4a000;
  --light-gray: #e0e0e0;
  --font-display: 'Poppins', sans-serif;
  --font-body: 'Inter', sans-serif;
}

.form-wrapper {
  display: flex;
  min-height: 100vh;
  background: var(--white-smoke);
  font-family: var(--font-body);
}

/* ============================================================================
   SIDEBAR
   ============================================================================ */

.sidebar {
  width: 280px;
  background: white;
  border-right: 1px solid var(--light-gray);
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.08);
  z-index: 1000;
}

.sidebar-header {
  padding: 24px 16px;
  border-bottom: 1px solid var(--light-gray);
}

.sidebar-logo {
  font-size: 24px;
  font-weight: 800;
  margin: 0;
  font-family: var(--font-display);
  letter-spacing: -0.5px;
  display: flex;
  align-items: center;
}

.logo-realty {
  color: var(--smoky-black);
}

.logo-ph {
  color: var(--palace-gold);
  margin-left: 2px;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  margin: 0 8px;
  color: var(--smoky-black);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 14px;
}

.nav-item:hover {
  background: var(--white-smoke);
  color: var(--palace-gold);
}

.nav-item.active {
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  font-weight: 600;
}

.nav-icon {
  font-size: 18px;
  min-width: 24px;
  text-align: center;
}

.nav-label {
  flex: 1;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid var(--light-gray);
}

.user-card {
  background: var(--white-smoke);
  border-radius: 8px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  flex-shrink: 0;
}

.user-name {
  font-size: 13px;
  font-weight: 700;
  color: var(--smoky-black);
  margin: 0;
}

/* ============================================================================
   MAIN CONTENT
   ============================================================================ */

.form-main {
  flex: 1;
  margin-left: 280px;
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
  border: 1px solid var(--light-gray);
  border-radius: 8px;
  color: var(--smoky-black);
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s;
  margin-bottom: 20px;
  font-size: 14px;
}

.btn-back:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
}

.form-title {
  font-size: 32px;
  font-weight: 800;
  color: var(--smoky-black);
  font-family: var(--font-display);
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
  border-bottom: 1px solid var(--light-gray);
}

.form-section:last-of-type {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.section-title {
  font-size: 18px;
  font-weight: 800;
  color: var(--smoky-black);
  font-family: var(--font-display);
  margin: 0 0 20px 0;
}

.subsection-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--smoky-black);
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
  color: var(--smoky-black);
  text-transform: uppercase;
  margin-bottom: 8px;
  letter-spacing: 0.5px;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--light-gray);
  border-radius: 6px;
  font-size: 14px;
  font-family: var(--font-body);
  transition: all 0.3s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: var(--palace-gold);
  box-shadow: 0 0 0 3px rgba(230, 174, 13, 0.1);
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
  border-bottom: 1px solid var(--light-gray);
}

.photos-info {
  font-size: 13px;
  color: #999;
  margin: 0 0 16px 0;
  padding: 12px;
  background: var(--white-smoke);
  border-radius: 6px;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.photo-item {
  border: 1px solid var(--light-gray);
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
  background: var(--palace-gold);
  color: var(--smoky-black);
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 700;
}

.btn-set-cover {
  position: absolute;
  bottom: 8px;
  left: 8px;
  background: var(--palace-gold);
  color: var(--smoky-black);
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
  background: var(--palace-gold-dark);
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
  border: 2px dashed var(--palace-gold);
  border-radius: 8px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  background: #fafaf8;
}

.upload-area:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold-dark);
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
  color: var(--smoky-black);
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
  background: var(--white-smoke);
  border-radius: 8px;
}

.selected-photos h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 700;
  color: var(--smoky-black);
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
  border-top: 1px solid var(--light-gray);
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
  background: linear-gradient(135deg, var(--palace-gold), var(--palace-gold-dark));
  color: var(--smoky-black);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(230, 174, 13, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: white;
  color: var(--smoky-black);
  border: 1px solid var(--light-gray);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-secondary:hover {
  background: var(--white-smoke);
  border-color: var(--palace-gold);
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
    left: -280px;
    transition: left 0.3s ease;
    width: 280px;
    z-index: 1001;
  }

  .form-main {
    margin-left: 0;
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
}
</style>