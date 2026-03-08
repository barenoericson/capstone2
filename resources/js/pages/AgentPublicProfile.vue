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
        <router-link to="/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>
        <div class="nav-section">
          <h3 class="section-title">Browse</h3>
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">Saved</span>
          </router-link>
        </div>
        <div class="nav-section">
          <h3 class="section-title">Activity</h3>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Inbox</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
        </div>
        <div class="nav-section">
          <h3 class="section-title">Settings</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profile</span>
          </router-link>
          <router-link to="/settings" class="nav-item">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">Settings</span>
          </router-link>
        </div>
      </nav>
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg">{{ myName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ myName }}</p>
            <p class="user-role-card">Buyer</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
          <button @click="$router.back()" class="btn-back">← Back</button>
          <div class="topbar-right">
            <router-link
              v-if="agent"
              :to="`/conversations/${agent.user_id}`"
              class="btn-message-agent"
            >
              💬 Message Agent
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
                  <span class="info-icon">📝</span>
                  <div>
                    <span class="info-label">About</span>
                    <p class="info-value bio">{{ agent.bio }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="agent.company_address">
                  <span class="info-icon">📍</span>
                  <div>
                    <span class="info-label">Office</span>
                    <p class="info-value">{{ agent.company_address }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="agent.license_number">
                  <span class="info-icon">🪪</span>
                  <div>
                    <span class="info-label">License</span>
                    <p class="info-value">{{ agent.license_number }}</p>
                  </div>
                </div>
                <div class="info-item" v-if="agent.accreditation">
                  <span class="info-icon">📜</span>
                  <div>
                    <span class="info-label">Accreditation</span>
                    <p class="info-value">{{ agent.accreditation }}</p>
                  </div>
                </div>
                <div class="info-item">
                  <span class="info-icon">📅</span>
                  <div>
                    <span class="info-label">Member Since</span>
                    <p class="info-value">{{ formatDate(agent.created_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="action-row">
                <button class="btn-rate" @click="openRateModal">
                  ⭐ Rate this Agent
                </button>
                <router-link :to="`/conversations/${agent.user_id}`" class="btn-chat">
                  💬 Send Message
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
              <div class="no-reviews-icon">💬</div>
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
  },
};
</script>

<style scoped>
:root {
  --gold: #FFD700;
  --gold-dark: #DAB600;
  --gold-light: #f5c842;
  --black: #100c08;
  --smoke: #f5f5f5;
  --gray: #e0e0e0;
  --white: #ffffff;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

/* =========================================================
   Layout
   ========================================================= */
.dashboard-wrapper {
  display: flex; min-height: 100vh;
  background: var(--smoke);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* =========================================================
   Sidebar
   ========================================================= */
.sidebar {
  width: 260px; min-height: 100vh;
  background: var(--white);
  border-right: 1px solid var(--gray);
  display: flex; flex-direction: column;
  position: fixed; left: 0; top: 0; height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 8px rgba(0,0,0,.07);
  z-index: 100;
}
.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--gray); }
.sidebar-logo { font-size: 22px; font-weight: 800; display: flex; margin: 0; }
.logo-realty { color: var(--black); }
.logo-ph { color: var(--gold); margin-left: 2px; }

.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-section { margin-top: 16px; }
.section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #aaa; margin: 0 16px 6px; display: block; }

.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 16px; margin: 0 8px; border-radius: 8px;
  color: var(--black); text-decoration: none; font-size: 14px; font-weight: 500;
  transition: all .2s;
}
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-weight: 700;
}
.nav-icon { font-size: 17px; min-width: 22px; text-align: center; }

.sidebar-footer { padding: 14px; border-top: 1px solid var(--gray); }
.user-card { background: var(--smoke); border-radius: 8px; padding: 10px; display: flex; align-items: center; gap: 10px; }
.user-avatar-lg {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 15px; flex-shrink: 0;
}
.user-name-card { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; }
.user-role-card { font-size: 11px; color: #999; margin: 2px 0 0; }

/* =========================================================
   Main Content
   ========================================================= */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: var(--white);
  border-bottom: 1px solid var(--gray);
  padding: 0 28px; height: 64px;
  position: sticky; top: 0; z-index: 50;
  box-shadow: 0 2px 6px rgba(0,0,0,.06);
}
.topbar-content { display: flex; align-items: center; justify-content: space-between; height: 100%; }

.btn-back {
  background: none; border: none;
  color: var(--black); font-size: 15px; font-weight: 600;
  cursor: pointer; padding: 8px 12px; border-radius: 8px;
}
.btn-back:hover { background: var(--smoke); color: var(--gold); }

.btn-message-agent {
  padding: 9px 20px; border-radius: 8px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-size: 14px; font-weight: 700;
  text-decoration: none; transition: transform .2s, box-shadow .2s;
}
.btn-message-agent:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(230,174,13,.4); }

