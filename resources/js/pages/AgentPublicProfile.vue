<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar (buyer sidebar) -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>
      <nav class="sidebar-nav">
        <router-link to="/dashboard" exact class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
          <span>Dashboard</span>
        </router-link>

        <div class="nav-group-label">Browse</div>
        <router-link to="/properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
          <span>Properties</span>
        </router-link>
        <router-link to="/saved-properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></span>
          <span>Saved</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
          <span>Messages</span>
        </router-link>
        <router-link to="/viewings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          <span>Viewings</span>
        </router-link>

        <div class="nav-group-label">Tools</div>
        <router-link to="/documents" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
          <span>Documents</span>
        </router-link>
      </nav>

      <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <router-link to="/profile" class="nav-item nav-user" @click="sidebarOpen = false">
          <div class="nav-av">
            <img v-if="myPhotoUrl" :src="myPhotoUrl" :alt="myName" />
            <span v-else>{{ myName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ myName }}</span>
            <span class="nav-user-role">Buyer</span>
          </div>
        </router-link>
        <router-link to="/settings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg></span>
          <span>Settings</span>
        </router-link>
        <button class="nav-item nav-logout" @click="logout">
          <span class="nav-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <button @click="$router.back()" class="btn-back">← Back</button>
          <div class="topbar-right">
            <router-link
              v-if="agent"
              :to="`/conversations/${agent.user_id}`"
              class="btn-message-agent"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              Message Agent
            </router-link>
          </div>
        </div>
      </nav>

      <div class="content-area">
        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading profile...</p>
        </div>

        <template v-else-if="agent">
          <!-- Agent Profile Card -->
          <div class="profile-card">
            <div class="profile-cover">
              <div class="cover-pattern"></div>
              <div class="agent-avatar-wrap">
                <div
                  class="agent-avatar"
                  :style="agent.profile_photo_url
                    ? `background-image:url(${agent.profile_photo_url});background-size:cover;background-position:center;`
                    : ''"
                >
                  <span v-if="!agent.profile_photo_url">{{ agent.name.charAt(0).toUpperCase() }}</span>
                </div>
                <span :class="['verification-badge', agent.verification_status]">
                  {{ agent.verification_status === 'verified' ? 'Verified' : agent.verification_status }}
                </span>
              </div>
            </div>

            <div class="profile-body">
              <div class="profile-head">
                <div class="profile-head-left">
                  <h1 class="agent-name">{{ agent.name }}</h1>
                  <p class="agent-company" v-if="agent.company_name">{{ agent.company_name }}</p>
                  <div class="agent-tags">
                    <span class="agent-tag">Real Estate Agent</span>
                    <span class="agent-tag gold" v-if="agent.verification_status === 'verified'">Verified</span>
                  </div>
                </div>

                <!-- Star Rating Display -->
                <div class="rating-card">
                  <div class="rating-big">{{ agent.average_rating > 0 ? agent.average_rating.toFixed(1) : '—' }}</div>
                  <div class="stars-row">
                    <span
                      v-for="star in 5"
                      :key="star"
                      class="star"
                      :class="star <= Math.round(agent.average_rating) ? 'filled' : ''"
                    >★</span>
                  </div>
                  <span class="rating-count">{{ agent.total_reviews }} review{{ agent.total_reviews !== 1 ? 's' : '' }}</span>
                </div>
              </div>

              <!-- Info grid -->
              <div class="info-grid">
                <div class="info-item" v-if="agent.bio">
                  <div class="info-icon-wrap ii-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                  </div>
                  <div>
                    <span class="info-label">About</span>
                    <p class="info-value bio">{{ agent.bio }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="agent.company_address">
                  <div class="info-icon-wrap ii-red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  </div>
                  <div>
                    <span class="info-label">Office</span>
                    <p class="info-value">{{ agent.company_address }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="agent.license_number">
                  <div class="info-icon-wrap ii-gold">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                  </div>
                  <div>
                    <span class="info-label">License</span>
                    <p class="info-value">{{ agent.license_number }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="agent.accreditation">
                  <div class="info-icon-wrap ii-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                  </div>
                  <div>
                    <span class="info-label">Accreditation</span>
                    <p class="info-value">{{ agent.accreditation }}</p>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon-wrap ii-green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  </div>
                  <div>
                    <span class="info-label">Member Since</span>
                    <p class="info-value">{{ formatDate(agent.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="action-row">
                <button class="btn-rate" @click="openRateModal">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  Rate this Agent
                </button>
                <router-link :to="`/conversations/${agent.user_id}`" class="btn-chat">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                  Send Message
                </router-link>
              </div>
            </div>
          </div>

          <!-- Reviews Section -->
          <div class="reviews-section">
            <div class="reviews-header">
              <h2 class="section-heading">Reviews</h2>
              <span class="reviews-count">{{ reviews.length }} review{{ reviews.length !== 1 ? 's' : '' }}</span>
            </div>

            <div v-if="reviews.length === 0" class="no-reviews">
              <div class="no-reviews-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              </div>
              <p class="no-reviews-title">No reviews yet</p>
              <p class="no-reviews-sub">Be the first to rate this agent!</p>
            </div>

            <div v-else class="reviews-list">
              <div v-for="review in reviews" :key="review.id" class="review-card">
                <div class="review-header">
                  <div class="reviewer-avatar">
                    <img
                      v-if="review.buyer && review.buyer.profile_photo_url"
                      :src="review.buyer.profile_photo_url"
                      :alt="review.buyer.name"
                      class="reviewer-photo"
                    />
                    <span v-else>{{ review.buyer ? review.buyer.name.charAt(0).toUpperCase() : '?' }}</span>
                  </div>
                  <div class="reviewer-info">
                    <p class="reviewer-name">{{ review.buyer ? review.buyer.name : 'Anonymous' }}</p>
                    <div class="review-meta">
                      <div class="review-stars">
                        <span v-for="s in 5" :key="s" class="star-sm" :class="s <= review.rating ? 'filled' : ''">★</span>
                      </div>
                      <span class="review-date">{{ formatDate(review.created_at) }}</span>
                    </div>
                  </div>
                </div>
                <p v-if="review.review_title" class="review-title">{{ review.review_title }}</p>
                <p v-if="review.review_content" class="review-content">{{ review.review_content }}</p>
              </div>
            </div>
          </div>
        </template>
      </div>
    </main>

    <!-- Rate Modal -->
    <transition name="modal-fade">
      <div v-if="showRateModal" class="modal-overlay" @click.self="showRateModal = false">
        <div class="modal-box">
          <div class="modal-header">
            <div>
              <h2 class="modal-title">Rate this Agent</h2>
              <p class="modal-sub">Share your experience with {{ agent?.name }}</p>
            </div>
            <button class="modal-close" @click="showRateModal = false">✕</button>
          </div>

          <div class="modal-body">
            <!-- Star picker -->
            <div class="star-picker-wrap">
              <div class="star-picker">
                <span
                  v-for="star in 5"
                  :key="star"
                  class="star-pick"
                  :class="{ filled: star <= hoverRating || (!hoverRating && star <= ratingForm.rating) }"
                  @mouseenter="hoverRating = star"
                  @mouseleave="hoverRating = 0"
                  @click="ratingForm.rating = star"
                >★</span>
              </div>
              <p class="rating-label" :class="{ active: ratingForm.rating > 0 }">
                {{ ratingLabels[hoverRating] || ratingLabels[ratingForm.rating] || 'Tap a star to rate' }}
              </p>
            </div>

            <div class="form-group">
              <label class="form-label">Review Title <span class="form-optional">(optional)</span></label>
              <input
                v-model="ratingForm.review_title"
                type="text"
                class="form-input"
                placeholder="e.g. Great agent, very professional"
                maxlength="100"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Your Review <span class="form-optional">(optional)</span></label>
              <textarea
                v-model="ratingForm.review_content"
                class="form-textarea"
                placeholder="Share your experience working with this agent..."
                rows="4"
                maxlength="1000"
              ></textarea>
              <span class="char-count" v-if="ratingForm.review_content">{{ ratingForm.review_content.length }}/1000</span>
            </div>

            <p v-if="rateError" class="form-error">{{ rateError }}</p>
          </div>

          <div class="modal-footer">
            <button class="btn-cancel" @click="showRateModal = false" :disabled="submittingRate">Cancel</button>
            <button class="btn-submit" @click="submitRating" :disabled="submittingRate || !ratingForm.rating">
              {{ submittingRate ? 'Submitting...' : 'Submit Rating' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Toast -->
    <transition name="toast-slide">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
        <span class="toast-icon">{{ toast.type === 'success' ? '✓' : '✕' }}</span>
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'AgentPublicProfile',

  data() {
    return {
      sidebarOpen: false,
      apiUrl: localStorage.getItem('api_url') || '',
      token: '',
      myName: '',
      myPhotoUrl: null,
      myId: null,
      loading: true,
      agent: null,
      reviews: [],

      // Rate modal
      showRateModal: false,
      hoverRating: 0,
      ratingForm: { rating: 0, review_title: '', review_content: '' },
      rateError: '',
      submittingRate: false,

      ratingLabels: {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent',
      },

      toast: { show: false, message: '', type: 'success' },
    };
  },

  mounted() {
    this.token = localStorage.getItem('auth_token') || '';
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.myName = user.name || '';
    this.myId = user.id;
    if (user.profile_photo_path) {
      this.myPhotoUrl = `${window.__API_URL__ || 'http://localhost:8000'}/storage/${user.profile_photo_path}`;
    }
    this.apiUrl = localStorage.getItem('api_url') || '';
    this.loadProfile();
  },

  methods: {
    async loadProfile() {
      this.loading = true;
      try {
        const userId = this.$route.params.userId;
        const res = await fetch(`${this.apiUrl}/api/users/${userId}/agent-profile`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          this.agent = data.agent;
          this.reviews = data.reviews || [];
        }
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },

    openRateModal() {
      this.ratingForm = { rating: 0, review_title: '', review_content: '' };
      this.rateError = '';
      this.hoverRating = 0;
      this.showRateModal = true;
    },

    async submitRating() {
      if (!this.ratingForm.rating) {
        this.rateError = 'Please select a star rating.';
        return;
      }
      this.rateError = '';
      this.submittingRate = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/agents/${this.agent.id}/reviews`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${this.token}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.ratingForm),
        });
        const data = await res.json();
        if (!res.ok) {
          this.rateError = data.message || 'Failed to submit rating.';
          return;
        }
        if (data.average_rating !== undefined) {
          this.agent.average_rating = data.average_rating;
          this.agent.total_reviews = data.total_reviews;
        }
        if (data.review) {
          const idx = this.reviews.findIndex(r => r.id === data.review.id);
          if (idx !== -1) {
            this.reviews.splice(idx, 1, data.review);
          } else {
            this.reviews.unshift(data.review);
          }
        }
        this.showRateModal = false;
        this.showToast('Rating submitted! Thank you.', 'success');
      } catch (e) {
        this.rateError = 'Network error. Please try again.';
      } finally {
        this.submittingRate = false;
      }
    },

    formatDate(iso) {
      if (!iso) return '—';
      return new Date(iso).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'long', day: 'numeric',
      });
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, message, type };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },

    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push('/');
    },
  },
};
</script>

<style scoped>
* { margin: 0; padding: 0; box-sizing: border-box; }

/* ── Tokens ── */
.dashboard-wrapper {
  --navy: #0B1C39; --navy2: #102445; --navy3: #1a3158;
  --gold: #D89B0F; --gold2: #E5B332; --gold3: #B07A08;
  --s50: #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s800: #292524; --s900: #1C1917;
  --white: #FFFFFF; --bg: #F0F2F5;
  --fd: 'Outfit','Inter',-apple-system,sans-serif;
  --fb: 'Inter',-apple-system,sans-serif;
  display: flex; min-height: 100vh; background: var(--bg);
  font-family: var(--fb); -webkit-font-smoothing: antialiased;
}

/* ── Sidebar ── */
.sidebar {
  width: 252px; min-height: 100vh; background: var(--navy);
  display: flex; flex-direction: column;
  position: fixed; left: 0; top: 0; height: 100vh;
  overflow-y: auto; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; display: flex; margin: 0; }
.logo-realty { color: #fff; }
.logo-ph { background: linear-gradient(135deg, var(--gold), var(--gold2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-left: 1px; }

.sidebar-nav { flex: 1; padding: 10px 10px 4px; overflow-y: auto; scrollbar-width: none; }
.sidebar-nav::-webkit-scrollbar { display: none; }

.nav-group-label {
  font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
  color: rgba(255,255,255,0.22); padding: 14px 12px 5px;
}

.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px 9px 10px; margin-bottom: 2px; border-radius: 9px;
  color: rgba(255,255,255,0.45); text-decoration: none; font-size: 13px; font-weight: 500;
  transition: all .18s; position: relative; cursor: pointer; border: none;
  background: none; width: 100%; text-align: left; font-family: var(--fb);
}
.nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.78); }
.nav-item.router-link-exact-active {
  background: linear-gradient(90deg, rgba(216,155,15,0.14) 0%, rgba(216,155,15,0.03) 100%);
  color: var(--gold2);
}
.nav-item.router-link-exact-active::before {
  content: ''; position: absolute; left: 0; top: 7px; bottom: 7px;
  width: 3px; background: var(--gold); border-radius: 0 3px 3px 0;
}
.nav-item.router-link-exact-active .nav-icon-wrap { color: var(--gold); }

.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }

.sidebar-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 10px 8px; }
.sidebar-bottom { flex-shrink: 0; padding: 0 10px 14px; }

.nav-user { gap: 10px; padding: 8px 10px; }
.nav-user:hover, .nav-user.router-link-exact-active { background: rgba(216,155,15,0.10); color: var(--gold2); }
.nav-av {
  width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 11px; color: #fff;
}
.nav-av img { width: 100%; height: 100%; object-fit: cover; }
.nav-user-info { flex: 1; min-width: 0; }
.nav-user-name { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nav-user-role { display: block; font-size: 10.5px; color: rgba(255,255,255,0.38); margin-top: 1px; text-transform: capitalize; }
.nav-logout { color: rgba(255,255,255,0.30); }
.nav-logout:hover { background: rgba(239,68,68,0.10); color: #f87171; }

.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 90; backdrop-filter: blur(2px); }

/* ── Main Content ── */
.main-content { margin-left: 252px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: var(--white); border-bottom: 1px solid var(--s200);
  padding: 0 28px; height: 56px; position: sticky; top: 0; z-index: 50;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.topbar-content { display: flex; align-items: center; justify-content: space-between; height: 100%; }
.hamburger-btn {
  display: none; width: 34px; height: 34px; border-radius: 8px;
  border: 1px solid var(--s200); background: var(--white); cursor: pointer;
  color: var(--s600); font-size: 16px; align-items: center; justify-content: center;
}

.btn-back {
  background: var(--s50); border: 1px solid var(--s200);
  color: var(--navy); font-size: 13px; font-weight: 600;
  cursor: pointer; padding: 7px 14px; border-radius: 50px; transition: all .2s;
}
.btn-back:hover { border-color: var(--gold); color: var(--gold3); background: rgba(216,155,15,0.05); }

.btn-message-agent {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 8px 20px; border-radius: 50px;
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  color: #fff; font-size: 13px; font-weight: 700;
  text-decoration: none; transition: all .22s;
  box-shadow: 0 2px 8px rgba(11,28,57,0.20);
}
.btn-message-agent:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(11,28,57,0.30); }

.content-area { padding: 28px 30px; max-width: 100%; }

/* Loading */
.loading-state { text-align: center; padding: 80px; color: var(--s500); }
.spinner { width: 36px; height: 36px; border: 3px solid var(--s200); border-top-color: var(--gold); border-radius: 50%; animation: spin 0.7s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Profile Card ── */
.profile-card {
  background: var(--white); border-radius: 20px; overflow: hidden;
  box-shadow: 0 2px 12px rgba(11,28,57,0.08); margin-bottom: 24px;
  border: 1px solid rgba(0,0,0,0.04); animation: fadeUp 0.5s ease-out;
}
@keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.profile-cover {
  height: 160px;
  background: linear-gradient(135deg, #0B1C39 0%, #132B52 50%, #1a3158 100%);
  display: flex; align-items: flex-end; padding: 0 36px;
  position: relative; overflow: hidden;
}
.cover-pattern {
  position: absolute; inset: 0;
  background: repeating-linear-gradient(
    45deg, transparent, transparent 30px,
    rgba(216,155,15,0.04) 30px, rgba(216,155,15,0.04) 60px
  );
}
.agent-avatar-wrap { position: relative; margin-bottom: -44px; z-index: 2; }
.agent-avatar {
  width: 110px; height: 110px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  border: 4px solid var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: 42px; font-weight: 700; color: #fff; overflow: hidden;
  box-shadow: 0 4px 20px rgba(11,28,57,0.20);
  transition: transform 0.25s;
}
.agent-avatar:hover { transform: scale(1.04); }

.verification-badge {
  position: absolute; bottom: 6px; right: -2px;
  background: var(--navy); color: #22c55e;
  border: 3px solid var(--white);
  border-radius: 50px; padding: 3px 10px; font-size: 10px; font-weight: 700;
  letter-spacing: 0.3px;
}
.verification-badge.verified { color: #22c55e; }
.verification-badge.pending { color: var(--gold2); }

.profile-body { padding: 56px 36px 36px; }
.profile-head {
  display: flex; justify-content: space-between; align-items: flex-start;
  flex-wrap: wrap; gap: 20px; margin-bottom: 28px;
}
.profile-head-left { flex: 1; min-width: 200px; }

.agent-name { font-family: var(--fd); font-size: 28px; font-weight: 800; color: var(--navy); margin: 0 0 2px; }
.agent-company { color: var(--s500); font-size: 15px; margin: 0 0 10px; }
.agent-tags { display: flex; gap: 8px; flex-wrap: wrap; }
.agent-tag {
  padding: 4px 12px; border-radius: 50px; font-size: 10.5px; font-weight: 700;
  background: var(--s50); color: var(--s600); border: 1px solid var(--s200);
  text-transform: uppercase; letter-spacing: 0.5px;
}
.agent-tag.gold {
  background: rgba(216,155,15,0.08); color: var(--gold3);
  border-color: rgba(216,155,15,0.25);
}

/* Rating card */
.rating-card {
  background: var(--s50); border-radius: 14px; padding: 16px 24px;
  text-align: center; min-width: 140px;
  border: 1px solid var(--s200);
}
.rating-big {
  font-family: var(--fd); font-size: 36px; font-weight: 800; color: var(--navy);
  line-height: 1; margin-bottom: 6px;
}
.stars-row { display: flex; gap: 2px; justify-content: center; margin-bottom: 4px; }
.star { font-size: 18px; color: var(--s200); transition: color 0.2s; }
.star.filled { color: var(--gold); }
.rating-count { color: var(--s400); font-size: 12px; font-weight: 500; }

/* Info grid */
.info-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 28px; }
.info-item {
  display: flex; gap: 14px; align-items: flex-start;
  padding: 14px 16px; border-radius: 12px;
  background: var(--s50); border: 1px solid transparent;
  transition: all 0.2s;
}
.info-item:hover { border-color: rgba(216,155,15,0.20); background: rgba(216,155,15,0.03); }
.info-icon { font-size: 18px; flex-shrink: 0; margin-top: 2px; }
.info-icon-wrap {
  width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; margin-top: 2px;
}
.info-icon-wrap svg { width: 18px; height: 18px; }
.ii-blue   { background: rgba(59,130,246,0.08); color: #3b82f6; }
.ii-red    { background: rgba(239,68,68,0.08); color: #ef4444; }
.ii-gold   { background: rgba(216,155,15,0.08); color: var(--gold); }
.ii-purple { background: rgba(168,85,247,0.08); color: #a855f7; }
.ii-green  { background: rgba(22,163,74,0.08); color: #16a34a; }
.info-label {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.8px; color: var(--s400); display: block; margin-bottom: 3px;
}
.info-value { font-size: 14px; color: var(--s700); margin: 0; line-height: 1.5; }
.info-value.bio { color: var(--s600); }

/* Action buttons */
.action-row { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-rate {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 11px 26px; border-radius: 50px;
  border: 2px solid var(--gold);
  background: var(--white); color: var(--gold3);
  font-size: 14px; font-weight: 700; cursor: pointer;
  transition: all 0.25s ease;
}
.btn-rate:hover {
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  border-color: var(--gold3); color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(216,155,15,.35);
}
.btn-chat {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 11px 26px; border-radius: 50px;
  border: 2px solid var(--navy);
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  color: #fff; font-size: 14px; font-weight: 700; cursor: pointer;
  text-decoration: none; transition: all 0.25s ease;
  box-shadow: 0 2px 8px rgba(11,28,57,0.20);
}
.btn-chat:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(11,28,57,0.30);
}

/* ── Reviews Section ── */
.reviews-section {
  background: var(--white); border-radius: 20px; padding: 28px;
  box-shadow: 0 2px 12px rgba(11,28,57,0.06);
  border: 1px solid rgba(0,0,0,0.04);
  animation: fadeUp 0.5s ease-out 0.1s both;
}
.reviews-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 20px;
}
.section-heading { font-family: var(--fd); font-size: 18px; font-weight: 700; color: var(--navy); margin: 0; }
.reviews-count {
  font-size: 12px; font-weight: 600; color: var(--gold3);
  background: rgba(216,155,15,0.08); padding: 4px 12px; border-radius: 50px;
  border: 1px solid rgba(216,155,15,0.15);
}
.no-reviews { text-align: center; padding: 48px 20px; }
.no-reviews-icon { margin-bottom: 12px; color: var(--gold); opacity: 0.4; }
.no-reviews-title { font-family: var(--fd); font-size: 16px; font-weight: 700; color: var(--navy); margin: 0 0 4px; }
.no-reviews-sub { font-size: 14px; color: var(--s400); margin: 0; }

.reviews-list { display: flex; flex-direction: column; gap: 12px; }

.review-card {
  padding: 18px; border: 1px solid var(--s100); border-radius: 14px;
  transition: all 0.2s;
}
.review-card:hover { border-color: rgba(216,155,15,0.20); box-shadow: 0 2px 12px rgba(11,28,57,0.05); }
.review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }

.reviewer-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-weight: 700; font-size: 15px;
  overflow: hidden; flex-shrink: 0;
}
.reviewer-photo { width: 100%; height: 100%; object-fit: cover; }
.reviewer-info { flex: 1; }
.reviewer-name { font-size: 14px; font-weight: 700; color: var(--s900); margin: 0 0 3px; }
.review-meta { display: flex; align-items: center; gap: 10px; }
.review-stars { display: flex; gap: 1px; }
.star-sm { font-size: 13px; color: var(--s200); }
.star-sm.filled { color: var(--gold); }
.review-date { font-size: 12px; color: var(--s400); }
.review-title { font-size: 15px; font-weight: 700; color: var(--s900); margin: 0 0 6px; }
.review-content { font-size: 14px; color: var(--s600); line-height: 1.6; margin: 0; }

/* ── Rating Modal ── */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(11,28,57,0.50);
  backdrop-filter: blur(6px);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000; padding: 20px;
}
.modal-box {
  background: var(--white); border-radius: 20px;
  width: 100%; max-width: 480px;
  box-shadow: 0 25px 70px rgba(0,0,0,.25);
  overflow: hidden;
  animation: modalIn 0.3s ease-out;
}
@keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.modal-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 28px 28px 0; gap: 16px;
}
.modal-title { font-family: var(--fd); font-size: 20px; font-weight: 800; color: var(--navy); margin: 0 0 4px; }
.modal-sub { font-size: 13px; color: var(--s400); margin: 0; }
.modal-close {
  background: var(--white); border: 1.5px solid var(--s200);
  width: 34px; height: 34px;
  border-radius: 50%; font-size: 15px; color: var(--s500); cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: all 0.2s;
}
.modal-close:hover { border-color: var(--gold); color: var(--gold3); background: rgba(216,155,15,0.05); }
.modal-body { padding: 24px 28px; }

/* Star picker */
.star-picker-wrap {
  text-align: center; padding: 24px 20px;
  margin-bottom: 24px;
  border-radius: 14px;
  border: 1.5px solid rgba(216,155,15,0.20);
  background: rgba(216,155,15,0.03);
}
.star-picker-wrap::before {
  content: 'How would you rate this agent?';
  display: block; font-size: 11px; font-weight: 700;
  color: var(--gold3); text-transform: uppercase;
  letter-spacing: 1.2px; margin-bottom: 14px;
}
.star-picker { display: flex; gap: 10px; justify-content: center; margin-bottom: 10px; }
.star-pick {
  font-size: 42px; color: var(--s200); cursor: pointer;
  transition: transform 0.15s, color 0.15s, text-shadow 0.15s;
  line-height: 1;
}
.star-pick:hover { transform: scale(1.25); text-shadow: 0 0 16px rgba(216,155,15,.5); }
.star-pick.filled { color: var(--gold); text-shadow: 0 0 12px rgba(216,155,15,.4); }
.rating-label {
  font-size: 14px; color: var(--s300); margin: 0;
  height: 22px; font-weight: 700; transition: color 0.2s;
}
.rating-label.active { color: var(--gold3); }

/* Form */
.form-group { margin-bottom: 18px; }
.form-label {
  display: block; font-size: 12px; font-weight: 700; color: var(--s600);
  margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.4px;
}
.form-optional { color: var(--s300); font-weight: 500; text-transform: none; letter-spacing: 0; }
.form-input {
  width: 100%; padding: 11px 15px; border: 1.5px solid var(--s200); border-radius: 10px;
  font-size: 14px; outline: none; font-family: var(--fb); color: var(--s900); background: var(--s50);
  transition: all .25s;
}
.form-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(216,155,15,.12); background: var(--white); }
.form-textarea {
  width: 100%; padding: 11px 15px; border: 1.5px solid var(--s200); border-radius: 10px;
  font-size: 14px; resize: vertical; font-family: var(--fb); outline: none;
  color: var(--s900); background: var(--s50); transition: all .25s;
}
.form-textarea:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(216,155,15,.12); background: var(--white); }
.char-count { display: block; text-align: right; font-size: 11px; color: var(--s400); margin-top: 4px; }
.form-error {
  color: #dc2626; font-size: 13px; margin-top: 8px;
  padding: 8px 12px; background: #fef2f2; border-radius: 8px;
}

.modal-footer {
  display: flex; justify-content: flex-end; gap: 10px;
  padding: 16px 28px 24px; border-top: 1px solid var(--s100);
  background: var(--s50);
}

.btn-cancel {
  padding: 10px 22px; border-radius: 50px;
  border: 1.5px solid var(--s200); background: var(--white);
  color: var(--s600); font-size: 13px; font-weight: 600; cursor: pointer;
  transition: all 0.22s;
}
.btn-cancel:hover { border-color: var(--s300); color: var(--s800); }

.btn-submit {
  padding: 10px 26px; border-radius: 50px;
  border: none;
  background: linear-gradient(135deg, var(--navy), var(--navy3));
  color: #fff; font-size: 13px; font-weight: 700;
  cursor: pointer;
  box-shadow: 0 3px 10px rgba(11,28,57,0.22);
  transition: all 0.22s;
}
.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(11,28,57,0.30);
}
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Transitions ── */
.modal-fade-enter-active { transition: all 0.25s ease; }
.modal-fade-leave-active { transition: all 0.2s ease; }
.modal-fade-enter-from { opacity: 0; }
.modal-fade-enter-from .modal-box { transform: scale(0.95) translateY(10px); }
.modal-fade-leave-to { opacity: 0; }
.modal-fade-leave-to .modal-box { transform: scale(0.95) translateY(10px); }

.toast-slide-enter-active { transition: all 0.3s ease; }
.toast-slide-leave-active { transition: all 0.25s ease; }
.toast-slide-enter-from,
.toast-slide-leave-to { opacity: 0; transform: translateY(12px); }

/* ── Toast ── */
.toast {
  position: fixed; bottom: 24px; right: 24px;
  padding: 13px 22px; border-radius: 50px; color: #fff;
  font-size: 13.5px; font-weight: 600;
  box-shadow: 0 8px 24px rgba(0,0,0,.18);
  z-index: 9999;
  display: flex; align-items: center; gap: 8px;
}
.toast-icon {
  width: 22px; height: 22px; border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 800;
}
.toast-success { background: #15803d; }
.toast-error { background: #dc2626; }

/* ── Responsive ── */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: transform .28s cubic-bezier(.4,0,.2,1); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay { display: block; }
  .main-content { margin-left: 0; }
  .hamburger-btn { display: flex; }
  .content-area { padding: 16px; max-width: 100%; }
  .topbar { padding: 0 16px; }
  .profile-cover { height: 120px; padding: 0 20px; }
  .agent-avatar { width: 80px; height: 80px; font-size: 30px; }
  .agent-avatar-wrap { margin-bottom: -36px; }
  .profile-body { padding: 44px 20px 24px; }
  .profile-head { flex-direction: column; gap: 14px; }
  .agent-name { font-size: 22px; }
  .rating-card { min-width: unset; width: 100%; }
  .action-row { flex-direction: column; }
  .action-row .btn-rate,
  .action-row .btn-chat { width: 100%; text-align: center; }
  .reviews-section { padding: 20px; }
  .review-card { padding: 14px; }
  .modal-box { max-width: 95vw; }
  .star-pick { font-size: 34px; }
  .modal-header { padding: 20px 20px 0; }
  .modal-body { padding: 18px 20px; }
  .modal-footer { padding: 16px 20px 20px; }
  .toast { left: 16px; right: 16px; bottom: 16px; text-align: center; }
}

@media (max-width: 480px) {
  .profile-cover { height: 100px; padding: 0 14px; }
  .agent-avatar { width: 64px; height: 64px; font-size: 24px; border-width: 3px; }
  .agent-avatar-wrap { margin-bottom: -28px; }
  .profile-body { padding: 36px 14px 18px; }
  .agent-name { font-size: 18px; }
  .info-item { padding: 10px 12px; }
  .star-pick { font-size: 28px; }
  .star-picker-wrap { padding: 18px 14px; }
  .reviews-section { padding: 14px; }
}
</style>