.content-area { padding: 28px; max-width: 900px; }

/* Loading */
.loading-state { text-align: center; padding: 80px; color: #999; }
.spinner { width: 40px; height: 40px; border: 4px solid var(--gray); border-top-color: var(--gold); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* =========================================================
   Profile Card
   ========================================================= */
.profile-card {
  background: var(--white); border-radius: 20px; overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,.08); margin-bottom: 24px;
}
.profile-cover {
  height: 160px;
  background: linear-gradient(135deg, var(--black) 0%, #2c2520 60%, #3a2f20 100%);
  display: flex; align-items: flex-end; padding: 0 36px;
  position: relative; overflow: hidden;
}
.cover-pattern {
  position: absolute; inset: 0;
  background: repeating-linear-gradient(
    45deg,
    transparent,
    transparent 30px,
    rgba(230,174,13,0.03) 30px,
    rgba(230,174,13,0.03) 60px
  );
}
.agent-avatar-wrap { position: relative; margin-bottom: -44px; z-index: 2; }
.agent-avatar {
  width: 110px; height: 110px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  border: 5px solid var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: 42px; font-weight: 700; color: var(--black); overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
}

.verification-badge {
  position: absolute; bottom: 6px; right: -2px;
  background: var(--black); color: #22c55e;
  border: 3px solid var(--white);
  border-radius: 20px; padding: 3px 10px; font-size: 10px; font-weight: 700;
  letter-spacing: 0.3px;
}
.verification-badge.verified { color: #22c55e; }
.verification-badge.pending { color: var(--gold); }

.profile-body { padding: 56px 36px 36px; }
.profile-head {
  display: flex; justify-content: space-between; align-items: flex-start;
  flex-wrap: wrap; gap: 20px; margin-bottom: 28px;
}
.profile-head-left { flex: 1; min-width: 200px; }

.agent-name { font-size: 28px; font-weight: 800; color: var(--black); margin: 0 0 2px; }
.agent-company { color: #777; font-size: 15px; margin: 0 0 10px; }
.agent-tags { display: flex; gap: 8px; flex-wrap: wrap; }
.agent-tag {
  padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
  background: var(--smoke); color: #666; border: 1px solid var(--gray);
  text-transform: uppercase; letter-spacing: 0.5px;
}
.agent-tag.gold {
  background: rgba(230,174,13,0.1); color: var(--gold-dark);
  border-color: rgba(230,174,13,0.3);
}

/* Rating card */
.rating-card {
  background: var(--smoke); border-radius: 14px; padding: 16px 24px;
  text-align: center; min-width: 140px;
  border: 1px solid var(--gray);
}
.rating-big {
  font-size: 36px; font-weight: 800; color: var(--black);
  line-height: 1; margin-bottom: 6px;
}
.stars-row { display: flex; gap: 2px; justify-content: center; margin-bottom: 4px; }
.star { font-size: 18px; color: var(--gray); transition: color 0.2s; }
.star.filled { color: var(--gold); }
.rating-count { color: #999; font-size: 12px; font-weight: 500; }

/* Info grid */
.info-grid { display: flex; flex-direction: column; gap: 16px; margin-bottom: 28px; }
.info-item {
  display: flex; gap: 14px; align-items: flex-start;
  padding: 14px 16px; border-radius: 12px;
  background: var(--smoke); border: 1px solid transparent;
  transition: border-color 0.2s;
}
.info-item:hover { border-color: rgba(230,174,13,0.2); }
.info-icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
.info-label {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.8px; color: #999; display: block; margin-bottom: 3px;
}
.info-value { font-size: 14px; color: #444; margin: 0; line-height: 1.5; }
.info-value.bio { color: #555; }

/* Action buttons */
.action-row { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-rate {
  padding: 12px 28px; border-radius: 10px;
  border: 2px solid var(--gold);
  background: var(--white); color: var(--black);
  font-size: 14px; font-weight: 700; cursor: pointer;
  transition: all 0.25s ease;
}
.btn-rate:hover {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  border-color: var(--gold-dark);
  color: var(--black);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(230,174,13,.4);
}
.btn-chat {
  padding: 12px 28px; border-radius: 10px;
  border: 2px solid var(--black);
  background: var(--black); color: var(--gold);
  font-size: 14px; font-weight: 700; cursor: pointer;
  text-decoration: none;
  transition: all 0.25s ease;
}
.btn-chat:hover {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  border-color: var(--gold-dark);
  color: var(--black);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(230,174,13,.4);
}

/* =========================================================
   Reviews Section
   ========================================================= */
.reviews-section {
  background: var(--white); border-radius: 20px; padding: 28px;
  box-shadow: 0 4px 20px rgba(0,0,0,.08);
}
.reviews-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 20px;
}
.section-heading { font-size: 20px; font-weight: 700; color: var(--black); margin: 0; }
.reviews-count {
  font-size: 13px; font-weight: 600; color: #999;
  background: var(--smoke); padding: 4px 12px; border-radius: 20px;
}
.no-reviews { text-align: center; padding: 48px 20px; }
.no-reviews-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
.no-reviews-title { font-size: 16px; font-weight: 700; color: var(--black); margin: 0 0 4px; }
.no-reviews-sub { font-size: 14px; color: #999; margin: 0; }

.reviews-list { display: flex; flex-direction: column; gap: 14px; }

.review-card {
  padding: 18px; border: 1px solid #f0f0f0; border-radius: 14px;
  transition: all 0.2s;
}
.review-card:hover { border-color: rgba(230,174,13,0.2); box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }

.reviewer-avatar {
  width: 42px; height: 42px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  display: flex; align-items: center; justify-content: center;
  color: var(--black); font-weight: 700; font-size: 16px;
  overflow: hidden; flex-shrink: 0;
}
.reviewer-photo { width: 100%; height: 100%; object-fit: cover; }
.reviewer-info { flex: 1; }
.reviewer-name { font-size: 14px; font-weight: 700; color: var(--black); margin: 0 0 3px; }
.review-meta { display: flex; align-items: center; gap: 10px; }
.review-stars { display: flex; gap: 1px; }
.star-sm { font-size: 13px; color: var(--gray); }
.star-sm.filled { color: var(--gold); }
.review-date { font-size: 12px; color: #bbb; }
.review-title { font-size: 15px; font-weight: 700; color: var(--black); margin: 0 0 6px; }
.review-content { font-size: 14px; color: #666; line-height: 1.6; margin: 0; }

/* =========================================================
   Rating Modal
   ========================================================= */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(16,12,8,0.55);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000; padding: 20px;
}
.modal-box {
  background: var(--white); border-radius: 20px;
  width: 100%; max-width: 480px;
  box-shadow: 0 24px 64px rgba(0,0,0,.25);
  overflow: hidden;
  border: 1px solid rgba(230,174,13,0.15);
}
.modal-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 28px 28px 0; gap: 16px;
  background: var(--white);
}
.modal-title { font-size: 22px; font-weight: 800; color: var(--black); margin: 0 0 4px; }
.modal-sub { font-size: 13px; color: #999; margin: 0; }
.modal-close {
  background: var(--white); border: 2px solid var(--gold);
  width: 36px; height: 36px;
  border-radius: 50%; font-size: 16px; color: var(--gold); cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: all 0.2s;
}
.modal-close:hover { background: var(--gold); color: var(--black); }
.modal-body { padding: 24px 28px; background: var(--white); }

/* Star picker */
.star-picker-wrap {
  text-align: center; padding: 28px 20px;
  margin-bottom: 24px;
  background: var(--white);
  border-radius: 16px;
  border: 2px solid rgba(230,174,13,0.3);
  box-shadow: 0 2px 12px rgba(230,174,13,.08);
}
.star-picker-wrap::before {
  content: 'How would you rate this agent?';
  display: block; font-size: 12px; font-weight: 700;
  color: var(--gold-dark); text-transform: uppercase;
  letter-spacing: 1.2px; margin-bottom: 16px;
}
.star-picker { display: flex; gap: 10px; justify-content: center; margin-bottom: 10px; }
.star-pick {
  font-size: 46px; color: var(--gray); cursor: pointer;
  transition: transform 0.15s, color 0.15s, text-shadow 0.15s;
  line-height: 1;
}
.star-pick:hover { transform: scale(1.25); text-shadow: 0 0 16px rgba(230,174,13,.5); }
.star-pick.filled { color: var(--gold); text-shadow: 0 0 12px rgba(230,174,13,.4); }
.rating-label {
  font-size: 15px; color: #bbb; margin: 0;
  height: 22px; font-weight: 700; transition: color 0.2s;
}
.rating-label.active { color: var(--gold-dark); }

/* Form */
.form-group { margin-bottom: 18px; }
.form-label {
  display: block; font-size: 13px; font-weight: 700; color: var(--black);
  margin-bottom: 7px;
}
.form-optional { color: #bbb; font-weight: 500; }
.form-input {
  width: 100%; padding: 12px 16px; border: 2px solid #eee; border-radius: 10px;
  font-size: 14px; outline: none; font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(230,174,13,.12); }
.form-textarea {
  width: 100%; padding: 12px 16px; border: 2px solid #eee; border-radius: 10px;
  font-size: 14px; resize: vertical; font-family: inherit; outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-textarea:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(230,174,13,.12); }
.char-count { display: block; text-align: right; font-size: 11px; color: #bbb; margin-top: 4px; }
.form-error {
  color: #dc2626; font-size: 13px; margin-top: 8px;
  padding: 8px 12px; background: #fef2f2; border-radius: 8px;
}

.modal-footer {
  display: flex; justify-content: flex-end; gap: 12px;
  padding: 20px 28px 28px; border-top: 2px solid rgba(230,174,13,0.2);
  background: var(--white);
}

.btn-cancel {
  padding: 12px 28px; border-radius: 10px;
  border: 2px solid var(--black); background: var(--white);
  color: var(--black); font-size: 14px; font-weight: 700; cursor: pointer;
  transition: all 0.25s ease;
}
.btn-cancel:hover {
  background: var(--black); color: var(--gold);
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(16,12,8,.2);
}

.btn-submit {
  padding: 12px 32px; border-radius: 10px;
  border: 2px solid var(--gold);
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-size: 14px; font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(230,174,13,.3);
  transition: all 0.25s ease;
}
.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(230,174,13,.45);
}
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

/* =========================================================
   Transitions
   ========================================================= */
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

/* =========================================================
   Toast
   ========================================================= */
.toast {
  position: fixed; bottom: 28px; right: 28px;
  padding: 14px 22px; border-radius: 12px; color: var(--white);
  font-size: 14px; font-weight: 600;
  box-shadow: 0 8px 28px rgba(0,0,0,.2);
  z-index: 9999;
  display: flex; align-items: center; gap: 8px;
}
.toast-icon {
  width: 22px; height: 22px; border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 800;
}
.toast-success { background: var(--black); color: var(--gold); }
.toast-error { background: #dc2626; }

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .sidebar { position: fixed; left: -280px; width: 280px; z-index: 1001; transition: left 0.3s ease; }
  .main-content { margin-left: 0; }
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
